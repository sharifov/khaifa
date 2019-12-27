<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Relation extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
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
		$this->data['fields'] = ['id', 'name', 'sort', 'status'];

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
			$relation_language_id = (int) $this->input->get('language_id');
			$this->session->set_userdata('relation_language_id', $relation_language_id);
		} elseif ($this->session->has_userdata('relation_language_id')) {
			$language_id = (int) $this->session->userdata('relation_language_id');
		} else {
			$language_id = $this->data['current_lang_id'];
		}
		// End Content Language


		// Filters for banned and not specified name
		$filter = [];
		if ($this->input->get('status') != null) {
			$filter['status'] = $this->input->get('status');
		}
		if ($this->input->get('name') != null) {
			$filter['name LIKE "%' . $this->input->get('name') . '%"'] = null;
		}

		// Sorts by column and order
		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
		];
		
		if ($this->data['languages']) {
			foreach ($this->data['languages'] as $language) {
				$this->data['language_list_holder'][] = [
					'id'    => $language['id'],
					'name'  => $language['name'],
					'code'  => $language['code'],
					'count' => $this->{$this->model}->with_translation($language['id'])->count_rows($filter),
					'class' => ($language_id == $language['id']) ? 'btn btn-primary' : 'btn btn-default'
				];
			}
		}
		
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

		// Sets custom row's data relations
		$custom_rows_data = [
			[
				'column' => 'status',
				'callback' => 'get_status',
				'params' => '',
			],
		];

		// Set action buttons
		$action_buttons = [];

		if (check_permission('relation', 'edit')) {
			$action_buttons['edit'] = true;
		}

		if (check_permission('relation', 'delete')) {
			$action_buttons['delete'] = true;
		}

		// Generates Table with given records
		$this->wc_table->set_module(false);
		$this->wc_table->set_columns($columns);
		$this->wc_table->set_rows($rows);
		$this->wc_table->set_custom_rows($custom_rows_data);
		$this->wc_table->set_action($action_buttons);
		$this->data['table'] = $this->wc_table->generate();

		// Sets Pagination relations and initialize
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

		$this->template->render();
	}

	/**
	 * public function trash()
	 * Runs as default when this controller requested if any other method is not specified in route file.
	 * Collects all data (buttons, table columns, fields, pagination config, breadcrumb links) which will be displayed on index page of this controller (generally it contains rows of database result). At final sends data to target template.
	 */

	public function trash()
	{
		$this->data['title'] = translate('trash_title');
		$this->data['subtitle'] = translate('trash_description');

		// Sets Table columns
		$this->data['fields'] = ['id', 'name', 'sort', 'status'];

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
			$relation_language_id = (int) $this->input->get('language_id');
			$this->session->set_userdata('relation_language_id', $relation_language_id);
		} elseif ($this->session->has_userdata('relation_language_id')) {
			$language_id = (int) $this->session->userdata('relation_language_id');
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
			],
		];

		// Filters for banned and not specified name
		$filter = [];
		if ($this->input->get('status') != null) {
			$filter['status'] = $this->input->get('status');
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
		$total_rows = $this->{$this->model}->only_trashed()->where($filter)->with_translation($language_id)->count_rows();
		$rows = $this->{$this->model}->fields($this->data['fields'])->only_trashed()->filter($filter)->with_translation($language_id)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();

		// Sets custom row's data relations
		$custom_rows_data = [
			[
				'column' => 'status',
				'callback' => 'get_status',
				'params' => '',
			],
		];

		// Set action buttons
		$action_buttons = [];
		if (check_permission('relation', 'restore')) {
			$action_buttons['restore'] = true;
		}

		if (check_permission('relation', 'remove')) {
			$action_buttons['remove'] = true;
		}


		// Generates Table with given records
		$this->wc_table->set_module(false);
		$this->wc_table->set_columns($columns);
		$this->wc_table->set_rows($rows);
		$this->wc_table->set_custom_rows($custom_rows_data);
		$this->wc_table->set_action($action_buttons);
		$this->data['table'] = $this->wc_table->generate();

		// Sets Pagination relations and initialize
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

		$this->template->render($this->controller.'/index');
	}

	/**
	* public function create()
	* Sets form fields for new data insertion to database (and buttons, breadcrumb links). Also cathces submitted form, validates and performs insert operation.
	*/
	public function create()
	{
		$this->data['title']    = translate('create_title');
		$this->data['subtitle'] = translate('create_description');
		
	

		// General Form Fields
		$this->data['form_field']['general'] = [
			'sort' => [
				'property'  => 'number',
				'min'       => '0',
				'name'      => 'sort',
				'class'     => 'form-control',
				'value'     => set_value('sort'),
				'label'     => translate('form_label_sort'),
				'placeholder' => translate('form_label_sort'),
				'validation' => []
			],
			'status' => [
				'property' => 'dropdown',
				'name' => 'status',
				'id' => 'status',
				'class' => 'bootstrap-select',
				'data-style' => 'btn-default btn-xs',
				'data-width' => '100%',
				'label' => translate('form_label_status'),
				'options' => [translate('disable', true), translate('enable', true)],
				'selected' => set_value('status'),
				'validation' => ['rules' => 'required']
			]
		];
		
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
		}

		$this->data['relation_value'] = $this->input->post('relation_value');
		
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
				$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
			}
		}
	
		//$this->form_validation->set_rules('relation_value', translate('form_label_relation_values'), 'required');
		
		$this->data['buttons'][] = [
			'type'       => 'button',
			'text'       => translate('form_button_save', true),
			'class'      => 'btn btn-primary btn-labeled heading-btn',
			'id'         => 'save',
			'icon'       => 'icon-floppy-disk',
			'additional' => [
				'onclick'    => "if(confirm('".translate('are_you_sure', true)."')){ $('#form-save').submit(); }else{ return false; }",
				'form'       => 'form-save',
				'formaction' => current_url()
			]
		];

		if ($this->input->method() == 'post') {
			if($this->form_validation->run() == true) {

				$general = [
					'sort'  => $this->input->post('sort'),
					'status'  => $this->input->post('status')
				];

				$id = $this->{$this->model}->insert($general);

				if($id) {
					// Insert relation value
					if($this->input->post('relation_value')) {
						foreach($this->input->post('relation_value') as $relation_value) {
	
							$general = [
								'relation_id' => $id,
								'sort'      => $relation_value['sort_order']
							];
	
							$relation_value_id = $this->{$this->model}->insert_relation_value($general);
							if($relation_value_id) {
								if($relation_value['relation_value_description']) {
									foreach($relation_value['relation_value_description'] as $lang_id => $relation_description) {
										$relation_value_translation = [
											'relation_value_id'  => $relation_value_id,
											'language_id'  => $lang_id,
											'relation_id'  => $id,
											'name' => $relation_description['name']
										];
										$this->{$this->model}->insert_relation_value_translation($relation_value_translation);
									}
								}
							}
						}
					}

					// Insert translation
					foreach ($this->input->post('translation') as $language_id => $translation) {
						$translation_data = [
							'relation_id' => $id,
							'language_id'   =>  $language_id,
							'name'  => $translation['name']
						];

						$this->{$this->model}->insert_translation($translation_data);
					}
				}

				$this->session->set_flashdata('message', translate('form_success_create'));
				redirect(site_url_multi($this->directory . $this->controller), 'refresh');
			} elseif(!$this->input->post('relation_value')) {
				$this->data['message'] = translate('error_message_relation_value_required');
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
		$this->data['title'] = translate('edit_title');
		$this->data['subtitle'] = translate('edit_description');

		$relation = $this->{$this->model}->filter(['id' => $id])->one();

		if($relation) {
			

			// General Form Fields
			$this->data['form_field']['general'] = [
				'sort' => [
					'property'  => 'number',
					'min'       => '0',
					'name'      => 'sort',
					'class'     => 'form-control',
					'value'     => (set_value('sort')) ? set_value('sort') : $relation->sort,
					'label'     => translate('form_label_sort'),
					'placeholder' => translate('form_label_sort'),
					'validation' => []
				],
				'status' => [
					'property' => 'dropdown',
					'name' => 'status',
					'id' => 'status',
					'class' => 'bootstrap-select',
					'data-style' => 'btn-default btn-xs',
					'data-width' => '100%',
					'label' => translate('form_label_status'),
					'options' => [translate('disable', true), translate('enable', true)],
					'selected' => (set_value('status')) ? set_value('status') : $relation->status,
					'validation' => ['rules' => 'required']
				]
			];
			
			// Translation Form Fields
			foreach ($this->data['languages'] as $language) { 
				$row_translation = $this->{$this->model}->filter(['relation_id' => $id])->with_translation($language['id'])->one();
				
				$this->data['form_field']['translation'][$language['id']]['name'] = [
					'property'    	=> "text",
					'name'        	=> 'translation[' . $language['id'] . '][name]',
					'class'       	=> 'form-control',
					'value'       	=> set_value('translation[' . $language['id'] . '][name]') ? set_value('translation[' . $language['id'] . '][name]') : $row_translation->name,
					'label'       	=> translate("form_label_name"),
					'placeholder' 	=> translate("form_label_name"),
					'validation'    => ['rules' => 'required']
				];
			}

			$relation_value = [];
			$rows = $this->{$this->model}->get_relation_value($id);

			if(isset($_GET['test'])) {

			    var_dump($rows);

			    die;
            }
			
			if($rows) {
				foreach ($rows as $row) {
					$relation_value_translations = $this->{$this->model}->get_relation_value_translation($row->id);
					if($relation_value_translations) {
						$relation_value_description = [];
						foreach($relation_value_translations as $relation_value_translation) {
							$relation_value_description[$relation_value_translation->language_id] = [
							    'name'=>$relation_value_translation->name,
                                'id' => $row->id
                            ];
						}
					}
					
					$relation_value[] = [
						'relation_value_description' => $relation_value_description,
						'sort_order' => $row->sort
					];
				}
			}
			
			$this->data['relation_value'] = ($this->input->post('relation_value')) ? $this->input->post('relation_value') : $relation_value;
			
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
					$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
				}
			}
			
			$this->data['buttons'][] = [
				'type'       => 'button',
				'text'       => translate('form_button_save', true),
				'class'      => 'btn btn-primary btn-labeled heading-btn',
				'id'         => 'save',
				'icon'       => 'icon-floppy-disk',
				'additional' => [
					'onclick'    => "if(confirm('".translate('are_you_sure', true)."')){ $('#form-save').submit(); }else{ return false; }",
					'form'       => 'form-save',
					'formaction' => current_url()
				]
			];

			if ($this->input->method() == 'post') {
				if($this->form_validation->run() == true) {

					$general = [
						'sort'  => $this->input->post('sort'),
						'status'  => $this->input->post('status')
					];

					$this->{$this->model}->update($general,['id' => $id]);

					// Delete relation value

					$this->{$this->model}->delete_relation_value($id);
					$this->{$this->model}->delete_relation_value_translation($id);
					// Insert relation value
					if($this->input->post('relation_value')) {
						foreach($this->input->post('relation_value') as $relation_value) {
							
							$general = [
								'relation_id' => $id,
								'sort'      => $relation_value['sort_order']
							];

							if(isset($relation_value['custom_value_id']) && $relation_value['custom_value_id']) {
                                $general['id'] = $relation_value['custom_value_id'];
							}
	
							$relation_value_id = $this->{$this->model}->insert_relation_value($general);
							
							if($relation_value_id) {
								if($relation_value['relation_value_description']) {
									foreach($relation_value['relation_value_description'] as $lang_id => $relation_description) {
										$relation_value_translation = [
											'relation_value_id'  => $relation_value_id,
											'language_id'  => $lang_id,
											'relation_id'  => $id,
											'name' => $relation_description['name']
										];
										$this->{$this->model}->insert_relation_value_translation($relation_value_translation);
									}
								}
							}
						}
					}

					// Delete and Insert translation
					$this->{$this->model}->delete_translation($id);
					foreach ($this->input->post('translation') as $language_id => $translation) {
						$translation_data = [
							'relation_id' => $id,
							'language_id'   =>  $language_id,
							'name'  => $translation['name']
						];
						$this->{$this->model}->insert_translation($translation_data);
					}
					

					$this->session->set_flashdata('message', translate('form_success_edit'));
					redirect(site_url_multi($this->directory . $this->controller), 'refresh');
				} else {

					$this->data['message'] = translate('error_warning', true);
				}
			}

			$this->template->render($this->controller . '/form');


		} else {
			show_404();
		}
	}

	/**
	* public function delete($id)
	* Deletes row record from database which id equals to $id.
	* @param integer $id
	*/
	
	public function delete($id = false)
	{
		if ($id) {
			$this->{$this->model}->delete($id);
			$this->template->json(['success' => 1]);
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

	/**
	* public function remove($id)
	* Hard deletes row record from database which id equals to $id.
	* @param integer $id
	*/

	public function remove($id = false) 
	{
		if ($id) {
			$this->{$this->model}->force_delete_relation($id);
			$this->template->json(['success' => 1]);
		} else {
			if ($this->input->method() == 'post') {
				$response  = ['success' => false, 'message' => translate('couldnt_remove_message',true)];
				if ($this->input->post('selected')) {
					foreach ($this->input->post('selected') as $id) {
						$this->{$this->model}->force_delete_relation($id);
					}
					$response = ['success' => true, 'message' => translate('successfully_remove_message',true)];
				}
				$this->template->json($response);
			}
		}
	}
	
	/**
	* public function restore($id)
	* Restore row record from database which id equals to $id.
	* @param integer $id
	*/

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
	
	/**
	* public function clean()
	* Clean records from database which trashed data.
	* @param integer $id
	*/

	public function clean()
	{
		$this->{$this->model}->force_delete_relation('all');
		redirect(site_url_multi($this->directory . $this->module_name));
	}
	

	public function changeStatus(){
		if ($this->input->method() == 'post') {
			$id = $this->input->post('id');
			if ($id){
				$row = $this->{$this->model}->filter(['id' => $id])->fields('status')->one();
				$status = ($row->status) ? 0 : 1;
				$this->{$this->model}->update(['status' => $status], ['id' => $id]);
				$this->template->json(['success' => 1]);
			}
		}
	}
}
