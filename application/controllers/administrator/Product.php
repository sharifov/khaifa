<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends Administrator_Controller
{
	public $relation_modules = [
		'manufacturer_id' => [
			"dynamic"=> true,
			"name"=> "brand",
			"translation"=> false,
			"key"=> "id",
			"columns"=> "name",
			"where"=> [
				[
					"key" 	=> "status",
					"value" => 1
				]
			],
			"sort" => [
				[
					"column"=> "name",
					"order" => "ASC"
				]
			]
		],
		'category_id' => [
			"dynamic"=> true,
			"name"=> "category",
			"translation"=> true,
			"key"=> "id",
			"columns"=> "name",
			"where"=> [
				[
					"key" 	=> "status",
					"value" => 1
				]
			],
			"sort" => [
				[
					"column"=> "name",
					"order" => "ASC"
				]
			]
		],
		'related_products' => [
			"dynamic"=> false,
			"name"=> "product",
			"translation"=> true,
			"key"=> "id",
			"columns"=> "name",
			"where"=> [
				[
					"key" 	=> "status",
					"value" => 1
				]
			],
			"sort" => [
				[
					"column"=> "name",
					"order" => "ASC"
				]
			]
		]
	];

	public function __construct()
	{
		parent::__construct();

		// Load models
		$this->load->model('Group_model');
		$this->load->model('modules/Customer_group_model');
		$this->load->model('Option_model');
		$this->load->model('Relation_model');
		$this->load->model('modules/Country_group_model');
		$this->load->model('Attribute_model');
		$this->load->model('modules/Stock_notifier_model');

		$this->data['country_groups'] = $this->Country_group_model->filter(['status' => 1])->all();

		$this->load->library('algolia');

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
				'name' => 'name',
				'class' => 'form-control',
				'value' => $this->input->get('name'),
				'placeholder' => translate('search_placeholder', true),
			],
		];

		// Sets Table columns
		$this->data['fields'] = ['id', 'image', 'model', 'price' ,'created_by', 'status'];

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
		if ($this->input->get('outstock') != null) {
			$filter['quantity'] = 0;
		}
		if($this->auth->is_member('vendor'))
		{
			$filter['created_by'] = $this->auth->get_user()->id;
		}
		if ($this->input->get('name') != null) {
			$filter['name LIKE "%' . $this->input->get('name') . '%"'] = null;
		}

        $filter['status!=9'] = null;

		// Sorts by column and order
		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
		];

		// Gets records count from database

		$this->data['total_rows'] = $this->{$this->model}->filter($filter)->with_translation($language_id)->count_rows();

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
		$total_rows = $this->{$this->model}->where($filter)->with_translation($language_id)->count_rows();
		$rows = $this->{$this->model}->fields($this->data['fields'])->with_translation($language_id)->filter($filter)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();


		// Sets custom row's data options
		$custom_rows_data = [
			[
				'column' => 'image',
				'callback' => 'get_image',
				'params' => ['width' => 100, 'height' => 100],
			],
			[
				'column' => 'price',
				'callback' => 'product_currency_format',
				'params' => []
			],
			[
				'column' => 'status',
				'callback' => 'get_status',
				'params' => '',
			],
			[
				'column' => 'created_by',
				'callback' => 'get_seller_name',
				'params' => [],
			]
		];

		// Set action buttons
		$action_buttons = [];
		if (check_permission('product', 'edit')) {
			$action_buttons['edit'] = true;
		}

		if (check_permission('product', 'delete')) {
			$action_buttons['delete'] = true;
		}

		// Generates Table with given records
		$this->wc_table->set_module(false);
		$this->wc_table->set_columns($columns);
		$this->wc_table->set_rows($rows);
		$this->wc_table->set_custom_rows($custom_rows_data);
		$this->wc_table->set_action($action_buttons);
		$this->data['table'] = $this->wc_table->generate();
		$this->data["percent_value"]=$rows->{0}->percent;

		if(isset($_GET["test"])){
			//print_r($rows->{0}->percent);
			//die();
		}

		// Sets Pagination options and initialize
		$config['base_url'] = site_url_multi($this->directory . $this->controller . '/index');
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->data['per_page'];
		$config['reuse_query_string'] = true;
		$config['use_page_numbers'] = true;

		$this->pagination->initialize($config);
		$this->data['pagination'] = $this->pagination->create_links();

		// Sets buttons
		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('header_button_create', true),
			'href' => site_url($this->directory . $this->controller . '/create'),
			'class' => 'btn btn-success btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-plus-circle2',
		];

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

		if($this->auth->is_admin())
		{

			// Sets Breadcrumb links
			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_all', true),
				'href' => site_url($this->directory . $this->controller),
				'icon_class' => 'icon-database position-left',
				'label_value' => $this->{$this->model}->with_translation($language_id)->count_rows(),
				'label_class' => 'label label-primary position-right',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_outstock', true),
				'href' => site_url($this->directory . $this->controller . '?outstock=true'),
				'icon_class' => 'icon-shield-check position-left',
				'label_value' => $this->{$this->model}->filter(['quantity'=>0])->with_translation($language_id)->count_rows(),
				'label_class' => 'label label-success position-right phiol-back',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_active', true),
				'href' => site_url($this->directory . $this->controller . '?status=1'),
				'icon_class' => 'icon-shield-check position-left',
				'label_value' => $this->{$this->model}->filter(['status' => 1])->with_translation($language_id)->count_rows(),
				'label_class' => 'label label-success position-right',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_deactive', true),
				'href' => site_url($this->directory . $this->controller . '?status=0'),
				'icon_class' => 'icon-shield-notice position-left',
				'label_value' => $this->{$this->model}->filter(['status' => 0])->with_translation($language_id)->count_rows(),
				'label_class' => 'label label-warning position-right',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_pending', true),
				'href' => site_url($this->directory . $this->controller . '?status=2'),
				'icon_class' => 'icon-shield-notice position-left',
				'label_value' => $this->{$this->model}->filter(['status' => 2])->with_translation($language_id)->count_rows(),
				'label_class' => 'label label-warning position-right',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_trash', true),
				'href' => site_url($this->directory . $this->controller . '/trash'),
				'icon_class' => 'icon-trash position-left',
				'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
				'label_class' => 'label label-danger position-right',
			];
		}

		$this->template->render();
	}

	public function change_percent(){
		$percent_value=$this->input->post("percent_value");
		$this->db->query("update wc_product set percent='$percent_value'");
		redirect(base_url("administrator/product/"));
	}

	public function trash()
	{
		$this->data['title'] = translate('trash_title');
		$this->data['subtitle'] = translate('trash_description');

		// Sets Table columns
		$this->data['fields'] = ['id', 'name', 'status'];

		if ($this->data['fields']) {
			foreach ($this->data['fields'] as $field) {
				$this->data['columns'][$field] = [
					'table' => [$this->data['current_lang'] => translate('table_head_' . $field),
					],
				];
			}
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

		// Sets search field
		$this->data['search_field'] = [
			'name' => [
				'property' => 'search',
				'type' => 'search',
				'name' => 'name',
				'class' => 'form-control',
				'value' => $this->input->get('name'),
				'placeholder' => translate('search_placeholder', true),
			]
		];

		// Filters for banned and not specified name
		$filter = [];
		if ($this->input->get('status') != null) {
			$filter['status'] = $this->input->get('status');
		}

		if($this->auth->is_member('vendor')) {
			$filter['created_by'] = $this->auth->get_user()->id;
		}

		if ($this->input->get('name') != null) {
			$filter['name LIKE "%' . $this->input->get('name') . '%"'] = null;
		}

		// Sorts by column and order
		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
		];

		// Gets records count from database
		$this->data['total_rows'] = $this->{$this->model}->only_trashed()->filter($filter)->with_translation($language_id)->count_rows();
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
		$total_rows = $this->{$this->model}->only_trashed()->where($filter)->count_rows();
		$rows = $this->{$this->model}->fields($this->data['fields'])->only_trashed()->filter($filter)->with_translation($language_id)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();

		// Sets custom row's data options
		$custom_rows_data = [
			[
				'column' => 'status',
				'callback' => 'get_status',
				'params' => '',
			],
		];

		// Set action buttons
		$action_buttons = [];
		if (check_permission('option', 'restore')) {
			$action_buttons['restore'] = true;
		}

		if (check_permission('option', 'remove')) {
			$action_buttons['remove'] = true;
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

		// Set buttons
		$this->data['buttons'][] = [
			'type' => 'button',
			'text' => translate('header_button_delete_permanently', true),
			'class' => 'btn btn-warning btn-labeled heading-btn',
			'id' => 'removeSelectedItems',
			'icon' => 'icon-trash',
			'additional' => [
				'data-href' => site_url($this->directory . $this->controller . '/remove')
			]
		];

		$this->data['buttons'][] = [
			'type' => 'button',
			'text' => translate('header_button_restore', true),
			'class' => 'btn btn-primary btn-labeled heading-btn',
			'id' => 'restoreSelectedItems',
			'icon' => 'icon-loop',
			'additional' => [
				'data-href' => site_url($this->directory . $this->controller . '/restore')
			]
		];

		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('header_button_clean', true),
			'href' => site_url($this->directory . $this->controller . '/clean'),
			'class' => 'btn btn-danger btn-labeled heading-btn',
			'icon' => 'icon-eraser2',
			'id' => ''
		];

		if($this->auth->is_admin())
		{
			// Sets Breadcrumb links
			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_all', true),
				'href' => site_url($this->directory . $this->controller),
				'icon_class' => 'icon-database position-left',
				'label_value' => $this->{$this->model}->count_rows(),
				'label_class' => 'label label-primary position-right',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_active', true),
				'href' => site_url($this->directory . $this->controller . '?status=1'),
				'icon_class' => 'icon-shield-check position-left',
				'label_value' => $this->{$this->model}->filter(['status' => 1])->count_rows(),
				'label_class' => 'label label-success position-right',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_deactive', true),
				'href' => site_url($this->directory . $this->controller . '?status=0'),
				'icon_class' => 'icon-shield-notice position-left',
				'label_value' => $this->{$this->model}->filter(['status' => 0])->count_rows(),
				'label_class' => 'label label-warning position-right',
			];

			$this->data['breadcrumb_links'][] = [
				'text' => translate('breadcrumb_link_trash', true),
				'href' => site_url($this->directory . $this->controller . '/trash'),
				'icon_class' => 'icon-trash position-left',
				'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
				'label_class' => 'label label-danger position-right',
			];
		}

		$this->template->render($this->controller.'/index');
	}

	public function create()
	{
		$this->data['title']    = translate('create_title');
		$this->data['subtitle'] = translate('create_description');

		$weight_class_options = $this->generate_options(['name'=>'weight_class', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
		$length_class_options = $this->generate_options(['name'=>'length_class', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
		$stock_options = $this->generate_options(['name'=>'stock_status', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
		$country_options = $this->generate_options(['name'=>'country', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[['column' => 'name', 'order' =>'ASC']]]);
		$tax_class_options = $this->generate_options(['name'=>'tax_class', 'key'=>'id', 'columns'=>'title', 'dynamic'=>false, 'translation'=>false, 'where'=>[], 'sort'=>[] ], true);
		$currency_options = $this->generate_options(['name'=>'currency', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>false, 'where'=>[], 'sort'=>[] ]);
		$currency_options = ($currency_options) ? $currency_options : [];

		$this->data['model_value'] = set_value("model");
		$this->form_validation->set_rules('model', translate('form_label_model'), "trim|required");
		$new_options = [];
		$new_options[''] = translate('select', true);
		$new_options[0] =  translate('form_label_new');
		$new_options[1] =  translate('form_label_used');
		$new_options[2] =  translate('form_label_refurbished');
		// General Form Fields
		$this->data['form_field']['general'] = [
			'sku' => [
				'property'  => 'text',
				'name'      => 'sku',
				'class'     => 'form-control',
				'value'     => set_value('sku'),
				'label'     => translate('form_label_sku'),
				'info' 		=> translate('form_label_description_sku'),
				'placeholder' 	=> translate('form_label_sku'),
				'validation' 	=> []
			],
			'upc' => [
				'property'  => 'text',
				'name'      => 'upc',
				'class'     => 'form-control',
				'value'     => set_value('upc'),
				'label'     => translate('form_label_upc'),
				'info' 		=> translate('form_label_description_upc'),
				'placeholder' 	=> translate('form_label_upc'),
				'validation' 	=> []
			],
			'ean' => [
				'property'  => 'text',
				'name'      => 'ean',
				'class'     => 'form-control',
				'value'     => set_value('ean'),
				'label'     => translate('form_label_ean'),
				'info' 		=> translate('form_label_description_ean'),
				'placeholder' 	=> translate('form_label_ean'),
				'validation' 	=> []
			],
			'jan' => [
				'property'  => 'text',
				'name'      => 'jan',
				'class'     => 'form-control',
				'value'     => set_value('jan'),
				'label'     => translate('form_label_jan'),
				'info' 		=> translate('form_label_description_jan'),
				'placeholder' 	=> translate('form_label_jan'),
				'validation'	=> []
			],
			'isbn' => [
				'property'  => 'text',
				'name'      => 'isbn',
				'class'     => 'form-control',
				'value'     => set_value('isbn'),
				'label'     => translate('form_label_isbn'),
				'info' 		=> translate('form_label_description_isbn'),
				'placeholder' 	=> translate('form_label_isbn'),
				'validation' 	=> []
			],
			'mpn' => [
				'property'  => 'text',
				'name'      => 'mpn',
				'class'     => 'form-control',
				'value'     => set_value('mpn'),
				'label'     => translate('form_label_mpn'),
				'info' 		=> translate('form_label_description_mpn'),
				'placeholder' 	=> translate('form_label_mpn'),
				'validation' 	=> []
			],
			'price' => [
				'property'  => 'text',
				'name'      => 'price',
				'class'     => 'form-control',
				'value'     => set_value('price'),
				'label'     => translate('form_label_price'),
				'placeholder'	=> translate('form_label_price'),
				'validation' 	=> ['rules' => 'required']
			],
			'currency' => [
				'property'  => 'dropdown',
				'name'      => 'currency',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label'     => translate('form_label_currency'),
				'options'	=> $currency_options,
				'selected'  => set_value('currency'),
				'validation' 	=> []
			],
			'tax_class_id' => [
				'property'  => 'dropdown',
				'name'      => 'tax_class_id',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label'     => translate('form_label_tax_class_id'),
				'options'	=> $tax_class_options,
				'selected'  => set_value('tax_class_id'),
				'validation' 	=> []
			],
			'quantity' => [
				'property'  => 'number',
				'min'		=> '0',
				'name'      => 'quantity',
				'class'     => 'form-control',
				'value'     => set_value('quantity'),
				'label'     => translate('form_label_quantity'),
				'placeholder' 	=> translate('form_label_quantity'),
				'validation' 	=> []
			],
			'day' => [
				'property'  => 'number',
				'min'		=> '0',
				'name'      => 'day',
				'class'     => 'form-control',
				'value'     => set_value('day'),
				'label'     => translate('form_label_day'),
				'placeholder' 	=> translate('form_label_day'),
				'validation' 	=> []
			],
			'subtract' => [
				'property'  => 'dropdown',
				'name'      => 'subtract',
				'class'     => 'form-control',
				'value'     => set_value('subtract'),
				'label'     => translate('form_label_subtract'),
				'options' 		=> [translate('no', true), translate('yes', true)],
				'validation' 	=> []
			],
			'stock_status_id' => [
				'property'  => 'dropdown',
				'name'      => 'stock_status_id',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label'     => translate('form_label_stock_status_id'),
				'options' 	=> $stock_options,
				'selected'  => set_value('stock_status_id'),
				'validation' 	=> []
			],
			'country_id' => [
				'property'  => 'dropdown',
				'name'      => 'country_id',
				'class' 	=> 'select-search',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label'     => translate('form_label_country_id'),
				'options' 	=> $country_options,
				'selected'  => set_value('country_id'),
				'validation' 	=> ['rules' => 'required|trim']
			],
			'region_id' => [
				'property'  => 'dropdown',
				'name'      => 'region_id',
				'class' 	=> 'select-search',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label'     => translate('form_label_region_id'),
				'options' 	=> [],
				'selected'  => set_value('region_id'),
				'data-selected_id'  => set_value('region_id'),
				'validation' 	=> ['rules' => 'required|trim']
			],
			'new' => [
				'property'  => 'dropdown',
				'name'      => 'new',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label'     => translate('form_label_new'),
				'options'	=> $new_options,
				'selected'  => set_value('new'),
				'validation' => ['rules' => 'required|is_natural']
			],
			'date_available' => [
				'property'  => 'date',
				'name'      => 'date_available',
				'class'     => 'form-control',
				'value'     => set_value('date_available'),
				'label'     => translate('form_label_date_available'),
				'placeholder' 	=> translate('form_label_date_available'),
				'validation' 	=> []
			],
			'length' => [
				'property'  => 'text',
				'name'      => 'length',
				'class'     => 'form-control',
				'value'     => set_value('length'),
				'label'     => translate('form_label_length'),
				'placeholder' 	=> translate('form_label_length'),
				'validation' 	=> ['rules' => 'required|trim']
			],
			'width' => [
				'property'  => 'text',
				'name'      => 'width',
				'class'     => 'form-control',
				'value'     => set_value('width'),
				'label'     => translate('form_label_width'),
				'placeholder' 	=> translate('form_label_width'),
				'validation' 	=> ['rules' => 'required|trim']
			],
			'height' => [
				'property'  => 'text',
				'name'      => 'height',
				'class'     => 'form-control',
				'value'     => set_value('height'),
				'label'     => translate('form_label_height'),
				'placeholder' 	=> translate('form_label_height'),
				'validation' 	=> ['rules' => 'required|trim']
			],
			'length_class_id' => [
				'property'  => 'dropdown',
				'name'      => 'length_class_id',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label'     => translate('form_label_length_class_id'),
				'options'	=> $length_class_options,
				'selected'  => set_value('length_class_id'),
				'validation' 	=> ['rules' => 'required|trim']
			],
			'weight' => [
				'property'  => 'text',
				'name'      => 'weight',
				'class'     => 'form-control',
				'value'     => set_value('weight'),
				'label'     => translate('form_label_weight'),
				'placeholder' 	=> translate('form_label_weight'),
				'validation' 	=> ['rules' => 'required|trim']
			],
			'weight_class_id' => [
				'property'  => 'dropdown',
				'name'      => 'weight_class_id',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'selected'     => set_value('weight_class_id'),
				'label'     => translate('form_label_weight_class_id'),
				'options'		=> $weight_class_options,
				'validation' 	=> ['rules' => 'required|trim']
			],
			'created_at' => [
				'property'  => 'date',
				'name'      => 'created_at',
				'class'     => 'form-control',
				'value'     => set_value('created_at'),
				'label'     => translate('form_label_created_at'),
				'placeholder' 	=> translate('form_label_created_at'),
				'validation' 	=> []
			]
		];

		if($this->auth->is_admin()) {
			$this->data['form_field']['general']['status'] = [
				'property' 	=> 'dropdown',
				'name' 		=> 'status',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label' 	=> translate('form_label_status'),
				'options' 	=> [translate('disable', true), translate('enable', true), translate('pending', true)],
				'selected' 		=> set_value('status'),
				'validation' 	=> []
			];
		}

		// Translation Form Fields
		foreach ($this->data['languages'] as $language) {
			$this->data['form_field']['translation'][$language['id']]['name'] = [
				'property'    	=> "text",
				'name'        	=> 'translation[' . $language['id'] . '][name]',
				'class'       	=> 'form-control',
				'value'       	=> set_value('translation[' . $language['id'] . '][name]'),
				'label'       	=> translate("form_label_name"),
				'placeholder' 	=> translate("form_label_name"),
				'validation'    => ['rules' => 'required']
			];

			$this->data['form_field']['translation'][$language['id']]['slug'] = [
				'property'    	=> "slug",
				'name'        	=> 'translation[' . $language['id'] . '][slug]',
				'data-for'		=> 'name',
				'data-type'		=> 'translation',
				'data-lang-id'	=> $language['id'],
				'class'       	=> 'form-control slugField',
				'value'       	=> set_value('translation[' . $language['id'] . '][slug]'),
				'label'       	=> translate("form_label_slug"),
				'placeholder' 	=> translate("form_label_slug"),
				'validation'    => ['rules' => 'required']
			];

			$this->data['form_field']['translation'][$language['id']]['description'] = [
				'property'    	=> "textarea",
				'name'   		=> 'translation[' . $language['id'] . '][description]',
				'class'       	=> 'form-control ckeditor',
				'value'       	=> set_value('translation[' . $language['id'] . '][description]'),
				'label'       	=> translate("form_label_description"),
				'placeholder' 	=> translate("form_label_description"),
				'validation'    => []
			];

			$this->data['form_field']['translation'][$language['id']]['seller_note'] = [
				'property'    	=> "textarea",
				'name'   		=> 'translation[' . $language['id'] . '][seller_note]',
				'class'       	=> 'form-control',
				'value'       	=> set_value('translation[' . $language['id'] . '][seller_note]'),
				'label'       	=> translate("form_label_seller_note"),
				'placeholder' 	=> translate("form_label_seller_note"),
				'validation'    => []
			];

			if($this->auth->is_admin()) {
				$this->data['form_field']['translation'][$language['id']]['meta_title'] = [
					'property'    	=> "text",
					'name' 			=> 'translation[' . $language['id'] . '][meta_title]',
					'class'       	=> 'form-control',
					'value'       	=> set_value('translation[' . $language['id'] . '][meta_title]'),
					'label'       	=> translate("form_label_meta_title"),
					'placeholder' 	=> translate("form_label_meta_title"),
					'validation'    => []
				];

				$this->data['form_field']['translation'][$language['id']]['meta_description'] = [
					'property'    	=> "textarea",
					'name'   		=> 'translation[' . $language['id'] . '][meta_description]',
					'class'       	=> 'form-control',
					'value'       	=> set_value('translation[' . $language['id'] . '][meta_description]'),
					'label'       	=> translate("form_label_meta_description"),
					'placeholder' 	=> translate("form_label_meta_description"),
					'validation'    => []
				];

				$this->data['form_field']['translation'][$language['id']]['meta_keyword'] = [
					'property'    	=> "textarea",
					'name'   		=> 'translation[' . $language['id'] . '][meta_keyword]',
					'class'       	=> 'form-control',
					'value'       	=> set_value('translation[' . $language['id'] . '][meta_keyword]'),
					'label'       	=> translate("form_label_meta_keyword"),
					'placeholder' 	=> translate("form_label_meta_keyword"),
					'validation'    => []
				];

				$this->data['form_field']['translation'][$language['id']]['tag'] = [
					'property'    	=> "textarea",
					'name' 			=> 'translation[' . $language['id'] . '][tag]',
					'class'       	=> 'form-control',
					'value'       	=> set_value('translation[' . $language['id'] . '][tag]'),
					'label'       	=> translate("form_label_tag"),
					'info'       	=> translate("form_label_description_comma_separated"),
					'placeholder' 	=> translate("form_label_tag"),
					'validation'    => []
				];
			}

		}

		$this->load->model('modules/Category_model');
		$categories = $this->Category_model->filter(['status' => 1, 'parent' => 0])->with_translation()->order_by('name', 'ASC')->all();
		$this->data['categories'] = [];
		if($categories) {
			foreach($categories as $category)
			{
				$category_option = new stdclass();
				$category_option->id = $category->id;
				$category_option->name = $category->name;
				$category_option->attribute_group_id = $category->attribute_group_id;
				$category_option->has_child = $this->Category_model->filter(['status' => 1, 'parent' => $category->id])->count_rows();

				$this->data['categories'][] = $category_option;
			}
		}

		// Links Form Fields
		$this->data['form_field']['links'] = [
			'manufacturer_id'=> [
				'property'   => 'dropdown-ajax',
				'type'		 => 'general',
				'element'	 => 'manufacturer_id',
				'name'       => 'manufacturer_id',
				'id'         => 'manufacturer_id',
				'class'		 => 'form-control dropdownSingleAjax',
				'label'      => translate('form_label_manufacturer'),
				'placeholder'=> translate('form_label_manufacturer'),
				'selected'   => set_value('manufacturer_id'),
				'selected_text' => $this->get_selected_element('manufacturer_id',set_value('manufacturer_id'))
			],
			'related_products' => [
				'property'   => 'multiselect_ajax',
				'type'		 => 'general',
				'element'	 => 'related_products',
				'name'       => 'related_products',
				'id'         => 'related_products',
				'class'		 => 'form-control dropdownMultiAjax',
				'label'      => translate('form_label_related_products'),
				'placeholder'=> translate('form_label_related_products'),
				'selected'   => set_value('related_products'),
				'selected_elements' => $this->get_selected_elments('related_products',set_value('related_products')),
				'selected_text' => ""
			]
		];

		$this->data['product_attributes'] = $this->input->post('product_attribute');
		$this->data['categories_data'] = $this->input->post('category_id');
		$this->data['attribute_group_id'] = $this->input->post('attribute_group_id');
		$attribute_values = [];

		if($this->data['product_attributes']) {
			foreach($this->data['product_attributes'] as $product_attribute) {
				if(!array_key_exists($product_attribute['attribute_id'],$attribute_values))
				{
					$all_attribute_values = $this->Product_model->get_additional_data('attribute_value','*', ['attribute_id' => $product_attribute['attribute_id'], 'custom' => 0]);
					if($all_attribute_values) {
						foreach($all_attribute_values as $attribute_value) {
							$value_description = $this->Product_model->get_additional_data('attribute_value_description','*', ['attribute_value_id' => $attribute_value->id],true);
							$attribute_values[$product_attribute['attribute_id']][] = [
								'product_attribute_id'	=> $product_attribute['attribute_id'],
								'attribute_id'	=> $product_attribute['attribute_id'],
								'attribute_value_id' => $attribute_value->id,
								'name' => ($value_description) ? $value_description->name : ""
							];

						}
					}
				}
			}
		}

		$this->data['attribute_values'] = $attribute_values;
		$this->data['attributes'] = $this->input->post('attribute');


		$this->data['product_country_groups'] = $this->input->post('product_country_group');

		// Discount tab data
		$this->data['product_discounts'] = $this->input->post('product_discount');
		$this->data['customer_groups'] = $this->Customer_group_model->fields("id, name")->with_translation()->as_array()->all();

		// Special tab data
		$this->data['product_specials'] = $this->input->post('product_special');

		// Option tab data
		$this->data['product_options'] = $this->input->post("product_option");
		$this->data['product_relations'] = $this->input->post("product_relation");

		$option_values = [];
		if($this->data['product_options']) {
			foreach($this->data['product_options'] as $product_option) {
				if(!array_key_exists($product_option['option_id'],$option_values))
				{
					$all_option_values = $this->Option_model->get_additional_data('option_value','id,image,sort', ['option_id' => $product_option['option_id']]);
					if($all_option_values) {
						foreach($all_option_values as $option_value) {
							$value_description = $this->Option_model->get_additional_data('option_value_description','*', ['option_value_id' => $option_value->id],true);

								$option_values[$product_option['option_id']][] = [
									'product_option_id'	=> $product_option['option_id'],
									'option_id'	=> $product_option['option_id'],
									'option_value_id' => $option_value->id,
									'image' => $option_value->image,
									'name' => ($value_description) ? $value_description->name : ""
								];

						}
					}
				}
			}
		}
		$this->data['option_values'] = $option_values;

		$relation_values = [];
		if($this->data['product_relations']) {
			foreach($this->data['product_relations'] as $product_relation) {
				if(!array_key_exists($product_relation['relation_id'],$relation_values))
				{
					$all_relation_values = $this->Relation_model->get_additional_data('relation_value','id,sort', ['relation_id' => $product_relation['relation_id']]);
					if($all_relation_values) {
						foreach($all_relation_values as $relation_value) {
							$value_description = $this->Relation_model->get_additional_data('relation_value_description','*', ['relation_value_id' => $relation_value->id],true);

								$relation_values[$product_relation['relation_id']][] = [
									'product_relation_id'	=> $product_relation['relation_id'],
									'relation_id'	=> $product_relation['relation_id'],
									'relation_value_id' => $relation_value->id,
									'name' => ($value_description) ? $value_description->name : ""
								];

						}
					}
				}
			}
		}
		$this->data['relation_values'] = $relation_values;

		// Product Images
		$this->data['images'] = [];
		$this->data['default_image'] = set_value('default_image');
		if($this->input->post('images')) {
			foreach($this->input->post('images') as $image){
				$new = new stdClass();
				$new->name = basename($image['url']);
				$new->sort = $image['sort'];
				$new->path = $image['url'];
				$new->alt_image = $image['alt'][0]; // added new
				$new->preview = $this->Model_tool_image->resize($image['url'], 250, 250);

				$this->data['images'][] = $new;
			}
		}


		// Set form validation rules
		foreach ($this->data['form_field']['general'] as $key => $value)
		{
			if($value['validation']) {
				$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
			}
		}

		foreach ($this->data['languages'] as $language)
		{
			foreach ($this->data['form_field']['translation'][$language['id']] as $key => $value)
			{
				if($value['validation']){
					$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
				}
			}
		}

		$this->data['buttons'][] = [
			'type'       => 'button',
			'text'       => translate('form_button_save', true),
			'class'      => 'btn btn-primary btn-labeled heading-btn',
			'id'         => 'save',
			'icon'       => 'icon-floppy-disk',
			'additional' => [
				'onclick'    => "if(confirm('".translate('are_you_sure', true)."')){ $('#form-save').submit(); return false; }else{ return false; }",
				'form'       => 'form-save',
				'formaction' => current_url()
			]
		];

		if ($this->input->method() == 'post') {
			if($this->form_validation->run() == true) {
				$general = [];
				foreach ($this->data['form_field']['general'] as $key => $value) {
					$general[$key] = $this->input->post($value['name']);
				}
				$general['model'] = $this->input->post('model');

				if(!$this->auth->is_admin()) {
					$general['status'] = 2;
				}
				$general['manufacturer_id'] = $this->input->post('manufacturer_id');


				if($this->input->post('images')) {
					if($this->input->post('default_image') != null) {
						$image = $this->input->post('images')[$this->input->post('default_image')]['url'];
						if(!empty($image)) {
							$general['image'] = $image;
							$general['alt_image'] = $this->input->post('images')[$this->input->post('default_image')]['alt'][0]; //added new
						}
					} else {
						foreach($this->input->post('images') as $key => $image) {
							$image_url = $image['url'];
							if(!empty($image_url)) {
								$general['image'] = $image_url;
								$general['alt_image'] = $image['alt'][0]; // added new
								$this->data['default_image'] = $key;
							}
							break;
						}

					}
				}


				$id = $this->{$this->model}->insert($general);

				if($id) {
					// Insert translation
					foreach ($this->input->post('translation') as $language_id => $translation) {
						$translation_data = [
							'product_id' 	=> $id,
							'language_id'   => $language_id,
							'name'  		=> $translation['name'],
							'slug'  		=> $translation['slug'],
							'description'  	=> $translation['description'],
							'seller_note'	=> $translation['seller_note']
						];
						if($this->auth->is_admin()) {
							$translation_data['tag']  			= $translation['tag'];
							$translation_data['meta_title']  	= $translation['meta_title'];
							$translation_data['meta_description']  = $translation['meta_description'];
							$translation_data['meta_keyword']  	= $translation['meta_keyword'];
							$translation_data['tag']  				= (!empty($translation['tag'])) ?  str_replace(' ',',',$translation['tag']) : null;
						}
						$this->{$this->model}->insert_translation($translation_data);
					}

					// Insert images
					if($this->input->post('images')) {
						$images = [];
						foreach($this->input->post('images') as $key => $image) {
							if($key != $this->data['default_image']) {
								$images[] = ['product_id' => $id, 'image' => $image['url'], 'sort' => $image['sort'], 'alt_image'=>$image['alt'][0]]; // added new ,'alt_image'=>$image['alt'][0]
							}
						}
						if($images) {
							$this->Product_model->insert_additional_data('product_images',$images);
						}
					}

					// Insert Categories
					if($this->input->post('category_id') && !empty($this->input->post('category_id'))) {
						foreach ($this->input->post('category_id') as $category_id) {
							$category_data[0] = ['product_id' => $id, 'category_id' => $category_id];
							$this->Product_model->insert_additional_data('product_to_category', $category_data);
						}

					}

					// Insert Related Products
					if($this->input->post('related_products') && !empty($this->input->post('related_products'))) {
						$related_products = explode(',',$this->input->post('related_products'));
						$related_products_data = [];
						foreach ($related_products as $related_id) {
							$related_products_data[] = ['product_id' => $id, 'related_id' => $related_id];
						}

						$this->Product_model->insert_additional_data('product_related', $related_products_data);
					}

					// Insert Product Special
					if($this->input->post('product_special')) {
						$product_special_data = [];
						foreach($this->input->post('product_special') as $product_special){
							$product_special_data[] = [
								'product_id' => $id,
								'customer_group_id' => $product_special['customer_group_id'],
								'priority' => $product_special['priority'],
								'price' => $product_special['price'],
								'date_start' => $product_special['date_start'],
								'date_end' => $product_special['date_end']
							];
						}
						$this->Product_model->insert_additional_data('product_special', $product_special_data);
					}

					// Insert Product Attribute
					if($this->input->post('product_attribute')) {
						foreach($this->input->post('product_attribute') as $product_attribute) {
							if((int)$product_attribute['attribute_value_id'] > 0) {
								$product_attr_data[0] = [
									'product_id' => $id,
									'attribute_id' => $product_attribute['attribute_id']
								];

								$product_attribute_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
								if($product_attribute_id) {
									$product_attr_value_data[0] = [
										'product_attribute_id' => $product_attribute_id,
										'product_id' => $id,
										'attribute_id' => $product_attribute['attribute_id'],
										'attribute_value_id' => $product_attribute['attribute_value_id']
									];

									$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
								}

							} elseif($product_attribute['attribute_value_id'] == 0) {
								$attr_value_data[0] = [
									'attribute_id' => $product_attribute['attribute_id'],
									'filter' => 0,
									'custom' => 1,
									'sort'	=> 0,
									'created_by' => $this->data['user']->id
								];

								$attribute_value_id = $this->Product_model->insert_additional_data('attribute_value', $attr_value_data);
								if($attribute_value_id) {
									foreach($product_attribute['attribute_value'] as $language_id => $attr_value) {
										$attr_value_description_data[0] = [
											'attribute_value_id' => $attribute_value_id,
											'language_id'  => $language_id,
											'attribute_id' => $product_attribute['attribute_id'],
											'name'		   => $attr_value['name']
										];

										$this->Product_model->insert_additional_data('attribute_value_description', $attr_value_description_data);
									}

									// Insert Product attribute
									$product_attr_data[0] = [
										'product_id' => $id,
										'attribute_id' => $product_attribute['attribute_id']
									];

									$product_attribute_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
									if($product_attribute_id) {
										$product_attr_value_data[0] = [
											'product_attribute_id' => $product_attribute_id,
											'product_id' => $id,
											'attribute_id' => $product_attribute['attribute_id'],
											'attribute_value_id' => $attribute_value_id
										];

										$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
									}

								}
							}
						}
					}

					// Insert Custom Attributes
					if($this->input->post('attribute')) {
						foreach($this->input->post('attribute') as $attribute) {
							if($this->valid_custom_attribute($attribute)) {
								$attr_general = [
									'attribute_group_id' => $this->input->post('attribute_group_id'),
									'filter' => 0,
									'custom' => 1,
									'custom_enable' => 0,
									'status' => 1
								];
								$attr_id = $this->Attribute_model->insert($attr_general);

								if($attr_id) {
									foreach($attribute['attribute_description'] as $language_id => $attribute_description) {
										$attr_translation_data = [
											'attribute_id' => $attr_id,
											'language_id'   =>  $language_id,
											'name'  => $attribute_description
										];
										$this->Attribute_model->insert_translation($attr_translation_data);
									}

									$attr_value_general = [
										'attribute_id' => $attr_id,
										'filter'  => 0,
										'custom' => 1,
										'sort'  => 0
									];

									$attribute_value_id = $this->Attribute_model->insert_attribute_value($attr_value_general);
									if($attribute_value_id) {
										if($attribute['attribute_value_description']) {
											foreach($attribute['attribute_value_description'] as $lang_id => $attribute_description) {
												$attr_value_translation = [
													'attribute_value_id'  => $attribute_value_id,
													'language_id'  => $lang_id,
													'attribute_id'  => $attr_id,
													'name' => $attribute_description
												];
												$this->Attribute_model->insert_attribute_value_translation($attr_value_translation);
											}
										}

										// Insert attr to Product
										$product_attr_data[0] = [
											'product_id' => $id,
											'attribute_id' => $attr_id
										];

										$product_attr_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
										if($product_attr_id) {
											$product_attr_value_data[0] = [
												'product_attribute_id' => $product_attr_id,
												'product_id' => $id,
												'attribute_id' => $attr_id,
												'attribute_value_id' => $attribute_value_id
											];

											$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
										}
									}

								}
							}
						}
					}

					// Insert Product Country Group
					if($this->input->post('product_country_group')) {
						foreach($this->input->post('product_country_group') as $product_country_group) {
							$product_country_group_data_general[0] = ['product_id' => $id, 'country_group_id' => $product_country_group['country_group_id'], 'percent' => $product_country_group['percent']];
							$product_country_group_id = $this->Product_model->insert_additional_data('product_country_group', $product_country_group_data_general);
						}
					}

					// Insert Product Options
					if($this->input->post('product_option')) {
						foreach($this->input->post('product_option') as $product_option) {
							if(array_key_exists('product_option_value',$product_option) && $product_option['product_option_value']) {
								$product_option_data[0] = [
									'product_id' => $id,
									'option_id'	 => $product_option['option_id'],
									'value'		 => (isset($product_option['value'])) ? $product_option['value'] : "",
									'required'	 => $product_option['required'],
								];
								$product_option_id = $this->Product_model->insert_additional_data('product_option', $product_option_data);
								if($product_option_id) {
									foreach($product_option['product_option_value'] as $product_option_value) {
										$product_option_value_data[0] = [
											'product_option_id'	=> $product_option_id,
											'product_id'		=> $id,
											'option_id'			=> $product_option['option_id'],
											'option_value_id'	=> $product_option_value['option_value_id'],
											'quantity'			=> $product_option_value['quantity'],
											'subtract'			=> $product_option_value['subtract'],
											'price'				=> $product_option_value['price'],
											'price_prefix'		=> $product_option_value['price_prefix'],
											'points'			=> 0,
											'points_prefix'		=> '+',
											'weight'			=> $product_option_value['weight'],
											'weight_prefix'		=> $product_option_value['weight_prefix'],
											'country_group_id'	=> isset($product_option_value['country_group_id']) ? $product_option_value['country_group_id'] : 0
										];
										$this->Product_model->insert_additional_data('product_option_value', $product_option_value_data);
									}
								}
							}
						}
					}

					// Insert Product Relations
					if($this->input->post('product_relation')) {
						foreach($this->input->post('product_relation') as $product_relation) {
							if(array_key_exists('product_relation_value',$product_relation) && $product_relation['product_relation_value']) {
								$relation_product_ids = [];
								foreach($product_relation['product_relation_value'] as $product_relation_value) {
									if((int)$product_relation_value['product_id'] == 0) {
										$product_relation_value['product_id'] = $id;
									}
									$relation_product_ids[(int)$product_relation_value['product_id']] = [
										'product_id' => (int) $product_relation_value['product_id'],
										'relation_value_id' => $product_relation_value['relation_value_id'],
									];
								}

								$product_ids = array_keys($relation_product_ids);
								for($i = 0; $i < count($product_ids); $i++) {
									$product_id = $product_ids[$i];

									$product_relation_data[0] = [
										'product_id' 	=> $product_id,
										'relation_id'	=> $product_relation['relation_id'],
										'value'		 	=> (isset($product_relation['value'])) ? $product_relation['value'] : "",
									];

									$product_relation_temp = $this->Product_model->get_additional_data('product_relation', 'id', ['product_id' => $product_id, 'relation_id' => $product_relation['relation_id']], true);
									if($product_relation_temp)
									{
										$product_relation_id = $product_relation_temp->id;
									}
									else
									{

										$product_relation_id = $this->Product_model->insert_additional_data('product_relation', $product_relation_data);
									}

									if($product_relation_id) {
										$product_relation_value_data[0] = [
											'product_relation_id'	=> $product_relation_id,
											'product_id'			=> $product_id,
											'relation_id'			=> 0,
											'relation_value_id'		=> $relation_product_ids[$product_id]['relation_value_id'],
											'current'				=> 1
										];

										$product_relation_value_temp = $this->Product_model->get_additional_data('product_relation_value', 'id', ['relation_value_id' =>  $relation_product_ids[$product_id]['relation_value_id'], 'product_id' => $product_id, 'relation_id' => 0], true);
										if(!$product_relation_value_temp)
										{
											$this->Product_model->insert_additional_data('product_relation_value', $product_relation_value_data);
										}

										foreach($relation_product_ids as $key => $relation_product_value) {
											if($key != $product_id) {
												$product_relation_value_data[0] = [
													'product_relation_id'	=> $product_relation_id,
													'product_id'			=> $product_id,
													'relation_id'			=> $key,
													'relation_value_id'		=> $relation_product_value['relation_value_id']
												];

												$product_relation_value_temp = $this->Product_model->get_additional_data('product_relation_value', 'id', ['relation_value_id' =>  $relation_product_ids[$product_id]['relation_value_id'], 'product_id' => $product_id, 'relation_id' => $key], true);
												if(!$product_relation_value_temp)
												{
													$this->Product_model->insert_additional_data('product_relation_value', $product_relation_value_data);
												}

											}
										}
									}

								}
							}
						}
					}

					$_product = $this->Product_model->get_products_static(['id' => $id])[0];

					$_product['objectID'] = $_product['id'];

					if($this->input->post('status'))
						$this->algolia->save('products', [$_product]);

				}

				$this->session->set_flashdata('message', translate('form_success_create'));
				redirect(site_url_multi($this->directory . $this->controller), 'refresh');
			} else {
				$this->data['message'] = translate('error_warning', true);
			}
		}

		$this->data['item_id'] = 0;
		$this->template->render($this->controller . '/form');
	}

	function valid_custom_attribute($attribute) {
		$valid = false;

		if(array_key_exists('attribute_description', $attribute)) {
			foreach($attribute['attribute_description'] as $attr_description) {
				if(!empty(trim($attr_description))) {
					$valid = true;
				}
			}
		}

		return $valid;
	}

	public function edit($id)
	{
		$this->data['title'] = translate('edit_title');
		$this->data['subtitle'] = translate('edit_description');

		$product = $this->{$this->model}->filter(['id' => $id])->one();



		if($product) {
			$weight_class_options = $this->generate_options(['name'=>'weight_class', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
			$length_class_options = $this->generate_options(['name'=>'length_class', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
			$stock_options = $this->generate_options(['name'=>'stock_status', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
			$country_options = $this->generate_options(['name'=>'country', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[['column' => 'name', 'order' =>'ASC']] ]);
			$tax_class_options = $this->generate_options(['name'=>'tax_class', 'key'=>'id', 'columns'=>'title', 'dynamic'=>false, 'translation'=>false, 'where'=>[], 'sort'=>[]], true);
			$currency_options = $this->generate_options(['name'=>'currency', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>false, 'where'=>[], 'sort'=>[] ]);
			$currency_options = ($currency_options) ? $currency_options : [];

			$this->data['model_value'] = (set_value('model')) ? set_value('model') : htmlentities($product->model, ENT_QUOTES);
			$this->form_validation->set_rules('model', translate('form_label_model'), "trim|required");

			$new_options = [];
			$new_options[''] = translate('select', true);
			$new_options[0] =  translate('form_label_new');
			$new_options[1] =  translate('form_label_used');
			$new_options[2] =  translate('form_label_refurbished');

			// General Form Fields
			$this->data['form_field']['general'] = [
				'sku' => [
					'property'  => 'text',
					'name'      => 'sku',
					'class'     => 'form-control',
					'value'     => (set_value('sku')) ? set_value('sku') : $product->sku,
					'label'     => translate('form_label_sku'),
					'info' 		=> translate('form_label_description_sku'),
					'placeholder' 	=> translate('form_label_sku'),
					'validation' 	=> []
				],
				'upc' => [
					'property'  => 'text',
					'name'      => 'upc',
					'class'     => 'form-control',
					'value'     => (set_value('upc')) ? set_value('upc') : $product->upc,
					'label'     => translate('form_label_upc'),
					'info' 		=> translate('form_label_description_upc'),
					'placeholder' 	=> translate('form_label_upc'),
					'validation' 	=> []
				],
				'ean' => [
					'property'  => 'text',
					'name'      => 'ean',
					'class'     => 'form-control',
					'value'     => (set_value('ean')) ? set_value('ean') : $product->ean,
					'label'     => translate('form_label_ean'),
					'info' 		=> translate('form_label_description_ean'),
					'placeholder' 	=> translate('form_label_ean'),
					'validation' 	=> []
				],
				'jan' => [
					'property'  => 'text',
					'name'      => 'jan',
					'class'     => 'form-control',
					'value'     => (set_value('jan')) ? set_value('jan') : $product->jan,
					'label'     => translate('form_label_jan'),
					'info' 		=> translate('form_label_description_jan'),
					'placeholder' 	=> translate('form_label_jan'),
					'validation'	=> []
				],
				'isbn' => [
					'property'  => 'text',
					'name'      => 'isbn',
					'class'     => 'form-control',
					'value'     => (set_value('isbn')) ? set_value('isbn') : $product->isbn,
					'label'     => translate('form_label_isbn'),
					'info' 		=> translate('form_label_description_isbn'),
					'placeholder' 	=> translate('form_label_isbn'),
					'validation' 	=> []
				],
				'mpn' => [
					'property'  => 'text',
					'name'      => 'mpn',
					'class'     => 'form-control',
					'value'     => (set_value('mpn')) ? set_value('mpn') : $product->mpn,
					'label'     => translate('form_label_mpn'),
					'info' 		=> translate('form_label_description_mpn'),
					'placeholder' 	=> translate('form_label_mpn'),
					'validation' 	=> []
				],
				'price' => [
					'property'  => 'text',
					'name'      => 'price',
					'class'     => 'form-control',
					'value'     => (set_value('price')) ? set_value('price') : $product->price,
					'label'     => translate('form_label_price'),
					'placeholder'	=> translate('form_label_price'),
					'validation' 	=> ['rules' => 'required']
				],
				'currency' => [
					'property'  => 'dropdown',
					'name'      => 'currency',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_currency'),
					'options'	=> $currency_options,
					'selected'  => (set_value('currency')) ? set_value('currency') : $product->currency,
					'validation' 	=> []
				],
				'tax_class_id' => [
					'property'  => 'dropdown',
					'name'      => 'tax_class_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_tax_class_id'),
					'options'	=> $tax_class_options,
					'selected'  => (set_value('tax_class_id')) ? set_value('tax_class_id') : $product->tax_class_id,
					'validation' 	=> []
				],
				'quantity' => [
					'property'  => 'number',
					'min'		=> '0',
					'name'      => 'quantity',
					'class'     => 'form-control',
					'value'     => (set_value('quantity')) ? set_value('quantity') : $product->quantity,
					'label'     => translate('form_label_quantity'),
					'placeholder' 	=> translate('form_label_quantity'),
					'validation' 	=> []
				],
				'day' => [
					'property'  => 'number',
					'min'		=> '0',
					'name'      => 'day',
					'class'     => 'form-control',
					'value'     => (set_value('day')) ? set_value('day') : $product->day,
					'label'     => translate('form_label_day'),
					'placeholder' 	=> translate('form_label_day'),
					'validation' 	=> []
				],
				'subtract' => [
					'property'  => 'dropdown',
					'name'      => 'subtract',
					'class'     => 'form-control',
					'label'     => translate('form_label_subtract'),
					'options' 	=> [translate('no', true), translate('yes', true)],
					'selected'  => (set_value('subtract')) ? set_value('subtract') : $product->subtract,
					'validation' 	=> []
				],
				'stock_status_id' => [
					'property'  => 'dropdown',
					'name'      => 'stock_status_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_stock_status_id'),
					'options' 	=> $stock_options,
					'selected'  => (set_value('stock_status_id')) ? set_value('stock_status_id') : $product->stock_status_id,
					'validation' 	=> []
				],
				'country_id' => [
					'property'  => 'dropdown',
					'name'      => 'country_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_country_id'),
					'options' 	=> $country_options,
					'selected'  => (set_value('country_id')) ? set_value('country_id') : $product->country_id,
					'validation' 	=> []
				],
				'region_id' => [
					'property'  => 'dropdown',
					'name'      => 'region_id',
					'class' 	=> 'select-search',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_region_id'),
					'options' 	=> [],
					'selected'  => (set_value('region_id')) ? set_value('region_id') : $product->region_id,
					'data-selected_id'  => (set_value('region_id')) ? set_value('region_id') : $product->region_id,
					'validation' 	=> []
				],
				'new' => [
					'property'  => 'dropdown',
					'name'      => 'new',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_new'),
					'options'	=> $new_options,
					'selected'  => (set_value('new')) ? set_value('new') : $product->new,
					'validation' => ['rules' => 'required|is_natural']
				],
				'date_available' => [
					'property'  => 'date',
					'name'      => 'date_available',
					'class'     => 'form-control',
					'value'     => (set_value('date_available')) ? set_value('date_available') : $product->date_available,
					'label'     => translate('form_label_date_available'),
					'placeholder' 	=> translate('form_label_date_available'),
					'validation' 	=> []
				],
				'length' => [
					'property'  => 'text',
					'name'      => 'length',
					'class'     => 'form-control',
					'value'     => (set_value('length')) ? set_value('length') : $product->length,
					'label'     => translate('form_label_length'),
					'placeholder' 	=> translate('form_label_length'),
					'validation' 	=> ['rules' => 'required|trim']
				],
				'width' => [
					'property'  => 'text',
					'name'      => 'width',
					'class'     => 'form-control',
					'value'     => (set_value('width')) ? set_value('width') : $product->width,
					'label'     => translate('form_label_width'),
					'placeholder' 	=> translate('form_label_width'),
					'validation' 	=> ['rules' => 'required|trim']
				],
				'height' => [
					'property'  => 'text',
					'name'      => 'height',
					'class'     => 'form-control',
					'value'     => (set_value('height')) ? set_value('height') : $product->height,
					'label'     => translate('form_label_height'),
					'placeholder' 	=> translate('form_label_height'),
					'validation' 	=> ['rules' => 'required|trim']
				],
				'length_class_id' => [
					'property'  => 'dropdown',
					'name'      => 'length_class_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_length_class_id'),
					'options'	=> $length_class_options,
					'selected'  	=> (set_value('length_class_id')) ? set_value('length_class_id') : $product->length_class_id,
					'validation' 	=> ['rules' => 'required|trim']
				],
				'weight' => [
					'property'  => 'text',
					'name'      => 'weight',
					'class'     => 'form-control',
					'value'     => (set_value('weight')) ? set_value('weight') : $product->weight,
					'label'     => translate('form_label_weight'),
					'placeholder' 	=> translate('form_label_weight'),
					'validation' 	=> ['rules' => 'required|trim']
				],
				'weight_class_id' => [
					'property'  => 'dropdown',
					'name'      => 'weight_class_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'selected'     => (set_value('weight_class_id')) ? set_value('weight_class_id') : $product->weight_class_id,
					'label'     => translate('form_label_weight_class_id'),
					'options'		=> $weight_class_options,
					'validation' 	=> ['rules' => 'required|trim']
				]
			];


			if($this->auth->is_admin()) {
				$this->data['form_field']['general']['status'] = [
					'property' 	=> 'dropdown',
					'name' 		=> 'status',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label' 	=> translate('form_label_status'),
					'options' 	=> [translate('disable', true), translate('enable', true), translate('pending', true)],
					'selected' 		=> set_value('status') ?  set_value('status') : $product->status,
					'validation' 	=> []
				];
				$this->data['form_field']['general']['created_at'] = [
					'property'  => 'datetime',
					'name'      => 'created_at',
					'class'     => 'form-control',
					'value'     => (set_value('created_at')) ?
                        set_value('created_at') :
                        date('Y-m-d',strtotime($product->created_at)) . 'T' . date('H:i',strtotime($product->created_at)),
					'label'     => translate('form_label_created_at'),
					'placeholder' 	=> translate('form_label_created_at'),
					'validation' 	=> []
				];
			}

			// Get Selected Categories
			$this->data['categories_data'] = $this->input->post('category_id');
			if(!$this->data['categories_data']) {
				$rows = $this->Product_model->get_additional_data('product_to_category','*',['product_id' => $id]);
				if($rows) {
					foreach ($rows as $selected_category) {
						$this->data['categories_data'][] = $selected_category->category_id;
					}
				}
			}

			if($this->input->server('REQUEST_METHOD') == 'POST') {

			    if($product->status == 0 && $this->input->post('status') == 1 && $product->created_by > 0) {

			        $vendor = $this->db->from('users')
                        ->where('id', $product->created_by)
                        ->get()
                        ->row();

			        send_custom_mail([
			            'to'        =>  $vendor->mail,
                        'subject'   =>  translate('vendor_product_approved_subject', true),
                        'message'   =>  translate('vendor_product_approved_text', true)
                    ]);

                }


            }

			// Get Related Products
			$related_products = "";
			if(!$this->input->post('related_products')) {
				$rows = $this->Product_model->get_additional_data('product_related','*',['product_id' => $id]);
				if($rows) {
					$temp_related_products = [];
					foreach ($rows as $related_product) {
						$temp_related_products[] = $related_product->related_id;
					}
					$related_products = implode(',',$temp_related_products);
				}
			}

			$this->load->model('modules/Category_model');
			$categories = $this->Category_model->filter(['status' => 1, 'parent' => 0])->with_translation()->order_by('name', 'ASC')->all();
			$this->data['categories'] = [];
			if($categories) {
				foreach($categories as $category) {
					$category_option = new stdclass();
					$category_option->id = $category->id;
					$category_option->name = $category->name;
					$category_option->attribute_group_id = $category->attribute_group_id;
					$category_option->has_child = $this->Category_model->filter(['status' => 1, 'parent' => $category->id])->count_rows();

					$this->data['categories'][] = $category_option;
				}
			}

			// Links Form Fields
			$this->data['form_field']['links'] = [
				'manufacturer_id'=> [
					'property'   => 'dropdown-ajax',
					'type'		 => 'general',
					'element'	 => 'manufacturer_id',
					'name'       => 'manufacturer_id',
					'id'         => 'manufacturer_id',
					'class'		 => 'form-control dropdownSingleAjax',
					'label'      => translate('form_label_manufacturer'),
					'placeholder'=> translate('form_label_manufacturer'),
					'selected'   => (set_value('manufacturer_id')) ? set_value('manufacturer_id') : $product->manufacturer_id,
					'selected_text' => ""
				],
				'related_products' => [
					'property'   => 'multiselect_ajax',
					'type'		 => 'general',
					'element'	 => 'related_products',
					'name'       => 'related_products',
					'id'         => 'related_products',
					'class'		 => 'form-control dropdownMultiAjax',
					'label'      => translate('form_label_related_products'),
					'placeholder'=> translate('form_label_related_products'),
					'selected'   => (set_value('related_products')) ? set_value('related_products') : $related_products,
					'selected_elements' => [],
					'selected_text' => ""
				]
			];
			if($this->data['form_field']['links']['manufacturer_id']['selected']){
				$this->data['form_field']['links']['manufacturer_id']['selected_text'] = $this->get_selected_element('manufacturer_id',$this->data['form_field']['links']['manufacturer_id']['selected']);
			}

			if($this->data['form_field']['links']['related_products']['selected']){
				$this->data['form_field']['links']['related_products']['selected_elements'] = $this->get_selected_elments('related_products',$this->data['form_field']['links']['related_products']['selected']);
			}

			$this->data['attribute_group_id'] = set_value('attribute_group_id');
			$attributes_custom_created = [];

			// Get Product Attributes
			$this->data['product_attributes'] = (set_value('product_attribute')) ? set_value('product_attribute') : [];
			if(!$this->input->post('product_attribute')) {
				$product_attributes = $this->Product_model->get_additional_data('product_attribute','*',['product_id' => $id]);

				if($product_attributes) {
					foreach($product_attributes as $product_attribute) {
						$temp_data = [];
						// Get product attribute values
						$product_attribute_value = $this->Product_model->get_additional_data('product_attribute_value', '*', ['product_attribute_id' => $product_attribute->id],true);

						if($product_attribute_value) {
							$attribute = $this->Attribute_model->filter(['id' => $product_attribute->attribute_id])->with_translation()->one();
							if($attribute) {
								if($attribute->custom == 0) {
									$temp_data = [
										'attribute_id' => $product_attribute->attribute_id,
										'name' => $attribute->name,
										'custom_enable' => $attribute->custom_enable,
										'attribute_value_id' => $product_attribute_value->attribute_value_id,
										'attribute_value' 	=> []
									];

									$temp_attr_value = $this->Product_model->get_additional_data('attribute_value', '*', ['id' => $product_attribute_value->attribute_value_id],true);
									if($temp_attr_value && $temp_attr_value->custom) {
										$temp_data['attribute_value_id'] = 0;
										$temp_attr_value_description = $this->Product_model->get_additional_data('attribute_value_description', '*', ['attribute_value_id' => $product_attribute_value->attribute_value_id]);
										if($temp_attr_value_description) {
											foreach($temp_attr_value_description as $description) {
												$temp_data['attribute_value'][$description->language_id] = ['name' => $description->name];
											}
										}
									}

									$this->data['product_attributes'][$product_attribute->attribute_id] = $temp_data;
								} else {
									$attributes_custom_created[] = $attribute->id;
								}
							}
						}
					}
				}


				// Get attribute group id
				if($this->data['categories_data']) {
					foreach($this->data['categories_data'] as $row) {
						$product_category = $this->Category_model->filter(['id' => $row])->one();
						if($product_category && $product_category->attribute_group_id != 0) {
							$this->data['attribute_group_id'] = $product_category->attribute_group_id;
						}
					}
				}
			}


			$attribute_group_attributes  = $this->get_attributes_by_group_id($this->data['attribute_group_id']);

			if($attribute_group_attributes) {
				foreach($attribute_group_attributes as $row) {
					if(!$this->data['product_attributes'] || !array_key_exists($row['attribute_id'], $this->data['product_attributes'])) {
						$this->data['product_attributes'][] = $row;
					}
				}
			}


			$attribute_values = [];
			if($this->data['product_attributes']) {
				foreach($this->data['product_attributes'] as $product_attribute) {
					if(!array_key_exists($product_attribute['attribute_id'],$attribute_values))
					{
						$all_attribute_values = $this->Product_model->get_additional_data('attribute_value','*', ['attribute_id' => $product_attribute['attribute_id'], 'custom' => 0]);
						if($all_attribute_values) {
							foreach($all_attribute_values as $attribute_value) {
								$value_description = $this->Product_model->get_additional_data('attribute_value_description','*', ['attribute_value_id' => $attribute_value->id],true);

									$attribute_values[$product_attribute['attribute_id']][] = [
										'product_attribute_id'	=> $product_attribute['attribute_id'],
										'attribute_id'	=> $product_attribute['attribute_id'],
										'attribute_value_id' => $attribute_value->id,
										'name' => ($value_description) ? $value_description->name : ""
									];

							}
						}
					}
				}
			}

			$this->data['attribute_values'] = $attribute_values;

			$this->data['attributes'] = $this->input->post('attribute');
			if(!$this->data['attributes'] && $attributes_custom_created) {
				foreach($attributes_custom_created as $attr_id) {
					$custom_attr_data = [];
					$attr_translation = $this->Product_model->get_additional_data('attribute_translation', '*', ['attribute_id' => $attr_id]);
					if($attr_translation) {
						foreach($attr_translation as $translation) {
							$custom_attr_data['attribute_description'][$translation->language_id] = $translation->name;
						}
						$attr_value_description = $this->Product_model->get_additional_data('attribute_value_description', '*', ['attribute_id' => $attr_id]);
						if($attr_value_description) {
							foreach($attr_value_description as $description) {
								$custom_attr_data['attribute_value_description'][$description->language_id] = $description->name;
							}
						}

						$this->data['attributes'][] = $custom_attr_data;
					}

				}
			}

			$this->data['product_country_groups'] = set_value('product_country_group');
			$product_country_groups = [];
			if(!$this->input->post('product_country_group')){
				$rows = $this->Product_model->get_additional_data('product_country_group','*',['product_id' => $id]);
				if($rows) {
					foreach($rows as $product_country_group)
					{
						$product_country_groups[] = [
							'percent'			=>  $product_country_group->percent,
							'country_group_id' => $product_country_group->country_group_id
						];
					}

					$this->data['product_country_groups'] = $product_country_groups;
				}
			}

			// Discount tab data
			$this->data['customer_groups'] = $this->Customer_group_model->fields("id, name")->with_translation()->as_array()->all();
			$this->data['product_discounts'] = set_value('product_discount');
			/*if(!$this->input->post('product_discount')) {
				$product_discounts = $this->Product_model->get_additional_data('product_discount','*',['product_id' => $id]);
				if($product_discounts) {
					foreach ($product_discounts as $product_discount) {
						$this->data['product_discounts'][] = [
							'customer_group_id' => $product_discount->customer_group_id,
							'quantity' => $product_discount->quantity,
							'priority' => $product_discount->priority,
							'price' => $product_discount->price,
							'date_start' => $product_discount->date_start,
							'date_end' => $product_discount->date_end
						];
					}
				}
			}*/

			// Speical tab data
			$this->data['product_specials'] = (set_value('product_special')) ? set_value('product_special') : [];
			if(!$this->input->post('product_special')) {
				$product_specials = $this->Product_model->get_additional_data('product_special','*',['product_id' => $id]);
				if($product_specials) {
					foreach ($product_specials as $product_special) {
						$this->data['product_specials'][] = [
							'customer_group_id' => $product_special->customer_group_id,
							'priority' => $product_special->priority,
							'price' => $product_special->price,
							'date_start' => $product_special->date_start,
							'date_end' => $product_special->date_end,
							'discount_id' => $product_special->discount_id,
						];
					}
				}
			}

			// Option tab data
			$option_values = [];
			$this->data['product_options'] = (set_value('product_option')) ? set_value('product_option') : [];
			if(!$this->input->post('product_option')) {
				$product_options = $this->Product_model->get_additional_data('product_option','*',['product_id' => $id]);
				if($product_options) {
					foreach($product_options as $product_option) {
						$temp_data = [];
						// Get product option values
						$product_option_values = $this->Product_model->get_additional_data('product_option_value','*,id as product_option_value_id',['product_option_id' => $product_option->id],false,true);
						if($product_option_values) {
							$option = $this->Option_model->filter(['id' => $product_option->option_id])->with_translation()->one();
							if($option) {
								$temp_data = [
									'product_option_id' => $product_option->id,
									'name' => $option->name,
									'option_id' => $product_option->option_id,
									'type' => $option->type,
									'required' => $product_option->required,
									'product_option_value' => $product_option_values,
								];
								$this->data['product_options'][] = $temp_data;
							}

						}
					}
				}
			}
			if($this->data['product_options']) {
				foreach($this->data['product_options'] as $product_option) {
					if(!array_key_exists($product_option['option_id'],$option_values))
					{
						$all_option_values = $this->Option_model->get_additional_data('option_value','id,image,sort', ['option_id' => $product_option['option_id']]);
						if($all_option_values) {
							foreach($all_option_values as $option_value) {
								$value_description = $this->Option_model->get_additional_data('option_value_description','*', ['option_value_id' => $option_value->id],true);

									$option_values[$product_option['option_id']][] = [
										'product_option_id'	=> $product_option['option_id'],
										'option_id'	=> $product_option['option_id'],
										'option_value_id' => $option_value->id,
										'image' => $option_value->image,
										'name' => ($value_description) ? $value_description->name : ""
									];

							}
						}
					}
				}
			}
			$this->data['option_values'] = $option_values;

			// Relation tab data
			$relation_values = [];
			$this->data['product_relations'] = (set_value('product_relation')) ? set_value('product_relation') : [];
			if(!$this->input->post('product_relation')) {
				$product_relations = $this->Product_model->get_additional_data('product_relation','*',['product_id' => $id]);
				if($product_relations) {
					foreach($product_relations as $product_relation) {
						$temp_data = [];
						// Get product relation values
						$product_relation_values = $this->Product_model->get_additional_data('product_relation_value','*,id as product_relation_value_id',['product_relation_id' => $product_relation->id],false,true);
						if($product_relation_values) {
							$relation = $this->Relation_model->filter(['id' => $product_relation->relation_id])->with_translation()->one();
							if($relation) {
								$temp_data = [
									'product_relation_id' => $product_relation->id,
									'name' => $relation->name,
									'relation_id' => $product_relation->relation_id,
									'product_relation_value' => [],
								];

								foreach($product_relation_values as $key => $prv) {

									if($prv['relation_id'] != 0) {
										$relation_product = $this->Product_model->fields('name')->filter(['id' => $prv['relation_id'], 'status' => 1])->with_translation()->one();
										if($relation_product) {
											$temp_data['product_relation_value'][] = [
												'relation_value_id' => $prv['relation_value_id'],
												'product_relation_value_id' => $prv['product_relation_value_id'],
												'product_name' => $relation_product->name,
												'product_id' => $prv['relation_id'],
											];
										}
									} else {
										$temp_data['product_relation_value'][] = [
											'relation_value_id' => $prv['relation_value_id'],
											'product_relation_value_id' => $prv['product_relation_value_id'],
											'product_name' => "",
											'product_id' => "",
										];

										if($prv['current'] == 1) {
											$temp_data['custom_current_product'] = $prv['relation_value_id'];
										}
									}
								}
								$this->data['product_relations'][] = $temp_data;
							}

						}
					}
				}
			}

			if($this->data['product_relations']) {
				foreach($this->data['product_relations'] as $product_relation) {
					if(!array_key_exists($product_relation['relation_id'],$relation_values))
					{
						$all_relation_values = $this->Relation_model->get_additional_data('relation_value','id,sort', ['relation_id' => $product_relation['relation_id']]);
						if($all_relation_values) {
							foreach($all_relation_values as $relation_value) {
								$value_description = $this->Relation_model->get_additional_data('relation_value_description','*', ['relation_value_id' => $relation_value->id],true);

									$relation_values[$product_relation['relation_id']][] = [
										'product_relation_id'	=> $product_relation['relation_id'],
										'relation_id'	=> $product_relation['relation_id'],
										'relation_value_id' => $relation_value->id,
										'name' => ($value_description) ? $value_description->name : ""
									];

							}
						}
					}
				}
			}

			$this->data['relation_values'] = $relation_values;

			// Images fields
			$this->data['images'] = [];
			$this->data['default_image'] = set_value('default_image');
			if(!$this->input->post('images')) {
				if(!empty($product->image)) {
					$new = new stdClass();
					$new->name = basename($product->image);
					$new->sort = 0;
					$new->path = $product->image;
					$new->preview = $this->Model_tool_image->resize($product->image, 250, 250);
                    $new->alt_image = $product->alt_image; // added new Vasif
					$this->data['images'][] = $new;
					$this->data['default_image'] = 0;
				}


				$product_images = $this->Product_model->get_additional_data('product_images','*',['product_id' => $id]);


				if($product_images) {
					foreach($product_images as $product_image) {
						$new = new stdClass();
						$new->name = basename($product_image->image);
						$new->sort = $product_image->sort;
						$new->path = $product_image->image;
						$new->alt_image  = $product_image->alt_image; // added new Vasif
						$new->preview = $this->Model_tool_image->resize($product_image->image, 250, 250);

						$this->data['images'][] = $new;
					}
				}
			} else {
				foreach($this->input->post('images') as $image)
				{
					$new = new stdClass();
					$new->name = basename($image['url']);
					$new->sort = $image['sort'];
					$new->path = $image['url'];
					$new->preview = $this->Model_tool_image->resize($image['url'], 250, 250);

					$this->data['images'][] = $new;
				}
			}



			// Set form validation rules
			foreach ($this->data['form_field']['general'] as $key => $value) {
				if($value['validation']) {
					$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
				}
			}

			// Translation Form Fields
			foreach ($this->data['languages'] as $language) {
				$row_translation = $this->{$this->model}->fields('*')->filter(['product_id' => $id])->with_translation($language['id'])->one();

				$this->data['form_field']['translation'][$language['id']]['name'] = [
					'property'    	=> "text",
					'name'        	=> 'translation[' . $language['id'] . '][name]',
					'class'       	=> 'form-control',
					'value'       	=> (set_value('translation[' . $language['id'] . '][name]')) ? set_value('translation[' . $language['id'] . '][name]') : $row_translation->name,
					'label'       	=> translate("form_label_name"),
					'placeholder' 	=> translate("form_label_name"),
					'validation'    => ['rules' => 'required']
				];

				$this->data['form_field']['translation'][$language['id']]['slug'] = [
					'property'    	=> "slug",
					'name'        	=> 'translation[' . $language['id'] . '][slug]',
					'data-for'		=> 'name',
					'data-type'		=> 'translation',
					'data-lang-id'	=> $language['id'],
					'class'       	=> 'form-control slugField',
					'value'       	=> (set_value('translation[' . $language['id'] . '][slug]')) ? set_value('translation[' . $language['id'] . '][slug]')  : $row_translation->slug,
					'label'       	=> translate("form_label_slug"),
					'placeholder' 	=> translate("form_label_slug"),
					'validation'    => ['rules' => 'required']
				];

				$this->data['form_field']['translation'][$language['id']]['description'] = [
					'property'    	=> "textarea",
					'name'   		=> 'translation[' . $language['id'] . '][description]',
					'class'       	=> 'form-control ckeditor',
					'value'       	=> (set_value('translation[' . $language['id'] . '][description]')) ? set_value('translation[' . $language['id'] . '][description]')  : $row_translation->description,
					'label'       	=> translate("form_label_description"),
					'placeholder' 	=> translate("form_label_description"),
					'validation'    => []
				];

				$this->data['form_field']['translation'][$language['id']]['seller_note'] = [
					'property'    	=> "textarea",
					'name'   		=> 'translation[' . $language['id'] . '][seller_note]',
					'class'       	=> 'form-control',
					'value'       	=> (set_value('translation[' . $language['id'] . '][seller_note]')) ? set_value('translation[' . $language['id'] . '][seller_note]')  : $row_translation->seller_note,
					'label'       	=> translate("form_label_seller_note"),
					'placeholder' 	=> translate("form_label_seller_note"),
					'validation'    => []
				];
				if($this->auth->is_admin()) {

					$this->data['form_field']['translation'][$language['id']]['meta_title'] = [
						'property'    	=> "text",
						'name' 			=> 'translation[' . $language['id'] . '][meta_title]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][meta_title]')) ? set_value('translation[' . $language['id'] . '][meta_title]')  : $row_translation->meta_title,
						'label'       	=> translate("form_label_meta_title"),
						'placeholder' 	=> translate("form_label_meta_title"),
						'validation'    => []
					];

					$this->data['form_field']['translation'][$language['id']]['meta_description'] = [
						'property'    	=> "textarea",
						'name'   		=> 'translation[' . $language['id'] . '][meta_description]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][meta_description]')) ? set_value('translation[' . $language['id'] . '][meta_description]')  : $row_translation->meta_description,
						'label'       	=> translate("form_label_meta_description"),
						'placeholder' 	=> translate("form_label_meta_description"),
						'validation'    => []
					];

					$this->data['form_field']['translation'][$language['id']]['meta_keyword'] = [
						'property'    	=> "textarea",
						'name'   		=> 'translation[' . $language['id'] . '][meta_keyword]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][meta_keyword]')) ? set_value('translation[' . $language['id'] . '][meta_keyword]')  : $row_translation->meta_keyword,
						'label'       	=> translate("form_label_meta_keyword"),
						'placeholder' 	=> translate("form_label_meta_keyword"),
						'validation'    => []
					];

					$this->data['form_field']['translation'][$language['id']]['tag'] = [
						'property'    	=> "textarea",
						'name' 			=> 'translation[' . $language['id'] . '][tag]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][tag]')) ? set_value('translation[' . $language['id'] . '][tag]')  : ((!empty($row_translation->tag)) ? str_replace(',',' ',$row_translation->tag) : ""),
						'label'       	=> translate("form_label_tag"),
						'info'       	=> translate("form_label_description_comma_separated"),
						'placeholder' 	=> translate("form_label_tag"),
						'validation'    => []
					];
				}

			}

			foreach ($this->data['languages'] as $language) {
				foreach ($this->data['form_field']['translation'][$language['id']] as $key => $value)
				{
					if($value['validation']){
						$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
					}
				}
			}

			$this->data['buttons'][] = [
				'type'       => 'button',
				'text'       => translate('form_button_save', true),
				'class'      => 'btn btn-primary btn-labeled heading-btn',
				'id'         => 'save',
				'icon'       => 'icon-floppy-disk',
				'additional' => [
					'onclick'    => "if(confirm('".translate('are_you_sure', true)."')){ $('#form-save').submit(); return false; }else{ return false; }",
					'form'       => 'form-save',
					'formaction' => current_url()
				]
			];

			if ($this->input->method() == 'post') {		
			
				if($this->form_validation->run() == true) {			
					
					$general = [];
					foreach ($this->data['form_field']['general'] as $key => $value) {
						$general[$key] = $this->input->post($value['name']);
					}
					$general['model'] = $this->input->post('model');
					if(!$this->auth->is_admin()) {
						$general['status'] = 2;
					}

					$general['manufacturer_id'] = $this->input->post('manufacturer_id');

					if($this->input->post('images')) {
						if($this->input->post('default_image') != null) {
							$image = $this->input->post('images')[$this->input->post('default_image')]['url'];
							$alt_image = $this->input->post('images')[$this->input->post('default_image')]['alt'][0];
							if(!empty($image)) {
								$general['image'] = $image;
								$general['alt_image'] = $alt_image;
							}
						} else {
							foreach($this->input->post('images') as $key => $image) {
								$image_url = $image['url'];
								if(!empty($image_url)) {
									$general['image'] = $image_url;
									$general['alt_image'] = $image['alt'][0];
									$this->data['default_image'] = $key;
								}
								break;
							}

						}
					}

					//print_r($general); die;

//					unset($general['created_at']);

					$this->Product_model->update($general,['id' => $id]);

					if($id) {
						// Delete and Insert translation
						$this->Product_model->delete_translation($id);
						foreach ($this->input->post('translation') as $language_id => $translation) {
							$translation_data = [
								'product_id' 	=> $id,
								'language_id'   => $language_id,
								'name'  		=> $translation['name'],
								'slug'  		=> $translation['slug'],
								'description'  	=> $translation['description'],
								'seller_note'  	=> $translation['seller_note']

							];
							if($this->auth->is_admin()) {
								$translation_data['tag']  			= $translation['tag'];
								$translation_data['meta_title']  	= $translation['meta_title'];
								$translation_data['meta_description']  = $translation['meta_description'];
								$translation_data['meta_keyword']  	= $translation['meta_keyword'];
								$translation_data['tag']  			= (!empty($translation['tag'])) ?  str_replace(' ',',',$translation['tag']) : null;
							}
							$this->Product_model->insert_translation($translation_data);
						}

						// Delete and Insert Categories
						$this->Product_model->delete_additional_data('product_to_category',['product_id' => $id]);
						if($this->input->post('category_id') && !empty($this->input->post('category_id'))) {
							foreach ($this->input->post('category_id') as $category_id) {
								$category_data[0] = ['product_id' => $id, 'category_id' => $category_id];
								$this->Product_model->insert_additional_data('product_to_category', $category_data);
							}

						}

						// Delete and Insert Related Products
						$this->Product_model->delete_additional_data('product_related',['product_id' => $id]);
						if($this->input->post('related_products') && !empty($this->input->post('related_products'))) {
							$related_products = explode(',',$this->input->post('related_products'));
							$related_products_data = [];
							foreach ($related_products as $related_id) {
								$related_products_data[] = ['product_id' => $id, 'related_id' => $related_id];
							}

							$this->Product_model->insert_additional_data('product_related', $related_products_data);
						}

						// Delete and Insert Product Discout
						/*$this->Product_model->delete_additional_data('product_discount',['product_id' => $id]);
						if($this->input->post('product_discount')) {
							$product_discount_data = [];
							foreach($this->input->post('product_discount') as $product_discount){
								$product_discount_data[] = [
									'product_id' => $id,
									'customer_group_id' => $product_discount['customer_group_id'],
									'priority' => $product_discount['priority'],
									'quantity' => $product_discount['quantity'],
									'price' => $product_discount['price'],
									'date_start' => $product_discount['date_start'],
									'date_end' => $product_discount['date_end']
								];
							}

							$this->Product_model->insert_additional_data('product_discount', $product_discount_data);
						}*/

						// Delete and Insert Product Special
						$this->Product_model->delete_additional_data('product_special',['product_id' => $id]);
						if($this->input->post('product_special')) {
							$product_special_data = [];
							foreach($this->input->post('product_special') as $product_special){
								$product_special_data[] = [
									'product_id' => $id,
									'customer_group_id' => $product_special['customer_group_id'],
									'priority' => $product_special['priority'],
									'price' => $product_special['price'],
									'date_start' => $product_special['date_start'],
									'date_end' => $product_special['date_end'],
									'discount_id' => $product_special['discount_id'] ?? null,
								];
							}

							$this->Product_model->insert_additional_data('product_special', $product_special_data);
						}

						// Delete Custom created attributes by user
						$product_attributes = $this->Product_model->get_additional_data('product_attribute','*', ['product_id' => $id]);
						if($product_attributes) {
							foreach($product_attributes as $product_attribute) {
								$attribute = $this->Attribute_model->filter(['id' => $product_attribute->attribute_id])->one();
								if($attribute) {
									if($attribute->custom_enable == 1 && $attribute->custom == 0) {
										$product_attribute_value = $this->Product_model->get_additional_data('product_attribute_value', '*', ['product_attribute_id' => $product_attribute->id],true);
										if($product_attribute_value) {
											$attribute_value = $this->Product_model->get_additional_data('attribute_value','*', ['id' => $product_attribute_value->attribute_value_id, 'attribute_id' => $product_attribute_value->attribute_id, 'created_by' => $this->data['user']->id], true);
											if($attribute_value) {
												// Delete Attribute Value
												$this->Product_model->delete_additional_data('attribute_value', ['id' => $product_attribute_value->attribute_value_id, 'attribute_id' => $product_attribute_value->attribute_id, 'created_by' => $this->data['user']->id]);
												$this->Product_model->delete_additional_data('attribute_value_description', ['attribute_value_id' => $attribute_value->id]);
											}
										}
									} elseif($attribute->custom == 1 && $attribute->created_by == $this->data['user']->id) {
										// Delete this attribute
										$this->Product_model->delete_additional_data('attribute', ['id' => $attribute->id]);
										$this->Product_model->delete_additional_data('attribute_translation', ['attribute_id' => $attribute->id]);
										$this->Product_model->delete_additional_data('attribute_value', ['attribute_id' => $attribute->id]);
										$this->Product_model->delete_additional_data('attribute_value_description', ['attribute_id' => $attribute->id]);
									}
								}

							}
						}

						// Delete all product attributes
						$this->Product_model->delete_additional_data('product_attribute', ['product_id' => $id]);
						$this->Product_model->delete_additional_data('product_attribute_value', ['product_id' => $id]);

						// Insert Custom Attributes
						if($this->input->post('attribute')) {
							foreach($this->input->post('attribute') as $attribute) {
								if($this->valid_custom_attribute($attribute)) {
									$attr_general = [
										'attribute_group_id' => $this->input->post('attribute_group_id'),
										'filter' => 0,
										'custom' => 1,
										'custom_enable' => 0,
										'status' => 1
									];
									$attr_id = $this->Attribute_model->insert($attr_general);

									if($attr_id) {
										foreach($attribute['attribute_description'] as $language_id => $attribute_description) {
											$attr_translation_data = [
												'attribute_id' => $attr_id,
												'language_id'   =>  $language_id,
												'name'  => $attribute_description
											];
											$this->Attribute_model->insert_translation($attr_translation_data);
										}

										$attr_value_general = [
											'attribute_id' => $attr_id,
											'filter'  => 0,
											'custom' => 1,
											'sort'  => 0
										];

										$attribute_value_id = $this->Attribute_model->insert_attribute_value($attr_value_general);
										if($attribute_value_id) {
											if($attribute['attribute_value_description']) {
												foreach($attribute['attribute_value_description'] as $lang_id => $attribute_description) {
													$attr_value_translation = [
														'attribute_value_id'  => $attribute_value_id,
														'language_id'  => $lang_id,
														'attribute_id'  => $attr_id,
														'name' => $attribute_description
													];
													$this->Attribute_model->insert_attribute_value_translation($attr_value_translation);
												}
											}

											// Insert attr to Product
											$product_attr_data[0] = [
												'product_id' => $id,
												'attribute_id' => $attr_id
											];

											$product_attr_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
											if($product_attr_id) {
												$product_attr_value_data[0] = [
													'product_attribute_id' => $product_attr_id,
													'product_id' => $id,
													'attribute_id' => $attr_id,
													'attribute_value_id' => $attribute_value_id
												];

												$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
											}
										}
									}
								}
							}
						}

						if($this->input->post('product_attribute')) {
							foreach($this->input->post('product_attribute') as $product_attribute) {
								if((int)$product_attribute['attribute_value_id'] > 0) {
									$product_attr_data[0] = [
										'product_id' => $id,
										'attribute_id' => $product_attribute['attribute_id']
									];

									$product_attribute_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
									if($product_attribute_id) {
										$product_attr_value_data[0] = [
											'product_attribute_id' => $product_attribute_id,
											'product_id' => $id,
											'attribute_id' => $product_attribute['attribute_id'],
											'attribute_value_id' => $product_attribute['attribute_value_id']
										];

										$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
									}

								} elseif($product_attribute['attribute_value_id'] == 0) {
									$attr_value_data[0] = [
										'attribute_id' => $product_attribute['attribute_id'],
										'filter' => 0,
										'custom' => 1,
										'sort'	=> 0,
										'created_by' => $this->data['user']->id
									];

									$attribute_value_id = $this->Product_model->insert_additional_data('attribute_value', $attr_value_data);
									if($attribute_value_id) {
										foreach($product_attribute['attribute_value'] as $language_id => $attr_value) {
											$attr_value_description_data[0] = [
												'attribute_value_id' => $attribute_value_id,
												'language_id'  => $language_id,
												'attribute_id' => $product_attribute['attribute_id'],
												'name'		   => $attr_value['name']
											];

											$this->Product_model->insert_additional_data('attribute_value_description', $attr_value_description_data);
										}

										// Insert Product attribute
										$product_attr_data[0] = [
											'product_id' => $id,
											'attribute_id' => $product_attribute['attribute_id']
										];

										$product_attribute_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
										if($product_attribute_id) {
											$product_attr_value_data[0] = [
												'product_attribute_id' => $product_attribute_id,
												'product_id' => $id,
												'attribute_id' => $product_attribute['attribute_id'],
												'attribute_value_id' => $attribute_value_id
											];

											$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
										}

									}
								}
							}
						}


						// Delete and Insert Product Country Group
						$this->Product_model->delete_additional_data('product_country_group',['product_id' => $id]);
						if($this->input->post('product_country_group')) {
							foreach($this->input->post('product_country_group') as $product_country_group) {
								$product_country_group_data_general[0] = ['product_id' => $id, 'country_group_id' => $product_country_group['country_group_id'], 'percent' => $product_country_group['percent']];
								$this->Product_model->insert_additional_data('product_country_group', $product_country_group_data_general);
							}
						}

						// Delete and Insert Product Options
						$product_options = $this->Product_model->get_additional_data('product_option','id',['product_id' => $id]);
						if($product_options) {
							foreach($product_options as $product_option) {
								$this->Product_model->delete_additional_data('product_option_value',['product_option_id' => $product_option->id]);
							}
						}
						$this->Product_model->delete_additional_data('product_option',['product_id' => $id]);
						if($this->input->post('product_option')) {
							foreach($this->input->post('product_option') as $product_option) {
								if(array_key_exists('product_option_value',$product_option) && $product_option['product_option_value']) {
									$product_option_data[0] = [
										'product_id' => $id,
										'option_id'	 => $product_option['option_id'],
										'value'		 => (isset($product_option['value'])) ? $product_option['value'] : "",
										'required'	 => $product_option['required'],
									];
									$product_option_id = $this->Product_model->insert_additional_data('product_option', $product_option_data);
									if($product_option_id) {
										foreach($product_option['product_option_value'] as $product_option_value) {
											$product_option_value_data[0] = [
												'product_option_id'	=> $product_option_id,
												'product_id'		=> $id,
												'option_id'			=> $product_option['option_id'],
												'option_value_id'	=> $product_option_value['option_value_id'],
												'quantity'			=> $product_option_value['quantity'],
												'subtract'			=> $product_option_value['subtract'],
												'price'				=> $product_option_value['price'],
												'price_prefix'		=> $product_option_value['price_prefix'],
												'points'			=> 0,
												'points_prefix'		=> '+',
												'weight'			=> $product_option_value['weight'],
												'weight_prefix'		=> $product_option_value['weight_prefix'],
												'country_group_id'	=> (isset($product_option_value['country_group_id'])) ? $product_option_value['country_group_id'] : 0
											];
											$this->Product_model->insert_additional_data('product_option_value', $product_option_value_data);
										}
									}
								}
							}
						}

						//Delete all relaton products
						$relation_products = $this->Product_model->get_additional_data('product_relation_value', 'DISTINCT(product_relation_id) as product_relation_id', ['relation_id' => $id]);
						if($relation_products) {
							foreach($relation_products as $relation_product) {
								$this->Product_model->delete_additional_data('product_relation',['id' => $relation_product->product_relation_id]);
								$this->Product_model->delete_additional_data('product_relation_value',['product_relation_id' => $relation_product->product_relation_id]);
							}
						}
						$this->Product_model->delete_additional_data('product_relation',['product_id' => $id]);
						$this->Product_model->delete_additional_data('product_relation_value',['product_id' => $id]);
						// Delete and Insert Product Relations
						if($this->input->post('product_relation')) {
							foreach($this->input->post('product_relation') as $product_relation) {
								if(array_key_exists('product_relation_value',$product_relation) && $product_relation['product_relation_value']) {
									// // Get all relation values
									// $relation_ids = $this->Product_model->get_additional_data('relation_value','id',['relation_id' => $product_relation['relation_id']]);
									// $ids = [];
									// if($relation_ids) {
									// 	foreach($relation_ids as $relation_id) {
									// 		$ids[] = $relation_id->id;
									// 	}
									// 	// Delete all product relation
									// 	$selected_product_relations = $this->Product_model->get_additional_data('product_relation_value','product_id',['relation_id' => $id, 'relation_value_id IN('.implode(',',$ids).')' => null]);
									// 	if($selected_product_relations) {
									// 		foreach($selected_product_relations as $selected_product_relation) {
									// 			$this->Product_model->delete_additional_data('product_relation',['product_id' => $selected_product_relation->product_id]);
									// 			$this->Product_model->delete_additional_data('product_relation_value',['product_id' => $selected_product_relation->product_id]);
									// 		}
									// 	}
									// 	$this->Product_model->delete_additional_data('product_relation',['product_id' => $id, 'relation_id' => $product_relation['relation_id']]);
									// 	$this->Product_model->delete_additional_data('product_relation_value',['product_id' => $id, 'relation_value_id IN('.implode(',',$ids).')' => null]);
									// 	// End Delete
									// }



									$relation_product_ids = [];
									foreach($product_relation['product_relation_value'] as $product_relation_value) {
										if((int)$product_relation_value['product_id'] == 0) {
											$product_relation_value['product_id'] = $id;
										}
										$relation_product_ids[(int)$product_relation_value['product_id']] = [
											'product_id' => (int) $product_relation_value['product_id'],
											'relation_value_id' => $product_relation_value['relation_value_id'],
										];
									}

									$product_ids = array_keys($relation_product_ids);
									for($i = 0; $i < count($product_ids); $i++) {
										$product_id = $product_ids[$i];
										$product_relation_data[0] = [
											'product_id' 	=> $product_id,
											'relation_id'	=> $product_relation['relation_id'],
											'value'		 	=> (isset($product_relation['value'])) ? $product_relation['value'] : "",
										];

										$product_relation_temp = $this->Product_model->get_additional_data('product_relation', 'id', ['product_id' => $product_id, 'relation_id' => $product_relation['relation_id']], true);
										if($product_relation_temp)
										{
											$product_relation_id = $product_relation_temp->id;
										}
										else
										{
											$product_relation_id = $this->Product_model->insert_additional_data('product_relation', $product_relation_data);
										}

										if($product_relation_id) {
											$product_relation_value_data[0] = [
												'product_relation_id'	=> $product_relation_id,
												'product_id'			=> $product_id,
												'relation_id'			=> 0,
												'relation_value_id'		=> $relation_product_ids[$product_id]['relation_value_id'],
												'current'				=> 1
											];

											$product_relation_value_temp = $this->Product_model->get_additional_data('product_relation_value', 'id', ['relation_value_id' =>  $relation_product_ids[$product_id]['relation_value_id'], 'product_id' => $product_id, 'relation_id' => 0], true);
											if(!$product_relation_value_temp)
											{
												$this->Product_model->insert_additional_data('product_relation_value', $product_relation_value_data);
											}

											foreach($relation_product_ids as $key => $relation_product_value) {
												if($key != $product_id) {
													$product_relation_value_data[0] = [
														'product_relation_id'	=> $product_relation_id,
														'product_id'			=> $product_id,
														'relation_id'			=> $key,
														'relation_value_id'		=> $relation_product_value['relation_value_id']
													];
													$product_relation_value_temp = $this->Product_model->get_additional_data('product_relation_value', 'id', ['relation_value_id' => $relation_product_value['relation_value_id'], 'product_id' => $product_id, 'relation_id' => $key], true);
													if(!$product_relation_value_temp)
													{
                                                        $this->Product_model->insert_additional_data('product_relation_value', $product_relation_value_data);
													}

												}
											}
										}

									}
								}
							}
						}

						if($product->quantity == 0 && $this->input->post('quantity') > 0)
						{
							$notify_subscribers = $this->Stock_notifier_model->filter(['product_id' => $product->id])->all();
							if($notify_subscribers)
							{
								foreach($notify_subscribers as $notify_subscriber)
								{
									$this->email->from(get_setting('mail_username'), get_setting('site_title', $this->data['current_lang']));
									$this->email->to($notify_subscriber->email);
									$this->email->subject('Mal var');
									$this->email->message('Bu produktdan artiq var');
									$this->email->send();
									//$this->email->send($notify_subscriber->email, 'MAIL Message');
									$this->Stock_notifier_model->update(['notified' => 1], ['id' => $notify_subscriber->id]);
								}
							}
						}


						// Delete and Insert Images
						$this->Product_model->delete_additional_data('product_images',['product_id' => $id]);

						// Insert images
						if($this->input->post('images')) {
							$images = [];
							foreach($this->input->post('images') as $key => $image) {
								if($key != $this->data['default_image']) {
									$images[] = ['product_id' => $id, 'image' => $image['url'], 'sort' => $image['sort'],'alt_image'=>$image['alt'][0]];
								}
							}
							if($images) {
								$this->Product_model->insert_additional_data('product_images',$images);
							}
						}
						
						$this->algolia->delete('products', $id);
						if($this->input->post('status')){
							$_product = $this->Product_model->get_products_static(['id' => $id])[0];
							$_product['objectID'] = $_product['id'];
							$this->algolia->save('products', [$_product]); 
						}
						
					}

					$this->session->set_flashdata('message', translate('form_success_edit'));
					redirect(site_url_multi($this->directory . $this->controller), 'refresh');
				} else {
					$this->data['message'] = translate('error_warning', true);
				}
			}

			$this->data['item_id'] = $id;

			if(isset($_GET['test'])) {

			    var_dump($this->data);

			    die;
            }


			$this->template->render($this->controller . '/form');

		} else {
			show_404();
		}
	}

	public function get_attributes_by_group_id($attribute_group_id)
	{

		$response = [];

		//Get Attribute Group Data
		$this->load->model('modules/Attribute_group_model');
		$attribute_group = $this->Attribute_group_model->filter(['id' => $attribute_group_id, 'status' => 1])->all();
		if($attribute_group)
		{
			$this->load->model('Attribute_model');
			$attributes = $this->Attribute_model->filter(['status' => 1, 'custom' => 0, 'FIND_IN_SET('.$attribute_group_id.', attribute_group_id)' => null])->with_translation()->order_by('sort', 'ASC')->all();

			if($attributes)
			{
				foreach($attributes as $attribute)
				{
					$response[] = [
						'attribute_id' => $attribute->id,
						'name' => $attribute->name,
						'custom_enable' => $attribute->custom,
						'attribute_value_id' => -1,
						'attribute_value' => []
					];
				}
			}
		}
		return $response;
	}

	public function copy($id)
	{
		$this->data['title'] = translate('edit_title');
		$this->data['subtitle'] = translate('edit_description');

		$product = $this->{$this->model}->filter(['id' => $id])->one();

		if($product) {
			$weight_class_options = $this->generate_options(['name'=>'weight_class', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
			$length_class_options = $this->generate_options(['name'=>'length_class', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
			$stock_options = $this->generate_options(['name'=>'stock_status', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[] ]);
			$country_options = $this->generate_options(['name'=>'country', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>true, 'where'=>[], 'sort'=>[['column' => 'name', 'order' =>'ASC']] ]);
			$tax_class_options = $this->generate_options(['name'=>'tax_class', 'key'=>'id', 'columns'=>'title', 'dynamic'=>false, 'translation'=>false, 'where'=>[], 'sort'=>[]], true);
			$currency_options = $this->generate_options(['name'=>'currency', 'key'=>'id', 'columns'=>'name', 'dynamic'=>true, 'translation'=>false, 'where'=>[], 'sort'=>[] ]);
			$currency_options = ($currency_options) ? $currency_options : [];

			$this->data['model_value'] = (set_value('model')) ? set_value('model') : $product->model;
			$this->form_validation->set_rules('model', translate('form_label_model'), "trim|required");

			$new_options = [];
			$new_options[''] = translate('select', true);
			$new_options[0] =  translate('form_label_new');
			$new_options[1] =  translate('form_label_used');
			$new_options[2] =  translate('form_label_refurbished');

			// General Form Fields
			$this->data['form_field']['general'] = [
				'sku' => [
					'property'  => 'text',
					'name'      => 'sku',
					'class'     => 'form-control',
					'value'     => (set_value('sku')) ? set_value('sku') : $product->sku,
					'label'     => translate('form_label_sku'),
					'info' 		=> translate('form_label_description_sku'),
					'placeholder' 	=> translate('form_label_sku'),
					'validation' 	=> []
				],
				'upc' => [
					'property'  => 'text',
					'name'      => 'upc',
					'class'     => 'form-control',
					'value'     => (set_value('upc')) ? set_value('upc') : $product->upc,
					'label'     => translate('form_label_upc'),
					'info' 		=> translate('form_label_description_upc'),
					'placeholder' 	=> translate('form_label_upc'),
					'validation' 	=> []
				],
				'ean' => [
					'property'  => 'text',
					'name'      => 'ean',
					'class'     => 'form-control',
					'value'     => (set_value('ean')) ? set_value('ean') : $product->ean,
					'label'     => translate('form_label_ean'),
					'info' 		=> translate('form_label_description_ean'),
					'placeholder' 	=> translate('form_label_ean'),
					'validation' 	=> []
				],
				'jan' => [
					'property'  => 'text',
					'name'      => 'jan',
					'class'     => 'form-control',
					'value'     => (set_value('jan')) ? set_value('jan') : $product->jan,
					'label'     => translate('form_label_jan'),
					'info' 		=> translate('form_label_description_jan'),
					'placeholder' 	=> translate('form_label_jan'),
					'validation'	=> []
				],
				'isbn' => [
					'property'  => 'text',
					'name'      => 'isbn',
					'class'     => 'form-control',
					'value'     => (set_value('isbn')) ? set_value('isbn') : $product->isbn,
					'label'     => translate('form_label_isbn'),
					'info' 		=> translate('form_label_description_isbn'),
					'placeholder' 	=> translate('form_label_isbn'),
					'validation' 	=> []
				],
				'mpn' => [
					'property'  => 'text',
					'name'      => 'mpn',
					'class'     => 'form-control',
					'value'     => (set_value('mpn')) ? set_value('mpn') : $product->mpn,
					'label'     => translate('form_label_mpn'),
					'info' 		=> translate('form_label_description_mpn'),
					'placeholder' 	=> translate('form_label_mpn'),
					'validation' 	=> []
				],
				'price' => [
					'property'  => 'text',
					'name'      => 'price',
					'class'     => 'form-control',
					'value'     => set_value('price'),
					'label'     => translate('form_label_price'),
					'placeholder'	=> translate('form_label_price'),
					'validation' 	=> ['rules' => 'required']
				],
				'currency' => [
					'property'  => 'dropdown',
					'name'      => 'currency',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_currency'),
					'options'	=> $currency_options,
					'selected'  => set_value('currency'),
					'validation' 	=> []
				],
				'tax_class_id' => [
					'property'  => 'dropdown',
					'name'      => 'tax_class_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_tax_class_id'),
					'options'	=> $tax_class_options,
					'selected'  => set_value('tax_class_id'),
					'validation' 	=> []
				],
				'quantity' => [
					'property'  => 'number',
					'min'		=> '0',
					'name'      => 'quantity',
					'class'     => 'form-control',
					'value'     => set_value('quantity'),
					'label'     => translate('form_label_quantity'),
					'placeholder' 	=> translate('form_label_quantity'),
					'validation' 	=> []
				],
				'day' => [
					'property'  => 'number',
					'min'		=> '0',
					'name'      => 'day',
					'class'     => 'form-control',
					'value'     => set_value('day'),
					'label'     => translate('form_label_day'),
					'placeholder' 	=> translate('form_label_day'),
					'validation' 	=> []
				],
				'subtract' => [
					'property'  => 'dropdown',
					'name'      => 'subtract',
					'class'     => 'form-control',
					'label'     => translate('form_label_subtract'),
					'options' 	=> [translate('no', true), translate('yes', true)],
					'selected'  => set_value('subtract'),
					'validation' 	=> []
				],
				'stock_status_id' => [
					'property'  => 'dropdown',
					'name'      => 'stock_status_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_stock_status_id'),
					'options' 	=> $stock_options,
					'selected'  => set_value('stock_status_id'),
					'validation' 	=> []
				],
				'country_id' => [
					'property'  => 'dropdown',
					'name'      => 'country_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_country_id'),
					'options' 	=> $country_options,
					'selected'  => set_value('country_id'),
					'validation' 	=> []
				],
				'region_id' => [
					'property'  => 'dropdown',
					'name'      => 'region_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_region_id'),
					'options' 	=> [],
					'selected'  => set_value('region_id'),
					'data-selected_id'  => set_value('region_id'),
					'validation' 	=> []
				],
				'new' => [
					'property'  => 'dropdown',
					'name'      => 'new',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_new'),
					'options'	=> $new_options,
					'selected'  => set_value('new'),
					'validation' => ['rules' => 'required|is_natural']
				],
				'date_available' => [
					'property'  => 'date',
					'name'      => 'date_available',
					'class'     => 'form-control',
					'value'     => set_value('date_available'),
					'label'     => translate('form_label_date_available'),
					'placeholder' 	=> translate('form_label_date_available'),
					'validation' 	=> []
				],
				'length' => [
					'property'  => 'text',
					'name'      => 'length',
					'class'     => 'form-control',
					'value'     => (set_value('length')) ? set_value('length') : $product->length,
					'label'     => translate('form_label_length'),
					'placeholder' 	=> translate('form_label_length'),
					'validation' 	=> []
				],
				'width' => [
					'property'  => 'text',
					'name'      => 'width',
					'class'     => 'form-control',
					'value'     => (set_value('width')) ? set_value('width') : $product->width,
					'label'     => translate('form_label_width'),
					'placeholder' 	=> translate('form_label_width'),
					'validation' 	=> []
				],
				'height' => [
					'property'  => 'text',
					'name'      => 'height',
					'class'     => 'form-control',
					'value'     => (set_value('height')) ? set_value('height') : $product->height,
					'label'     => translate('form_label_height'),
					'placeholder' 	=> translate('form_label_height'),
					'validation' 	=> []
				],
				'length_class_id' => [
					'property'  => 'dropdown',
					'name'      => 'length_class_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label'     => translate('form_label_length_class_id'),
					'options'	=> $length_class_options,
					'selected'  	=> (set_value('length_class_id')) ? set_value('length_class_id') : $product->length_class_id,
					'validation' 	=> []
				],
				'weight' => [
					'property'  => 'text',
					'name'      => 'weight',
					'class'     => 'form-control',
					'value'     => (set_value('weight')) ? set_value('weight') : $product->weight,
					'label'     => translate('form_label_weight'),
					'placeholder' 	=> translate('form_label_weight'),
					'validation' 	=> []
				],
				'weight_class_id' => [
					'property'  => 'dropdown',
					'name'      => 'weight_class_id',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'selected'     => (set_value('weight_class_id')) ? set_value('weight_class_id') : $product->weight_class_id,
					'label'     => translate('form_label_weight_class_id'),
					'options'		=> $weight_class_options,
					'validation' 	=> []
				]
			];

			if($this->auth->is_admin()) {
				$this->data['form_field']['general']['status'] = [
					'property' 	=> 'dropdown',
					'name' 		=> 'status',
					'class' 	=> 'bootstrap-select',
					'data-style'=> 'btn-default btn-xs',
					'data-width'=> '100%',
					'label' 	=> translate('form_label_status'),
					'options' 	=> [translate('disable', true), translate('enable', true), translate('pending', true)],
					'selected' 		=> set_value('status') ?  set_value('status') : $product->status,
					'validation' 	=> []
				];

				$this->data['form_field']['general']['created_at'] = [
					'property'  => 'date',
					'name'      => 'created_at',
					'class'     => 'form-control',
					'value'     => (set_value('created_at')) ? set_value('created_at') : $product->created_at,
					'label'     => translate('form_label_created_at'),
					'placeholder' 	=> translate('form_label_created_at'),
					'validation' 	=> []
				];
			}

			// Get Selected Categories
			$this->data['categories_data'] = $this->input->post('category_id');
			if(!$this->data['categories_data']) {
				$rows = $this->Product_model->get_additional_data('product_to_category','*',['product_id' => $id]);
				if($rows) {
					foreach ($rows as $selected_category) {
						$this->data['categories_data'][] = $selected_category->category_id;
					}
				}
			}

			// Get Related Products
			$related_products = "";
			if(!$this->input->post('related_products')) {
				$rows = $this->Product_model->get_additional_data('product_related','*',['product_id' => $id]);
				if($rows) {
					$temp_related_products = [];
					foreach ($rows as $related_product) {
						$temp_related_products[] = $related_product->related_id;
					}
					$related_products = implode(',',$temp_related_products);
				}
			}

			$this->load->model('modules/Category_model');
			$categories = $this->Category_model->filter(['status' => 1, 'parent' => 0])->with_translation()->order_by('name', 'ASC')->all();
			$this->data['categories'] = [];
			if($categories) {
				foreach($categories as $category) {
					$category_option = new stdclass();
					$category_option->id = $category->id;
					$category_option->name = $category->name;
					$category_option->attribute_group_id = $category->attribute_group_id;
					$category_option->has_child = $this->Category_model->filter(['status' => 1, 'parent' => $category->id])->count_rows();

					$this->data['categories'][] = $category_option;
				}
			}

			// Links Form Fields
			$this->data['form_field']['links'] = [
				'manufacturer_id'=> [
					'property'   => 'dropdown-ajax',
					'type'		 => 'general',
					'element'	 => 'manufacturer_id',
					'name'       => 'manufacturer_id',
					'id'         => 'manufacturer_id',
					'class'		 => 'form-control dropdownSingleAjax',
					'label'      => translate('form_label_manufacturer'),
					'placeholder'=> translate('form_label_manufacturer'),
					'selected'   => (set_value('manufacturer_id')) ? set_value('manufacturer_id') : $product->manufacturer_id,
					'selected_text' => ""
				],
				'related_products' => [
					'property'   => 'multiselect_ajax',
					'type'		 => 'general',
					'element'	 => 'related_products',
					'name'       => 'related_products',
					'id'         => 'related_products',
					'class'		 => 'form-control dropdownMultiAjax',
					'label'      => translate('form_label_related_products'),
					'placeholder'=> translate('form_label_related_products'),
					'selected'   => (set_value('related_products')) ? set_value('related_products') : $related_products,
					'selected_elements' => [],
					'selected_text' => ""
				]
			];
			if($this->data['form_field']['links']['manufacturer_id']['selected']){
				$this->data['form_field']['links']['manufacturer_id']['selected_text'] = $this->get_selected_element('manufacturer_id',$this->data['form_field']['links']['manufacturer_id']['selected']);
			}
			if($this->data['form_field']['links']['related_products']['selected']){
				$this->data['form_field']['links']['related_products']['selected_elements'] = $this->get_selected_elments('related_products',$this->data['form_field']['links']['related_products']['selected']);
			}

			$this->data['attribute_group_id'] = set_value('attribute_group_id');
			$attributes_custom_created = [];

			// Get Product Attributes
			$this->data['product_attributes'] = (set_value('product_attribute')) ? set_value('product_attribute') : [];
			if(!$this->input->post('product_attribute')) {
				$product_attributes = $this->Product_model->get_additional_data('product_attribute','*',['product_id' => $id]);

				if($product_attributes) {
					foreach($product_attributes as $product_attribute) {
						$temp_data = [];
						// Get product attribute values
						$product_attribute_value = $this->Product_model->get_additional_data('product_attribute_value', '*', ['product_attribute_id' => $product_attribute->id],true);

						if($product_attribute_value) {
							$attribute = $this->Attribute_model->filter(['id' => $product_attribute->attribute_id])->with_translation()->one();
							if($attribute) {
								if($attribute->custom == 0) {
									$temp_data = [
										'attribute_id' => $product_attribute->attribute_id,
										'name' => $attribute->name,
										'custom_enable' => $attribute->custom_enable,
										'attribute_value_id' => $product_attribute_value->attribute_value_id,
										'attribute_value' 	=> []
									];

									$temp_attr_value = $this->Product_model->get_additional_data('attribute_value', '*', ['id' => $product_attribute_value->attribute_value_id],true);
									if($temp_attr_value && $temp_attr_value->custom) {
										$temp_data['attribute_value_id'] = 0;
										$temp_attr_value_description = $this->Product_model->get_additional_data('attribute_value_description', '*', ['attribute_value_id' => $product_attribute_value->attribute_value_id]);
										if($temp_attr_value_description) {
											foreach($temp_attr_value_description as $description) {
												$temp_data['attribute_value'][$description->language_id] = ['name' => $description->name];
											}
										}
									}

									$this->data['product_attributes'][] = $temp_data;
								} else {
									$attributes_custom_created[] = $attribute->id;
								}
							}
						}
					}
				}

				// Get attribute group id
				if($this->data['categories_data']) {
					foreach($this->data['categories_data'] as $row) {
						$product_category = $this->Category_model->filter(['id' => $row])->one();
						if($product_category && $product_category->attribute_group_id != 0) {
							$this->data['attribute_group_id'] = $product_category->attribute_group_id;
						}
					}
				}
			}

			// $attribute_group_attributes  = $this->get_attributes_by_group_id($this->data['attribute_group_id']);
			// if($attribute_group_attributes) {
			// 	foreach($attribute_group_attributes as $row) {
			// 		if(!$this->data['product_attributes'] || !array_key_exists($row['attribute_id'], $this->data['product_attributes'])) {
			// 			$this->data['product_attributes'][] = $row;
			// 		}
			// 	}
			// }

			$attribute_values = [];
			if($this->data['product_attributes']) {
				foreach($this->data['product_attributes'] as $product_attribute) {
					if(!array_key_exists($product_attribute['attribute_id'],$attribute_values))
					{
						$all_attribute_values = $this->Product_model->get_additional_data('attribute_value','*', ['attribute_id' => $product_attribute['attribute_id'], 'custom' => 0]);
						if($all_attribute_values) {
							foreach($all_attribute_values as $attribute_value) {
								$value_description = $this->Product_model->get_additional_data('attribute_value_description','*', ['attribute_value_id' => $attribute_value->id],true);

									$attribute_values[$product_attribute['attribute_id']][] = [
										'product_attribute_id'	=> $product_attribute['attribute_id'],
										'attribute_id'	=> $product_attribute['attribute_id'],
										'attribute_value_id' => $attribute_value->id,
										'name' => ($value_description) ? $value_description->name : ""
									];

							}
						}
					}
				}
			}
			$this->data['attribute_values'] = $attribute_values;

			$this->data['attributes'] = $this->input->post('attribute');
			if(!$this->data['attributes'] && $attributes_custom_created) {
				foreach($attributes_custom_created as $attr_id) {
					$custom_attr_data = [];
					$attr_translation = $this->Product_model->get_additional_data('attribute_translation', '*', ['attribute_id' => $attr_id]);
					if($attr_translation) {
						foreach($attr_translation as $translation) {
							$custom_attr_data['attribute_description'][$translation->language_id] = $translation->name;
						}
						$attr_value_description = $this->Product_model->get_additional_data('attribute_value_description', '*', ['attribute_id' => $attr_id]);
						if($attr_value_description) {
							foreach($attr_value_description as $description) {
								$custom_attr_data['attribute_value_description'][$description->language_id] = $description->name;
							}
						}

						$this->data['attributes'][] = $custom_attr_data;
					}

				}
			}

			$this->data['product_country_groups'] = set_value('product_country_group');
			$product_country_groups = [];
			if(!$this->input->post('product_country_group')){
				$rows = $this->Product_model->get_additional_data('product_country_group','*',['product_id' => $id]);
				if($rows) {
					foreach($rows as $product_country_group)
					{
						$product_country_groups[] = [
							'percent'			=>  $product_country_group->percent,
							'country_group_id' => $product_country_group->country_group_id
						];
					}

					$this->data['product_country_groups'] = $product_country_groups;
				}
			}

			// Discount tab data
			$this->data['customer_groups'] = $this->Customer_group_model->fields("id, name")->with_translation()->as_array()->all();
			$this->data['product_discounts'] = set_value('product_discount');
			/*if(!$this->input->post('product_discount')) {
				$product_discounts = $this->Product_model->get_additional_data('product_discount','*',['product_id' => $id]);
				if($product_discounts) {
					foreach ($product_discounts as $product_discount) {
						$this->data['product_discounts'][] = [
							'customer_group_id' => $product_discount->customer_group_id,
							'quantity' => $product_discount->quantity,
							'priority' => $product_discount->priority,
							'price' => $product_discount->price,
							'date_start' => $product_discount->date_start,
							'date_end' => $product_discount->date_end
						];
					}
				}
			}*/

			// Option tab data
			$option_values = [];
			$this->data['product_options'] = (set_value('product_option')) ? set_value('product_option') : [];
			// if(!$this->input->post('product_option')) {
			// 	$product_options = $this->Product_model->get_additional_data('product_option','*',['product_id' => $id]);
			// 	if($product_options) {
			// 		foreach($product_options as $product_option) {
			// 			$temp_data = [];
			// 			// Get product option values
			// 			$product_option_values = $this->Product_model->get_additional_data('product_option_value','*,id as product_option_value_id',['product_option_id' => $product_option->id],false,true);
			// 			if($product_option_values) {
			// 				$option = $this->Option_model->filter(['id' => $product_option->option_id])->with_translation()->one();
			// 				if($option) {
			// 					$temp_data = [
			// 						'product_option_id' => $product_option->id,
			// 						'name' => $option->name,
			// 						'option_id' => $product_option->option_id,
			// 						'type' => $option->type,
			// 						'required' => $product_option->required,
			// 						'product_option_value' => $product_option_values,
			// 					];
			// 					$this->data['product_options'][] = $temp_data;
			// 				}

			// 			}
			// 		}
			// 	}
			// }
			if($this->data['product_options']) {
				foreach($this->data['product_options'] as $product_option) {
					if(!array_key_exists($product_option['option_id'],$option_values))
					{
						$all_option_values = $this->Option_model->get_additional_data('option_value','id,image,sort', ['option_id' => $product_option['option_id']]);
						if($all_option_values) {
							foreach($all_option_values as $option_value) {
								$value_description = $this->Option_model->get_additional_data('option_value_description','*', ['option_value_id' => $option_value->id],true);

									$option_values[$product_option['option_id']][] = [
										'product_option_id'	=> $product_option['option_id'],
										'option_id'	=> $product_option['option_id'],
										'option_value_id' => $option_value->id,
										'image' => $option_value->image,
										'name' => ($value_description) ? $value_description->name : ""
									];

							}
						}
					}
				}
			}
			$this->data['option_values'] = $option_values;

			// Relation tab data
			$relation_values = [];
			$this->data['product_relations'] = (set_value('product_relation')) ? set_value('product_relation') : [];

			if(!$this->input->post('product_relation')) {
				$product_relations = $this->Product_model->get_additional_data('product_relation','*',['product_id' => $id]);
				if($product_relations) {
					foreach($product_relations as $product_relation) {
						$temp_data = [];
						// Get product relation values
						$product_relation_values = $this->Product_model->get_additional_data('product_relation_value','*,id as product_relation_value_id',['product_relation_id' => $product_relation->id],false,true);
						if($product_relation_values) {
							$relation = $this->Relation_model->filter(['id' => $product_relation->relation_id])->with_translation()->one();
							if($relation) {
								$temp_data = [
									'product_relation_id' => $product_relation->id,
									'name' => $relation->name,
									'relation_id' => $product_relation->relation_id,
									'product_relation_value' => [],
								];

								foreach($product_relation_values as $key => $prv) {

									if($prv['relation_id'] != 0) {
										$relation_product = $this->Product_model->fields('name')->filter(['id' => $prv['relation_id'], 'status' => 1])->with_translation()->one();
										if($relation_product) {
											$temp_data['product_relation_value'][] = [
												'relation_value_id' => $prv['relation_value_id'],
												'product_relation_value_id' => $prv['product_relation_value_id'],
												'product_name' => $relation_product->name,
												'product_id' => $prv['relation_id'],
											];
										}
									} else {
										$temp_data['product_relation_value'][] = [
											'relation_value_id' => $prv['relation_value_id'],
											'product_relation_value_id' => $prv['product_relation_value_id'],
											'product_name' => "",
											'product_id' => "",
										];

										if($prv['current'] == 1) {
											$temp_data['custom_current_product'] = $prv['relation_value_id'];
										}
									}
								}
								$this->data['product_relations'][] = $temp_data;
							}

						}
					}
				}
			}

			if($this->data['product_relations']) {
				foreach($this->data['product_relations'] as $product_relation) {
					if(!array_key_exists($product_relation['relation_id'],$relation_values))
					{
						$all_relation_values = $this->Relation_model->get_additional_data('relation_value','id,sort', ['relation_id' => $product_relation['relation_id']]);
						if($all_relation_values) {
							foreach($all_relation_values as $relation_value) {
								$value_description = $this->Relation_model->get_additional_data('relation_value_description','*', ['relation_value_id' => $relation_value->id],true);

									$relation_values[$product_relation['relation_id']][] = [
										'product_relation_id'	=> $product_relation['relation_id'],
										'relation_id'	=> $product_relation['relation_id'],
										'relation_value_id' => $relation_value->id,
										'name' => ($value_description) ? $value_description->name : ""
									];

							}
						}
					}
				}
			}
			$this->data['relation_values'] = $relation_values;


			// Set form validation rules
			foreach ($this->data['form_field']['general'] as $key => $value) {
				if($value['validation']) {
					$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
				}
			}

			// Translation Form Fields
			foreach ($this->data['languages'] as $language) {
				$row_translation = $this->{$this->model}->fields('*')->filter(['product_id' => $id])->with_translation($language['id'])->one();

				$this->data['form_field']['translation'][$language['id']]['name'] = [
					'property'    	=> "text",
					'name'        	=> 'translation[' . $language['id'] . '][name]',
					'class'       	=> 'form-control',
					'value'       	=> (set_value('translation[' . $language['id'] . '][name]')) ? set_value('translation[' . $language['id'] . '][name]') : $row_translation->name,
					'label'       	=> translate("form_label_name"),
					'placeholder' 	=> translate("form_label_name"),
					'validation'    => ['rules' => 'required']
				];

				$this->data['form_field']['translation'][$language['id']]['slug'] = [
					'property'    	=> "slug",
					'name'        	=> 'translation[' . $language['id'] . '][slug]',
					'data-for'		=> 'name',
					'data-type'		=> 'translation',
					'data-lang-id'	=> $language['id'],
					'class'       	=> 'form-control slugField',
					'value'       	=> (set_value('translation[' . $language['id'] . '][slug]')) ? set_value('translation[' . $language['id'] . '][slug]')  : $row_translation->slug,
					'label'       	=> translate("form_label_slug"),
					'placeholder' 	=> translate("form_label_slug"),
					'validation'    => ['rules' => 'required']
				];

				$this->data['form_field']['translation'][$language['id']]['description'] = [
					'property'    	=> "textarea",
					'name'   		=> 'translation[' . $language['id'] . '][description]',
					'class'       	=> 'form-control ckeditor',
					'value'       	=> (set_value('translation[' . $language['id'] . '][description]')) ? set_value('translation[' . $language['id'] . '][description]')  : $row_translation->description,
					'label'       	=> translate("form_label_description"),
					'placeholder' 	=> translate("form_label_description"),
					'validation'    => []
				];

				$this->data['form_field']['translation'][$language['id']]['seller_note'] = [
					'property'    	=> "textarea",
					'name'   		=> 'translation[' . $language['id'] . '][seller_note]',
					'class'       	=> 'form-control',
					'value'       	=> (set_value('translation[' . $language['id'] . '][seller_note]')) ? set_value('translation[' . $language['id'] . '][seller_note]')  : $row_translation->seller_note,
					'label'       	=> translate("form_label_seller_note"),
					'placeholder' 	=> translate("form_label_seller_note"),
					'validation'    => []
				];
				if($this->auth->is_admin()) {
					$this->data['form_field']['translation'][$language['id']]['meta_title'] = [
						'property'    	=> "text",
						'name' 			=> 'translation[' . $language['id'] . '][meta_title]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][meta_title]')) ? set_value('translation[' . $language['id'] . '][meta_title]')  : $row_translation->meta_title,
						'label'       	=> translate("form_label_meta_title"),
						'placeholder' 	=> translate("form_label_meta_title"),
						'validation'    => []
					];

					$this->data['form_field']['translation'][$language['id']]['meta_description'] = [
						'property'    	=> "textarea",
						'name'   		=> 'translation[' . $language['id'] . '][meta_description]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][meta_description]')) ? set_value('translation[' . $language['id'] . '][meta_description]')  : $row_translation->meta_description,
						'label'       	=> translate("form_label_meta_description"),
						'placeholder' 	=> translate("form_label_meta_description"),
						'validation'    => []
					];

					$this->data['form_field']['translation'][$language['id']]['meta_keyword'] = [
						'property'    	=> "textarea",
						'name'   		=> 'translation[' . $language['id'] . '][meta_keyword]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][meta_keyword]')) ? set_value('translation[' . $language['id'] . '][meta_keyword]')  : $row_translation->meta_keyword,
						'label'       	=> translate("form_label_meta_keyword"),
						'placeholder' 	=> translate("form_label_meta_keyword"),
						'validation'    => []
					];

					$this->data['form_field']['translation'][$language['id']]['tag'] = [
						'property'    	=> "textarea",
						'name' 			=> 'translation[' . $language['id'] . '][tag]',
						'class'       	=> 'form-control',
						'value'       	=> (set_value('translation[' . $language['id'] . '][tag]')) ? set_value('translation[' . $language['id'] . '][tag]')  : ((!empty($row_translation->tag)) ? str_replace(',',' ',$row_translation->tag) : ""),
						'label'       	=> translate("form_label_tag"),
						'info'       	=> translate("form_label_description_comma_separated"),
						'placeholder' 	=> translate("form_label_tag"),
						'validation'    => []
					];
				}

			}

			foreach ($this->data['languages'] as $language) {
				foreach ($this->data['form_field']['translation'][$language['id']] as $key => $value)
				{
					if($value['validation']){
						$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
					}
				}
			}

			$this->data['buttons'][] = [
				'type'       => 'button',
				'text'       => translate('form_button_save', true),
				'class'      => 'btn btn-primary btn-labeled heading-btn',
				'id'         => 'save',
				'icon'       => 'icon-floppy-disk',
				'additional' => [
					'onclick'    => "if(confirm('".translate('are_you_sure', true)."')){ $('#form-save').submit(); return false;}else{ return false; }",
					'form'       => 'form-save',
					'formaction' => current_url()
				]
			];

			// Product Images
			$this->data['images'] = [];
			$this->data['default_image'] = set_value('default_image');
			if($this->input->post('images')) {
				foreach($this->input->post('images') as $image){
					$new = new stdClass();
					$new->name = basename($image['url']);
					$new->sort = $image['sort'];
					$new->path = $image['url'];
					$new->preview = $this->Model_tool_image->resize($image['url'], 250, 250);

					$this->data['images'][] = $new;
				}
			}

			if ($this->input->method() == 'post') {

				if($this->form_validation->run() == true) {
					$general = [];
					foreach ($this->data['form_field']['general'] as $key => $value) {
						$general[$key] = $this->input->post($value['name']);
					}
					$general['model'] = $this->input->post('model');

					if(!$this->auth->is_admin()) {
						$general['status'] = 2;
					}
					$general['copied_product_id'] = $id;
					$general['manufacturer_id'] = $this->input->post('manufacturer_id');

					if($this->input->post('images')) {
						if($this->input->post('default_image') != null) {
							$image = $this->input->post('images')[$this->input->post('default_image')]['url'];
							if(!empty($image)) {
								$general['image'] = $image;
							}
						} else {
							foreach($this->input->post('images') as $key => $image) {
								$image_url = $image['url'];
								if(!empty($image_url)) {
									$general['image'] = $image_url;
									$this->data['default_image'] = $key;
								}
								break;
							}

						}
					}

					$product_id = $this->{$this->model}->insert($general);

					if($product_id) {
						// Insert translation
						foreach ($this->input->post('translation') as $language_id => $translation) {
							$translation_data = [
								'product_id' 	=> $product_id,
								'language_id'   => $language_id,
								'name'  		=> $translation['name'],
								'slug'  		=> $translation['slug'],
								'description'  	=> $translation['description'],
								'seller_note'	=> $translation['seller_note']

							];
							if($this->auth->is_admin()) {
								$translation_data['tag']  			= $translation['tag'];
								$translation_data['meta_title']  	= $translation['meta_title'];
								$translation_data['meta_description']  = $translation['meta_description'];
								$translation_data['meta_keyword']  	= $translation['meta_keyword'];
								$translation_data['tag']  				= (!empty($translation['tag'])) ?  str_replace(' ',',',$translation['tag']) : null;
							}
							$this->{$this->model}->insert_translation($translation_data);
						}

						// Insert images
						if($this->input->post('images')) {
							$images = [];
							foreach($this->input->post('images') as $key => $image) {
								if($key != $this->data['default_image']) {
									$images[] = ['product_id' => $id, 'image' => $image['url'], 'sort' => $image['sort']];
								}
							}
							if($images) {
								$this->Product_model->insert_additional_data('product_images',$images);
							}
						}

						// Insert Categories
						if($this->input->post('category_id') && !empty($this->input->post('category_id'))) {
							foreach ($this->input->post('category_id') as $category_id) {
								$category_data[0] = ['product_id' => $product_id, 'category_id' => $category_id];
								$this->Product_model->insert_additional_data('product_to_category', $category_data);
							}

						}

						// Insert Related Products
						// if($this->input->post('related_products') && !empty($this->input->post('related_products'))) {
						// 	$related_products = explode(',',$this->input->post('related_products'));
						// 	$related_products_data = [];
						// 	foreach ($related_products as $related_id) {
						// 		$related_products_data[] = ['product_id' => $product_id, 'related_id' => $related_id];
						// 	}

						// 	$this->Product_model->insert_additional_data('product_related', $related_products_data);
						// }

						// Insert Product Special
						// if($this->input->post('product_special')) {
						// 	$product_special_data = [];
						// 	foreach($this->input->post('product_special') as $product_special){
						// 		$product_special_data[] = [
						// 			'product_id' => $product_id,
						// 			'customer_group_id' => $product_special['customer_group_id'],
						// 			'priority' => $product_special['priority'],
						// 			'price' => $product_special['price'],
						// 			'date_start' => $product_special['date_start'],
						// 			'date_end' => $product_special['date_end']
						// 		];
						// 	}
						// 	$this->Product_model->insert_additional_data('product_special', $product_special_data);
						// }

						// Insert Product Attribute
						if($this->input->post('product_attribute')) {
							foreach($this->input->post('product_attribute') as $product_attribute) {
								if((int)$product_attribute['attribute_value_id'] > 0) {
									$product_attr_data[0] = [
										'product_id' => $product_id,
										'attribute_id' => $product_attribute['attribute_id']
									];

									$product_attribute_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
									if($product_attribute_id) {
										$product_attr_value_data[0] = [
											'product_attribute_id' => $product_attribute_id,
											'product_id' => $product_id,
											'attribute_id' => $product_attribute['attribute_id'],
											'attribute_value_id' => $product_attribute['attribute_value_id']
										];

										$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
									}

								} elseif($product_attribute['attribute_value_id'] == 0) {
									$attr_value_data[0] = [
										'attribute_id' => $product_attribute['attribute_id'],
										'filter' => 0,
										'custom' => 1,
										'sort'	=> 0,
										'created_by' => $this->data['user']->id
									];

									$attribute_value_id = $this->Product_model->insert_additional_data('attribute_value', $attr_value_data);
									if($attribute_value_id) {
										foreach($product_attribute['attribute_value'] as $language_id => $attr_value) {
											$attr_value_description_data[0] = [
												'attribute_value_id' => $attribute_value_id,
												'language_id'  => $language_id,
												'attribute_id' => $product_attribute['attribute_id'],
												'name'		   => $attr_value['name']
											];

											$this->Product_model->insert_additional_data('attribute_value_description', $attr_value_description_data);
										}

										// Insert Product attribute
										$product_attr_data[0] = [
											'product_id' => $product_id,
											'attribute_id' => $product_attribute['attribute_id']
										];

										$product_attribute_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
										if($product_attribute_id) {
											$product_attr_value_data[0] = [
												'product_attribute_id' => $product_attribute_id,
												'product_id' => $product_id,
												'attribute_id' => $product_attribute['attribute_id'],
												'attribute_value_id' => $attribute_value_id
											];

											$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
										}

									}
								}
							}
						}

						// Insert Custom Attributes
						if($this->input->post('attribute')) {
							foreach($this->input->post('attribute') as $attribute) {
								if($this->valid_custom_attribute($attribute)) {
									$attr_general = [
										'attribute_group_id' => $this->input->post('attribute_group_id'),
										'filter' => 0,
										'custom' => 1,
										'custom_enable' => 0,
										'status' => 1
									];
									$attr_id = $this->Attribute_model->insert($attr_general);

									if($attr_id) {
										foreach($attribute['attribute_description'] as $language_id => $attribute_description) {
											$attr_translation_data = [
												'attribute_id' => $attr_id,
												'language_id'   =>  $language_id,
												'name'  => $attribute_description
											];
											$this->Attribute_model->insert_translation($attr_translation_data);
										}

										$attr_value_general = [
											'attribute_id' => $attr_id,
											'filter'  => 0,
											'custom' => 1,
											'sort'  => 0
										];

										$attribute_value_id = $this->Attribute_model->insert_attribute_value($attr_value_general);
										if($attribute_value_id) {
											if($attribute['attribute_value_description']) {
												foreach($attribute['attribute_value_description'] as $lang_id => $attribute_description) {
													$attr_value_translation = [
														'attribute_value_id'  => $attribute_value_id,
														'language_id'  => $lang_id,
														'attribute_id'  => $attr_id,
														'name' => $attribute_description
													];
													$this->Attribute_model->insert_attribute_value_translation($attr_value_translation);
												}
											}

											// Insert attr to Product
											$product_attr_data[0] = [
												'product_id' => $product_id,
												'attribute_id' => $attr_id
											];

											$product_attr_id = $this->Product_model->insert_additional_data('product_attribute', $product_attr_data);
											if($product_attr_id) {
												$product_attr_value_data[0] = [
													'product_attribute_id' => $product_attr_id,
													'product_id' => $product_id,
													'attribute_id' => $attr_id,
													'attribute_value_id' => $attribute_value_id
												];

												$this->Product_model->insert_additional_data('product_attribute_value', $product_attr_value_data);
											}
										}

									}
								}
							}
						}

						// Insert Product Country Group
						if($this->input->post('product_country_group')) {
							foreach($this->input->post('product_country_group') as $product_country_group) {
								$product_country_group_data_general[0] = ['product_id' => $product_id, 'country_group_id' => $product_country_group['country_group_id'], 'percent' => $product_country_group['percent']];
								$product_country_group_id = $this->Product_model->insert_additional_data('product_country_group', $product_country_group_data_general);
							}
						}

						// Insert Product Options
						if($this->input->post('product_option')) {
							foreach($this->input->post('product_option') as $product_option) {
								if(array_key_exists('product_option_value',$product_option) && $product_option['product_option_value']) {
									$product_option_data[0] = [
										'product_id' => $product_id,
										'option_id'	 => $product_option['option_id'],
										'value'		 => (isset($product_option['value'])) ? $product_option['value'] : "",
										'required'	 => $product_option['required'],
									];
									$product_option_id = $this->Product_model->insert_additional_data('product_option', $product_option_data);
									if($product_option_id) {
										foreach($product_option['product_option_value'] as $product_option_value) {
											$product_option_value_data[0] = [
												'product_option_id'	=> $product_option_id,
												'product_id'		=> $product_id,
												'option_id'			=> $product_option['option_id'],
												'option_value_id'	=> $product_option_value['option_value_id'],
												'quantity'			=> $product_option_value['quantity'],
												'subtract'			=> $product_option_value['subtract'],
												'price'				=> $product_option_value['price'],
												'price_prefix'		=> $product_option_value['price_prefix'],
												'points'			=> 0,
												'points_prefix'		=> '+',
												'weight'			=> $product_option_value['weight'],
												'weight_prefix'		=> $product_option_value['weight_prefix'],
												'country_group_id'	=> isset($product_option_value['country_group_id']) ? $product_option_value['country_group_id'] : 0
											];
											$this->Product_model->insert_additional_data('product_option_value', $product_option_value_data);
										}
									}
								}
							}
						}

						// Insert Product Relations
						// if($this->input->post('product_relation')) {
						// 	foreach($this->input->post('product_relation') as $product_relation) {
						// 		if(array_key_exists('product_relation_value',$product_relation) && $product_relation['product_relation_value']) {
						// 			$relation_product_ids = [];
						// 			foreach($product_relation['product_relation_value'] as $product_relation_value) {
						// 				if((int)$product_relation_value['product_id'] == 0) {
						// 					$product_relation_value['product_id'] = $product_id;
						// 				}
						// 				$relation_product_ids[(int)$product_relation_value['product_id']] = [
						// 					'product_id' => (int) $product_relation_value['product_id'],
						// 					'relation_value_id' => $product_relation_value['relation_value_id'],
						// 				];
						// 			}

						// 			$product_ids = array_keys($relation_product_ids);
						// 			for($i = 0; $i < count($product_ids); $i++) {
						// 				$product_id = $product_ids[$i];

						// 				$product_relation_data[0] = [
						// 					'product_id' 	=> $product_id,
						// 					'relation_id'	=> $product_relation['relation_id'],
						// 					'value'		 	=> (isset($product_relation['value'])) ? $product_relation['value'] : "",
						// 				];
						// 				$product_relation_id = $this->Product_model->insert_additional_data('product_relation', $product_relation_data);
						// 				if($product_relation_id) {
						// 					$product_relation_value_data[0] = [
						// 						'product_relation_id'	=> $product_relation_id,
						// 						'product_id'			=> $product_id,
						// 						'relation_id'			=> 0,
						// 						'relation_value_id'		=> $relation_product_ids[$product_id]['relation_value_id'],
						// 						'current'				=> 1
						// 					];
						// 					$this->Product_model->insert_additional_data('product_relation_value', $product_relation_value_data);

						// 					foreach($relation_product_ids as $key => $relation_product_value) {
						// 						if($key != $product_id) {
						// 							$product_relation_value_data[0] = [
						// 								'product_relation_id'	=> $product_relation_id,
						// 								'product_id'			=> $product_id,
						// 								'relation_id'			=> $key,
						// 								'relation_value_id'		=> $relation_product_value['relation_value_id']
						// 							];
						// 							$this->Product_model->insert_additional_data('product_relation_value', $product_relation_value_data);
						// 						}
						// 					}
						// 				}

						// 			}
						// 		}
						// 	}
						// }

					}

					$this->session->set_flashdata('message', translate('form_success_create'));
					redirect(site_url_multi($this->directory . $this->controller), 'refresh');
				} else {
					$this->data['message'] = translate('error_warning', true);
				}
			}

			$this->data['item_id'] = 0;


            $this->data['product_options'] = [];
            $this->data['product_relations'] = [];

			$this->template->render($this->controller . '/form');

		} else {
			show_404();
		}
	}

	public function delete($id = false)
	{
		if ($id) {
			$this->{$this->model}->delete($id);
			$this->template->json(['success' => 1]);
			$this->algolia->delete('products', $id);
		} else {
			if ($this->input->method() == 'post') {
				$response  = ['success' => false, 'message' => translate('couldnt_delete_message',true)];
				if ($this->input->post('selected')) {
					foreach ($this->input->post('selected') as $id) {
						$this->{$this->model}->delete($id);
					}
					$response = ['success' => true, 'message' => translate('successfully_delete_message',true)];
				}
				$this->template->json($response);
			}
		}
	}

	public function remove($id = false)
	{
		if ($id) {
			$this->{$this->model}->force_delete_option($id);
			$this->template->json(['success' => 1]);
		} else {
			if ($this->input->method() == 'post') {
				$response  = ['success' => false, 'message' => translate('couldnt_remove_message',true)];
				if ($this->input->post('selected')) {
					foreach ($this->input->post('selected') as $id) {
						$this->{$this->model}->force_delete_product_relation_data($id);
					}
					$response = ['success' => true, 'message' => translate('successfully_remove_message',true)];
				}
				$this->template->json($response);
			}
		}
	}

	public function restore($id = false)
	{
		if ($id) {
			$this->{$this->model}->restore($id);
			$this->template->json(['success' => 1]);
		} else {
			if ($this->input->method() == 'post') {
				$response  = ['success' => false, 'message' => translate('couldnt_restore_message',true)];
				if ($this->input->post('selected')) {
					foreach ($this->input->post('selected') as $id) {
						$this->{$this->model}->restore($id);
					}
					$response = ['success' => true, 'message' => translate('successfully_restore_message',true)];
				}
				$this->template->json($response);
			}
		}
	}

	public function clean()
	{
		$this->{$this->model}->force_delete(['deleted_at !=' => null]);
		redirect(site_url_multi($this->directory . $this->module_name));
	}

	public function changeStatus()
	{
		if ($this->input->method() == 'post') {
			$id = $this->input->post('id');
			if ($id){
				$row = $this->{$this->model}->filter(['id' => $id])->fields('status')->one();

                $product = $this->Product_model->get_products_static(['id' => $id])[0];

                $product['objectID'] = $product['id'];

				$status = ($row->status) ? 0 : 1;

				if($status) {

				    var_dump($this->algolia->save('products', [$product]));

                }
				else {

                    $this->algolia->delete('products', $id);

                }


				$this->{$this->model}->update(['status' => $status], ['id' => $id]);
				$this->template->json(['success' => 1]);
			}
		}
	}

	public function checkSlugExist($lang_id, $slug, $index = 0, $item_id)
	{
		if($lang_id)
		{
			$where = ['slug' => $slug];
			if($item_id){
				$where[$this->module_name.'_id != '.$item_id] = null;
			}
			$translation = $this->{$this->model}->filter($where)->with_trashed()->with_translation($lang_id)->one();
			if($translation){
				$index++;
				$slug = $slug.'-'.$index;
				return  $this->checkSlugExist($lang_id, $slug, $index, $item_id);
			} else {
				return $slug;
			}
		}
		else
		{
			$where = ['slug' => $slug];
			if($item_id){
				$where['id != '.$item_id] = null;
			}
			$general = $this->{$this->model}->filter($where)->with_trashed()->one();
			if($general)
			{
				$index++;
				$slug = $slug.'-'.$index;
				return  $this->checkSlugExist(false, $slug, $index, $item_id);
			}
			else
			{
				return $slug;
			}
		}

	}

	public function slugGenerator()
	{
		$response  = ['success' => false];
		if ($this->input->method() == 'post') {
			$lang_id = $this->input->post('lang_id');
			$text	 = $this->input->post('text');
			$item_id = $this->input->post('item_id');

			if(!empty($text)){
				$slug = slug(strtolower($text));
				$slug = $this->checkSlugExist($lang_id, $slug, 0, $item_id);
				$response = ['success' => true, 'slug' => $slug];
			}

		}

		$this->template->json($response);
	}

	public function generate_options($module, $please_select = false)
	{
		$model_name = ucfirst($module['name']).'_model';
		$this->load->model(($module['dynamic']) ? 'modules/'.$model_name : $model_name);
		$columns = explode(',',$module['columns']);
		$select = "";
		if(count($columns) > 1){
			$select = "CONCAT(";
			for ($i=0; $i <= count($columns) - 1; $i++) {
				if($i == count($columns)-1) {
					$select .= ",".$columns[$i];
				} else {
					$select .= $columns[$i].",' '";
				}
			}
			$select .= ")";
		} elseif(count($columns) == 1){
			$select = $columns[0];
		}

		$where = [];
		if($module['where']) {
			foreach($module->where as $module_where) {
				$where[$module_where['key']] =  $module_where['value'];
			}
		}

		$order = ["Created_at" => "DESC"];
		if($module['sort']) {
			foreach($module['sort'] as $module_sort){
				$order = [$module_sort['column'] => $module_sort['order']];
			}
		}

		if($module['translation']){
			$rows = $this->{$model_name}->fields("id,".$select." as value")->order_by($order)->filter($where)->with_translation($this->data['current_lang_id'])->all();
		} else {
			$rows = $this->{$model_name}->fields("id,".$select." as value")->order_by($order)->filter($where)->all();
		}

		$result = [];
		if($please_select) {
			$result[0] = translate('select',true);
		}
		if($rows) {
			foreach($rows as $row) {
				$result[$row->id] = $row->value;
			}
		}

		return $result;
	}

	public function get_selected_element($module, $selected_element)
	{

		if($selected_element) {
			$module = $this->relation_modules[$module];
			$model_name = ucfirst($module['name']).'_model';
			$this->load->model(($module['dynamic']) ? 'modules/'.$model_name : $model_name);
			$columns = explode(',',$module['columns']);
			$select = "";
			if(count($columns) > 1){
				$select = "CONCAT(";
				for ($i=0; $i <= count($columns) - 1; $i++) {
					if($i == count($columns)-1) {
						$select .= ",".$columns[$i];
					} else {
						$select .= $columns[$i].",' '";
					}
				}
				$select .= ")";
			} elseif(count($columns) == 1){
				$select = $columns[0];
			}

			$where = [$module['key'] => $selected_element];
			if($module['translation']){
				$row = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->with_translation($this->data['current_lang_id'])->one();
			} else {
				$row = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->one();
			}

			if($row) {
				return $row->value;
			}
		}


		return "";
	}

	public function get_selected_elments($module, $selected_elements)
	{
		if($selected_elements) {
			$module = $this->relation_modules[$module];
			$model_name = ucfirst($module['name']).'_model';
			$this->load->model(($module['dynamic']) ? 'modules/'.$model_name : $model_name);

			$columns = explode(',',$module['columns']);
			$select = "";
			if(count($columns) > 1){
				$select = "CONCAT(";
				for ($i=0; $i <= count($columns) - 1; $i++) {
					if($i == count($columns)-1) {
						$select .= ",".$columns[$i];
					} else {
						$select .= $columns[$i].",' '";
					}
				}
				$select .= ")";
			} elseif(count($columns) == 1){
				$select = $columns[0];
			}

			$where = [$module['key'].' IN ('.$selected_elements.')' => null];
			if($module['translation']){
				$rows = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->with_translation($this->data['current_lang_id'])->all();
			} else {
				$rows = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->all();
			}

			if($rows) {
				return $rows;
			}
		}

		return "";

	}

	public function attributeAutocomplete()
	{
		$response = [];
		if($this->input->method() == 'post'){
			$filter_name = $this->input->post('filter_name');
			if(!empty($filter_name)) {
				$where = ['name LIKE "%'.$filter_name.'%"' => null];
				$this->load->model('Attribute_model');
				$rows = $this->Attribute_model->fields("id,name")->filter($where)->with_translation($this->data['current_lang_id'])->limit(10)->as_array()->all();
				if($rows) {
					foreach($rows as $row) {
						$attr_values = [];
						$attribute_values = $this->Attribute_model->get_additional_data('attribute_value','*',['attribute_id' => $row['id']]);
						if($attribute_values) {
							foreach($attribute_values as $attribute_value) {
								$attribute_description = $this->Attribute_model->get_additional_data('attribute_value_description', 'name', ['attribute_value_id' => $attribute_value->id, 'language_id' => $this->data['current_lang_id']], true);
								if($attribute_description) {
									$attr_values[] = [
										'attribute_value_id' => $attribute_value->id,
										'name' => $attribute_description->name
									];
								}
							}
						}

						$response[] = ['id' => $row['id'], 'name' => $row['name'], 'attribute_value' => $attr_values];

					}
				}
			}
		}

		$this->template->json($response);
	}

	public function productAutocomplete()
	{
		$response = [];
		if($this->input->method() == 'get'){
			$filter_name = $this->input->get('filter_name');
			if(!empty($filter_name)) {
				$_fs = explode(' ', $filter_name);
				foreach($_fs as $_f)
					$where['name LIKE "%'.$_f.'%"'] = null;

				$this->load->model('Product_model');
				$rows = $this->Product_model->fields("id,name")->filter($where)->with_translation($this->data['current_lang_id'])->limit(10)->as_array()->all();

				if($rows) {
					$response = $rows;
				}
			}
		}

		$this->template->json($response);
	}

	public function optionAutocomplete()
	{
		$response = [];
		if($this->input->method() == 'post'){
			$this->load->model('Option_model');
			$filter_name = $this->input->post('filter_name');
			$where = ['status' => 1];
			if(!empty($filter_name)) {
				$where = ['name LIKE "%'.$filter_name.'%"' => null, 'status' => 1];
			}
			$rows = $this->Option_model->filter($where)->with_translation()->limit(10)->all();
			if($rows) {
				foreach($rows as $row) {
					$data = [
						'option_id' => $row->id,
						'name' => $row->name,
						'type' => $row->type,
						'category' => ucfirst($row->type),
						'option_value' => []
					];

					$option_values = $this->Option_model->get_additional_data('option_value','id,image,sort', ['option_id' => $row->id]);
					if($option_values) {
						foreach($option_values as $option_value) {
							$value_description = $this->Option_model->get_additional_data('option_value_description','*', ['option_value_id' => $option_value->id],true);
							$data['option_value'][] = [
								'option_value_id' => $option_value->id,
								'image' => $option_value->image,
								'name' => ($value_description) ? $value_description->name : ""
							];
						}
					}
					$response[] = $data;
				}
			}
		}

		$this->template->json($response);
	}

	public function relationAutocomplete()
	{
		$response = [];
		if($this->input->method() == 'post'){
			$this->load->model('Relation_model');
			$filter_name = $this->input->post('filter_name');
			$where = ['status' => 1];
			if(!empty($filter_name)) {
				$where = ['name LIKE "%'.$filter_name.'%"' => null, 'status' => 1];
			}
			$rows = $this->Relation_model->filter($where)->with_translation()->limit(10)->all();
			if($rows) {
				foreach($rows as $row) {
					$data = [
						'relation_id' => $row->id,
						'name' => $row->name,
						'relation_value' => []
					];

					$relation_values = $this->Relation_model->get_additional_data('relation_value','id,sort', ['relation_id' => $row->id]);
					if($relation_values) {
						foreach($relation_values as $relation_value) {
							$value_description = $this->Relation_model->get_additional_data('relation_value_description','*', ['relation_value_id' => $relation_value->id],true);
							$data['relation_value'][] = [
								'relation_value_id' => $relation_value->id,
								'name' => ($value_description) ? $value_description->name : ""
							];
						}
					}
					$response[] = $data;
				}
			}
		}

		$this->template->json($response);
	}

	public function modelAutocomplete()
	{
		$response = [];
		if($this->input->method() == 'post'){
			$filter_name = $this->input->post('filter_name');
			if(!empty($filter_name)) {
				$where = [
				    'model LIKE "%'.$filter_name.'%"' => null,
                    'copied_product_id' => 0,
                    'status!=9' => null
                    ];
				$this->load->model('Product_model');
				$rows = $this->Product_model->fields("id,model")
                    ->filter($where)
                    ->with_translation($this->data['current_lang_id'])
                    ->limit(10)
                    ->as_array()
                    ->all();

				if($rows) {
					$response = $rows;
				}
			}
		}

		$this->template->json($response);
	}

	public function ajaxDropdownSearch()
	{
		$response = ['success' => false, 'elements' => []];

		if ($this->input->method() == 'post') {
			$type = $this->input->post('type');
			$element = $this->input->post('element');

			$form_field = $this->relation_modules[$element];

			$dynamic = $form_field['dynamic'];
			$module = $form_field['name'];
			$translation = $form_field['translation'];
			$key = $form_field['key'];
			$columns = $form_field['columns'];
			$module_wheres = $form_field['where'];
			$module_sorts = $form_field['sort'];
			$keyword = $this->input->post('keyword');
			if($this->input->post('element') == 'category_id' &&  !empty(trim($keyword))) {
				$this->load->model('modules/Category_model');
				$data = [];
				$parent_categories = $this->Category_model->fields('id, parent, name')->filter(['name LIKE "%'.$keyword.'%"' => null])->with_translation()->limit(10, 0)->all();

				$response['success'] = true;
				if($parent_categories) {
					foreach ($parent_categories as $category) {
							$parent_category = $this->Category_model->fields('id, parent, name')->filter(['id' => $category->parent])->with_translation()->one();
							$label = "";
							if($parent_category) {
								$label = $parent_category->name." > ";
							}
							$label .= $category->name;
							$response['elements'][] = ['id' => $category->id, 'value' => $label];
					}
				} else {
					$response['elements'] = [['id' => 0, 'value' => translate('no_result',true)]];
				}

			} else {
				if(!empty($keyword) && $module && $key && $columns) {
					$model_name = ucfirst($module).'_model';
					$this->load->model(($dynamic) ? 'modules/'.$model_name : $model_name);
					$columns = explode(',',$columns);
					$select = "";
					if(count($columns) > 1){
						$select = "CONCAT(";
						for ($i=0; $i <= count($columns) - 1; $i++) {
							if($i == count($columns)-1) {
								$select .= ",".$columns[$i];
							} else {
								$select .= $columns[$i].",' '";
							}
						}
						$select .= ")";
					} elseif(count($columns) == 1){
						$select = $columns[0];
					}

					$where = [$select.' LIKE "%'.$keyword.'%"' => null];
					if($module_wheres) {
						foreach($module_wheres as $module_where) {
							$where[$module_where['key']] =  $module_where['value'];
						}
					}

					$order = ["Created_at" => "DESC"];
					if($module_sorts) {
						foreach($module_sorts as $module_sort)
						$order[$module_sort['column']] =  $module_sort['order'];
					}

					if($translation){
						$rows = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->with_translation($this->data['current_lang_id'])->order_by($order)->limit(10)->as_array()->all();
					} else {
						$rows = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->order_by($order)->limit(10)->as_array()->all();
					}
					$response['success'] = true;
					if($rows) {
						$response['elements'] = $rows;
					} else {
						$response['elements'] = [['id' => 0, 'value' => translate('no_result',true)]];
					}
				}
			}

		}
		$this->template->json($response);
	}

	public function category()
	{
		if($this->input->get('parent_id'))
		{
			$parent_id = (int)$this->input->get('parent_id');
		}
		else
		{
			$parent_id = 0;
		}


		$this->load->model('modules/Category_model');
		$categories = $this->Category_model->filter(['status' => 1, 'parent' => $parent_id])->with_translation()->order_by('name', 'ASC')->all();
		$this->data['categories'] = [];
		if($categories)
		{
			foreach($categories as $category)
			{
				$category_option = new stdclass();
				$category_option->id = $category->id;
				$category_option->name = $category->name;
				$category_option->attribute_group_id = $category->attribute_group_id;
				$category_option->has_child = $this->Category_model->filter(['status' => 1, 'parent' => $category->id])->count_rows();

				$this->data['categories'][] = $category_option;
			}
		}


		$this->template->json($this->data['categories']);


	}

	public function attribute()
	{
		if($this->input->get('attribute_group'))
		{
			$attribute_group_id = (int)$this->input->get('attribute_group');

			//Get Attribute Group Data
			$this->load->model('modules/Attribute_group_model');
			$attribute_group = $this->Attribute_group_model->filter(['id' => $attribute_group_id, 'status' => 1])->one();
			$html = '';
			if($attribute_group)
			{
				$this->load->model('Attribute_model');
				$attributes = $this->Attribute_model->filter(['status' => 1, 'custom' => 0, 'FIND_IN_SET('.$attribute_group_id.', attribute_group_id)' => null])->with_translation()->order_by('sort', 'ASC')->all();

				if($attributes)
				{
					$i = 0;
					foreach($attributes as $attribute)
					{
						$html .= "<tr>";
						$attr_values = [];
						$attribute_values = $this->Attribute_model->get_additional_data('attribute_value','*',['attribute_id' => $attribute->id, 'custom' => 0]);
						if($attribute_values) {
							foreach($attribute_values as $attribute_value) {
								$attribute_description = $this->Attribute_model->get_additional_data('attribute_value_description', 'name', ['attribute_value_id' => $attribute_value->id, 'language_id' => $this->data['current_lang_id']], true);
								if($attribute_description) {
									$attr_values[] = [
										'attribute_value_id' => $attribute_value->id,
										'name' => $attribute_description->name
									];
								}
							}
						}

						$html .= "<td width='30%'><strong>".$attribute->name."</strong><input type='hidden' name='product_attribute[".$i."][attribute_id]' value='".$attribute->id."'><input type='hidden' name='product_attribute[".$i."][name]' value='".$attribute->name."'><input type='hidden' name='product_attribute[".$i."][custom_enable]' value='".$attribute->custom_enable."'></td>";
						$html .= "<td  width='30%'><select class='form-control select-search attribute_value_select' name='product_attribute[".$i."][attribute_value_id]'>";

						$html .= "<option value='-1'>Please Select</option>";
						foreach($attr_values as $attr_value)
						{
							$html .= "<option value='".$attr_value['attribute_value_id']."'>".$attr_value['name']."</option>";
						}

						if($attribute->custom_enable == 1)
						{
							$html .= "<option value='0'>Custom value</option>";
						}
						$html .= "</select></td>";

						$html .= "<td  width='40%'><div class='custom' style='display:none'>";
						if($attribute->custom_enable == 1)
						{
							foreach($this->data['languages'] as $lang_key => $language)
							{
								$html .= "<div class='form-group'><div class='input-group'><span class='input-group-addon'><img src='".base_url('templates/administrator/new/global_assets/images/flags/'.$lang_key.'.png')."' title='".$language['name']."'></span><input type='text' name='product_attribute[".$i."][attribute_value][".$language['id']."][name]' value='' placeholder='Attribute value name' class='form-control'></div></div>";
							}
						}
						$html .= "</div></td>";
						$html .= "</tr>";
						$i++;
					}
				}
			}


			echo $html;
		}
	}

}
