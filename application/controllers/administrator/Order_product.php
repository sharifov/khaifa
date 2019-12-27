<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_product extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
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

		$this->data['fields'] = ['id', 'name', 'model', 'quantity', 'price_original', 'total_original', 'currency_original', 'status', 'verify'];

		if ($this->data['fields'])
		{
			foreach ($this->data['fields'] as $field)
			{
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


		// Filters for banned and not specified name
		$filter = [];

		$products = $this->Product_model->filter(['created_by' => $this->auth->get_user()->id])->all();

		$product_ids = [];
		if($products)
		{
			foreach($products as $product)
			{
				$product_ids[] = $product->id;
			}
		}

		

		
		if ($this->input->get('id') != null) {
			$filter['id LIKE "%' . $this->input->get('id') . '%"'] = null;
		}
		if(!empty($product_ids))
		{
			$filter['product_id IN (' . implode(',', $product_ids) . ')'] = null;
		}
		else
		{
			$filter['product_id IN (999999999999999999999)'] = null;
		}


		
		
		// Sorts by column and order
		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'id',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
		];
		
		// Gets records count from database
		$this->data['total_rows'] = $this->{$this->model}->filter($filter)->with_trashed()->count_rows();
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
		$total_rows = $this->{$this->model}->where($filter)->with_trashed()->count_rows();
		$rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->with_trashed()->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();

		// Sets custom row's data options
		$custom_rows_data = [
			[
				'column' => 'currency_original',
				'callback' => 'currency_formatter',
				'params' => false
			],
			[
				'column' => 'status',
				'callback' => 'status_vendor',
				'params' => false
			]
		];

		// Set action buttons
		$action_buttons = [];

		foreach ($rows as &$row) {

		    if($row->verify == 2) {
                $row->status = 3;
            }

		    unset($row->verify);

        }

		unset($columns['verify']);
		
		// Generates Table with given records
		$this->wc_table->set_module(false);
		$this->wc_table->set_columns($columns);
		$this->wc_table->set_rows($rows);
		$this->wc_table->set_custom_rows($custom_rows_data);
		$this->wc_table->set_action($action_buttons);
		$this->data['table'] = $this->wc_table->generate();

//		var_dump($rows);die;

		// Sets Pagination options and initialize
		$config['base_url'] = site_url_multi($this->directory . $this->controller . '/index');
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->data['per_page'];
		$config['reuse_query_string'] = true;
		$config['use_page_numbers'] = true;

		$this->pagination->initialize($config);
		$this->data['pagination'] = $this->pagination->create_links();

		$this->template->render();
	}

	public function status($id = false, $status = 0)
	{
		if($id)
		{
			if(in_array($status, [1, 2]))
			{
				$this->Order_product_model->update(['status' => $status], ['id' => $id]);
				redirect($this->input->server('HTTP_REFERER'));
			}
			else
			{
				show_404();
			}
		}
		else
		{
			show_404();
		}
	}
}
