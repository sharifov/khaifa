<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Featured_product extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Geo_zone_model');
    }

    public $relation_modules = [
		'products' => [
			"dynamic"=> false,
			"name"=> "product",
			"translation"=> true,
			"key"=> "id",
			"columns"=> "name",
			"where"=> [
                [
                    'key' => 'status', 
                    'value' => 1
                ]
            ],
			"sort" => []
        ],
        'category_id' => [
			"dynamic"=> true,
			"name"=> "category",
			"translation"=> true,
			"key"=> "id",
			"columns"=> "name",
			"where"=> [
                [
                    'key' => 'status', 
                    'value' => 1
                ],
                [
                    'key' => 'parent', 
                    'value' => 0
                ]
            ],
			"sort" => []
		]
	];

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
        $this->data['fields'] = ['id', 'name', 'percent', 'expired_date', 'status'];

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


        // Filters for banned and not specified name
        $filter = [];
        if ($this->input->get('name') != null) {
            $filter['name LIKE "%' . $this->input->get('name') . '%"'] = null;
        }

        // Sorts by column and order
        $sort = [
            'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
            'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
        ];
        
        $this->data['language_list_holder'] = [];

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
        $rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();

        // Sets custom row's data options
        $custom_rows_data = [
            [
                'column' => 'status',
                'callback' => 'get_status',
                'params' => []
            ]
        ];

        // Set action buttons
        $action_buttons = [];

		if (check_permission('option', 'edit')) {
			$action_buttons['edit'] = true;
        }
        
        if (check_permission('option', 'delete')) {
			$action_buttons['delete'] = true;
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
    
    public function trash()
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
        $this->data['fields'] = ['id', 'name', 'percent', 'expired_date', 'status'];

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


        // Filters for banned and not specified name
        $filter = [];
        if ($this->input->get('name') != null) {
            $filter['name LIKE "%' . $this->input->get('name') . '%"'] = null;
        }

        // Sorts by column and order
        $sort = [
            'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
            'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
        ];
        
        $this->data['language_list_holder'] = [];

        // Gets records count from database
        $this->data['total_rows'] = $this->{$this->model}->only_trashed()->filter($filter)->count_rows();
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
        $rows = $this->{$this->model}->fields($this->data['fields'])->only_trashed()->filter($filter)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();

        // Sets custom row's data options
        $custom_rows_data = [
            [
                'column' => 'status',
                'callback' => 'get_status',
                'params' => []
            ]
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

        // Sets Breadcrumb general
        $this->data['breadcrumb_links'][] = [
            'text' => translate('breadcrumb_link_all', true),
            'href' => site_url($this->directory . $this->controller),
            'icon_class' => 'icon-database position-left',
            'label_value' => $this->{$this->model}->count_rows(),
            'label_class' => 'label label-primary position-right',
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

    public function create()
    {
        $this->data['title']    = translate('create_title');
        $this->data['subtitle'] = translate('create_description');
        $types = [
            'featured_1' => "Featured 1",
            'featured_2' => "Featured 2",
            'category'   => "Category"
        ];
        // General Form Fields
        $this->data['form_field']['general'] = [
            'name' => [
                'property' => 'text',
                'name' => 'name',
                'class' => 'form-control',
                'label' => translate('form_label_name'),
                'placeholder' => translate('form_label_name'),
                'value' => set_value('name'),
                'validation' => ['rules' => 'required']
            ],
            'type' => [
                'property' => 'dropdown',
                'name' => 'type',
                'class' => 'bootstrap-select',
                'data-style' => 'btn-default btn-xs',
                'data-width' => '100%',
                'label' => translate('form_label_type'),
                'options' => $types,
                'selected' => set_value('type'),
                'validation' => []
            ],
            'percent' => [
                'property' => 'number',
                'min'   => '0',
                'name' => 'percent',
                'class' => 'form-control',
                'label' => translate('form_label_percent'),
                'placeholder' => translate('form_label_percent'),
                'value' => set_value('percent'),
                'validation' => ['rules' => 'required']
            ],
            'category_id'=> [
                'property'   => 'dropdown-ajax',
                'type'		 => 'general',
                'element'	 => 'category_id',
                'name'       => 'category_id',
                'id'         => 'category_id',
                'class'		 => 'form-control dropdownSingleAjax',
                'label'      => translate('form_label_category'),
                'placeholder'=> translate('form_label_category'),
                'selected'   => set_value('category_id'),
                'selected_text' => ""
            ],
            'products' => [
                'property'   => 'multiselect_ajax',
                'type'		 => 'general',
                'element'	 => 'products',
                'name'       => 'products',
                'id'         => 'products',
                'class'		 => 'form-control dropdownMultiAjax',
                'label'      => translate('form_label_products'),
                'placeholder'   => translate('form_label_products'),
                'selected'      => set_value('products'), 
                'selected_elements' => [],
                'selected_text' => ""
            ],
            'status' => [
                'property' => 'dropdown',
                'name' => 'status',
                'class' => 'bootstrap-select',
                'data-style' => 'btn-default btn-xs',
                'data-width' => '100%',
                'label' => translate('form_label_status'),
                'options' => [translate('disable',true),translate('enable',true)],
                'selected' => set_value('status'),
                'validation' => []
            ]
        ];
        
        if($this->data['form_field']['general']['products']['selected']){
            $this->data['form_field']['general']['products']['selected_elements'] = $this->get_selected_elments('products',$this->data['form_field']['general']['products']['selected']);
        }

        if($this->data['form_field']['general']['category_id']['selected']){
            $this->data['form_field']['general']['category_id']['selected_text'] = $this->get_selected_element('category_id',$this->data['form_field']['general']['category_id']['selected']);
        } 

        $this->data['start_date']   = set_value('start_date');
        $this->data['expired_date'] = set_value('expired_date');
        
        // Set form validation rules
        foreach ($this->data['form_field']['general'] as $key => $value)
        {   
            if(isset($value['validation']) && $value['validation']) {
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
                    'name'  => $this->input->post('name'),
                    'type'  => $this->input->post('type'),
                    'category_id'  => $this->input->post('category_id'),
                    'percent'  => (int)$this->input->post('percent'),
                    'status'  => (int)$this->input->post('status'),
                    'products' => $this->input->post('products')
                ];

                if($this->input->post('start_date')) {
                    $general['start_date'] = $this->input->post('start_date');
                }
                
                if($this->input->post('expired_date')) {
                    $general['expired_date'] = $this->input->post('expired_date');
                }
                
                $id = $this->{$this->model}->insert($general);

                $this->session->set_flashdata('message', translate('form_success_create'));
                redirect(site_url_multi($this->directory . $this->controller), 'refresh');
            } else {
                $this->data['message'] = translate('error_warning', true);
            }
        }

        $this->template->render($this->controller . '/form');
    }

    public function edit($id)
    {
        $this->data['title'] = translate('edit_title');
        $this->data['subtitle'] = translate('edit_description');

        $featured_product = $this->{$this->model}->filter(['id' => $id])->one();

        if($featured_product) {

            $types = [
                'featured_1' => "Featured 1",
                'featured_2' => "Featured 2",
                'category'   => "Category"
            ];

            // General Form Fields
            $this->data['form_field']['general'] = [
                'name' => [
                    'property' => 'text',
                    'name' => 'name',
                    'class' => 'form-control',
                    'label' => translate('form_label_name'),
                    'placeholder' => translate('form_label_name'),
                    'value' => (set_value('name')) ? set_value('name') : $featured_product->name,
                    'validation' => ['rules' => 'required']
                ],
                'type' => [
                    'property' => 'dropdown',
                    'name' => 'type',
                    'class' => 'bootstrap-select',
                    'data-style' => 'btn-default btn-xs',
                    'data-width' => '100%',
                    'label' => translate('form_label_type'),
                    'options' => $types,
                    'selected' => (set_value('type')) ? set_value('type') : $featured_product->type,
                    'validation' => []
                ],
                'percent' => [
                    'property' => 'number',
                    'min'   => '0',
                    'name' => 'percent',
                    'class' => 'form-control',
                    'label' => translate('form_label_percent'),
                    'placeholder' => translate('form_label_percent'),
                    'value' => (set_value('percent')) ? set_value('percent') : $featured_product->percent,
                    'validation' => ['rules' => 'required']
                ],
                'category_id'=> [
                    'property'   => 'dropdown-ajax',
                    'type'		 => 'general',
                    'element'	 => 'category_id',
                    'name'       => 'category_id',
                    'id'         => 'category_id',
                    'class'		 => 'form-control dropdownSingleAjax',
                    'label'      => translate('form_label_category'),
                    'placeholder'=> translate('form_label_category'),
                    'selected'   => (set_value('category_id')) ? set_value('category_id') : $featured_product->category_id,
                    'selected_text' => ""
                ],
                'products' => [
                    'property'   => 'multiselect_ajax',
                    'type'		 => 'general',
                    'element'	 => 'products',
                    'name'       => 'products',
                    'id'         => 'products',
                    'class'		 => 'form-control dropdownMultiAjax',
                    'label'      => translate('form_label_products'),
                    'placeholder'   => translate('form_label_products'),
                    'selected'      => (set_value('products')) ? set_value('products') : $featured_product->products, 
                    'selected_elements' => [],
                    'selected_text' => ""
                ],
                'status' => [
                    'property' => 'dropdown',
                    'name' => 'status',
                    'class' => 'bootstrap-select',
                    'data-style' => 'btn-default btn-xs',
                    'data-width' => '100%',
                    'label' => translate('form_label_status'),
                    'options' => [translate('disable',true),translate('enable',true)],
                    'selected' => (set_value('status')) ? set_value('status') : $featured_product->status,
                    'validation' => []
                ]

            ];
            
            if($this->data['form_field']['general']['products']['selected']){
				$this->data['form_field']['general']['products']['selected_elements'] = $this->get_selected_elments('products',$this->data['form_field']['general']['products']['selected']);
            }
            
            if($this->data['form_field']['general']['category_id']['selected']){
                $this->data['form_field']['general']['category_id']['selected_text'] = $this->get_selected_element('category_id',$this->data['form_field']['general']['category_id']['selected']);
            } 

            $this->data['start_date'] = (set_value('start_date')) ? set_value('start_date') : $featured_product->start_date;
            $this->data['expired_date'] = (set_value('expired_date')) ? set_value('expired_date') : $featured_product->expired_date;
            
            // Set form validation rules
            foreach ($this->data['form_field']['general'] as $key => $value)
            {   
                if(isset($value['validation']) && $value['validation']) {
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
                        'name'  => $this->input->post('name'),
                        'type'  => $this->input->post('type'),
                        'category_id'  => (int)$this->input->post('category_id'),
                        'percent'  => (int)$this->input->post('percent'),
                        'products'  => $this->input->post('products'),
                        'start_date'  => ($this->input->post('start_date')) ? $this->input->post('start_date') : null,
                        'expired_date'  => ($this->input->post('expired_date')) ? $this->input->post('expired_date') : null,
                        'status'  => (int)$this->input->post('status')
                    ];

                    $id = $this->{$this->model}->update($general,['id' => $id]);
                    
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
			$this->{$this->model}->force_delete_option($id);
			$this->template->json(['success' => 1]);
		} else {
			if ($this->input->method() == 'post') {
                $response  = ['success' => false, 'message' => translate('couldnt_remove_message',true)];
				if ($this->input->post('selected')) {
					foreach ($this->input->post('selected') as $id) {
						$this->{$this->model}->force_delete_option($id);
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
		$this->{$this->model}->force_delete_option('all');
        redirect(site_url_multi($this->directory . $this->module_name));
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
		$this->template->json($response);
	}
    
}
