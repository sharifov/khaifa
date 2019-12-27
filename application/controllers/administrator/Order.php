<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Order_model');
		$this->load->model('Order_option_model');
		$this->load->model('modules/Address_model');
		$this->load->model('Customers_model');
		$this->load->model('modules/Order_status_model');

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
		$this->data['fields'] = ['id', 'customer_id',  'total', 'order_status_id', 'created_at', 'updated_at'];

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
		
		$this->data['date_from'] = $this->input->get('date_from');
		if ($this->data['date_from'] != null) {
			$_d_from = date('Y-m-d', strtotime($this->data['date_from']));
			$filter["created_at >= '{$_d_from}'"] = null;
		}
		$this->data['date_to'] = $this->input->get('date_to');
		if ($this->data['date_to 24:00:00'] != null) {
			$_d_end = date('Y-m-d', strtotime($this->data['date_to']));
			$filter["created_at <= '{$_d_end}'"] = null;
		}
		if ($this->input->get('order_status_id') != null) {
			$filter['order_status_id'] = (int)$this->input->get('order_status_id');
		}else{
			$filter['order_status_id!=0'] = null;
		}
		
		$_seria = false;
		
		if($this->input->get('id') != null) {
			if(!intval($this->input->get('id'))){
				$_name = (string)$this->input->get('id');
			}elseif(strlen($this->input->get('id')) < 12){
				$filter['id LIKE "%' . $this->input->get('id') . '%"'] = null;
			}else
				$_seria = $this->input->get('id');
		}
		
		// Sorts by column and order
		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
		];
		
		if($_name){
			$filter['(firstname LIKE "%'.$_name.'%" OR lastname LIKE "%'.$_name.'%")'] = null;
			$this->data['total_rows'] = $this->{$this->model}->filter($filter)->join('customer', 'order.customer_id = customer.id', 'left')->count_rows();
		}elseif($_seria){
			$filter['product_seria'] = $_seria;
			$this->data['total_rows'] = $this->{$this->model}->filter($filter)->join('order_product', 'order.id = order_product.order_id', 'left')->count_rows();
		}else
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
		if($_name){
			$filter['(firstname LIKE "%'.$_name.'%" OR lastname LIKE "%'.$_name.'%")'] = null;
			$total_rows = $this->{$this->model}->where($filter)->join('customer', 'order.customer_id = customer.id', 'left')->count_rows();
			$rows = $this->{$this->model}->fields('order.*')->filter($filter)->join('customer', 'order.customer_id = customer.id', 'left')->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();
		}elseif($_seria){
			$total_rows = $this->{$this->model}->where($filter)->join('order_product', 'order.id = order_product.order_id', 'left')->count_rows();
			$rows = $this->{$this->model}->fields('order.*')->filter($filter)->join('order_product', 'order.id = order_product.order_id', 'left')->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();
		}else{
			$total_rows = $this->{$this->model}->where($filter)->count_rows();
			$rows = $this->{$this->model}->fields('order.*')->filter($filter)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();
		}
		
		

//		var_dump($this->data['fields'], $rows);die;

