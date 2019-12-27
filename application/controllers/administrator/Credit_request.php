<?php

defined('BASEPATH') or exit('No direct script access allowed');


ini_set('display_errors',1);
error_reporting(1);

class Credit_request extends Administrator_Controller

{

	public function __construct()

	{

		parent::__construct();

		$this->load->model('Credit_request_model');

		$this->load->model('modules/Address_model');

		$this->load->model('Customers_model');



    }



	public function index()

	{

		$this->data['title'] = translate('index_title');

		$this->data['subtitle'] = translate('index_description');



		// Sets search field

		$this->data['search_field'] = [

			'name' => [

				'property' => 'search',

				'type' => 'search',

				'name' => 'id',

				'class' => 'form-control',

				'value' => $this->input->get('id'),

				'placeholder' => translate('search_placeholder', true),

			],

		];



		// Sets Table columns

		$this->data['fields'] = ['id', 'product_name', 'username', 'phone', 'e-mail', 'date' , 'created_at', 'status' ];



		if ($this->data['fields']) {

			foreach ($this->data['fields'] as $field) {

				$this->data['columns'][$field] = [

					'table' => [

						$this->data['current_lang'] => translate('table_head_' . $field),

					],

				];

			}

		}

		// Checks GET method, session and collects field records

		if ($this->input->get('fields')) {

			$this->data['fields'] = $this->input->get('fields');

			$this->session->set_userdata($this->controller . '_fields', $this->input->get('fields'));

		} elseif ($this->session->has_userdata($this->controller . '_fields')) {

			$this->data['fields'] = $this->session->userdata($this->controller . '_fields');

		} else {

			$this->data['fields'] = array_keys($this->data['columns']);

		}



		foreach ($this->data['fields'] as $field) {

			$columns[$field] = $this->data['columns'][$field];

		}



		//Content Language

		if ($this->input->get('language_id')) {

			$language_id = (int) $this->input->get('language_id');

			$option_language_id = (int) $this->input->get('language_id');

			$this->session->set_userdata('option_language_id', $option_language_id);

		} elseif ($this->session->has_userdata('option_language_id')) {

			$language_id = (int) $this->session->userdata('option_language_id');

		} else {

			$language_id = $this->data['current_lang_id'];

		}

		// End Content Language





		// Filters for banned and not specified name

		$filter = [];

		if ($this->input->get('status') != null) {

			$filter['status'] = $this->input->get('status');

		}



		if ($this->input->get('order_status_id') != null) {

			$filter['order_status_id'] = (int)$this->input->get('order_status_id');

		}

		if ($this->input->get('id') != null) {

			$filter['id LIKE "%' . $this->input->get('id') . '%"'] = null;

		}

		// Sorts by column and order

		$sort = [

			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',

			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',

		];



      //  $filter['order_status_id!=0'] = null;

		

		// Gets records count from database

		$this->data['total_rows'] = $this->{$this->model}->filter($filter)->count_rows();

		$segment_array = $this->uri->segment_array();

		$page = (ctype_digit(end($segment_array))) ? (int)end($segment_array) : 1;



		// Checks if per_page retrieved from GET method and sets per_page to session and to data.

		if ($this->input->get('per_page')) {

			$this->data['per_page'] = (int)$this->input->get('per_page');

			${$this->controller . '_per_page'} = (int)$this->input->get('per_page');

			$this->session->set_userdata($this->controller . '_per_page', ${$this->controller . '_per_page'});

		} elseif ($this->session->has_userdata($this->controller . '_per_page')) {

			$this->data['per_page'] = $this->session->userdata($this->controller . '_per_page');

		} else {

			$this->data['per_page'] = 10;

		}

		

		$this->data['message'] = ($this->session->flashdata('message')) ? $this->session->flashdata('message') : '';



		// Gets all records from database with given criterias

		$total_rows = $this->{$this->model}->where($filter)->count_rows();

		$rows = $this->{$this->model}->fields('*')->filter($filter)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();


		// Sets custom row's data options

		$custom_rows_data = [
			 [
			 	'column' => 'status',
			 	'callback' => 'get_accept',
			 	'params' => '',
			 ]
		];



		// Set action buttons

		$action_buttons = [];

		if (check_permission('order', 'show')) {

			$action_buttons['show'] = true;

		}

		if($rows) {

			foreach ($rows as &$row) {

				//$this->data['fields'] = ['id', 'username', 'product_name', 'phone', 'created_at', 'updated_at'];

				//$row->total = $this->currency->formatter(ceil($row->loan_period*$row->monthly_payment));

	

				unset(

					$row->loan_period, $row->salary, $row->staj,

					$row->staj_muddeti, $row->rate, $row->product_salary,

					$row->monthly_payment, $row->personality_no, $row->personality_fin_no,

					$row->birth, $row->address, $row->home_phone, $row->order_status_id,

					$row->language_id, $row->currency_id, $row->currency_code, $row->currency_value

					, $row->ip, $row->forwarded_ip, $row->user_agent, $row->accept_language, $row->deleted_at,$row->delivery_type,

					$row->delivery_cost,$row->count,$row->total,$row->quaranty_price,$row->cancelled_reason

				);

			}

		}

		// Generates Table with given records

		$this->wc_table->set_module(false);
		$this->wc_table->set_columns($columns);
		$this->wc_table->set_rows($rows);
		$this->wc_table->set_custom_rows($custom_rows_data);
		$this->wc_table->set_action($action_buttons);
		$this->data['table'] = $this->wc_table->generate();

		// Sets Pagination options and initialize

		$config['base_url'] = site_url_multi($this->directory . $this->controller . '/index');
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->data['per_page'];
		$config['reuse_query_string'] = true;
		$config['use_page_numbers'] = true;

		$this->pagination->initialize($config);
		$this->data['pagination'] = $this->pagination->create_links();

		$this->data['buttons'][] = [
			'type' => 'button',
			'text' => translate('header_button_delete', true),
			'class' => 'btn btn-danger btn-labeled heading-btn',
			'id' => 'deleteSelectedItems',
			'icon' => 'icon-trash',
			'additional' => [
				'data-href' => site_url($this->directory . $this->controller . '/delete')
			]

		];

		$this->load->model('modules/Order_status_model');

		$order_statuses = $this->Order_status_model->with_translation()->all();

		// Sets Breadcrumb links

		$this->data['breadcrumb_links'][] = [

			'text' => translate('breadcrumb_link_all', true),

			'href' => site_url($this->directory . $this->controller),

			'icon_class' => 'icon-database position-left',

			'label_value' => $this->{$this->model}->count_rows(),

			'label_class' => 'label label-primary position-right',

		];



		if($order_statuses)

		{

			foreach($order_statuses as $order_status)

			{

				if($this->{$this->model}->filter(['order_status_id' => $order_status->id])->count_rows() > 0)

				{

					$this->data['breadcrumb_links'][] = [

						'text' => $order_status->name,

						'href' => site_url($this->directory . $this->controller).'?order_status_id='.$order_status->id,

						'icon_class' => 'icon-menu7 position-left',

						'label_value' => $this->{$this->model}->filter(['order_status_id' => $order_status->id])->count_rows(),

						'label_class' => 'label label-default position-right',

					];
				}
			}
		}



		$this->template->render();

	}

	



