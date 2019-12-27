<?php defined('BASEPATH') or exit('No direct script access allowed');

class User extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Group_model');
		$this->load->model('modules/Country_model');
	}

	/**
	 * public function index()
	 * Runs as default when this controller requested if any other method is not specified in route file.
	 * Collects all data (buttons, table columns, fields, pagination config, breadcrumb links) which will be displayed on index page of this controller (generally it contains rows of database result). At final sends data to target template.
	 */
	public function index()
	{
		$this->data['title'] = translate('index_title');
		$this->data['subtitle'] = translate('index_description');

		// Sets buttons
		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('header_button_create', true),
			'href' => site_url($this->directory . $this->controller . '/create'),
			'class' => 'btn btn-success btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-plus-circle2'
		];

		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('header_button_delete', true),
			'href' => site_url($this->directory . $this->controller . '/delete'),
			'class' => 'btn btn-danger btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-trash'
		];


		// Sets Table columns
		$this->data['fields'] = ['id', 'firstname', 'lastname', 'brand', 'username', 'email', 'last_activity', 'banned', 'balance'];

		// Sets translated Table heads
		if ($this->data['fields']) {
			foreach ($this->data['fields'] as $field) {
				$this->data['columns'][$field] = [
					'table' => [
						$this->data['current_lang'] => translate('table_head_' . $field)
					]
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
		if ($this->input->get('banned') != null) {
			$filter['banned'] = $this->input->get('banned');
		}
		if ($this->input->get('name') != null) {
			$filter['firstname'] = $this->input->get('name');
		}

		// Sorts by column and order
		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC'
		];

		// Gets records count from database
		$this->data['total_rows'] = $this->{$this->model}->filter($filter)->count_rows();
		$segment_array = $this->uri->segment_array();
		$page = (ctype_digit(end($segment_array))) ? end($segment_array) : 1;

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
		$total_rows = $this->{$this->model}->filter($filter)->count_rows();
		$rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->order_by($sort['column'],
			$sort['order'])->limit($this->data['per_page'], $page - 1)->all();

		// Sets action button options
		$action_buttons = [
			'show' => true,
			'edit' => true,
			'delete' => true
		];

		// Sets custom row's data options
		$custom_rows_data = [
			[
				'column' => 'banned',
				'callback' => 'get_user_banned',
				'params' => []
			],
			[
				'column' => 'balance',
				'callback' => 'currency_format',
				'params'   => ['currency' => get_setting('currency')]
			]
		];

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

		// Sets Breadcrumb links
		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_all', true),
			'href' => site_url($this->directory . $this->controller),
			'icon_class' => 'icon-database position-left',
			'label_value' => $this->{$this->model}->count_rows(),
			'label_class' => 'label label-primary position-right'
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_active', true),
			'href' => site_url($this->directory . $this->controller . '?banned=0'),
			'icon_class' => 'icon-shield-check position-left',
			'label_value' => $this->{$this->model}->filter(['banned' => 0])->count_rows(),
			'label_class' => 'label label-success position-right'
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_deactive', true),
			'href' => site_url($this->directory . $this->controller . '?banned=1'),
			'icon_class' => 'icon-shield-notice position-left',
			'label_value' => $this->{$this->model}->filter(['banned' => 1])->count_rows(),
			'label_class' => 'label label-warning position-right'
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_trash', true),
			'href' => site_url($this->directory . $this->controller . '/trash'),
			'icon_class' => 'icon-trash position-left',
			'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
			'label_class' => 'label label-danger position-right'
		];

		/**
		 * Executes render function of its parent
		 * @see application/core/Webcoder_controller.php - render()
		 */
		$this->template->render();
	}

	/**
	 * public function index()
	 * Runs as default when this controller requested if any other method is not specified in route file.
	 * Collects all data (buttons, table columns, fields, pagination config, breadcrumb links) which will be displayed on index page of this controller (generally it contains rows of database result). At final sends data to target template.
	 */
	public function trash()
	{
		$this->data['title'] = translate('trash_title');
		$this->data['subtitle'] = translate('trash_description');

		// Sets buttons
		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('header_button_restore', true),
			'href' => site_url($this->directory . $this->controller . '/restore'),
			'class' => 'btn btn-primary btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-plus-circle2'
		];

		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('header_button_delete_permanently', true),
			'href' => site_url($this->directory . $this->controller . '/remove'),
			'class' => 'btn btn-warning btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-plus-circle2'
		];

		$this->data['buttons'][] = [
			'type' => 'a',
			'text' => translate('header_button_clean', true),
			'href' => site_url($this->directory . $this->controller . '/clean'),
			'class' => 'btn btn-danger btn-labeled heading-btn',
			'id' => '',
			'icon' => 'icon-trash'
		];


		// Sets Table columns
		$this->data['fields'] = ['id', 'firstname', 'lastname', 'username', 'email', 'last_activity', 'banned'];

		// Sets translated Table heads
		if ($this->data['fields']) {
			foreach ($this->data['fields'] as $field) {
				$this->data['columns'][$field] = [
					'table' => [
						$this->data['current_lang'] => translate('table_head_' . $field)
					]
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
		if ($this->input->get('banned') != null) {
			$filter['banned'] = $this->input->get('banned');
		}
		if ($this->input->get('name') != null) {
			$filter['firstname'] = $this->input->get('name');
		}

		// Sorts by column and order
		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC'
		];

		// Gets records count from database
		$this->data['total_rows'] = $this->{$this->model}->filter($filter)->only_trashed()->count_rows();
		$segment_array = $this->uri->segment_array();
		$page = (ctype_digit(end($segment_array))) ? end($segment_array) : 1;

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
		$total_rows = $this->{$this->model}->filter($filter)->only_trashed()->count_rows();
		$rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->only_trashed()->order_by($sort['column'],
			$sort['order'])->limit($this->data['per_page'], $page - 1)->all();

		// Sets action button options
		$action_buttons = [
			'remove' => true,
			'restore' => true
		];

		// Sets custom row's data options
		$custom_rows_data = [
			[
				'column' => 'banned',
				'data' => [
					'1' => "<span class='label label-danger'>" . translate('disable', true) . "</span>",
					'0' => "<span class='label label-success'>" . translate('enable', true) . "</span>"
				]
			]
		];

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

		// Sets Breadcrumb links
		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_all', true),
			'href' => site_url($this->directory . $this->controller),
			'icon_class' => 'icon-database position-left',
			'label_value' => $this->{$this->model}->count_rows(),
			'label_class' => 'label label-primary position-right'
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_active', true),
			'href' => site_url($this->directory . $this->controller . '?banned=0'),
			'icon_class' => 'icon-shield-check position-left',
			'label_value' => $this->{$this->model}->filter(['banned' => 0])->count_rows(),
			'label_class' => 'label label-success position-right'
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_deactive', true),
			'href' => site_url($this->directory . $this->controller . '?banned=1'),
			'icon_class' => 'icon-shield-notice position-left',
			'label_value' => $this->{$this->model}->filter(['banned' => 1])->count_rows(),
			'label_class' => 'label label-warning position-right'
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_trash', true),
			'href' => site_url($this->directory . $this->controller . '/trash'),
			'icon_class' => 'icon-trash position-left',
			'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
			'label_class' => 'label label-danger position-right'
		];

		/**
		 * Executes render function of its parent
		 * @see application/core/Webcoder_controller.php - render()
		 */
		$this->template->render($this->controller . '/index');
	}

	/**
	 * public function create()
	 * Sets form fields for new data insertion to database (and buttons, breadcrumb links). Also cathces submitted form, validates and performs insert operation.
	 */
	public function create()
	{
		$this->data['title'] = translate('create_title');
		$this->data['subtitle'] = translate('create_description');

		$groups = $this->Group_model->all('id,name', []);
		$group = [];
		foreach ($groups as $item) {
			$group[''] = translate('form_please_select');
			$group[$item->id] = $item->name;
		}

		$countries = $this->Country_model->filter(['status' => 1])->with_translation()->all('id,name');
		$country_options = [];
		foreach ($countries as $item) {
			$country_options[''] = translate('form_please_select');
			$country_options[$item->id] = $item->name;
		}

		$this->data['form_field']['general'] = [
			'firstname' => [
				'property' => 'text',
				'id' => 'firstname',
				'name' => 'firstname',
				'class' => 'form-control',
				'value' => set_value('firstname'),
				'label' => translate('form_label_firstname'),
				'placeholder' => translate('form_placeholder_firstname'),
				'validation' => ['rules' => 'required']
			],
			'lastname' => [
				'property' => 'text',
				'id' => 'lastname',
				'name' => 'lastname',
				'class' => 'form-control',
				'value' => set_value('lastname'),
				'label' => translate('form_label_lastname'),
				'placeholder' => translate('form_placeholder_lastname'),
				'validation' => ['rules' => 'required']
			],
			'brand' => [
				'property' => 'text',
				'id' => 'brand',
				'name' => 'brand',
				'class' => 'form-control',
				'value' => set_value('brand'),
				'label' => translate('form_label_brand'),
				'placeholder' => translate('form_placeholder_brand'),
				'validation' => ['rules' => 'required']
			],
			'country' => [
				'property' => 'dropdown',
				'id' => 'country',
				'name' => 'country',
				'class' => 'form-control',
				'selected' => set_value('country'),
				'label' => translate('form_label_country'),
				'options' => $country_options,
				'validation' => ['rules' => 'required']
			],
			'city' => [
				'property' => 'dropdown',
				'id' => 'city',
				'name' => 'city',
				'class' => 'form-control',
				'selected' => set_value('city'),
				'label' => translate('form_label_city'),
				'options' => [],
				'validation' => ['rules' => 'required']
			],
			'address' => [
				'property' => 'text',
				'id' => 'address',
				'name' => 'address',
				'class' => 'form-control',
				'value' => set_value('address'),
				'label' => translate('form_label_address'),
				'placeholder' => translate('form_placeholder_address'),
				'validation' => ['rules' => 'required']
			],
			'address2' => [
				'property' => 'text',
				'id' => 'address2',
				'name' => 'address2',
				'class' => 'form-control',
				'value' => set_value('address2'),
				'label' => translate('form_label_address2'),
				'placeholder' => translate('form_placeholder_address2'),
				'validation' => ['rules' => '']
			],
			'postcode' => [
				'property' => 'text',
				'id' => 'postcode',
				'name' => 'postcode',
				'class' => 'form-control',
				'value' => set_value('postcode'),
				'label' => translate('form_label_postcode'),
				'placeholder' => translate('form_placeholder_postcode'),
				'validation' => ['rules' => 'required']
			],
			'mobile' => [
				'property' => 'text',
				'id' => 'mobile',
				'name' => 'mobile',
				'class' => 'form-control',
				'value' => set_value('mobile'),
				'label' => translate('form_label_mobile'),
				'placeholder' => translate('form_placeholder_mobile'),
				'validation' => ['rules' => '']
			],
			'email' => [
				'property' => 'email',
				'type' => 'email',
				'id' => 'email',
				'name' => 'email',
				'class' => 'form-control',
				'value' => set_value('email'),
				'label' => translate('form_label_email'),
				'placeholder' => translate('form_placeholder_email'),
				'validation' => ['rules' => 'required|valid_email']
			],
			
			'group_id' => [
				'property' => 'dropdown',
				'name' => 'group_id',
				'id' => 'group_id',
				'label' => translate('form_label_group'),
				'class' => 'bootstrap-select',
				'data-style' => 'btn-default btn-xs',
				'data-width' => '100%',
				'options' => $group,
				'selected' => set_value('group_id'),
				'validation' => ['rules' => 'required']
			],
			'username' => [
				'property' => 'text',
				'id' => 'username',
				'name' => 'username',
				'class' => 'form-control',
				'value' => set_value('username'),
				'label' => translate('form_label_username'),
				'placeholder' => translate('form_placeholder_username'),
				'validation' => ['rules' => 'required']
			],
			'type' => [
				'property' => 'dropdown',
				'id' => 'type',
				'name' => 'type',
				'class' => 'form-control',
				'selected' => set_value('type'),
				'label' => translate('form_label_type'),
				'options' => ['0' => translate('select', true), '1' => translate('personal'), '2' => translate('business')],
				'validation' => ['rules' => 'required']
			],
			'banned' => [
				'property' => 'dropdown',
				'id' => 'banned',
				'name' => 'banned',
				'class' => 'form-control',
				'selected' => set_value('banned'),
				'label' => translate('form_label_banned'),
				'options' => ['0' => translate('breadcrumb_link_active', true), '1' => translate('breadcrumb_link_deactive', true)],
				'validation' => ['rules' => '']
			],
			'password' => [
				'property' => 'text',
				'type' => 'password',
				'id' => 'password',
				'name' => 'password',
				'class' => 'form-control',
				'value' => set_value('password'),
				'label' => translate('form_label_password'),
				'placeholder' => translate('form_placeholder_password'),
				'validation' => ['rules' => 'required']
			]
		];
		foreach ($this->data['form_field']['general'] as $key => $value) {
			$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
		}

		$this->data['buttons'][] = [
			'type' => 'button',
			'text' => translate('form_button_save', true),
			'class' => 'btn btn-primary btn-labeled heading-btn',
			'id' => 'save',
			'icon' => 'icon-floppy-disk',
			'additional' => [
				'onclick' => "confirm('Are you sure?') ? $('#form-save').submit() : false;",
				'form' => 'form-save',
				'formaction' => current_url()
			]
		];

		$this->breadcrumbs->push(translate('create_title'), $this->directory . $this->controller . '/create');

		if ($this->input->method() == 'post') {
			if ($this->form_validation->run() == true) {
				$general = [
					'firstname' => $this->input->post('firstname'),
					'lastname' => $this->input->post('lastname'),
					'email' => $this->input->post('email'),
					'username' => $this->input->post('username'),
					'password' => $this->input->post('password'),
					'group_id' => $this->input->post('group_id'),
					'banned' => $this->input->post('banned'),
				];

				$user_id = $this->auth->create_user($general['email'], $general['password'], $general['username'],
					$general['firstname'], $general['lastname'], $general['group_id'], $general['banned']);
				if ($user_id) {
					redirect(site_url_multi($this->directory . $this->controller));
				}
			} else {
				$this->data['message'] = translate('error_warning', true);
			}
		}

		$this->template->render($this->controller . '/form');
	}

	/**
	 * public function edit($id)
	 * Gets row record from database which id equals to $id and fills proper fields. Sets form fields for data update (and buttons, breadcrumb links). Also cathces submitted form, validates and performs update operation.
	 * @param integer $id
	 */
	public function edit($id)
	{
		if ($id && ctype_digit($id)) {
			$this->data['general'] = $this->{$this->model}->one($id);
			if ($this->data['general']) {
				//Set title & description
				$this->data['title'] = translate('edit_title');
				$this->data['subtitle'] = translate('edit_description');
				
				$this->data['files'] = explode(',', $this->data['general']->file);

				// Set General Form Field
				$groups = $this->Group_model->fields(['id', 'name'])->all();
				$group = [];
				foreach ($groups as $item) {
					$group[''] = translate('form_please_select');
					$group[$item->id] = $item->name;
				}

				$countries = $this->Country_model->filter(['status' => 1])->with_translation()->all('id,name');
				$country_options = [];
				foreach ($countries as $item) {
					$country_options[''] = translate('form_please_select');
					$country_options[$item->id] = $item->name;
				}

				// selected group
				$user_group = $this->{$this->model}->get_user_group('*', ['user_id' => $id]);
				if ($user_group) {
					$user_group = $user_group[0]['group_id'];
				}

				$this->data['form_field']['general'] = [
					'firstname' => [
						'property' => 'text',
						'id' => 'firstname',
						'name' => 'firstname',
						'class' => 'form-control',
						'value' => (set_value('firstname')) ? set_value('firstname') : $this->data['general']->firstname,
						'label' => translate('form_label_firstname'),
						'placeholder' => translate('form_placeholder_firstname'),
						'validation' => ['rules' => 'required']
					],
					'lastname' => [
						'property' => 'text',
						'id' => 'lastname',
						'name' => 'lastname',
						'class' => 'form-control',
						'value' => (set_value('lastname')) ? set_value('lastname') : $this->data['general']->lastname,
						'label' => translate('form_label_lastname'),
						'placeholder' => translate('form_placeholder_lastname'),
						'validation' => ['rules' => 'required']
					],
					'brand' => [
						'property' => 'text',
						'id' => 'brand',
						'name' => 'brand',
						'class' => 'form-control',
						'value' => (set_value('brand')) ? set_value('brand') : $this->data['general']->brand,
						'label' => translate('form_label_brand'),
						'placeholder' => translate('form_placeholder_brand'),
						'validation' => ['rules' => 'required']
					],
					'country' => [
						'property' => 'dropdown',
						'id' => 'country',
						'name' => 'country',
						'class' => 'form-control',
						'selected' => (set_value('country')) ? set_value('country') : $this->data['general']->country,
						'label' => translate('form_label_country'),
						'options' => $country_options,
						'validation' => ['rules' => 'required']
					],
					'city' => [
						'property' => 'dropdown',
						'id' => 'city',
						'name' => 'city',
						'class' => 'form-control',
						'data-selected' => (set_value('city')) ? set_value('city') : $this->data['general']->city,
						'label' => translate('form_label_city'),
						'options' => [],
						'validation' => ['rules' => 'required']
					],
					'address' => [
						'property' => 'text',
						'id' => 'address',
						'name' => 'address',
						'class' => 'form-control',
						'value' => (set_value('address')) ? set_value('address') : $this->data['general']->address,
						'label' => translate('form_label_address'),
						'placeholder' => translate('form_placeholder_address'),
						'validation' => ['rules' => 'required']
					],
					'address2' => [
						'property' => 'text',
						'id' => 'address2',
						'name' => 'address2',
						'class' => 'form-control',
						'value' => (set_value('address2')) ? set_value('address2') : $this->data['general']->address2,
						'label' => translate('form_label_address2'),
						'placeholder' => translate('form_placeholder_address2'),
						'validation' => ['rules' => '']
					],
					'postcode' => [
						'property' => 'text',
						'id' => 'postcode',
						'name' => 'postcode',
						'class' => 'form-control',
						'value' => (set_value('postcode')) ? set_value('postcode') : $this->data['general']->postcode,
						'label' => translate('form_label_postcode'),
						'placeholder' => translate('form_placeholder_postcode'),
						'validation' => ['rules' => 'required']
					],
					'mobile' => [
						'property' => 'text',
						'id' => 'mobile',
						'name' => 'mobile',
						'class' => 'form-control',
						'value' => (set_value('mobile')) ? set_value('mobile') : $this->data['general']->mobile,
						'label' => translate('form_label_mobile'),
						'placeholder' => translate('form_placeholder_mobile'),
						'validation' => ['rules' => '']
					],
					'email' => [
						'property' => 'text',
						'type' => 'email',
						'id' => 'email',
						'name' => 'email',
						'class' => 'form-control',
						'value' => (set_value('email')) ? set_value('email') : $this->data['general']->email,
						'label' => translate('form_label_email'),
						'placeholder' => translate('form_placeholder_email'),
						'validation' => ['rules' => 'required|valid_email']
					],
					'group_id' => [
						'property' => 'dropdown',
						'name' => 'group_id',
						'id' => 'group_id',
						'label' => translate('form_label_group'),
						'class' => 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'options' => $group,
						'selected' => (set_value('group_id')) ? set_value('group_id') : $user_group,
						'validation' => ['rules' => 'required']
					],
					'username' => [
						'property' => 'text',
						'id' => 'username',
						'name' => 'username',
						'class' => 'form-control',
						'value' => (set_value('username')) ? set_value('username') : $this->data['general']->username,
						'label' => translate('form_label_username'),
						'placeholder' => translate('form_placeholder_username'),
						'autocomplete' => 'off',
						'validation' => ['rules' => '']
					],
					'type' => [
						'property' => 'dropdown',
						'id' => 'type',
						'name' => 'type',
						'class' => 'form-control',
						'selected' => (set_value('type')) ? set_value('type') : $this->data['general']->type,
						'label' => translate('form_label_type'),
						'options' => ['0' => translate('select', true), '1' => translate('personal'), '2' => translate('business')],
						'validation' => ['rules' => 'required']
					],
					'banned' => [
						'property' => 'dropdown',
						'id' => 'banned',
						'name' => 'banned',
						'class' => 'form-control',
						'selected' => (set_value('banned')) ? set_value('banned') : $this->data['general']->banned,
						'label' => translate('form_label_banned'),
						'options' => ['0' => translate('breadcrumb_link_active', true), '1' => translate('breadcrumb_link_deactive', true)],
						'validation' => ['rules' => '']
					],
					'password' => [
						'property' => 'text',
						'type' => 'password',
						'id' => 'password',
						'name' => 'password',
						'class' => 'form-control" readonly onfocus="this.removeAttribute(\'readonly\');',
						'value' => set_value('password'),
						'label' => translate('form_label_password'),
						'placeholder' => translate('form_placeholder_password'),
						'autocomplete' => 'off',
						'validation' => ['rules' => '']
						
					]
				];

				// Set Form Validation General Form Field
				foreach ($this->data['form_field']['general'] as $key => $value) {
					$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
				}


				$this->data['buttons'][] = [
					'type' => 'button',
					'text' => translate('form_button_save', true),
					'class' => 'btn btn-primary btn-labeled heading-btn',
					'id' => 'save',
					'icon' => 'icon-floppy-disk',
					'additional' => [
						'onclick' => "confirm('Are you sure?') ? $('#form-save').submit() : false;",
						'form' => 'form-save',
						'formaction' => current_url()
					]
				];

				$this->breadcrumbs->push(translate('edit_title'), $this->directory . $this->controller . '/edit');

				if ($this->input->method() == 'post' && $this->form_validation->run() == true) {
					$general = [
						'firstname' => $this->input->post('firstname'),
						'lastname' => $this->input->post('lastname'),
						'email' => $this->input->post('email'),
						'password' => $this->input->post('password'),
						'username' => $this->input->post('username'),
						'group_id' => $this->input->post('group_id'),
						'banned'	=> $this->input->post('banned')
					];
					$additional_data = [
						'mobile' => $this->input->post('mobile'),
						'address' => $this->input->post('address'),
						'address2' => $this->input->post('address2'),
						'country' => $this->input->post('country'),
						'city' => $this->input->post('city'),
						'postcode' => $this->input->post('postcode'),
						'type' => $this->input->post('type'),
						'username' => $this->input->post('username')
					];

					$response = $this->auth->update_user($id, $general['email'], $general['password'], false, $general['firstname'], $general['lastname'], $general['group_id'], $general['banned']);
					$this->{$this->model}->update($additional_data, ['id' => $id]);
					
					if((int)$this->data['general']->banned == 1 && (int)$general['banned'] == 0) {
						$this->email->from(get_setting('mail_username'), get_setting('site_title', $this->data['current_lang']));
						$this->email->to($this->input->post('email'));
						$this->email->subject(translate('email_verification_subject_text'));
						$this->email->message(translate('email_verification_message_text'));
						$this->email->send();
					}
					
					if($general['password']){
						$this->email->from(get_setting('mail_username'), get_setting('site_title', $this->data['current_lang']));
						$this->email->to($this->input->post('email'));
						$this->email->subject(translate('email_password'));
						$this->email->message(translate('email_password').$general['password']);
						$this->email->send();
					}

					redirect(site_url_multi($this->directory . $this->controller));
				} else {
					$this->data['message'] = translate('error_warning', true);

                    foreach ($this->data['files'] as $key => $file) {

                        if( ! file_exists(dirname(__DIR__, 3) . '/uploads/' . $file) || is_dir(dirname(__DIR__, 3) . '/uploads/' . $file)) {

                            unset($this->data['files'][$key]);

                        }
                    }

					$this->template->render($this->controller . '/form');
				}
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}

	/**
	 * public function edit($id)
	 * Gets row record from database which id equals to $id and fills proper fields. Sets form fields for data update (and buttons, breadcrumb links). Also cathces submitted form, validates and performs update operation.
	 * @param integer $id
	 */
	public function show($id)
	{
		if ($id && ctype_digit($id)) {
			$this->data['general'] = $this->{$this->model}->one($id);
			if ($this->data['general']) {
				//Set title & description
				$this->data['title'] = translate('show_title');
				$this->data['subtitle'] = translate('show_description');
				
				$this->data['files'] = explode(',', $this->data['general']->file);

				// Set General Form Field
				$groups = $this->Group_model->fields(['id', 'name'])->all();
				$group = [];
				foreach ($groups as $item) {
					$group[''] = translate('form_please_select');
					$group[$item->id] = $item->name;
				}

				$countries = $this->Country_model->filter(['status' => 1])->with_translation()->all('id,name');
				$country_options = [];
				foreach ($countries as $item) {
					$country_options[''] = translate('form_please_select');
					$country_options[$item->id] = $item->name;
				}

				// selected group
				$user_group = $this->{$this->model}->get_user_group('*', ['user_id' => $id]);
				if ($user_group) {
					$user_group = $user_group[0]['group_id'];
				}

				$this->data['form_field']['general'] = [
					'firstname' => [
						'property' => 'text',
						'id' => 'firstname',
						'disabled' => 'disabled',
						'name' => 'firstname',
						'class' => 'form-control',
						'value' => (set_value('firstname')) ? set_value('firstname') : $this->data['general']->firstname,
						'label' => translate('form_label_firstname'),
						'placeholder' => translate('form_placeholder_firstname'),
						'validation' => ['rules' => 'required']
					],
					'lastname' => [
						'property' => 'text',
						'id' => 'lastname',
						'disabled' => 'disabled',
						'name' => 'lastname',
						'class' => 'form-control',
						'value' => (set_value('lastname')) ? set_value('lastname') : $this->data['general']->lastname,
						'label' => translate('form_label_lastname'),
						'placeholder' => translate('form_placeholder_lastname'),
						'validation' => ['rules' => 'required']
					],
					'brand' => [
						'property' => 'text',
						'id' => 'brand',
						'disabled' => 'disabled',
						'name' => 'brand',
						'class' => 'form-control',
						'value' => (set_value('brand')) ? set_value('brand') : $this->data['general']->brand,
						'label' => translate('form_label_brand'),
						'placeholder' => translate('form_placeholder_brand'),
						'validation' => ['rules' => 'required']
					],
					'country' => [
						'property' => 'dropdown',
						'id' => 'country',
						'disabled' => 'disabled',
						'name' => 'country',
						'class' => 'form-control',
						'selected' => (set_value('country')) ? set_value('country') : $this->data['general']->country,
						'label' => translate('form_label_country'),
						'options' => $country_options,
						'validation' => ['rules' => 'required']
					],
					'city' => [
						'property' => 'dropdown',
						'id' => 'city',
						'disabled' => 'disabled',
						'name' => 'city',
						'class' => 'form-control',
						'data-selected' => (set_value('city')) ? set_value('city') : $this->data['general']->city,
						'label' => translate('form_label_city'),
						'options' => [],
						'validation' => ['rules' => 'required']
					],
					'address' => [
						'property' => 'text',
						'id' => 'address',
						'disabled' => 'disabled',
						'name' => 'address',
						'class' => 'form-control',
						'value' => (set_value('address')) ? set_value('address') : $this->data['general']->address,
						'label' => translate('form_label_address'),
						'placeholder' => translate('form_placeholder_address'),
						'validation' => ['rules' => 'required']
					],
					'address2' => [
						'property' => 'text',
						'id' => 'address2',
						'disabled' => 'disabled',
						'name' => 'address2',
						'class' => 'form-control',
						'value' => (set_value('address2')) ? set_value('address2') : $this->data['general']->address2,
						'label' => translate('form_label_address2'),
						'placeholder' => translate('form_placeholder_address2'),
						'validation' => ['rules' => '']
					],
					'postcode' => [
						'property' => 'text',
						'id' => 'postcode',
						'disabled' => 'disabled',
						'name' => 'postcode',
						'class' => 'form-control',
						'value' => (set_value('postcode')) ? set_value('postcode') : $this->data['general']->postcode,
						'label' => translate('form_label_postcode'),
						'placeholder' => translate('form_placeholder_postcode'),
						'validation' => ['rules' => 'required']
					],
					'mobile' => [
						'property' => 'text',
						'id' => 'mobile',
						'disabled' => 'disabled',
						'name' => 'mobile',
						'class' => 'form-control',
						'value' => (set_value('mobile')) ? set_value('mobile') : $this->data['general']->mobile,
						'label' => translate('form_label_mobile'),
						'placeholder' => translate('form_placeholder_mobile'),
						'validation' => ['rules' => '']
					],
					'email' => [
						'property' => 'text',
						'type' => 'email',
						'id' => 'email',
						'disabled' => 'disabled',
						'name' => 'email',
						'class' => 'form-control',
						'value' => (set_value('email')) ? set_value('email') : $this->data['general']->email,
						'label' => translate('form_label_email'),
						'placeholder' => translate('form_placeholder_email'),
						'validation' => ['rules' => 'required|valid_email']
					],
					'group_id' => [
						'property' => 'dropdown',
						'name' => 'group_id',
						'id' => 'group_id',
						'disabled' => 'disabled',
						'label' => translate('form_label_group'),
						'class' => 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'options' => $group,
						'selected' => (set_value('group_id')) ? set_value('group_id') : $user_group,
						'validation' => ['rules' => 'required']
					],
					'username' => [
						'property' => 'text',
						'id' => 'username',
						'disabled' => 'disabled',
						'name' => 'username',
						'class' => 'form-control',
						'value' => (set_value('username')) ? set_value('username') : $this->data['general']->username,
						'label' => translate('form_label_username'),
						'placeholder' => translate('form_placeholder_username'),
						'validation' => ['rules' => '']
					],
					'type' => [
						'property' => 'dropdown',
						'id' => 'type',
						'disabled' => 'disabled',
						'name' => 'type',
						'class' => 'form-control',
						'selected' => (set_value('type')) ? set_value('type') : $this->data['general']->type,
						'label' => translate('form_label_type'),
						'options' => ['0' => translate('select', true), '1' => translate('personal'), '2' => translate('business')],
						'validation' => ['rules' => 'required']
					],
					'banned' => [
						'property' => 'dropdown',
						'id' => 'banned',
						'disabled' => 'disabled',
						'name' => 'banned',
						'class' => 'form-control',
						'selected' => (set_value('banned')) ? set_value('banned') : $this->data['general']->banned,
						'label' => translate('form_label_banned'),
						'options' => ['0' => translate('breadcrumb_link_active', true), '1' => translate('breadcrumb_link_deactive', true)],
						'validation' => ['rules' => '']
					]
				];

				// Set Form Validation General Form Field
				foreach ($this->data['form_field']['general'] as $key => $value) {
					$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
				}


				
				$this->breadcrumbs->push(translate('show_title'), $this->directory . $this->controller . '/show');

				foreach ($this->data['files'] as $key => $file) {

				    if( ! file_exists(dirname(__DIR__, 3) . '/uploads/' . $file) || is_dir(dirname(__DIR__, 3) . '/uploads/' . $file)) {

				        unset($this->data['files'][$key]);

                    }
                }
				
                $this->template->render($this->controller . '/form');
				
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}

	/**
	 * public function delete($id)
	 * Deletes row record from database which id equals to $id.
	 * @param integer $id
	 */
	public function delete($id)
	{
		$this->{$this->model}->force_delete($id);
		echo json_encode(['success' => 1]);
	}
}