//		var_dump($rows);die;

		// Sets custom row's data options
		$custom_rows_data = [
			[
				'column' => 'customer_id',
				'callback' => 'get_customer',
				'params' => '',
			],
			[
				'column' => 'order_status_id',
				'callback' => 'get_order_status',
				'params' => '',
			]
		];

		// Set action buttons
		$action_buttons = [];
		if (check_permission('order', 'show')) {
			$action_buttons['show'] = true;
			$action_buttons['edit'] = true;
		}
		if($rows) {
			foreach ($rows as &$row) {

				$row->total = $this->currency->formatter(ceil($row->total), $row->currency_code, $row->currency_code);
	
				unset(
					$row->currency_code, $row->accept_language, $row->address_id,
					$row->payment_method, $row->payment_code, $row->shipping_method,
					$row->shipping_code, $row->language_id, $row->currency_id,
					$row->currency_value, $row->ip, $row->forwarded_ip, $row->user_agent,
					$row->deleted_at, $row->combined_shipping
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
		$this->data['breadcrumb_links'] = [
			[
				'text' => translate('breadcrumb_link_all', true),
				'href' => site_url($this->directory . $this->controller),
				'icon_class' => 'icon-database position-left',
				'label_value' => $this->{$this->model}->count_rows(),
				'label_class' => 'label label-primary position-right',
			],[
				'text' => translate('breadcrumb_link_fail', true),
				'href' => site_url($this->directory . $this->controller).'?order_status_id=0',
				'icon_class' => 'icon-menu7 position-left',
				'label_value' => $this->{$this->model}->filter(['order_status_id' => 0])->count_rows(),
				'label_class' => 'label label-default position-right red-back',
			]
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
	
	public function ajax(){
		if ($this->input->is_ajax_request()) {
			
			function _output_($data = 0){
				header('Content-Type: application/json');
				print json_encode( ['success' => true, 'data'=>$data] );
				exit;
			}
			
			if($this->input->post('order_id')){
				$this->db->set($this->input->post('field'), $this->input->post('value'));
				$this->db->where('id', $this->input->post('order_id'));
				$this->db->update('order');
				_output_($this->input->post('value'));
			}
			elseif($this->input->post('address_id')){
				$this->db->set($this->input->post('field'), $this->input->post('value'));
				$this->db->where('id', $this->input->post('address_id'));
				$this->db->update('address');
				_output_($this->input->post('name'));
			}elseif($this->input->post('order_product_id')){
				$this->db->set($this->input->post('field'), $this->input->post('value'));
				$this->db->where('id', $this->input->post('order_product_id'));
				$this->db->update('order_product');
				_output_('Product Serial Number - '.$this->input->post('value'));
			}
		 	
		}
		
	}
	
	public function edit($id)
	{
		
		$this->data['title'] = translate('edit_title');
		$this->data['subtitle'] = translate('edit_description');

		$this->data['order'] = $this->Order_model->filter(['id' => $id])->one();

		$this->data['address'] = $this->Address_model->filter(['id' => $this->data['order']->address_id])->with_trashed()->one();

		$this->data['customer'] = $this->Customers_model->filter(['id' => $this->data['order']->customer_id])->one();
		$this->data['order_statuses'] = $this->Order_status_model->with_translation()->all();

		$order_amount = $this->db->from('checkoutcom')
            ->where('order_id', $id)
            ->limit(1)
            ->order_by('id', 'ASC')
            ->get()
            ->row()
            ->amount ?? 0;

		$this->data['order_refund_amount'] = $order_amount / 100;
		
		
		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('invoice'),
			'class' => 'btn btn-primary btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-calculator',
			'href'	=> site_url_multi($this->admin_url.'/order/invoice/'.$id)
		];
		

		$products = $this->Order_model->get_additional_data('order_product', '*' ,['order_id' => $id]);
		
		$this->data['ems_express'] = false;
		$this->data['ems_standard'] = false;
		$this->data['ems_economy'] = false;
		$this->data['ems_premium'] = false;

		$this->load->model('Product_model');

		$order_options = $this->Order_option_model->filter(['order_id' => $this->data['order']->id])->all();


		if($products)
		{
			foreach($products as $product)
			{	


				$shipping = json_decode($product->shipping);
				$shipping_code = (isset($shipping[0]->code) && !empty($shipping[0]->code)) ? $shipping[0]->code : '';

				$product_obj = new stdClass();
				$product_obj->id = $product->id;
				$product_obj->name = $product->name;
				$product_obj->model = $product->model;
				$product_obj->quantity = $product->quantity;
				$product_obj->product_seria = $product->product_seria;
				$product_obj->price = $product->price;
				$product_obj->vendor_percent = $product->vendor_percent;
				$product_obj->currency_original = $product->currency_original;
				$product_obj->total = $product->total;
				$product_obj->shipping = $product->shipping;
				$product_obj->shipping_code = $shipping_code;
				$product_obj->tracking_code = $product->tracking_code;
				$product_obj->verify = $product->verify;
				$product_obj->seller = get_seller($this->Product_model->filter(['id' => $product->product_id])->one()->created_by ?? '');
				$product_obj->seller_id = $this->Product_model->filter(['id' => $product->product_id])->one()->created_by ?? '';

                $c_option_id = $this->db->from('product_option')
                    ->where(['product_id' => $product->product_id, 'id' => $this->data['order']->id])
                    ->get()
                    ->row();


				if($shipping_code == 'ems_express')
				{
					$this->data['ems_express'] = true;
				}

				if($shipping_code == 'ems_standard')
				{
					$this->data['ems_standard'] = true;
				}
				if($shipping_code == 'ems_premium')
				{
					$this->data['ems_premium'] = true;
				}
				if($shipping_code == 'ems_economy')
				{
					$this->data['ems_economy'] = true;
				}
				$product_obj->status = $product->status;


				$product_obj->options = [];

				if($order_options) {

                    foreach ($order_options as $key => $option) {
                        if($option->order_product_id == $product_obj->id){
                            $product_obj->options[] = $option;
                        }
                    }
                }

				$product_obj->warranty = '';

				foreach ($product_obj->options as $key => $p_opt) {
					if($p_opt->name == 'Warranty' || $p_opt->name == 'Гарантия'|| $p_opt->name == 'Zəmanət')
						$product_obj->warranty  = $p_opt->value;
				}


				$this->data['products'][$product->product_id] = $product_obj;
			}
		}

		$combined_shipping_methods = json_decode($this->data['order']->combined_shipping, true);

		if( ! is_array($combined_shipping_methods) ) {
            $combined_shipping_methods = [];
        }

        $combined_shipping_methods['products'] = [];

        $combined_shipping_total = 0;

        if(is_array($combined_shipping_methods)) {

            foreach ($combined_shipping_methods as &$combined_shipping_method) {

                if($combined_shipping_method) {

                    $combined_shipping_total += (int) $combined_shipping_method['price'];

                    foreach ($combined_shipping_method['products'] as $combined_shipping_product) {

                        if(isset($this->data['products'][$combined_shipping_product])) {

                            $combined_shipping_method['products_array'][] = $this->data['products'][$combined_shipping_product];

                        }
                    }
                }
            }
        }

        $this->data['shipping_methods'] = $combined_shipping_methods;

		$this->data['combined_shipping_price'] = $combined_shipping_total;

		function sortByOrder($a, $b) {
			return strcmp($a->shipping_code, $b->shipping_code);
		}
		
		usort($this->data['products'], 'sortByOrder');

		$this->data['order_status_history'] = $this->Order_model->get_additional_data('order_history', '*' ,['order_id' => $id]);

		$this->breadcrumbs->push(translate('edit_title'), $this->directory . $this->controller . '/edit');
		
		$this->form_validation->set_rules('order_status_id', translate('order_status_id'), 'required|trim');

		if($this->form_validation->run())
		{
			$order_status_data[0] = [
				'order_status_id'		=> (int)$this->input->post('order_status_id'),
				'comment'				=> $this->input->post('comment'),
				'notify'				=> ($this->input->post('notify')) ? 1 : 0,
				'order_id'				=> $id,
				'created_at'			=> date('Y-m-d H:i:s')
			];

			$this->Order_model->change_order_status($id, $this->input->post('order_status_id'));
			redirect(current_url());
		}

		$this->template->render();
	}
	

	public function show($id)
	{
		$this->data['title'] = translate('show_title');
		$this->data['subtitle'] = translate('show_description');

		$this->data['order'] = $this->Order_model->filter(['id' => $id])->one();

		$this->data['address'] = $this->Address_model->filter(['id' => $this->data['order']->address_id])->with_trashed()->one();

		$this->data['customer'] = $this->Customers_model->filter(['id' => $this->data['order']->customer_id])->one();
		$this->data['order_statuses'] = $this->Order_status_model->with_translation()->all();

		$order_amount = $this->db->from('checkoutcom')
            ->where('order_id', $id)
            ->limit(1)
            ->order_by('id', 'ASC')
            ->get()
            ->row()
            ->amount ?? 0;

		$this->data['order_refund_amount'] = $order_amount / 100;
		
		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('invoice'),
			'class' => 'btn btn-primary btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-calculator',
			'href'	=> site_url_multi($this->admin_url.'/order/invoice/'.$id)
		];
		

		$products = $this->Order_model->get_additional_data('order_product', '*' ,['order_id' => $id]);

		$this->data['ems_express'] = false;
		$this->data['ems_standard'] = false;
		$this->data['ems_economy'] = false;
		$this->data['ems_premium'] = false;

		$this->load->model('Product_model');

		$order_options = $this->Order_option_model->filter(['order_id' => $this->data['order']->id])->all();


		if($products)
		{
			foreach($products as $product)
			{	


				$shipping = json_decode($product->shipping);
				$shipping_code = (isset($shipping[0]->code) && !empty($shipping[0]->code)) ? $shipping[0]->code : '';

				$product_obj = new stdClass();
				$product_obj->id = $product->id;
				$product_obj->name = $product->name;
				$product_obj->model = $product->model;
				$product_obj->quantity = $product->quantity;
				$product_obj->price = $product->price;
				$product_obj->product_seria = $product->product_seria;
				$product_obj->vendor_percent = $product->vendor_percent;
				$product_obj->currency_original = $product->currency_original;
				$product_obj->total = $product->total;
				$product_obj->shipping = $product->shipping;
				$product_obj->shipping_code = $shipping_code;
				$product_obj->tracking_code = $product->tracking_code;
				$product_obj->verify = $product->verify;
				$product_obj->seller = get_seller($this->Product_model->filter(['id' => $product->product_id])->one()->created_by ?? '');
				$product_obj->seller_id = $this->Product_model->filter(['id' => $product->product_id])->one()->created_by ?? '';

                $c_option_id = $this->db->from('product_option')
                    ->where(['product_id' => $product->product_id, 'id' => $this->data['order']->id])
                    ->get()
                    ->row();


				if($shipping_code == 'ems_express')
				{
					$this->data['ems_express'] = true;
				}

				if($shipping_code == 'ems_standard')
				{
					$this->data['ems_standard'] = true;
				}
				if($shipping_code == 'ems_premium')
				{
					$this->data['ems_premium'] = true;
				}
				if($shipping_code == 'ems_economy')
				{
					$this->data['ems_economy'] = true;
				}
				$product_obj->status = $product->status;


				$product_obj->options = [];

				if($order_options) {

                    foreach ($order_options as $key => $option) {
                        if($option->order_product_id == $product_obj->id){
                            $product_obj->options[] = $option;
                        }
                    }
                }

				$product_obj->warranty = '';

				foreach ($product_obj->options as $key => $p_opt) {
					if($p_opt->name == 'Warranty' || $p_opt->name == 'Гарантия'|| $p_opt->name == 'Zəmanət')
						$product_obj->warranty  = $p_opt->value;
				}


				$this->data['products'][$product->product_id] = $product_obj;
			}
		}

		if(isset($_GET['tester'])){
		    print_r($this->data['products']); die;
        }

		$combined_shipping_methods = json_decode($this->data['order']->combined_shipping, true);

		if( ! is_array($combined_shipping_methods) ) {
            $combined_shipping_methods = [];
        }

        $combined_shipping_methods['products'] = [];

        $combined_shipping_total = 0;

        if(is_array($combined_shipping_methods)) {

            foreach ($combined_shipping_methods as &$combined_shipping_method) {

                if($combined_shipping_method) {

                    $combined_shipping_total += (int) $combined_shipping_method['price'];

                    foreach ($combined_shipping_method['products'] as $combined_shipping_product) {

                        if(isset($this->data['products'][$combined_shipping_product])) {

                            $combined_shipping_method['products_array'][] = $this->data['products'][$combined_shipping_product];

                        }
                    }
                }
            }
        }

        $this->data['shipping_methods'] = $combined_shipping_methods;

		$this->data['combined_shipping_price'] = $combined_shipping_total;

		function sortByOrder($a, $b) {
			return strcmp($a->shipping_code, $b->shipping_code);
		}
		
		usort($this->data['products'], 'sortByOrder');

		$this->data['order_status_history'] = $this->Order_model->get_additional_data('order_history', '*' ,['order_id' => $id]);


		$this->form_validation->set_rules('order_status_id', translate('order_status_id'), 'required|trim');

		if($this->form_validation->run())
		{
			$order_status_data[0] = [
				'order_status_id'		=> (int)$this->input->post('order_status_id'),
				'comment'				=> $this->input->post('comment'),
				'notify'				=> ($this->input->post('notify')) ? 1 : 0,
				'order_id'				=> $id,
				'created_at'			=> date('Y-m-d H:i:s')
			];

			$this->Order_model->change_order_status($id, $this->input->post('order_status_id'));
			redirect(current_url());
		}

		$this->template->render();
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

		$this->data['order'] = $this->Order_model->filter(['id' => $id])->one();

		$this->data['address'] = $this->Address_model->filter(['id' => $this->data['order']->address_id])->with_trashed()->one();
		$this->data['customer'] = $this->Customers_model->filter(['id' => $this->data['order']->customer_id])->one();
		$this->data['order_statuses'] = $this->Order_status_model->with_translation()->all();
		

		$products = $this->Order_model->get_additional_data('order_product', '*' ,['order_id' => $id]);


		switch ($this->data['order']->currency_code) {

            case 'AZN':
                $this->data['order']->currency_sign = '₼';
                break;

            case 'EURO' :
                $this->data['order']->currency_sign = '€';
                break;

            case 'USD' :
                $this->data['order']->currency_sign = '$';
                break;

            default:
                $this->data['order']->currency_sign = $this->data['order']->currency_code;

        }

//        $this->data['order']->currency_sign = currency_symbol_converter($this->data['order']->currency_sign);


		$this->data['ems_express'] = false;
		$this->data['ems_standard'] = false;
		$this->data['ems_economy'] = false;
		$this->data['ems_premium'] = false;

		$order_options = $this->Order_option_model->filter(['order_id' => $this->data['order']->id])->all();

		if($products)
		{
			foreach($products as $product)
			{	
				$shipping = json_decode($product->shipping);
				$shipping_code = (isset($shipping[0]->code) && !empty($shipping[0]->code)) ? $shipping[0]->code : '';

				
				$product->shipping = $product->shipping;
				$product->shipping_code = $shipping_code;
				$product->options = [];

				if($order_options) {
                    foreach ($order_options as $key => $option) {
                        if($option->order_product_id == $product->id){
                            $product->options[] = $option;
                        }
                    }
                }


				$product->warranty='';


				foreach ($product->options as $key => $p_opt) {
                    if($p_opt->name == 'Warranty' || $p_opt->name == 'Гарантия'|| $p_opt->name == 'Zəmanət')
						$product->warranty  = $p_opt->value;
				}


				if($shipping_code == 'ems_express')
				{
					$this->data['ems_express'] = true;
				}

				if($shipping_code == 'ems_standard')
				{
					$this->data['ems_standard'] = true;
				}
				if($shipping_code == 'ems_premium')
				{
					$this->data['ems_premium'] = true;
				}
				if($shipping_code == 'ems_economy')
				{
					$this->data['ems_economy'] = true;
				}
			

				
			}
		}

		$this->data['products'] = $products;

		$this->data['order_status_history'] = $this->Order_model->get_additional_data('order_history', '*' ,['order_id' => $id]);

		$this->template->render('order/invoice', 'invoice');
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

	public function delete(){
		if ($this->input->method() == 'post') {
			$response  = ['success' => false, 'message' => translate('couldnt_delete_message',true)];
			if ($this->input->post('selected')) {
				$_ids = implode(',', array_map(function($val){
					return "'{$val}'";
				}, $this->input->post('selected')));
				
				$this->db->query("UPDATE wc_order SET deleted_at= NOW() WHERE id IN({$_ids})");
				
				/* $this->db->query("UPDATE wc_order_product_payment WHERE order_id IN({$_ids})");
				$this->db->query("UPDATE wc_order_product SET deleted_at='NOW()' WHERE order_id IN({$_ids})");
				$this->db->query("UPDATE wc_order_option SET deleted_at='NOW()' WHERE order_id IN({$_ids})");
				$this->db->query("UPDATE wc_order_history WHERE order_id IN({$_ids})"); */
				
				$response = ['success' => true, 'message' => translate('successfully_delete_message',true)];
			}
			$this->template->json($response);
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