	public function show($id)

	{

		



		$this->data['title'] = translate('show_title');

		$this->data['subtitle'] = translate('show_description');

		

		$this->data['buttons'][] = [

			'type' => 'a',

			'text' => translate('invoice'),

			'class' => 'btn btn-primary btn-labeled heading-btn',

			'id' => '',

			'icon' => 'icon-calculator',

			'href'	=> site_url_multi($this->admin_url.'/credit_request/invoice/'.$id)

		];



		$this->data["request"] = $this->db->query("select * from wc_credit_request where id='$id'")->row();



		$this->template->render();

	}

	public function change_request_status($stat,$id){

		if($stat=="accept"){

			$this->db->query("update wc_credit_request set status='1' where id='$id'");

		}

		else if($stat=="cancel"){

			$reason = $this->input->post("cancelled_reason");
			$this->db->query("update wc_credit_request set status='2',cancelled_reason='$reason' where id='$id'");

		}

		redirect($this->input->server('HTTP_REFERER'));

	}

	public function booking()

	{

		if($this->input->method() == 'post')

		{

			$this->form_validation->set_rules('type', 'Type', 'required|trim');

			$this->form_validation->set_rules('order_id', 'Order ID', 'required|trim');

			$this->form_validation->set_rules('tracking_code', 'Tracking Code', 'required|trim');

			

			if($this->form_validation->run())

			{

				if($this->input->post('ems_express_product'))

				{

					$products = explode(',', $this->input->post('ems_express_product'));

					$service = 'Express';

				}

				elseif($this->input->post('ems_standard_product'))

				{

					$products = explode(',', $this->input->post('ems_standard_product'));

					$service = 'Standard';

				}

				elseif($this->input->post('ems_premium_product'))

				{

					$products = explode(',', $this->input->post('ems_premium_product'));

					$service = 'Premium';

				}

				elseif($this->input->post('ems_economy_product'))

				{

					$products = explode(',', $this->input->post('ems_economy_product'));

					$service = 'Economy';

				}



				$weight = 0;

				$length = 0;

				$height = 0;

				$width = 0;



				$this->load->model('Order_product_model');

				$this->load->model('Product_model');

				$this->load->model('Customers_model');

				$this->load->model('modules/Address_model');

				$order_product_list = $this->Order_product_model->filter(['id IN('.implode(',', $products).')' => NULL])->with_trashed()->all();

				$order_data = $this->Order_model->filter(['id' => $this->input->post('order_id')])->one();



				$customer_data = $this->Customers_model->filter(['id' => $order_data->customer_id])->one();

				$address_data = $this->Address_model->filter(['id' => $order_data->address_id])->with_trashed()->one();





				

				if($order_product_list)

				{

					foreach($order_product_list as $order_product)

					{

						$product_arr[] = $order_product->product_id;

					}



					

					foreach($product_arr as $product_single)

					{

						$product_data = $this->Product_model->filter(['id' => $product_single])->one();

						if($product_data)

						{

							if($product_data->length_class_id != 1)

							{

								$this->load->library('Length');

								$Width_temp = $this->length->convert($product_data->width, $product_data->length_class_id, 1);

								$height_temp = $this->length->convert($product_data->height, $product_data->length_class_id, 1);

								$length_temp =  $this->length->convert($product_data->length, $product_data->length_class_id, 1);

							}

							else

							{

								$width_temp = $product_data->width;

								$height_temp = $product_data->height;

								$length_temp = $product_data->length;

							}



							

							$width = $width+$width_temp;							

							$height = $height+$height_temp;

							$length = $length+$length_temp;



							if($product_data->weight_class_id != 2)

							{

								$this->load->library('Weight');

								$weight_temp = $this->weight->convert($product_data->weight, $product_data->weight_class_id, 2);

							}

							else

							{

								$weight_temp = $product_data->weight;

							}



							$weight = $weight+$weight_temp;

						}

					}

				}



				





				// $receiver = [  

				// 	'contact_name'	=> $address_data->firstname,

				// 	'company_name' => 'asdf',//$address_data->company,

				// 	'address' => $address_data->address_1,

				// 	'city' => '156911',//$address_data->zone_id,

				// 	'contact_mobile' => '04565879', //$address_data->phone,

				// 	'contact_phone' => '04565879', //$address_data->phone,

				// 	'email' => $customer_data->email,

				// 	'zip_code' => $address_data->postcode,

				// 	'state' => '156911',

				// 	'country' => $address_data->country_id,

				// 	'shipment_type'	=> $service,

				// 	'weight' => $weight,

				// 	'width' => $width,

				// 	'height' => $height,

				// 	'length' => $length

				// ];



				// $receiver = [  

				// 	'contact_name'	=> 'siraj',

				// 	'company_name' => 'eppppg',//$address_data->company,

				// 	'address' => 'adf',

				// 	'city' => '348626',//$address_data->zone_id,

				// 	'contact_mobile' => '1412412', //$address_data->phone,

				// 	'contact_phone' => '455566', //$address_data->phone,

				// 	'email' => 'sasdf@asdf.com',

				// 	'zip_code' => '1111',

				// 	'state' => '348626',

				// 	'country' => '162',

				// 	'shipment_type'	=> 'Premium',

				// 	'weight' => 100,

				// 	'width' => 12,

				// 	'height' => 12,

				// 	'length' => 12

				// ];

				 



				// $this->load->library('shipping/Ems');

				// $response = $this->ems->booking($receiver);

				// if($response)

				// {

					foreach($products as $product_n)

					{

						$this->db->where('id', $product_n);

						$this->db->update('order_product', ['tracking_code' => $this->input->post('tracking_code')]);

					}



					redirect($this->input->server('HTTP_REFERER'));

				//}

				



			}



		}

	}



