<?php defined('BASEPATH') or exit('No direct script access allowed');

ini_set('display_errors', false);

class Module extends Administrator_Controller
{
	public $module_setting;
	public $model;

	public function __construct()
	{
		parent::__construct();
		$this->module_setting = module_setting($this->module_name);

		if ($this->module_setting) {
			if ($this->module_setting->status == 1) {
				if (in_array($this->method, json_decode($this->module_setting->methods, true))) {
					if (check_permission($this->module_name, false)) {
						
						$this->data['module_name'] = $this->module_name;
						$this->model               = $this->module_name . '_model';
						$this->load->model('modules/'.$this->model);
						

						if (in_array($this->method, ['index', 'trash', 'create', 'edit', 'show'])) {
							$this->data['index_title']    = valid_lang(json_decode($this->module_setting->name)->{$this->method}->title);
							$this->data['title']          = valid_lang(json_decode($this->module_setting->name)->{$this->method}->title);
							$this->data['subtitle']       = valid_lang(json_decode($this->module_setting->name)->{$this->method}->subtitle);
							
							$this->breadcrumbs->push(valid_lang(json_decode($this->module_setting->name)->index->title), $this->directory.$this->module_name);
						}

						if ($this->method != 'index' && in_array($this->method, ['trash', 'create', 'edit', 'show'])) {
							$this->breadcrumbs->push($this->data['subtitle'], $this->module_name . '/' . $this->method);
						}

						
					} else {
						show_error('Sizin giriş icazəniz yoxdur');
					}
				} else {
					show_error('Method not exists');
				}
			} else {
				show_error('Modul aktiv deyil');
			}
		} else {
			show_error('Belə bir modul yoxdur');
		}
	}

