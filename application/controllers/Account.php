<?php
defined('BASEPATH') or exit('No direct script access allowed');

ini_set('display_errors',1);

class Account extends Site_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Customers_model');
        $this->load->model('modules/Address_model');
        $this->load->model('modules/Country_model');
        $this->load->model('modules/Zone_model');
        $this->load->model('Order_model');

        $this->data['countries'] = $this->Country_model->filter(['status' => 1])->with_translation()->order_by('name', 'ASC')->all();
    }

    public function index()
    {
        $this->data['title'] = translate('index_title');

        if ($this->customer->is_loggedin()) {


            foreach ($this->data['languages'] as $key => $value) {

                $link = site_url($key . '/account');
                $this->data['languages'][$key] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'code' => $value['code'],
                    'slug' => $value['slug'],
                    'admin' => $value['admin'],
                    'directory' => $value['directory'],
                    'dir' => $value['dir'],
                    'link' => $link
                ];
            }

            $this->form_validation->set_rules('firstname', translate('form_label_firstname'), 'trim|required');
            $this->form_validation->set_rules('lastname', translate('form_label_lastname'), 'trim|required');
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', translate('form_label_password'), 'trim|required|min_length[6]');
            }
            $this->form_validation->set_rules('birthday', translate('form_label_date_of_birth'), 'trim');
            $this->form_validation->set_rules('email', translate('form_label_email'), 'trim|required|valid_email|callback_email_exist');


            if ($this->input->method() == 'post') {
                if ($this->form_validation->run() == true) {
                    $customer = [
                        'firstname' => trim($this->input->post('firstname')),
                        'lastname' => trim($this->input->post('lastname')),
                        'email' => trim($this->input->post('email')),
                        'birthday' => null
                    ];

                    if ($this->input->post("password")) {
                        $customer["password"] = $this->auth->hash_password($this->input->post("password"), $this->data['customer']->id);
                    }

                    $this->Customers_model->update($customer, ['id' => $this->data['customer']->id]);
                    $this->data['message'] = translate('form_success_update');
                }
            }

            $this->template->render('my-account');
        } else {
            redirect(site_url_multi('account/login?redirect=account'));
        }


    }

    public function create()
    {
        $this->data['title'] = translate('create_title');

        // Set validation
        $this->form_validation->set_rules('firstname', translate('form_label_firstname'), 'required|trim');
        $this->form_validation->set_rules('lastname', translate('form_label_lastname'), 'required|trim');
        $this->form_validation->set_rules('password', translate('form_label_password'), 'trim|required|min_length[6]');
        $this->form_validation->set_rules('email', translate('form_label_email'), 'trim|required|valid_email|callback_email_exist');
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password_confirm', translate('form_label_repeat_password'), 'matches[password]');
        }

        if ($this->input->method() == 'post') {
            if ($this->form_validation->run() == true) {
				$_email = trim($this->input->post('email'));
                $_password = $this->auth->hash_password($this->input->post("password"), 0);
				$customer = [
                    'firstname' => trim($this->input->post('firstname')),
                    'lastname' => trim($this->input->post('lastname')),
                    'email' => $_email,
                    'birthday' => null,
                    'customer_group_id' => 0,
                    'password' => $_password
                ];
                $customer_group_id = get_setting('default_customer_group_id');
                if ($customer_group_id) {
                    $customer['customer_group_id'] = $customer_group_id;
                    $this->load->model('modules/Customer_group_model');
                    $customer_group = $this->Customer_group_model->fields('approval')->filter(['id' => $customer_group_id])->one();
                    if ($customer_group && $customer_group->approval == 0) {
                        $customer['approval'] = 1;
                        $customer['status'] = 1;
                    }
                }

                $id = $this->Customers_model->insert($customer);
                if ($id) {

					$this->email->from($this->config->item('auth')['email'], $this->config->item('auth')['name']);
					$this->email->to($_email);
					$this->email->subject(translate('thanks_register_title'));
					$this->email->message(sprintf(translate('thanks_register'), $_email, $_password));
					$this->email->send();

                    redirect(site_url_multi('account/login'));
                }
            } else {
                $this->data['message'] = validation_errors();
            }
        }
        $this->template->render('create_account');
    }

    public function forget_password()
    {
        $this->data['title'] = translate('form_label_forgot_password');

        $this->form_validation->set_rules('email', translate('form_label_email'), 'required|trim');

        if ($this->form_validation->run()) {
            if ($this->customer->remind_password($this->input->post('email'))) {
                $this->data['message'] = translate('successfully_forget_password');
            } else {
                $this->data['message'] = translate('user_not_found');
            }
        } else {
            $this->data['message'] = validation_errors();
        }

        $this->template->render('forget_password');
    }

    public function reset_password($key = false)
    {
		if($this->input->server('REQUEST_METHOD') != 'POST' && !$this->customer->getByKey($key))
			redirect(site_url('account/login'));

        $this->data['title'] = translate('form_label_forgot_password');
		//$this->data['reset'] = false;
        //Breadcrumb
        //$this->breadcrumbs->push(translate('reset_password'), 'user/reset_password');

		$this->form_validation->set_rules('password', translate('form_label_password'), 'required|trim');

        if ($this->form_validation->run()) {
			$this->customer->reset_password($this->input->post('key'), $this->input->post('password'));
			print '<script>alert("'.translate('form_success_password').'");location.href="'.site_url('account/login').'"</script>';
		}

      /*   if ($this->customer->reset_password($key)) {
            $this->data['message'] = translate('password_successfully_send');
			$this->data['reset'] = true;
        } else {
            $this->data['message'] = translate('your_verification_key_error');
        } */

		$this->data['key'] = $key;
        $this->template->render('reset_password');
    }

    // Callback function
    function email_exist($email)
    {
        $where = ['email' => $email];

        if ($this->customer->is_loggedin()) {
            $where['id !='] = $this->data['customer']->id;
        }
        $customer_count = $this->Customers_model->filter($where)->count_rows();
        //$user = $this->auth->user_exist_by_email($email);
        if ($customer_count == 0) {
            return true;
        } else {
            $this->form_validation->set_message('email_exist', translate('form_error_email_exist'));
            return false;
        }
    }

    public function orders()
    {
        if (!$this->customer->is_loggedin()) {
            redirect(site_url_multi('account/login?redirect=account/orders'));
        }

        $this->data['title'] = translate('my_orders');


        foreach ($this->data['languages'] as $key => $value) {

            $link = site_url($key . '/account/orders');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }

        $orders = $this->Order_model->filter(['customer_id' => $this->data['customer']->id, 'order_status_id!=0' => null])->order_by('id', 'DESC')->all();

        $statusesQuery = $this->db->select('order_status_id as id, name')
            ->from('wc_order_status_translation')
            ->where('language_id', $this->data['current_lang_id'])
            ->get()
            ->result();

        $statuses = [];

        foreach ($statusesQuery as $value) {

            $statuses[$value->id] = $value->name;

        }

        $custom_currencies = [];

        foreach ($this->data['currencies'] as $c_currency) {

            $custom_currencies[$c_currency->id] = $c_currency;

        }


        if ($orders) {
            foreach ($orders as $order) {
                $data = new stdClass();
                $data->id = $order->id;
                $data->total = $this->currency->formatter($order->total, $order->currency_code, $this->data['current_currency']);
                $data->products = $this->Order_model->get_additional_data('order_product', '*', ['order_id' => $order->id]);

                $data->status = isset($statuses[$order->order_status_id]) ? $statuses[$order->order_status_id] : $statuses[1];

                $this->data['orders'][] = $data;
            }

        }


        if ($this->input->get('order_id') != NULL) {
            $last_orders = $this->Order_model->filter(['customer_id' => $this->data['customer']->id, 'id' => $this->input->get('order_id')])->one();
        } else {
            $last_orders = $this->Order_model->filter(['customer_id' => $this->data['customer']->id])->order_by('id', 'DESC')->one();
        }


        if ($last_orders) {
            $data = new stdClass();
            $data->id = $last_orders->id;
            $data->status = isset($statuses[$last_orders->order_status_id]) ? $statuses[$last_orders->order_status_id] : $statuses[1];
            $data->created_at = $last_orders->created_at;
            $data->total = $this->currency->formatter($last_orders->total, $last_orders->currency_code, $this->data['current_currency']);
            $products = $this->Order_model->get_additional_data('order_product', '*', ['order_id' => $last_orders->id]);
            $this->data['last_order'] = [];
            $data->item = count($products);

            if ($products) {
                foreach ($products as $product) {
                    $product->options = $this->Order_model->get_additional_data('order_option', '*', ['order_product_id' => $product->product_id, 'order_id' => $last_orders->id]);

                    $product->price = $this->currency->formatter($product->price, $custom_currencies[$product->currency_original]->code, $this->data['current_currency']);

                    $data->products[] = $product;
                }
                $this->data['last_order'] = $data;
            }
        }

        if(isset($_GET['d'])){
            print_r($this->data['last_order']); die;
        }

        $this->template->render('my-order');
    }


    public function track($code = null)
    {
        $this->load->library('shipping/Ems');

        foreach ($this->data['languages'] as $key => $value) {

            $link = site_url($key . '/account/track/' . $code);
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }

        $order_products = $this->db->select('*')->from('wc_order_product')->where('tracking_code', $code)->get()->result_array();

        if (count($order_products) > 0) {

            $product_ids = [];
            $order_ids = [];

            foreach ($order_products as $order_product) {
                $product_ids[] = $order_product['product_id'];
                $order_ids[]   = $order_product['order_id'];
            }

            $order = new stdClass();
            $order->order = $order_ids[0];

            $products       = $this->db->select('*')->from('wc_product')->where_in('id', $product_ids)->get()->result_array();
            $user_orders    = $this->db->select('*')->from('wc_order')->where_in('id', $order_ids)->get()->result_array();

            $shipping = [];
            foreach ($user_orders as $user_order){
               $sh = json_decode($user_order['combined_shipping']);
               foreach ($sh as $shp){
                  $shipping[] = $shp->code;
               }
            }

            $order_status_id = $this->db->select('*')->from('wc_order_history')->where_in('order_id', $order_ids)->get()->result_array();
            $order_status = end($order_status_id);


            if($order_status['order_status_id'] == 2){

                if(in_array('ems_standart',$shipping)){
                    $this->data['start_date']   = strtotime($order_status['created_at']) + (12 * 86400);
                    $this->data['end_date']     = strtotime($order_status['created_at']) + (20 * 86400);
                }elseif (in_array('ems_express',$shipping)){
                    $this->data['start_date']   = strtotime($order_status['created_at']) + (3 * 86400);
                    $this->data['end_date']     = strtotime($order_status['created_at']) + (5 * 86400);
                }else{
                    $this->data['start_date']   = strtotime($order_status['created_at']) + (1 * 86400);
                    $this->data['end_date']     = strtotime($order_status['created_at']) + (3 * 86400);
                }

                $time                           = time() - strtotime($order_status['created_at']);

            }elseif($order_status['order_status_id'] == 4 or $order_status['order_status_id'] == 13){
                $this->data['transit'] = false;
            }



            //if ($user_orders[0]['customer_id'] == $this->data['customer']->id) {

                $end                    = end($order_status_id);
                $order_translate_name   = $this->db->select('*')->from('wc_order_status_translation')->where_in('order_status_id', $end['order_status_id'])->where('language_id', $this->data['current_lang_id'])->get()->result_array();


                $weight = 0;
                foreach ($products as $product) {

                    if ($product['weight_class_id'] == 2) {
                        $weight += (int)$product['weight'];
                    }

                    if ($product['weight_class_id'] == 1) {
                        $weight += (int)$product['weight'] * 1000;
                    }

                    if ($product['weight_class_id'] == 3) {
                        $weight += (int)$product['weight'];
                    }
                }


                if(isset($this->data['start_date']) && isset($this->data['end_date'])){

                    $this->data['end_date']     = date('d.m.y',$this->data['end_date']);
                    $this->data['start_date']   = date('d.m.y',$this->data['start_date']);
                    $this->data['transit']      = true;
                }

                $order->weight = $weight;

                $this->data['track']    = $order;
                $this->data['time']     = abs(round(($time ?? 0)  / 60 / 60 / 24));
                $this->data['status']   = end($order_translate_name);

                if ($code) {
                    $this->data['results']  = $this->ems->tracking($code);
                    $this->data['code']     = $code;
                    $this->template->render('tracking');
                }

//            } else {
//                redirect(site_url('account'));
//            }



        } elseif(intval($code)) {

            $this->data['transit'] = false;

            $order_products       = $this->db->select('*')->from('wc_order_product')->where('order_id', $code)->get()->result_array();
            if(count($order_products) > 0){
                $prod_id = [];
                $weight = 0;
                foreach ($order_products as $product) {
                    $prod_id[] = $product['product_id'];
                }

                $products = $this->db->select('*')->from('wc_product')->where_in('id',$prod_id)->get()->result_array();
                foreach ($products as $product){
                    if ($product['weight_class_id'] == 2) {
                        $weight += (int)$product['weight'];
                    }

                    if ($product['weight_class_id'] == 1) {
                        $weight += (int)$product['weight'] * 1000;
                    }

                    if ($product['weight_class_id'] == 3) {
                        $weight += (int)$product['weight'] * ceil('453,592');
                    }
                }

                // 3_[Lr9#uubX6
                $orders    = end($this->db->select('*')->from('wc_order_history')->where('order_id', $code)->get()->result_array());

                if($orders['order_status_id'] == 2){
                    $time                           = time() - strtotime($orders['created_at']);
                    $time                           = date('d',$time);
                    $this->data['transit'] = true;
                }elseif($orders['order_status_id'] == 4 or $orders['order_status_id'] == 13){
                    $this->data['transit'] = false;
                }


                $this->data['time']   = abs(round(($time ?? 0)  / 60 / 60 / 24));
                $this->data['status'] = $orders['order_status_id'];
                $this->data['weight'] = $weight;
                $this->data['code']   = $code;
                $this->template->render('order-track');
            }


        }else{
            redirect(site_url('account'));
        }



    }

    public function testtrack($code)
    {
        if ($code) {
            $this->load->library('shipping/Ems');
            $this->data['results'] = $this->ems->tracking($code);

            $this->template->render('track');
        }
    }

    public function address_book()
    {
        if (!$this->data['customer']) {
            redirect(site_url_multi('account/login?redirect=account/address_book'));
        }

        foreach ($this->data['languages'] as $key => $value) {

            $link = site_url($key . '/account/address_book/');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }


        $addresses = $this->Address_model->filter(['customer_id' => $this->data['customer']->id])->with_trashed()->all();

        if ($addresses) {
            foreach ($addresses as $address) {
                $this->data['addresses'][] = [
                    'id' => $address->id,
                    'firstname' => $address->firstname,
                    'lastname' => $address->lastname,
                    'company' => $address->company,
                    'address1' => $address->address_1,
                    'address2' => $address->address_2,
                    'city' => $address->city,
                    'postcode' => $address->postcode,
                    'phone' => $address->phone,
                    'country' => $this->Country_model->filter(['id' => $address->country_id])->with_translation()->one()->name,
                    'zone' => $this->Zone_model->filter(['id' => $address->zone_id])->one()->name ?? '',
                ];
            }

        }

        $this->data['title'] = translate('address_book_title');


        $this->template->render('address-book');
    }

    public function add_address_book()
    {
        $this->data['title'] = translate('address_book_title');

        foreach ($this->data['languages'] as $key => $value) {

            $link = site_url($key . '/account/add_address_book/');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }

        $this->form_validation->set_rules('firstname', translate('firstname'), 'required|trim');
        $this->form_validation->set_rules('lastname', translate('lastname'), 'required|trim');
        $this->form_validation->set_rules('city', translate('city'), 'required|trim');
        $this->form_validation->set_rules('address_1', translate('address_1'), 'required|trim');
        $this->form_validation->set_rules('postcode', translate('postcode'), 'required|trim');
        $this->form_validation->set_rules('country_id', translate('country'), 'required|trim');
        $this->form_validation->set_rules('zone_id', translate('region'), 'required|trim|greater_than[0]');
        $this->form_validation->set_rules('phone', translate('phone'), 'required|trim');

        if ($this->form_validation->run()) {
            $address_data = [
                'customer_id' => $this->data['customer']->id,
                'firstname' => $this->input->post('firstname'),
                'lastname' => $this->input->post('lastname'),
                'company' => $this->input->post('company'),
                'address_1' => $this->input->post('address_1'),
                'address_2' => $this->input->post('address_2'),
                'city' => $this->input->post('city'),
                'postcode' => $this->input->post('postcode'),
                'country_id' => $this->input->post('country_id'),
                'zone_id' => $this->input->post('zone_id'),
                'phone' => $this->input->post('phone')
            ];

            $this->Address_model->insert($address_data);
            redirect(site_url_multi('account/address_book'));
        } else {
            $this->data['address'] = new stdClass();
            $this->data['address']->firstname = $this->input->post('firstname');
            $this->data['address']->lastname = $this->input->post('lastname');
            $this->data['address']->company = $this->input->post('company');
            $this->data['address']->address_1 = $this->input->post('address_1');
            $this->data['address']->address_2 = $this->input->post('address_2');
            $this->data['address']->city = $this->input->post('city');
            $this->data['address']->postcode = $this->input->post('postcode');
            $this->data['address']->country_id = $this->input->post('country_id');
            $this->data['address']->zone_id = $this->input->post('zone_id');
            $this->data['address']->phone = $this->input->post('phone');
        }

        $this->template->render('address-book-form');
    }

    public function edit_address_book($id)
    {
        $this->data['title'] = translate('address_book_title');

        $this->data['address'] = $this->Address_model->filter(['id' => $id])->with_trashed()->one();

        foreach ($this->data['languages'] as $key => $value) {

            $link = site_url($key . '/account/edit_address_book/' . $id);
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }

        if ($this->data['address']) {

            $this->form_validation->set_rules('firstname', translate('firstname'), 'required|trim');
            $this->form_validation->set_rules('lastname', translate('lastname'), 'required|trim');
            $this->form_validation->set_rules('city', translate('city'), 'required|trim');
            $this->form_validation->set_rules('address_1', translate('address_1'), 'required|trim');
            $this->form_validation->set_rules('postcode', translate('postcode'), 'required|trim');
            $this->form_validation->set_rules('country_id', translate('country_id'), 'required|trim');
            $this->form_validation->set_rules('zone_id', translate('zone_id'), 'required|trim|greater_than[0]');
            $this->form_validation->set_rules('phone', translate('phone'), 'required|trim');

            if ($this->form_validation->run()) {
                $address_data = [
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname'),
                    'company' => $this->input->post('company'),
                    'address_1' => $this->input->post('address_1'),
                    'address_2' => $this->input->post('address_2'),
                    'city' => $this->input->post('city'),
                    'postcode' => $this->input->post('postcode'),
                    'country_id' => $this->input->post('country_id'),
                    'zone_id' => $this->input->post('zone_id'),
                    'phone' => $this->input->post('phone')
                ];

                $this->Address_model->update($address_data, ['customer_id' => $this->data['customer']->id, 'id' => $id]);
                redirect(site_url_multi('account/address_book'));
            }

            $this->template->render('address-book-form');
        } else {
            show_404();
        }
    }

    public function delete_address_book($id)
    {
        $this->db->where([
            'id' => $id,
            'customer_id' => $this->data['customer']->id
        ]);
        $this->db->delete('address');


//		$this->Address_model->delete(['id' => $id, 'customer_id' => $this->data['customer']->id]);
        redirect(site_url_multi('account/address_book'));
    }

    public function ajax_add_address()
    {
        $response = ['status' => false, 'error' => ""];

        if ($this->customer->is_loggedin()) {
            $this->form_validation->set_rules('firstname', translate('firstname'), 'required|trim');
            $this->form_validation->set_rules('lastname', translate('lastname'), 'required|trim');
            $this->form_validation->set_rules('city', translate('city'), 'required|trim');
            $this->form_validation->set_rules('address_1', translate('address_1'), 'required|trim');
            $this->form_validation->set_rules('postcode', translate('postcode'), 'required|trim');
            $this->form_validation->set_rules('country_id', translate('country'), 'required|trim');
            $this->form_validation->set_rules('zone_id', translate('region'), 'required|trim|greater_than[0]');
            $this->form_validation->set_rules('phone', translate('phone'), 'required|trim');

            if ($this->input->method() == 'post') {
                if ($this->form_validation->run() == true) {
                    $address_data = [
                        'customer_id' => $this->data['customer']->id,
                        'firstname' => $this->input->post('firstname'),
                        'lastname' => $this->input->post('lastname'),
                        'company' => $this->input->post('company'),
                        'address_1' => $this->input->post('address_1'),
                        'address_2' => $this->input->post('address_2'),
                        'city' => $this->input->post('city'),
                        'postcode' => $this->input->post('postcode'),
                        'country_id' => $this->input->post('country_id'),
                        'zone_id' => $this->input->post('zone_id'),
                        'phone' => $this->input->post('phone')
                    ];

                    $response['status'] = true;
                    $id = $this->Address_model->insert($address_data);
                    $response['address_id'] = $id;
                } else {
                    $response['error'] = $this->form_validation->error_array();
                }
            }
        }

        $this->template->json($response);
    }

    public function login()
    {
        if (!$this->customer->is_loggedin()) {

            $this->data['title'] = translate('login_title');

            if ($this->input->get('redirect') != NULL) {
                $this->data['redirect'] = $this->input->get('redirect');
            } else {
                $this->data['redirect'] = '';
            }

            foreach ($this->data['languages'] as $key => $value) {

                $link = site_url($key . '/account/login/');
                $this->data['languages'][$key] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'code' => $value['code'],
                    'slug' => $value['slug'],
                    'admin' => $value['admin'],
                    'directory' => $value['directory'],
                    'dir' => $value['dir'],
                    'link' => $link
                ];
            }

            $this->form_validation->set_rules('email', translate('form_label_email'), 'trim|required|valid_email');
            $this->form_validation->set_rules('password', translate('form_label_password'), 'trim|required');

            if ($this->form_validation->run() === true) {
                if ($this->customer->login($this->input->post('email'), $this->input->post('password'))) {
                    if ($this->input->post('redirect')) {
						$r = $this->input->post('redirect');
						redirect(site_url_multi($r));
						die;
                    } else {
                        redirect($_SERVER['HTTP_REFERER']);
                    }

                } else {
                    $this->data['error_message'] = $this->customer->print_errors();
                }
            }

            $this->template->render('login');
        } else {
            redirect(base_url());
        }
    }

    public function logout()
    {
        $this->customer->logout();
        redirect(site_url_multi('account/login'));
    }

    public function region()
    {

        $this->data['rows'][0] = [
            'id' => 0,
            'name' => translate('select_region')
        ];

        $filter['status'] = 1;

        if ($this->input->get('country_id')) {
            $filter['country_id'] = (int)$this->input->get('country_id');

            if ($this->input->get('sell')) {
                $filter['sell'] = 1;
            }

            $this->load->model('modules/Zone_model');
            $rows = $this->Zone_model->filter($filter)->order_by('name', 'ASC')->all();

            if ($rows) {
                foreach ($rows as $row) {
                    $data = new stdClass();
                    $data->id = $row->id;
                    $data->name = $row->name;

                    $this->data['rows'][] = $data;
                }
            }

        }
        $this->template->json($this->data['rows']);
    }

    public function facebook_login()
    {
		
        $this->load->library('facebook');
        $data['user'] = array();
        // Check if user is logged in
        if ($this->facebook->is_authenticated()) {
            // User logged in, get user details
            $user = $this->facebook->request('get', '/me?fields=id,name,email,picture.type(large).width(250).height(250)');
            if (!isset($user['error'])) {

                // Success login
                $user_id = $user['id'];
                $fullname = $user['name'];
                $email = $user['email'];

                $filename = '';

                $data = $user;

                // Check user exist
                $customer_id = $this->customer->get_user_id($email);

				
                if (!$customer_id) {

                    $firstname = '';
                    $lastname = '';
                    if ($fullname) {
                        $fullname = explode(' ', $fullname);
                        if (array_key_exists(0, $fullname)) {
                            $firstname = $fullname[0];
                            $lastname = $fullname[1];
                        }
                    }

                    $customer = [
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'email' => $email,
                        'birthday' => null,
                        'customer_group_id' => 0,
                        'status' => 1,
                        'password' => $this->auth->hash_password($data['id'], 0)
                    ];

                    $response = $this->Customers_model->insert($customer);
					
                    if ($response) {
                        $this->customer->login_fast($response);
                        redirect($this->session->URI_SCRIPT);
                    }
                } else {
                    $this->customer->login_fast($customer_id);
					
                    redirect($this->session->URI_SCRIPT);
                }

            }
        }

        redirect(base_url());
    }

    // Google
    public function google_login()
    {
        $this->load->library('google');
        $google_data = $this->google->validate();

        $data = array(
            'id' => $google_data['id'],
            'fullname' => $google_data['name'],
            'email' => $google_data['email'],
        );

        if ($data && !$this->customer->is_loggedin()) {

            $customer_id = $this->customer->get_user_id($data['email']);
            // Check user exist
            if (!$customer_id) {
                // email doesnt exist
                $firstname = '';
                $lastname = '';

                if ($data['fullname']) {
                    $fullname = explode(' ', $data['fullname']);
                    if (array_key_exists(0, $fullname)) {
                        $firstname = $fullname[0];
                    }
                    if (array_key_exists(1, $fullname)) {
                        $lastname = $fullname[1];
                    }
                }

                $customer = [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $data['email'],
                    'birthday' => null,
                    'customer_group_id' => 0,
                    'status' => 1,
                    'password' => $this->auth->hash_password($data['id'], 0)
                ];

                $response = $this->Customers_model->insert($customer);

                if ($response) {

                    $this->customer->login_fast($response);
                    redirect('/');
                }
            } else {
                $this->customer->login_fast($customer_id);
                redirect('/');
            }
        }

        redirect(base_url());
    }

    public function change_address()
    {
        $addressID = (int) $this->input->post('address');

        $addresses = $this->Address_model->filter(['customer_id' => $this->data['customer']->id, 'id' => $addressID])->with_trashed()->all();

        if(count($addresses)) {

            $this->db->query('UPDATE wc_customer SET address_id=' . $addressID . ' WHERE id=' . $this->data['customer']->id);

            unset($_SESSION['country']);

        }
    }



}