	public function invoice($id)

	{

		$this->data['title'] = translate('show_title');

		$this->data['subtitle'] = translate('show_description');

		

		$this->data["request"] = $this->db->query("select * from wc_credit_request where id='$id'")->row();



		$this->template->render('credit_request/invoice', 'invoice');

	}



	public function refund($order_id)

	{

		if($this->input->post('amount'))

		{

			$amount = (int) ($this->input->post('amount') * 100);



			$payment = $this->db->from('checkoutcom')

                ->where('order_id', $order_id)

                ->limit(1)

                ->order_by('id', 'ASC')

                ->get()

                ->row();





			if($amount > (int) $payment->amount) {

                redirect($this->input->server('HTTP_REFERER'));

                die;

            }



            $password='sk_d5f00eb9-3cca-431d-aea9-0d5927d0ea3b';

            $URL='https://api.checkout.com/payments/'.($payment_id = $payment->c_payment_id ?: $payment->payment_id).'/refunds';



            $ch = curl_init();





            $headr = array();

            $headr[] = 'Content-type: application/json';

            $headr[] = 'Authorization: '.$password;



            curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);

            curl_setopt($ch, CURLOPT_POST,true);



            curl_setopt($ch, CURLOPT_URL,$URL);

            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds

            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_USERPWD, $password);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data = json_encode([

                'amount'    =>  $amount,

                'reference' =>  null,

                'metadata' =>  null,

            ]));