	public function index()
	{
		$this->data['filter'] = [];
		if ($this->module_setting->search_field) {
			$this->data['search_field'] = [
				'property'    => 'search',
				'type'        => 'search',
				'name'        => $this->module_setting->search_field,
				'class'       => 'form-control',
				'value'       => $this->input->get($this->module_setting->search_field),
				'placeholder' => translate('search_placeholder', true)
			];

			if ($this->input->get($this->module_setting->search_field) != null) {
				$this->data['filter'][$this->module_setting->search_field . ' LIKE'] = "%" . $this->input->get($this->module_setting->search_field) . "%";
			}
		}

		$fieldss = json_decode($this->module_setting->fields, true);

		$custom_rows_data = [];
		if ($fieldss['general']) {
			foreach ($fieldss['general'] as $column => $general) {
				if ($general['show_on_table'] == 1) {
					$this->data['all_fields'][$column] = $general;

					if (isset($general['table_function']) && !empty($general['table_function'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> $general['table_function']['name'],
							'params'	=> isset($general['table_function']['params']) ? $general['table_function']['params'] : false
						];
					} elseif (($general['element'] == 'dropdown' || $general['element'] == 'dropdown_ajax') && isset($general['module']) && !empty($general['module'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_option',
							'params'	=> ['module' => $general['module']]
						];
					} elseif ($general['element'] == 'status') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_status',
							'params'	=> false
						];
					} elseif ($general['element'] == 'image') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_image',
							'params'	=> ['width' => 200, 'height' => 200]
						];
					}
				}
			}
		}
		
		if (isset($fieldss['translation']) && !empty($fieldss['translation'])) {
			foreach ($fieldss['translation'] as $column => $translation) {
				if ($translation['show_on_table'] == 1) {
					$this->data['all_fields'][$column] = $translation;

					if (isset($translation['table_function']) && !empty($translation['table_function'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> $translation['table_function']['name'],
							'params'	=> isset($translation['table_function']['params']) ? $translation['table_function']['params'] : false
						];
					} elseif ($translation['element'] == 'dropdown' && isset($translation['relation']) && !empty($translation['relation'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_option',
							'params'	=> ['table' => $translation['relation']['table'], 'key' => $translation['relation']['key'], 'value' => $translation['relation']['value']]
						];
					} elseif ($translation['element'] == 'status') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_status',
							'params'	=> false
						];
					} elseif ($translation['element'] == 'image') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_image',
							'params'	=> ['width' => 200, 'height' => 200]
						];
					}
				}
			}
		}

		//Show Fields

		if ($this->input->get('fields')) {
			$this->data['fields'] = $this->input->get('fields');
			$this->session->set_userdata($this->module_name . '_fields', $this->input->get('fields'));
		} elseif ($this->session->has_userdata($this->module_name . '_fields')) {
			$this->data['fields'] = $this->session->userdata($this->module_name . '_fields');
		} else {
			$this->data['fields'] = array_keys($this->data['all_fields']);
		}

		$columns = [];
		foreach ($this->data['fields'] as $field) {
			$columns[$field] = $this->data['all_fields'][$field];
		}

		if ($this->input->get('status') != null) {
			$this->data['filter']['status'] = $this->input->get('status');
		}

		//Content Language
		if ($this->input->get('language_id')) {
			$language_id = (int) $this->input->get('language_id');
			${$this->module_name . '_language_id'} = (int) $this->input->get('language_id');
			$this->session->set_userdata($this->module_name . '_language_id', ${$this->module_name . '_language_id'});
		} elseif ($this->session->has_userdata($this->module_name . '_language_id')) {
			$language_id = (int) $this->session->userdata($this->module_name . '_language_id');
		} elseif ($this->module_setting->language_id) {
			$language_id = (int) $this->module_setting->language_id;
		} else {
			$language_id = $this->data['current_lang_id'];
		}
		// End Content Language

		$default_sort = json_decode($this->module_setting->default_sort);

		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : $default_sort->column,
			'order'  => ($this->input->get('order')) ? $this->input->get('order') : $default_sort->sort
		];

		if ($this->module_setting->multilingual == 1) {
			if ($this->data['languages']) {
				foreach ($this->data['languages'] as $language) {
					$this->data['language_list_holder'][] = [
						'id'    => $language['id'],
						'name'  => $language['name'],
						'code'  => $language['code'],
						'count' => $this->{$this->model}->with_translation($language['id'])->count_rows($this->data['filter']),
						'class' => ($language_id == $language['id']) ? 'btn btn-primary' : 'btn btn-default'
					];
				}
			}
		}

		if ($this->module_setting->multilingual == 1) {
			$this->data['total_rows'] = $this->{$this->model}->with_translation()->count_rows($this->data['filter']);
		} else {
			$this->data['total_rows'] = $this->{$this->model}->count_rows($this->data['filter']);
		}
		
		$segment_array            = $this->uri->segment_array();
		$page                     = (ctype_digit(end($segment_array))) ? end($segment_array) : 1;


		if ($this->input->get('per_page')) {
			$per_page = (int) $this->input->get('per_page');
			${$this->module_name . '_per_page'} = (int) $this->input->get('per_page');
			$this->session->set_userdata($this->module_name . '_per_page', ${$this->module_name . '_per_page'});
		} elseif ($this->session->has_userdata($this->module_name . '_per_page')) {
			$per_page = $this->session->userdata($this->module_name . '_per_page');
		} else {
			$per_page = (int) $this->module_setting->per_page;
		}

		$this->data['limit'] = ['per_page' => $per_page, 'page' => $page];

		if($this->module_name == 'bank_account')
		{
			if($this->auth->is_member('vendor'))
			{
				$this->data['filter']['created_by'] = $this->auth->get_user()->id;
			}
		}
		
		
		if ($this->module_setting->multilingual == 1) {
			$rows = $this->{$this->model}->fields($this->data['fields'])->with_translation($language_id)->order_by($sort['column'], $sort['order'])->limit($per_page, $page-1)->all($this->data['filter']);
		} else {
			$rows = $this->{$this->model}->fields($this->data['fields'])->order_by($sort['column'], $sort['order'])->limit($per_page, $page-1)->all($this->data['filter']);
		}


		$actions = json_decode($this->module_setting->action);

		$action_buttons = [];

		if (check_permission($this->module_setting->slug, 'show')) {
			$action_buttons['show'] = true;
		}

		if (check_permission($this->module_setting->slug, 'edit')) {
			$action_buttons['edit'] = true;
		}

		if (check_permission($this->module_setting->slug, 'delete')) {
			$action_buttons['delete'] = true;
		}
		
		// Generate Table
		$this->wc_table->set_module(true);
		$this->wc_table->set_columns($columns);
		$this->wc_table->set_rows($rows);
		$this->wc_table->set_custom_rows($custom_rows_data);
		$this->wc_table->set_action($action_buttons);

		$this->data['table'] = $this->wc_table->generate();

		//Pagination
		$config['base_url']   = site_url_multi($this->directory . $this->module_name . '/index');
		$config['total_rows'] = $this->data['total_rows'];
		$config['per_page']   = $per_page;

		$this->pagination->initialize($config);
		$this->data['pagination'] = $this->pagination->create_links();

		if (in_array('create', json_decode($this->module_setting->methods, true))) {
			if (check_permission($this->module_name, 'create')) {
				$this->data['buttons'][] = [
					'type'  => 'a',
					'text'  => translate('header_button_create', true),
					'href'  => site_url($this->directory . $this->module_name . '/create'),
					'class' => 'btn btn-success btn-labeled heading-btn',
					'id'    => '',
					'icon'  => 'icon-plus-circle2'
				];
			}
		}

		if (in_array('delete', json_decode($this->module_setting->methods, true))) {
			if (check_permission($this->module_name, 'delete')) {
				$this->data['buttons'][] = [
					'type' => 'button',
					'text' => translate('header_button_delete', true),
					'class' => 'btn btn-danger btn-labeled heading-btn',
					'id' => 'deleteSelectedItems',
					'icon' => 'icon-trash',
					'additional' => [
						'data-href' => site_url($this->directory . $this->module_name . '/delete')
					]
				];
			}
		}

		// Sets Breadcrumb links
		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_all', true),
			'href' => site_url_multi($this->directory . $this->module_name),
			'icon_class' => 'icon-database position-left',
			'label_value' => $this->{$this->model}->count_rows(),
			'label_class' => 'label label-primary position-right',
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_active', true),
			'href' => site_url_multi($this->directory . $this->module_name).'?status=1',
			'icon_class' => 'icon-shield-check position-left',
			'label_value' => $this->{$this->model}->filter(['status' => 1])->count_rows(),
			'label_class' => 'label label-success position-right',
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_deactive', true),
			'href' => site_url_multi($this->directory . $this->module_name).'?status=0',
			'icon_class' => 'icon-shield-notice position-left',
			'label_value' => $this->{$this->model}->filter(['status' => 0])->count_rows(),
			'label_class' => 'label label-warning position-right',
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_trash', true),
			'href' => site_url_multi($this->directory . $this->module_name . '/trash'),
			'icon_class' => 'icon-trash position-left',
			'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
			'label_class' => 'label label-danger position-right',
		];

		$this->data['message'] = $this->session->flashdata('message');

		$this->template->render();
	}

	public function trash()
	{
		$this->data['filter'] = [];
		if ($this->module_setting->search_field) {
			$this->data['search_field'] = [
				'property'    => 'search',
				'type'        => 'search',
				'name'        => $this->module_setting->search_field,
				'class'       => 'form-control',
				'value'       => $this->input->get($this->module_setting->search_field),
				'placeholder' => translate('search_placeholder', true)
			];

			if ($this->input->get($this->module_setting->search_field) != null) {
				$this->data['filter'][$this->module_setting->search_field . ' LIKE'] = "%" . $this->input->get($this->module_setting->search_field) . "%";
			}
		}

		$fieldss = json_decode($this->module_setting->fields, true);
		$custom_rows_data = [];
		if ($fieldss['general']) {
			foreach ($fieldss['general'] as $column => $general) {
				if ($general['show_on_table'] == 1) {
					$this->data['all_fields'][$column] = $general;

					if (isset($general['table_function']) && !empty($general['table_function'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> $general['table_function']['name'],
							'params'	=> isset($general['table_function']['params']) ? $general['table_function']['params'] : false
						];
					} elseif ($general['element'] == 'dropdown' && isset($general['relation']) && !empty($general['relation'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_option',
							'params'	=> ['table' => $general['relation']['table'], 'key' => $general['relation']['key'], 'value' => $general['relation']['value']]
						];
					} elseif ($general['element'] == 'status') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_status',
							'params'	=> false
						];
					} elseif ($general['element'] == 'image') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_image',
							'params'	=> ['width' => 200, 'height' => 200]
						];
					}
				}
			}
		}
		
		if (isset($fieldss['translation']) && !empty($fieldss['translation'])) {
			foreach ($fieldss['translation'] as $column => $translation) {
				if ($translation['show_on_table'] == 1) {
					$this->data['all_fields'][$column] = $translation;

					if (isset($translation['table_function']) && !empty($translation['table_function'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> $translation['table_function']['name'],
							'params'	=> isset($translation['table_function']['params']) ? $translation['table_function']['params'] : false
						];
					} elseif ($translation['element'] == 'dropdown' && isset($translation['relation']) && !empty($translation['relation'])) {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_option',
							'params'	=> ['table' => $translation['relation']['table'], 'key' => $translation['relation']['key'], 'value' => $translation['relation']['value']]
						];
					} elseif ($translation['element'] == 'status') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_status',
							'params'	=> false
						];
					} elseif ($translation['element'] == 'image') {
						$custom_rows_data[] = [
							'column'	=> $column,
							'callback'	=> 'get_image',
							'params'	=> ['width' => 200, 'height' => 200]
						];
					}
				}
			}
		}

		//Show Fields

		if ($this->input->get('fields')) {
			$this->data['fields'] = $this->input->get('fields');
			$this->session->set_userdata($this->module_name . '_fields', $this->input->get('fields'));
		} elseif ($this->session->has_userdata($this->module_name . '_fields')) {
			$this->data['fields'] = $this->session->userdata($this->module_name . '_fields');
		} else {
			$this->data['fields'] = array_keys($this->data['all_fields']);
		}

		$columns = [];
		foreach ($this->data['fields'] as $field) {
			$columns[$field] = $this->data['all_fields'][$field];
		}

		if ($this->input->get('status') != null) {
			$this->data['filter']['status'] = $this->input->get('status');
		}

		//Content Language
		if ($this->input->get('language_id')) {
			$language_id = (int) $this->input->get('language_id');
			${$this->module_name . '_language_id'} = (int) $this->input->get('language_id');
			$this->session->set_userdata($this->module_name . '_language_id', ${$this->module_name . '_language_id'});
		} elseif ($this->session->has_userdata($this->module_name . '_language_id')) {
			$language_id = (int) $this->session->userdata($this->module_name . '_language_id');
		} elseif ($this->module_setting->language_id) {
			$language_id = (int) $this->module_setting->language_id;
		} else {
			$language_id = $this->data['current_lang_id'];
		}
		// End Content Language

		$default_sort = json_decode($this->module_setting->default_sort);

		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : $default_sort->column,
			'order'  => ($this->input->get('order')) ? $this->input->get('order') : $default_sort->sort
		];

		if ($this->module_setting->multilingual == 1) {
			if ($this->data['languages']) {
				foreach ($this->data['languages'] as $language) {
					$this->data['language_list_holder'][] = [
						'id'    => $language['id'],
						'name'  => $language['name'],
						'code'  => $language['code'],
						'count' => $this->{$this->model}->with_translation($language['id'])->only_trashed()->count_rows($this->data['filter']),
						'class' => ($language_id == $language['id']) ? 'btn btn-success' : 'btn btn-default'
					];
				}
			}
		}

		if ($this->module_setting->multilingual == 1) {
			$this->data['total_rows'] = $this->{$this->model}->with_translation()->only_trashed()->count_rows($this->data['filter']);
		} else {
			$this->data['total_rows'] = $this->{$this->model}->only_trashed()->count_rows($this->data['filter']);
		}
		
		$segment_array            = $this->uri->segment_array();
		$page                     = (ctype_digit(end($segment_array))) ? end($segment_array) : 1;


		if ($this->input->get('per_page')) {
			$per_page = (int) $this->input->get('per_page');
			${$this->module_name . '_per_page'} = (int) $this->input->get('per_page');
			$this->session->set_userdata($this->module_name . '_per_page', ${$this->module_name . '_per_page'});
		} elseif ($this->session->has_userdata($this->module_name . '_per_page')) {
			$per_page = $this->session->userdata($this->module_name . '_per_page');
		} else {
			$per_page = (int) $this->module_setting->per_page;
		}

		$this->data['limit'] = ['per_page' => $per_page, 'page' => $page];

		if ($this->module_setting->multilingual == 1) {
			$rows = $this->{$this->model}->fields($this->data['fields'])->with_translation($language_id)->only_trashed()->order_by($sort['column'], $sort['order'])->limit($per_page, $page-1)->all($this->data['filter']);
		} else {
			$rows = $this->{$this->model}->fields($this->data['fields'])->only_trashed()->order_by($sort['column'], $sort['order'])->limit($per_page, $page-1)->all($this->data['filter']);
		}


		$actions = json_decode($this->module_setting->action);

		$action_buttons = [];

		if (check_permission($this->module_setting->slug, 'restore')) {
			$action_buttons['restore'] = true;
		}

		if (check_permission($this->module_setting->slug, 'remove')) {
			$action_buttons['remove'] = true;
		}

		
		// Generate Table
		$this->wc_table->set_module(true);
		$this->wc_table->set_columns($columns);
		$this->wc_table->set_rows($rows);
		$this->wc_table->set_custom_rows($custom_rows_data);
		$this->wc_table->set_action($action_buttons);

		$this->data['table'] = $this->wc_table->generate();

		//Pagination
		$config['base_url']   = site_url_multi($this->directory . $this->module_name . '/trash');
		$config['total_rows'] = $this->data['total_rows'];
		$config['per_page']   = $per_page;

		$this->pagination->initialize($config);
		$this->data['pagination'] = $this->pagination->create_links();

		if (in_array('remove', json_decode($this->module_setting->methods, true))) {
			if (check_permission($this->module_name, 'remove')) {
				$this->data['buttons'][] = [
					'type' => 'button',
					'text' => translate('header_button_delete_permanently', true),
					'class' => 'btn btn-warning btn-labeled heading-btn',
					'id' => 'removeSelectedItems',
					'icon' => 'icon-trash',
					'additional' => [
						'data-href' => site_url($this->directory . $this->module_name . '/remove')
					]
				];
			}
		}

		if (in_array('restore', json_decode($this->module_setting->methods, true))) {
			if (check_permission($this->module_name, 'restore')) {
				$this->data['buttons'][] = [
					'type' => 'button',
					'text' => translate('header_button_restore', true),
					'class' => 'btn btn-primary btn-labeled heading-btn',
					'id' => 'restoreSelectedItems',
					'icon' => 'icon-loop',
					'additional' => [
						'data-href' => site_url($this->directory . $this->module_name . '/restore')
					]
				];
			}
		}

		if (in_array('clean', json_decode($this->module_setting->methods, true))) {
			if (check_permission($this->module_name, 'clean')) {
				$this->data['buttons'][] = [
					'type' => 'a',
					'text' => translate('header_button_clean', true),
					'href' => site_url($this->directory . $this->module_name . '/clean'),
					'class' => 'btn btn-danger btn-labeled heading-btn',
					'icon' => 'icon-eraser2',
					'id' => ''
				];
			}
		}


		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_all', true),
			'href' => site_url_multi($this->directory . $this->module_name),
			'icon_class' => 'icon-database position-left',
			'label_value' => $this->{$this->model}->count_rows(),
			'label_class' => 'label label-primary position-right',
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_active', true),
			'href' => site_url_multi($this->directory . $this->module_name).'?status=1',
			'icon_class' => 'icon-shield-check position-left',
			'label_value' => $this->{$this->model}->filter(['status' => 1])->count_rows(),
			'label_class' => 'label label-success position-right',
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_deactive', true),
			'href' => site_url_multi($this->directory . $this->module_name).'?status=0',
			'icon_class' => 'icon-shield-notice position-left',
			'label_value' => $this->{$this->model}->filter(['status' => 0])->count_rows(),
			'label_class' => 'label label-warning position-right',
		];

		$this->data['breadcrumb_links'][] = [
			'text' => translate('breadcrumb_link_trash', true),
			'href' => site_url_multi($this->directory . $this->module_name . '/trash'),
			'icon_class' => 'icon-trash position-left',
			'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
			'label_class' => 'label label-danger position-right',
		];

		$this->data['message'] = $this->session->flashdata('message');

		$this->template->render($this->controller.'/index');
	}

	public function create()
	{
		$form_fields = json_decode($this->module_setting->fields);
		if (isset($form_fields->translation)) {
			foreach ($form_fields->translation as $translation_field) {
				if ($translation_field->show_on_form == 1) {
					foreach ($this->data['languages'] as $language) {
						if (!isset($translation_field->label->{$language['code']})) {

							$label = (isset($translation_field->label->{$this->data['current_lang']})) ? $translation_field->label->{$this->data['current_lang']} : $translation_field->label->{$this->data['default_language']};

							$translation_field->label->{$language['code']} = $label;
						}

						if (isset($translation_field->placeholder->{$language['code']})) {

							$placeholder = (isset($translation_field->placeholder->{$this->data['current_lang']})) ? $translation_field->placeholder->{$this->data['current_lang']} : $translation_field->placeholder->{$this->data['default_language']};

							$translation_field->placeholder->{$language['code']} = $placeholder;
						}

						if (in_array($translation_field->element, ['text', 'slug', 'password', 'email', 'number', 'tel', 'url', 'range', 'search', 'date', 'datetime', 'time', 'color', 'month', 'week', 'submit', 'reset', 'button', 'radio', 'checkbox', 'textarea'])) {
							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'    	=> $translation_field->element,
								'name'        	=> 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'class'       	=> $translation_field->class,
								'value'       	=> set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'label'       	=> $translation_field->label->{$language['code']},
								'placeholder' 	=> valid_lang($translation_field->placeholder)
							];
							if($translation_field->element == 'slug'){
								$this->data['form_field']['translation'][$language['id']][$translation_field->column]['data-for'] = $translation_field->for; 
								$this->data['form_field']['translation'][$language['id']][$translation_field->column]['data-type'] = 'translation'; 
								$this->data['form_field']['translation'][$language['id']][$translation_field->column]['data-lang-id'] = $language['id']; 
							}
						} elseif ($translation_field->element == 'image') {
							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'    => 'image',
								'id'		  => 'input-image'.$language['id'],
								'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'value'       => set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'src'         => $this->Model_tool_image->resize(set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'), 200, 200),
								'label'       => $translation_field->label->{$language['code']},
								'placeholder' => $this->Model_tool_image->resize($translation_field->placeholder->{$language['code']}, 200, 200)
							];
						} elseif ($translation_field->element == 'file') {
							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'    => 'file',
								'id'		  => 'input-image'.$language['id'],
								'class'		  => 'form-control',
								'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'value'       => set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'label'       => $translation_field->label->{$language['code']},
								'placeholder' => ''
							];
						} elseif ($translation_field->element == 'multiselect') {
							$options = [];
							if ($translation_field->module) {
								$options = $this->generate_options($translation_field->module);
							}

							$selected = [];
							if ($this->input->post($translation_field->column)) {
								foreach ($this->input->post($translation_field->column) as $select) {
									$selected[] = $select;
								}
							}

							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'    	=> 'multiselect',
								'name'        	=> 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'class'      	=> 'multiselect-select-all-filtering',
								'value'       	=> set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'label'      	=> $translation_field->label->{$language['code']},
								'options'    	=> $options,
								'selected'  	=> $selected,
								'placeholder' 	=> $translation_field->placeholder->{$language['code']}
							];
						} elseif ($translation_field->element == 'multiselect_ajax') {
							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'   => 'multiselect_ajax',
								'type'		 => 'translation',
								'element'	 =>  $translation_field->column,
								'name'       => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'id'	 	 => 'translation_' . $language['id'] . '_' . $translation_field->column,
								'class'		 => $translation_field->class,
								'label'      => $label,
								'placeholder'=> $placeholder,
								'selected'   => set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'selected_elements' => [],
								'selected_text' => ""
							];
		
							if($this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected']) {
								$selected_value =  $this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected'];
								$selected_elements = $this->get_selected_elments($translation_field->module->name, $translation_field->module->key, $translation_field->module->columns, $selected_value, $translation_field->module->translation, $translation_field->module->dynamic);
								$this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected_elements'] = $selected_elements;
							}
						} elseif ($translation_field->element == 'dropdown') {
							$options = [];
							if ($translation_field->module) {
								$options = $this->generate_options($translation_field->module);
							}

							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'    => 'dropdown',
								'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'data-style' => 'btn-default btn-xs',
								'data-width' => '100%',
								'class'       => isset($translation_field->class) ?  $translation_field->class : 'bootstrap-select',
								'options'    => $options,
								'selected'   => set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'label'       => $translation_field->label->{$language['code']},
								'placeholder' => $translation_field->placeholder->{$language['code']}
							];
						} elseif ($translation_field->element == 'dropdown_ajax') {
							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'   => 'dropdown-ajax',
								'type'		 => 'translation',
								'element'	 => $translation_field->column,
								'name'       => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'id'	 	 => 'translation_' . $language['id'] . '_' . $translation_field->column,
								'class'		 => $translation_field->class,
								'label'      => $translation_field->label->{$language['code']},
								'placeholder'=> $placeholder,
								'selected'   => set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'selected_text' => ""
							];
		
							if($this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected']) {
								$selected_value =  $this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected'];
								$selected_element = $this->get_selected_element($translation_field->module,$selected_value);
								$this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected_text'] = $selected_element;
							}

						} elseif ($translation_field->element == 'status') {
							$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
								'property'    => 'status',
								'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
								'class'      => 'bootstrap-select',
								'data-style' => 'btn-default btn-xs',
								'data-width' => '100%',
								'selected'   => set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'),
								'label'       => $translation_field->label->{$language['code']},
								'placeholder' => $translation_field->placeholder->{$language['code']}
							];
						}

						$rule_array = [];
						foreach ($translation_field->rules as $rules) {
							if (isset($rules->rules_parametr) && !empty($rules->rules_parametr)) {
								$rule_array[] = $rules->rules . '[' . $rules->rules_parametr . ']';
							} else {
								$rule_array[] = $rules->rules;
							}
						}
						$form_rule = implode('|', $rule_array);

						if (!empty($form_rule)) {

							$label = (isset($translation_field->label->{$this->data['current_lang']})) ? $translation_field->label->{$this->data['current_lang']} : $translation_field->label->{$this->data['default_language']};

							$this->form_validation->set_rules('translation[' . $language["id"] . '][' . $translation_field->column . ']', $label, $form_rule);
						}
						
						unset($rule_array);
					}
				}
			}
		}
		
		foreach ($form_fields->general as $general_field) {
			if ($general_field->show_on_form == 1) {
				$label = (isset($general_field->label->{$this->data['current_lang']})) ? $general_field->label->{$this->data['current_lang']} : $general_field->label->{$this->data['default_language']};
				$placeholder = (isset($general_field->placeholder->{$this->data['current_lang']})) ? $general_field->placeholder->{$this->data['current_lang']} : $general_field->placeholder->{$this->data['default_language']};

				if (in_array($general_field->element, ['text', 'slug', 'password', 'email', 'number', 'tel', 'url', 'range', 'search', 'date', 'datetime', 'time', 'color', 'month', 'week', 'submit', 'reset', 'button', 'radio', 'textarea'])) {
					$this->data['form_field']['general'][$general_field->column] = [
						'property'    => $general_field->element,
						'name'        => $general_field->column,
						'class'       => $general_field->class,
						'value'       => set_value($general_field->column),
						'label'       => $label,
						'placeholder' => $placeholder,
					];
					
					if($general_field->element == 'slug'){
						$this->data['form_field']['general'][$general_field->column]['data-for'] = $general_field->for; 
						$this->data['form_field']['general'][$general_field->column]['data-type'] = 'general';
					}
				} elseif ($general_field->element == 'checkbox') {
					$this->data['form_field']['general'][$general_field->column] = [
						'property'    => 'checkbox',
						'name'        => $general_field->column,
						'class'       => $general_field->class,
						'checked'	  => (set_value($general_field->column)) ? true : false,
						'value'       => (isset($general_field->checked_value)) ? $general_field->checked_value : 1,
						'label'       => $label,
						'placeholder' => $placeholder,
					];
				} elseif ($general_field->element == 'image') {
					$this->data['form_field']['general'][$general_field->column] = [
						'property'    => 'image',
						'id'          => 'input-'.$general_field->column,
						'name'        => $general_field->column,
						'value'       => set_value($general_field->column),
						'src'         => (set_value($general_field->column)) ? $this->Model_tool_image->resize(set_value($general_field->column), 200, 200) : $this->Model_tool_image->resize($general_field->placeholder->{$this->data['current_lang']}, 200, 200),
						'label'       => $label,
						'placeholder' => $this->Model_tool_image->resize($placeholder, 200, 200),
					];
				} elseif ($general_field->element == 'file') {
					$this->data['form_field']['general'][$general_field->column] = [
						'property'    => 'file',
						'id'          => 'input-'.$general_field->column,
						'class'       => 'form-control',
						'name'        => $general_field->column,
						'value'       => set_value($general_field->column),
						'label'       => $label,
						'placeholder' => $placeholder
					];
				} elseif ($general_field->element == 'multiselect') {
					$options = [];
					if($general_field->module) {
						$options = $this->generate_options($general_field->module);
					} elseif($general_field->options) {
						foreach ($general_field->options as $option) {
							$options[$option->key] = $option->value;
						}
					}

					$selected = [];
					if ($this->input->post($general_field->column)) {
						foreach ($this->input->post($general_field->column) as $select) {
							$selected[] = $select;
						}
					}

					$this->data['form_field']['general'][$general_field->column] = [
						'property'   => 'multiselect',
						'name'       => $general_field->column . '[]',
						'id'         => $general_field->column,
						'label'      => $label,
						'class'      => 'multiselect-select-all-filtering',
						'options'    => $options,
						'selected'   => $selected,
					];

				} elseif ($general_field->element == 'multiselect_ajax') {
					$this->data['form_field']['general'][$general_field->column] = [
						'property'   => 'multiselect_ajax',
						'type'		 => 'general',
						'element'	 => $general_field->column,
						'name'       => $general_field->column,
						'id'         => $general_field->column,
						'class'		 => $general_field->class,
						'label'      => $label,
						'placeholder'=> $placeholder,
						'selected'   => set_value($general_field->column),
						'selected_elements' => [],
						'selected_text' => ""
					];

					if($this->data['form_field']['general'][$general_field->column]['selected']) {
						$selected_value =  $this->data['form_field']['general'][$general_field->column]['selected'];
						$selected_elements = $this->get_selected_elments($general_field->module,$selected_value);
						$this->data['form_field']['general'][$general_field->column]['selected_elements'] = $selected_elements;
					}

				} elseif ($general_field->element == 'dropdown') {
					$options = [];
					if(isset($general_field->module)) {
						$options = $this->generate_options($general_field->module);
					} elseif($general_field->options) {
						foreach ($general_field->options as $option) {
							$options[$option->key] = $option->value;
						}
					}

					$this->data['form_field']['general'][$general_field->column] = [
						'property'   => 'dropdown',
						'name'       => $general_field->column,
						'id'         => $general_field->column,
						'label'      => $label,
						'class'      => (isset($general_field->class)) ? $general_field->class : 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'options'    => $options,
						'selected'   => set_value($general_field->column)
					];
				} elseif($general_field->element == 'dropdown_ajax'){
					$this->data['form_field']['general'][$general_field->column] = [
						'property'   => 'dropdown-ajax',
						'type'		 => 'general',
						'element'	 => $general_field->column,
						'name'       => $general_field->column,
						'id'         => $general_field->column,
						'class'		 => $general_field->class,
						'label'      => $label,
						'placeholder'=> $placeholder,
						'selected'   => set_value($general_field->column),
						'selected_text' => ""
					];

					if($this->data['form_field']['general'][$general_field->column]['selected']) {
						$selected_value =  $this->data['form_field']['general'][$general_field->column]['selected'];
						$selected_element = $this->get_selected_element($general_field->module,$selected_value);
						$this->data['form_field']['general'][$general_field->column]['selected_text'] = $selected_element;
					}

				} elseif ($general_field->element == 'status') {

					$this->data['form_field']['general'][$general_field->column] = [
						'property'   => 'status',
						'name'       => $general_field->column,
						'id'         => $general_field->column,
						'label'      => $label,
						'class'      => 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'selected'   => set_value($general_field->column)
					];
				}

				$rule_array = [];
				foreach ($general_field->rules as $rules) {
					if (isset($rules->rules_parametr) && !empty($rules->rules_parametr)) {
						$rule_array[] = $rules->rules . '[' . $rules->rules_parametr . ']';
					} else {
						$rule_array[] = $rules->rules;
					}
				}

				$form_rule = implode('|', $rule_array);

				if (!empty($form_rule)) {
					$this->form_validation->set_rules($general_field->column, $label, $form_rule);
				}
				unset($rule_array);
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
			if ($this->form_validation->run() == true) {
				$general = [];
				if (isset($form_fields->general) && !empty($form_fields->general)) {
					foreach ($form_fields->general as $general_field) {
						if ($general_field->element == 'multiselect') {
							$general[$general_field->column] = implode(',', $this->input->post($general_field->column));
						} elseif($general_field->element == 'checkbox') {
							if($this->input->post($general_field->column)) {
								$general[$general_field->column] = (isset($general_field->checked_value)) ? $general_field->checked_value : 1;
							} else {
								$general[$general_field->column] = (isset($general_field->unchecked_value)) ? $general_field->unchecked_value : 0;
							}
						} else {
							$general[$general_field->column] = $this->input->post($general_field->column);
						}
					}
				}
				if (!empty($general)) {
					$id = $this->{$this->model}->insert($general);
					if ($this->module_setting->multilingual == 1) {
						foreach ($this->input->post('translation') as $language_id => $translation_fields) {
							$translation_data[$this->module_setting->slug . '_id'] = $id;
							$translation_data['language_id'] = $language_id;
							foreach ($translation_fields as $translation_field_key => $translation_field_value) {
								$translation_data[$translation_field_key] = $translation_field_value;
							}
							$this->{$this->model}->insert_translation($translation_data);
						}
					}
					
					$this->session->set_flashdata('message', ['type' => 'success', 'text' => translate('success_create_message', true)]);

					redirect(site_url_multi($this->directory . $this->module_name));

				} else {
					$this->data['message'] = translate('error_warning', true);
				}
			} else {
				$this->data['message'] = translate('error_warning', true);
			}
		}
		
		$this->data['item_id'] = 0;
		$this->template->render($this->controller.'/form');
	}

    public function get_categories() {
        $categories = $this->Category_model->fields('id, name, slug')->filter(['status' => 1, 'parent' => 0, 'top' => 1])->with_translation()->order_by(['sort' => 'ASC'])->as_array()->all();
        $i=0;
        if($categories) {
            foreach($categories as $category) {
                $categories[$i]['sub_categories'] = $this->sub_categories($category['id']);
                $i++;
            }
        }
        return $categories;
    }

    public function sub_categories($id) {
        $categories = $this->Category_model->fields('id, name, slug')->filter(['status' => 1, 'parent' => $id, 'top' => 1])->with_translation()->order_by(['sort' => 'ASC'])->as_array()->all();
        $i=0;
        if($categories)
        {
            foreach($categories as $category) {
                $categories[$i]['sub_categories'] = $this->sub_categories($category['id']);
                $i++;
            }
            return $categories;
        } else {
            return [];
        }


    }

	public function edit($id = false)
	{
		if ($id) {

			$row = $this->{$this->model}->filter(['id' => $id])->one();

			if ($row) {
				$form_fields = json_decode($this->module_setting->fields);
				
				if ($this->module_setting->multilingual == 1) {
					if (isset($form_fields->translation) && !empty($form_fields->translation)) {
						foreach ($form_fields->translation as $translation_field) {
							if ($translation_field->show_on_form == 1) {
								foreach ($this->data['languages'] as $language) {
									$row_translation = $this->{$this->model}->filter([$this->module_name.'_id' => $id])->with_translation($language['id'])->one();

									$label = (isset($translation_field->label->{$this->data['current_lang']})) ? $translation_field->label->{$this->data['current_lang']} : $translation_field->label->{$this->data['default_language']};
									
									$placeholder = (isset($translation_field->placeholder->{$this->data['current_lang']})) ? $translation_field->placeholder->{$this->data['current_lang']} : $translation_field->placeholder->{$this->data['default_language']};

									if (!isset($translation_field->label->{$language['code']})) {
										$translation_field->label->{$language['code']} = $label;
									}
	
									if (!isset($translation_field->placeholder->{$language['code']})) {
										$translation_field->placeholder->{$language['code']} = $placeholder;
									}
	
									if (in_array($translation_field->element, ['text', 'slug', 'password', 'email', 'number', 'tel', 'url', 'range', 'search', 'date', 'datetime', 'time', 'color', 'month', 'week', 'submit', 'reset', 'button', 'radio', 'checkbox', 'textarea'])) {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => $translation_field->element,
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'class'       => $translation_field->class,
											'value'       => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
										if($translation_field->element == 'slug'){
											$this->data['form_field']['translation'][$language['id']][$translation_field->column]['data-for'] = $translation_field->for; 
											$this->data['form_field']['translation'][$language['id']][$translation_field->column]['data-type'] = 'translation'; 
											$this->data['form_field']['translation'][$language['id']][$translation_field->column]['data-lang-id'] = $language['id']; 
										}
									} elseif ($translation_field->element == 'image') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'image',
											'id'		  => 'input-image'.$language['id'],
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'value'       => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'src'         => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? $this->Model_tool_image->resize(set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'), 200, 200) : ($this->Model_tool_image->resize($row_translation->{$translation_field->column}, 200, 200)) ? $this->Model_tool_image->resize($row_translation->{$translation_field->column}, 200, 200) : $this->Model_tool_image->resize($placeholder, 200, 200),
											'label'       => $label,
											'placeholder' => $this->Model_tool_image->resize($placeholder, 200, 200)
										];
									} elseif ($translation_field->element == 'file') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'file',
											'id'		  => 'input-image'.$language['id'],
											'class'		  => 'form-control',
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'value'       => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
									} elseif ($translation_field->element == 'multiselect') {
										$options = [];
										if($translation_field->module) {
											$options = $this->generate_options($translation_field->module);
										} elseif($translation_field->options) {
											foreach ($translation_field->options as $option) {
												$options[$option->key] = $option->value;
											}
										}

										$selected = [];
										if ($this->input->post($translation_field->column)) {
											foreach ($this->input->post($translation_field->column) as $select) {
												$selected[] = $select;
											}
										} elseif ($row->{$translation_field->column}) {
											$selected = explode(',', $row->{$translation_field->column});
										}

										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    	=> 'multiselect',
											'name'        	=> 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'class'      	=> 'multiselect-select-all-filtering',
											'value'       	=> (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'      	=> $label,
											'options'    	=> $options,
											'selected'  	=> $selected,
											'placeholder' 	=> $placeholder
										];
									} elseif ($translation_field->element == 'multiselect_ajax') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'   => 'multiselect_ajax',
											'type'   	 => 'translation',
											"element"	 => $translation_field->column,
											'name'       => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'id'	 	 => 'translation_' . $language['id'] . '_' . $translation_field->column,
											'class'		 => $translation_field->class,
											'label'      => $label,
											'placeholder'=> $placeholder,
											'selected'   => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'selected_elements' => [],
											'selected_text' => ""
										];
					
										if($this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected']) {
											$selected_value =  $this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected'];
											$selected_elements = $this->get_selected_elments($translation_field->module, $selected_value);
											$this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected_elements'] = $selected_elements;
										}
									} elseif ($translation_field->element == 'dropdown') {
										$options = [];
										if($translation_field->module) {
											$options = $this->generate_options($translation_field->module);
										} elseif($translation_field->options) {
											foreach ($translation_field->options as $option) {
												$options[$option->key] = $option->value;
											}
										}

										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'dropdown',
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'data-style'  => 'btn-default btn-xs',
											'data-width'  => '100%',
											'class'       => (isset($translation_field->column)) ? $translation_field->column : 'bootstrap-select',
											'options'     => $options,
											'selected'    => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
									} elseif ($translation_field->element == 'dropdown_ajax') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'   => 'dropdown-ajax',
											'type'		 => 'translation',
											'element'	 => $translation_field->column,
											'dynamic'	 => $translation_field->module->dynamic,
											'module'	 => $translation_field->module->name,
											'translation'=> $translation_field->module->translation,
											'key'		 => $translation_field->module->key,
											'columns'	 => $translation_field->module->columns,
											'name'       => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'id'	 	 => 'translation_' . $language['id'] . '_' . $translation_field->column,
											'class'		 => $translation_field->class,
											'label'      => $translation_field->label->{$language['code']},
											'placeholder'=> $placeholder,
											'selected'   => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'selected_text' => ""
										];
					
										if($this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected']) {
											$selected_value =  $this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected'];
											$selected_element = $this->get_selected_element($translation_field->module, $selected_value);
											$this->data['form_field']['translation'][$language['id']][$translation_field->column]['selected_text'] = $selected_element;
										}
			
									}elseif ($translation_field->element == 'status') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'status',
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'class'       => 'bootstrap-select',
											'data-style'  => 'btn-default btn-xs',
											'data-width'  => '100%',
											'selected'    => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
									}

									$rule_array = [];
									foreach ($translation_field->rules as $rules) {
										if (isset($rules->rules_parametr) && !empty($rules->rules_parametr)) {
											$rule_array[] = $rules->rules . '[' . $rules->rules_parametr . ']';
										} else {
											$rule_array[] = $rules->rules;
										}
									}
									$form_rule = implode('|', $rule_array);

									if (!empty($form_rule)) {
										$this->form_validation->set_rules('translation[' . $language["id"] . '][' . $translation_field->column . ']', $label, $form_rule);
									}
									unset($rule_array);
								}
							}
						}
					}
				}

				if (isset($form_fields->general) && !empty($form_fields->general)) {
					foreach ($form_fields->general as $general_field) {                        
						if ($general_field->show_on_form == 1) {
							$label = (isset($general_field->label->{$this->data['current_lang']})) ? $general_field->label->{$this->data['current_lang']} : $general_field->label->{$this->data['default_language']};
							$placeholder = (isset($general_field->placeholder->{$this->data['current_lang']})) ? $general_field->placeholder->{$this->data['current_lang']} : $general_field->placeholder->{$this->data['default_language']};

							if (in_array($general_field->element, ['text', 'slug', 'password', 'email', 'number', 'tel', 'url', 'range', 'search', 'date', 'datetime', 'time', 'color', 'month', 'week', 'submit', 'reset', 'button', 'radio', 'textarea'])) {
								$this->data['form_field']['general'][$general_field->column] = [
									'property'    => $general_field->element,
									'name'        => $general_field->column,
									'class'       => $general_field->class,
									'value'       => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column},
									'label'       => $label,
									'placeholder' => $placeholder
								];
								if($general_field->element == 'slug'){
									$this->data['form_field']['general'][$general_field->column]['data-for'] = $general_field->for; 
									$this->data['form_field']['general'][$general_field->column]['data-type'] = 'general';
								}
							} elseif ($general_field->element == 'checkbox') {
								$this->data['form_field']['general'][$general_field->column] = [
									'property'    => 'checkbox',
									'name'        => $general_field->column,
									'class'       => $general_field->class,
									'checked'	  => (set_value($general_field->column)) ? true : $row->{$general_field->column},
									'value'       => (isset($general_field->checked_value)) ? $general_field->checked_value : 1,
									'label'       => $label,
									'placeholder' => $placeholder,
								];
								if(isset($general_field->checked_value) && isset($general_field->unchecked_value)) {
									if(!$this->input->post($general_field->column)) {
										if($row->{$general_field->column} == $general_field->checked_value) {
											$this->data['form_field']['general'][$general_field->column]['checked'] = true;
										} else {
											$this->data['form_field']['general'][$general_field->column]['checked'] = false;
										}
									}
								}
							} elseif ($general_field->element == 'image') {
								$this->data['form_field']['general'][$general_field->column] = [
									'property'    => 'image',
									'id'          => 'input-'.$general_field->column,
									'name'        => $general_field->column,
									'value'       => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column},
									'src'         => (set_value($general_field->column)) ? $this->Model_tool_image->resize(set_value($general_field->column), 200, 200) : ($this->Model_tool_image->resize($row->{$general_field->column}, 200, 200)) ? $this->Model_tool_image->resize($row->{$general_field->column}, 200, 200) : $this->Model_tool_image->resize($placeholder, 200, 200),
									'label'       => $label,
									'placeholder' => $this->Model_tool_image->resize($placeholder, 200, 200)
								];
							} elseif ($general_field->element == 'file') {
								$this->data['form_field']['general'][$general_field->column] = [
									'property'    => 'file',
									'id'          => 'input-'.$general_field->column,
									'class'       => 'form-control',
									'name'        => $general_field->column,
									'value'       => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column},
									'label'       => $label,
									'placeholder' => $placeholder
								];
							} elseif ($general_field->element == 'multiselect') {
								$options = [];

								if($general_field->module) {
									$options = $this->generate_options($general_field->module);
								} elseif($general_field->options) {
									foreach ($general_field->options as $option) {
										$options[$option->key] = $option->value;
									}
								}

								$selected = [];
								if ($this->input->post($general_field->column)) {
									foreach ($this->input->post($general_field->column) as $select) {
										$selected[] = $select;
									}
								} elseif ($row->{$general_field->column}) {
									$selected = explode(',', $row->{$general_field->column});
								}

								$this->data['form_field']['general'][$general_field->column] = [
									'property'   => 'multiselect',
									'name'       => $general_field->column . '[]',
									'id'         => $general_field->column,
									'label'      => $label,
									'class'      => 'multiselect-select-all-filtering',
									'options'    => $options,
									'selected'   => $selected
								];
							} elseif ($general_field->element == 'multiselect_ajax') {
								$this->data['form_field']['general'][$general_field->column] = [
									'property'   => 'multiselect_ajax',
									'type'		 => "general",
									'element'    => $general_field->column,
									'name'       => $general_field->column,
									'id'         => $general_field->column,
									'class'		 => $general_field->class,
									'label'      => $label,
									'placeholder'=> $placeholder,
									'selected'   => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column},
									'selected_elements' => [],
									'selected_text' => ""
								];
			
								if($this->data['form_field']['general'][$general_field->column]['selected']) {
									$selected_value =  $this->data['form_field']['general'][$general_field->column]['selected'];
									$selected_elements = $this->get_selected_elments($general_field->module, $selected_value);
									$this->data['form_field']['general'][$general_field->column]['selected_elements'] = $selected_elements;
								}
			
							} elseif ($general_field->element == 'dropdown') {
								$options = [];


								if(isset($general_field->module)) {

									$options = $this->generate_options($general_field->module);


									/*
									 * Test code block will check it again
									 * */
                                    if(true) {

                                        if($general_field->column == 'parent' && $this->module_name == 'category') {

                                            $tmp = [0 => ''];

                                            $parents = $this->get_categories();

                                            foreach ($parents as $parent) {

                                                $tmp[$parent['id']] = $parent['name'];

                                                if(isset($parent['sub_categories']) && count($parent['sub_categories'])) {

                                                    foreach ($parent['sub_categories'] as $child) {

                                                        $tmp[$child['id']] = str_repeat('&nbsp;', 3) . '-' . $child['name'];

                                                    }

                                                }

                                            }

                                            $options = $tmp;
                                        }

                                    }

								} elseif($general_field->options) {
									foreach ($general_field->options as $option) {
										$options[$option->key] = $option->value;
									}
								}

								$this->data['form_field']['general'][$general_field->column] = [
									'property'   => 'dropdown',
									'name'       => $general_field->column,
									'id'         => $general_field->column,
									'label'      => $label,
									'class'      => (isset($general_field->class)) ? $general_field->class : 'bootstrap-select',
									'data-style' => 'btn-default btn-xs',
									'data-width' => '100%',
									'options'    => $options,
									'selected'   => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column}
								];
							} elseif ($general_field->element == 'dropdown_ajax') {
								$this->data['form_field']['general'][$general_field->column] = [
									'property'   => 'dropdown-ajax',
									'type'		 => 'general',
									'element'	 => $general_field->column,
									'dynamic'	 => $general_field->module->dynamic,
									'module'	 => $general_field->module->name,
									'translation'=> $general_field->module->translation,
									'key'		 => $general_field->module->key,
									'columns'	 => $general_field->module->columns,
									'name'       => $general_field->column,
									'id'         => $general_field->column,
									'class'		 => $general_field->class,
									'label'      => $label,
									'placeholder'=> $placeholder,
									'selected'   => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column},
									'selected_text' => ""
								];
			
								if($this->data['form_field']['general'][$general_field->column]['selected']) {
									$selected_value =  $this->data['form_field']['general'][$general_field->column]['selected'];
									$selected_element = $this->get_selected_element($general_field->module, $selected_value);
									$this->data['form_field']['general'][$general_field->column]['selected_text'] = $selected_element;
								}
							} elseif ($general_field->element == 'status') {
								$this->data['form_field']['general'][$general_field->column] = [
									'property'   => 'status',
									'name'       => $general_field->column,
									'id'         => $general_field->column,
									'label'      => $label,
									'class'      => 'bootstrap-select',
									'data-style' => 'btn-default btn-xs',
									'data-width' => '100%',
									'selected'   => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column}
								];
							}

							$rule_array = [];

							foreach ($general_field->rules as $rules) {
								if (isset($rules->rules_parametr) && !empty($rules->rules_parametr)) {
									$rule_array[] = $rules->rules . '[' . $rules->rules_parametr . ']';
								} else {
									$rule_array[] = $rules->rules;
								}
							}

							$form_rule = implode('|', $rule_array);

							if (!empty($form_rule)) {
								$this->form_validation->set_rules($general_field->column, $label, $form_rule);
							}
							unset($rule_array);
						}
					}
				}




				if ($this->input->method() == 'post') {
					if ($this->form_validation->run() == true) {
						$general = [];
						if (isset($form_fields->general) && !empty($form_fields->general)) {
							foreach ($form_fields->general as $general_field) {
								if ($general_field->show_on_form == 1) {
									if ($general_field->element == 'multiselect') {
										$general[$general_field->column] = implode(',', $this->input->post($general_field->column));
									} elseif($general_field->element == 'checkbox') {
										if($this->input->post($general_field->column)) {
											$general[$general_field->column] = (isset($general_field->checked_value)) ? $general_field->checked_value : 1;
										} else {
											$general[$general_field->column] = (isset($general_field->unchecked_value)) ? $general_field->unchecked_value : 0;
										}
									} else {
										$general[$general_field->column] = $this->input->post($general_field->column);
									}
								}
							}
						}
						
						if (!empty($general)) {
							$this->{$this->model}->update($general, ['id' => $id]);
							
							if ($this->module_setting->multilingual == 1) {
								$this->{$this->model}->delete_translation($id);
								foreach ($this->input->post('translation') as $language_id => $translation_fields) {
									$translation_data[$this->module_setting->slug . '_id'] = $id;
									$translation_data['language_id'] = $language_id;
									
									foreach ($translation_fields as $translation_field_key => $translation_field_value) {
										if ($form_fields->translation->{$translation_field_key}->element == 'multiselect') {
											$translation_data[$translation_field_key] = implode(',', $translation_field_value);
										} else {
											$translation_data[$translation_field_key] =  $translation_field_value;
										}
									}
									$this->{$this->model}->insert_translation($translation_data);
								}
							}

							$this->session->set_flashdata('message', ['type' => 'success', 'text' => translate('success_edit_message', true)]);
							
							redirect(site_url_multi($this->directory . $this->module_name));

						} else {
							$this->data['message'] = translate('error_warning', true);
						}
					} else {
						$this->data['message'] = translate('error_warning', true);
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

				$this->data['item_id'] = $id;
				$this->template->render($this->controller.'/form');
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}

	public function show($id = false)
	{
		if($id) {
			$row = $this->{$this->model}->filter(['id' => $id])->one();


			if ($row) {
				$form_fields = json_decode($this->module_setting->fields);

				if ($this->module_setting->multilingual == 1) {
					if (isset($form_fields->translation) && !empty($form_fields->translation)) {
						foreach ($form_fields->translation as $translation_field) {
							if ($translation_field->show_on_form == 1) {
								foreach ($this->data['languages'] as $language) {
									$row_translation = $this->{$this->model}->filter([$this->module_name.'_id' => $id])->with_translation($language['id'])->one();

									$label = (isset($translation_field->label->{$this->data['current_lang']})) ? $translation_field->label->{$this->data['current_lang']} : $translation_field->label->{$this->data['default_language']};
									
									$placeholder = (isset($translation_field->placeholder->{$this->data['current_lang']})) ? $translation_field->placeholder->{$this->data['current_lang']} : $translation_field->placeholder->{$this->data['default_language']};

									if (!isset($translation_field->label->{$language['code']})) {
										$translation_field->label->{$language['code']} = $label;
									}
	
									if (!isset($translation_field->placeholder->{$language['code']})) {
										$translation_field->placeholder->{$language['code']} = $placeholder;
									}
	
									if (in_array($translation_field->element, ['text', 'password', 'email', 'number', 'tel', 'url', 'range', 'search', 'date', 'datetime', 'time', 'color', 'month', 'week', 'submit', 'reset', 'button', 'radio', 'checkbox', 'textarea'])) {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => $translation_field->element,
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'class'       => $translation_field->class,
											'value'       => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
									} elseif ($translation_field->element == 'image') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'image',
											'id'		  		=> 'input-image'.$language['id'],
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'value'       => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'src'         => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? $this->Model_tool_image->resize(set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']'), 200, 200) : $this->Model_tool_image->resize($row_translation->{$translation_field->column}, 200, 200),
											'label'       => $label,
											'placeholder' => $this->Model_tool_image->resize($placeholder, 200, 200)
										];
									} elseif ($translation_field->element == 'file') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'file',
											'id'		  		=> 'input-image'.$language['id'],
											'class'		  	=> 'form-control',
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'value'       => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
									} elseif ($translation_field->element == 'multiselect') {
										$options = [];
										if (isset($translation_field->relation) && !empty($translation_field->relation)) {
											$options = $this->Module_model->generate_option($translation_field->table, $translation_field->key, $translation_field->value);
										}

										$selected = [];
										if ($this->input->post($translation_field->column)) {
											foreach ($this->input->post($translation_field->column) as $select) {
												$selected[] = $select;
											}
										} elseif ($row->{$translation_field->column}) {
											$selected = explode(',', $row->{$translation_field->column});
										}

										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    	=> 'multiselect',
											'name'        	=> 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'class'      		=> 'multiselect-select-all-filtering',
											'value'       	=> (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'      		=> $label,
											'options'    		=> $options,
											'selected'  		=> $selected,
											'placeholder' 	=> $placeholder
										];
									} elseif ($translation_field->element == 'dropdown') {
										$options = [];

										if (isset($translation_field->relation) && !empty($translation_field->relation)) {
											$options = $this->Module_model->generate_option($translation_field->table, $translation_field->key, $translation_field->value);
										}

										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'dropdown',
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'data-style'  => 'btn-default btn-xs',
											'data-width'  => '100%',
											'class'       => (isset($translation_field->class)) ? $translation_field->class : 'bootstrap-select',
											'options'     => $options,
											'selected'    => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
									} elseif ($translation_field->element == 'status') {
										$this->data['form_field']['translation'][$language['id']][$translation_field->column] = [
											'property'    => 'status',
											'name'        => 'translation[' . $language['id'] . '][' . $translation_field->column . ']',
											'class'       => 'bootstrap-select',
											'data-style'  => 'btn-default btn-xs',
											'data-width'  => '100%',
											'selected'    => (set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']')) ? set_value('translation[' . $language['id'] . '][' . $translation_field->column . ']') : $row_translation->{$translation_field->column},
											'label'       => $label,
											'placeholder' => $placeholder
										];
									}

									$rule_array = [];
									foreach ($translation_field->rules as $rules) {
										if (isset($rules->rules_parametr) && !empty($rules->rules_parametr)) {
											$rule_array[] = $rules->rules . '[' . $rules->rules_parametr . ']';
										} else {
											$rule_array[] = $rules->rules;
										}
									}
									$form_rule = implode('|', $rule_array);

									if (!empty($form_rule)) {
										$this->form_validation->set_rules('translation[' . $language["id"] . '][' . $translation_field->column . ']', $label, $form_rule);
									}
									unset($rule_array);
								}
							}
						}
					}
				}

				if (isset($form_fields->general) && !empty($form_fields->general)) {
					foreach ($form_fields->general as $general_field) {                        
						if ($general_field->show_on_form == 1) {

							$label = (isset($general_field->label->{$this->data['current_lang']})) ? $general_field->label->{$this->data['current_lang']} : $general_field->label->{$this->data['default_language']};
							
							$placeholder = (isset($general_field->placeholder->{$this->data['current_lang']})) ? $general_field->placeholder->{$this->data['current_lang']} : $general_field->placeholder->{$this->data['default_language']};

							if (in_array($general_field->element, ['text', 'password', 'email', 'number', 'tel', 'url', 'range', 'search', 'date', 'datetime', 'time', 'color', 'month', 'week', 'submit', 'reset', 'button', 'radio', 'checkbox', 'textarea'])) {
								$this->data['form_field']['general'][$general_field->column] = [
									'value'       => (set_value($general_field->column)) ? set_value($general_field->column) : $row->{$general_field->column},
									'label'       => $label
								];
							} elseif ($general_field->element == 'image') {
								$this->data['form_field']['general'][$general_field->column] = [
									'value'       => $this->Custom_model->callback_get_image_label($row->{$general_field->column}),
									'label'       => $label
								];
							} elseif ($general_field->element == 'file') {
								$this->data['form_field']['general'][$general_field->column] = [
									'value'       => $this->Custom_model->callback_get_file_label($row->{$general_field->column}),
									'label'       => $label,
								];
							} elseif ($general_field->element == 'multiselect' || $general_field->element == 'multiselect_ajax') {
								$options = "";
								
								if (isset($general_field->relation) && !empty($general_field->relation)) {
									$options = $this->Custom_model->callback_get_multiselect_label($row->{$general_field->column}, ['table' => $general_field->table, 'key' => $general_field->key, 'value' => $general_field->value]);
								}

								$selected = [];
								 if ($row->{$general_field->column}) {
									$selected = explode(',', $row->{$general_field->column});
								}
								
								$this->data['form_field']['general'][$general_field->column] = [
									'label'      => $label,
									'value'      => $options
								];
							} elseif ($general_field->element == 'dropdown' || $general_field->element == 'dropdown_ajax') {

								if (isset($general_field->module) && !empty($general_field->module)) {
									$module_data = (array)$general_field->module;
									if(array_key_exists('where', $module_data) && $module_data['where']) {
										$temp_where = $module_data['where'];
										$module_data['where'] = [];
										foreach($temp_where as $temp_row) {
											$module_data['where'][] = [
												'key' => $temp_row->key,
												'value' => $temp_row->value
											];
										}
									} else {
										$module_data['where'] = [];
									}

									$options = $this->Custom_model->callback_get_option($row->{$general_field->column}, ['module' => $module_data]);
								} else {


								    if($general_field->column == 'type') {

								        $types = [];

								        foreach ($general_field->options as $custom_option) {

								            $types[$custom_option->key] = $custom_option->value;

                                        }
                                    }

									$options = $types[$row->{$general_field->column}];
								}

								$this->data['form_field']['general'][$general_field->column] = [
									'label'      => $label,
									'value'      => $options
								];
							
							} elseif ($general_field->element == 'status') {
								$this->data['form_field']['general'][$general_field->column] = [
									'label'      => $label,
									'value'      => $this->Custom_model->callback_get_status_label($row->{$general_field->column}),
								];
							}

							$rule_array = [];

							foreach ($general_field->rules as $rules) {
								if (isset($rules->rules_parametr) && !empty($rules->rules_parametr)) {
									$rule_array[] = $rules->rules . '[' . $rules->rules_parametr . ']';
								} else {
									$rule_array[] = $rules->rules;
								}
							}

							$form_rule = implode('|', $rule_array);

							if (!empty($form_rule)) {
								$this->form_validation->set_rules($general_field->column, $label, $form_rule);
							}
							unset($rule_array);
						}
					}
				}

				$this->template->render($this->controller.'/show');
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}

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

	public function remove($id = false) 
	{
		if ($id) {
			$this->{$this->model}->force_delete($id);
			if ($this->module_setting->multilingual == 1) {
				$this->{$this->model}->delete_translation($id);
			}
			$this->template->json(['success' => 1]);
		} else {
			if ($this->input->method() == 'post') {
								$response  = ['success' => false, 'message' => translate('couldnt_remove_message',true)];
				if ($this->input->post('selected')) {
					foreach ($this->input->post('selected') as $id) {
						$this->{$this->model}->force_delete($id);
						if ($this->module_setting->multilingual == 1) {
							$this->{$this->model}->delete_translation($id);
						}
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
		redirect(site_url_multi($this->directory . $this->module_name.'/trash'));
	}

	public function changeStatus()
	{
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
				$slug = slug(mb_strtolower($text));
				$slug = $this->checkSlugExist($lang_id, $slug, 0, $item_id);
				$response = ['success' => true, 'slug' => $slug];
			}

		}

		$this->template->json($response);
	}

	public function generate_options($module){
		$model_name = ucfirst($module->name).'_model';
		$this->load->model(($module->dynamic) ? 'modules/'.$model_name : $model_name);
		$columns = explode(',',$module->columns);
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
		if($module->where) {
			foreach($module->where as $module_where) {
				$where[$module_where->key] =  $module_where->value;
			}
		}

		$order = ["Created_at" => "DESC"];
		if($module->sort) {
			foreach($module->sort as $module_sort){
				$order[$module_sort->column] = $module_sort->order;
			}
		}

		if($module->translation){
			$rows = $this->{$model_name}->fields("id, ".$module->key.", ".$select." as value")->order_by($order)->filter($where)->with_translation($this->data['current_lang_id'])->all();
		} else {
			$rows = $this->{$model_name}->fields("id, ".$module->key.", ".$select." as value")->order_by($order)->filter($where)->all();
		}

		$result[0] = translate('select', true);
		if($rows) {
			foreach($rows as $row) {
				$result[$row->{$module->key}] = $row->value;
			}
		}

		return $result;
	}

	public function get_selected_element($module, $selected_element)
	{
		$model_name = ucfirst($module->name).'_model';
		$this->load->model(($module->dynamic) ? 'modules/'.$model_name : $model_name);
		$columns = explode(',',$module->columns);
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
		
		$where = [$module->key => $selected_element];
		if($module->translation){
			$row = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->with_translation($this->data['current_lang_id'])->one();
		} else {
			$row = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->one();
		}
		
		if($row) {
			return $row->value;
		}

		return false;
	}

	public function get_selected_elments($module, $selected_elements)
	{
		$model_name = ucfirst($module->name).'_model';
		$this->load->model(($module->dynamic) ? 'modules/'.$model_name : $model_name);
		$columns = explode(',',$module->columns);
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
		
		$where = [$module->key.' IN ('.$selected_elements.')' => null];
		if($module->translation){
			$rows = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->with_translation($this->data['current_lang_id'])->all();
		} else {
			$rows = $this->{$model_name}->fields("id,".$select." as value")->filter($where)->all();
		}
		
		if($rows) {
			return $rows;
		}

		return false;

	}
	
	public function ajaxDropdownSearch()
	{	
		$response = ['success' => false, 'elements' => []];

		if ($this->input->method() == 'post') {
			$type = $this->input->post('type');
			$element = $this->input->post('element');
			$form_fields = json_decode($this->module_setting->fields);
			$form_field = [];
			if($type == 'translation') {
				$form_field = $form_fields->translation;
			} else {
				$form_field = $form_fields->general;
			}

			$dynamic = $form_field->$element->module->dynamic;
			$module = $form_field->$element->module->name;
			$translation = $form_field->$element->module->translation;
			$key = $form_field->$element->module->key;
			$columns = $form_field->$element->module->columns;
			$module_wheres = $form_field->$element->module->where;
			$module_sorts = $form_field->$element->module->sort;
			$keyword = $this->input->post('keyword');

			if($this->module_name == 'category' && $this->input->post('element') == 'parent' &&  !empty(trim($keyword))) {
				$this->load->model('Category_model');
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
							$where[$module_where->key] =  $module_where->value;
						}
					}
					
					$order = ["Created_at" => "DESC"];
					if($module_sorts) {
						foreach($module_sorts as $module_sort)
						$order[$module_sort->column] =  $module_sort->order;
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

}