//            var_dump($data);die;



            $respond = curl_exec ($ch);



            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);



            /*if(isset($_COOKIE['test'])) {



                var_dump($respond, $status_code, $URL, $data);



                die;

            }*/



            curl_close ($ch);



            if($status_code == 202) {



                $data = array(

                    'order_id'          => $order_id,

                    'order_status_id'   => 8,

                    'created_at'        => date('Y-m-d H:i:s'),

                    'comment'           =>  'Refunded: $ ' .$amount

                );



                $this->db->insert('order_history', $data);



                $this->db->set('order_status_id', 8);

                $this->db->where('id', $order_id);

                $this->db->update('order');



            }







//			$this->load->library('payment/Credit');

//			$res = $this->credit->refund($order_id, $payment['amount']);

//			var_dump($res);die();

			redirect($this->input->server('HTTP_REFERER'));

		}

	}



	public function refund_product()

    {

        if($product_id = $this->input->post('product_id')) {

            $this->db->set('verify', '2');

            $this->db->where('id', $product_id);

            $this->db->update('wc_order_product');



            $total = $this->db->select('total_original')

                ->from('wc_order_product')

                ->where('id', $product_id)

                ->get()

                ->row()

                ->total_original;



            $vendor_percent = $this->db->select('vendor_percent')

                ->from('wc_order_product')

                ->where('id', $product_id)

                ->get()

                ->row()

                ->vendor_percent;



            $total = $total- ($total / 100) * $vendor_percent;



            $user_id = $this->db

                ->select('P.created_by as user_id')

                ->from('wc_order_product OP')

                ->join('wc_product P', 'OP.product_id=P.id')

                ->where('OP.id', $product_id)

                ->get()

                ->row()

                ->user_id;



            $this->db->insert('wc_transaction', [

                'bank_account'  =>  0,

                'amount'        =>  -$total,

                'user_id'       =>  $user_id,

                'status'        =>  3,

                'comment'       =>  'Refunded',

                'created_by'    =>  $this->auth->get_user()->id

            ]);



            $this->db->set('balance', 'balance-' . $total, false);

            $this->db->where('id', $user_id);

            $this->db->update('wc_users');

        }



        return false;

    }



	public function add_payment($order_id = false)

	{

		if($order_id)

		{



		    $percent = $this->input->post('percent') ?: 0;



			$this->load->model('Order_product_model');

			$order_product = $this->Order_product_model->filter(['id' => $order_id])->with_trashed()->one();

			if($order_product)

			{

				$this->load->model('Product_model');

				$product = $this->Product_model->filter(['id' => $order_product->product_id])->one();

				

				if($product)

				{

					$order_product_payment = [

						'user_id' => $product->created_by,

						'order_id' => $order_product->order_id,

						'product_id' => $order_product->product_id,

						'order_product_id' => $order_id,

						'amount'	=>  ($price = $this->currency->formatter_without_symbol(

                            $order_product->total_original,

                            $this->currency->getCode($order_product->currency_original), 'USD')) - (($price / 100) * $percent)

					];



					$this->db->insert('order_product_payment', $order_product_payment);



					$this->db->set('balance', 'balance+'.$order_product_payment['amount'], FALSE);

					$this->db->where('id', $product->created_by);

					$this->db->update('users');



					$this->db->set('verify', 1);

					$this->db->set('vendor_percent', $percent);

					$this->db->where('id', $order_id);

					$this->db->update('order_product');



					redirect($this->input->server('HTTP_REFERER'));

				}

			}

		}

	}

}

