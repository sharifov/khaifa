<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_translation extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function translate() {

        $this->data['title']    = translate('index_title');
        $this->data['subtitle'] = translate('index_description');
        

        // Get admin_menu
        $admin_menu_data = [];
        $admin_menus = $this->Menu_translation_model->get_menu_data('admin_menu');
        if($admin_menus) {
            foreach($admin_menus as $menu) {
                $menu = (array) $menu;
                $menu['name'] = (array)json_decode($menu['name']);
                $admin_menu_data[$menu['id']] = [];
                foreach($this->data['languages'] as $language) {
                    if(array_key_exists($language['code'], $menu['name'])){
                        $admin_menu_data[$menu['id']]['name'][$language['code']] = $menu['name'][$language['code']];
                    }
                }
                
            }
        }
        $this->data['admin_menus'] = $admin_menu_data;

        // Get vendor_menu
        $vendor_menu_data = [];
        $vendor_menus = $this->Menu_translation_model->get_menu_data('vendor_menu');
        if($vendor_menus) {
            foreach($vendor_menus as $menu) {
                $menu = (array) $menu;
                $menu['name'] = (array)json_decode($menu['name']);
                $vendor_menu_data[$menu['id']] = [];
                foreach($this->data['languages'] as $language) {
                    if(array_key_exists($language['code'], $menu['name'])){
                        $vendor_menu_data[$menu['id']]['name'][$language['code']] = $menu['name'][$language['code']];
                    }
                }
                
            }
        }
        $this->data['vendor_menus'] = $vendor_menu_data;

                
        if ($this->input->method() == 'post') {
            if($this->input->post('admin_menu')) {
                foreach($this->input->post('admin_menu')  as $menu_id => $name) {
                    $this->Menu_translation_model->update_menu_data('admin_menu', ['id' => $menu_id], ['name' => json_encode($name, JSON_UNESCAPED_UNICODE)]);
                }
            }

            if($this->input->post('vendor_menu')) {
                foreach($this->input->post('vendor_menu')  as $menu_id => $name) {
                    $this->Menu_translation_model->update_menu_data('vendor_menu', ['id' => $menu_id], ['name' => json_encode($name, JSON_UNESCAPED_UNICODE)]);
                }
            }
            
            redirect(site_url_multi('administrator/menu_translation/translate'), 'refresh');
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

        
        $this->template->render($this->controller . '/form');
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
                'name' => 'author',
                'class' => 'form-control',
                'value' => $this->input->get('author'),
                'placeholder' => translate('search_placeholder', true),
            ],
        ];

        // Sets Table columns
        $this->data['fields'] = ['id', 'product_id', 'customer_id', 'author', 'rating', 'status'];

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
        if ($this->input->get('author') != null) {
            $filter['author LIKE "%' . $this->input->get('author') . '%"'] = null;
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
				'params' => '',
			],
            [
                'column' => 'customer_id',
                'callback' => 'get_option',
                'params' => [
                    'module'=> [
                        "dynamic"=> false,
                        "name"=> "customers",
                        "translation"=> false,
                        "key"=> "id",
                        "columns"=> "firstname",
                        "where"=> [],
                        "sort" => []
                    ]
                ]
            ],
            [
                'column' => 'product_id',
                'callback' => 'get_option',
                'params' => [
                    'module'=> [
                        "dynamic"=> false,
                        "name"=> "product",
                        "translation"=> true,
                        "key"=> "id",
                        "columns"=> "name",
                        "where"=> [],
                        "sort" => []
                    ]
                ]
            ],
            [
                'column' => 'rating',
                'callback' => 'get_rating',
                'params' => ''
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
                'name' => 'author',
                'class' => 'form-control',
                'value' => $this->input->get('author'),
                'placeholder' => translate('search_placeholder', true),
            ],
        ];

        // Sets Table columns
        $this->data['fields'] = ['id', 'product_id', 'customer_id', 'author', 'rating', 'status'];

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
        if ($this->input->get('author') != null) {
            $filter['author LIKE "%' . $this->input->get('author') . '%"'] = null;
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
				'params' => '',
			],
            [
                'column' => 'customer_id',
                'callback' => 'get_option',
                'params' => [
                    'module'=> [
                        "dynamic"=> false,
                        "name"=> "customers",
                        "translation"=> false,
                        "key"=> "id",
                        "columns"=> "firstname",
                        "where"=> [],
                        "sort" => []
                    ]
                ]
            ],
            [
                'column' => 'product_id',
                'callback' => 'get_option',
                'params' => [
                    'module'=> [
                        "dynamic"=> false,
                        "name"=> "product",
                        "translation"=> true,
                        "key"=> "id",
                        "columns"=> "name",
                        "where"=> [],
                        "sort" => []
                    ]
                ]
            ],
            [
                'column' => 'rating',
                'callback' => 'get_rating',
                'params' => ''
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

        // Sets Breadcrumb links
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

        // Form data
        $rows = $this->Customers_model->all();
        $customers[0] = '--- '.translate('none',true).' ---';
        if($rows) {
            foreach($rows as $row) {
                $customers[$row->id] = $row->firstname.' '.$row->lastname;
            }
        }
        
        // General Form Fields
        $this->data['form_field']['general'] = [
            'customer_id' => [
                'property' => 'dropdown',
                'name' => 'customer_id',
                'class' => 'bootstrap-select',
                'data-style' => 'btn-default btn-xs',
                'data-width' => '100%',
                'label' => translate('form_label_customer'),
                'options' => $customers,
                'selected' => set_value('customer_id'),
                'validation' => []
            ],
            'product_id'=> [
				'property'   => 'dropdown-ajax',
				'type'		 => 'general',
				'element'	 => 'product_id',
				'name'       => 'product_id',
				'id'         => 'product_id',
				'class'		 => 'form-control dropdownSingleAjax',
				'label'      => translate('form_label_product'),
				'placeholder'=> translate('form_label_product'),
				'selected'   => set_value('product_id'),
                'selected_text' => $this->get_selected_element('product_id',set_value('product_id')),
                'validation' => ['rules' => 'required']
			],
            'status' => [
				'property' 	=> 'dropdown',
				'name' 		=> 'status',
				'class' 	=> 'bootstrap-select',
				'data-style'=> 'btn-default btn-xs',
				'data-width'=> '100%',
				'label' 	=> translate('form_label_status'),
				'options' 	=> [translate('disable', true), translate('enable', true)],
				'selected' 		=> set_value('status'),
				'validation' 	=> []
            ],
            'author' => [
                'property' => 'text',
                'name' => 'author',
                'class' => 'form-control',
                'label' => translate('form_label_author'),
                'placeholder' => translate('form_label_author'),
                'value' => set_value('author'),
                'validation' => []
            ],
            'text' => [
                'property' => 'textarea',
                'name' => 'text',
                'class' => 'form-control',
                'label' => translate('form_label_review'),
                'placeholder' => translate('form_label_review'),
                'value' => set_value('text'),
                'validation' => ['rules' => 'required']
            ]
        ];
        $this->data['selected_rating'] = set_value('rating');
        if($this->data['form_field']['general']['product_id']['selected']){
            $this->data['form_field']['general']['product_id']['selected_text'] = $this->get_selected_element('product_id',$this->data['form_field']['general']['product_id']['selected']);
        }
        
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
                    'customer_id'  => (int)$this->input->post('customer_id'),
                    'rating'  => (int)$this->input->post('rating'),
                    'product_id'  => (int)$this->input->post('product_id'),
                    'status'  => $this->input->post('status'),
                    'text'  => $this->input->post('text'),
                    'author'  => $this->input->post('author')
                ];
                $this->{$this->model}->insert($general);

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

        $review = $this->{$this->model}->filter(['id' => $id])->one();

        if($review) {

            // Form data
            $rows = $this->Customers_model->all();
            $customers[0] = '--- '.translate('none',true).' ---';
            if($rows) {
                foreach($rows as $row) {
                    $customers[$row->id] = $row->firstname.' '.$row->lastname;
                }
            }
            
            // General Form Fields
            $this->data['form_field']['general'] = [
                'customer_id' => [
                    'property' => 'dropdown',
                    'name' => 'customer_id',
                    'class' => 'bootstrap-select',
                    'data-style' => 'btn-default btn-xs',
                    'data-width' => '100%',
                    'label' => translate('form_label_customer'),
                    'options' => $customers,
                    'selected' => (set_value('customer_id')) ? set_value('customer_id') : $review->customer_id,
                    'validation' => []
                ],
                'product_id'=> [
                    'property'   => 'dropdown-ajax',
                    'type'		 => 'general',
                    'element'	 => 'product_id',
                    'name'       => 'product_id',
                    'id'         => 'product_id',
                    'class'		 => 'form-control dropdownSingleAjax',
                    'label'      => translate('form_label_product'),
                    'placeholder'=> translate('form_label_product'),
                    'selected'   => (set_value('product_id')) ? set_value('product_id') : $review->product_id,
                    'validation' => ['rules' => 'required']
                ],
                'status' => [
                    'property' 	=> 'dropdown',
                    'name' 		=> 'status',
                    'class' 	=> 'bootstrap-select',
                    'data-style'=> 'btn-default btn-xs',
                    'data-width'=> '100%',
                    'label' 	=> translate('form_label_status'),
                    'options' 	=> [translate('disable', true), translate('enable', true)],
                    'selected' 		=> (set_value('status')) ? set_value('status') : $review->status,
                    'validation' 	=> []
                ],
                'author' => [
                    'property' => 'text',
                    'name' => 'author',
                    'class' => 'form-control',
                    'label' => translate('form_label_author'),
                    'placeholder' => translate('form_label_author'),
                    'value' => (set_value('author')) ? set_value('author') : $review->author,
                    'validation' => []
                ],
                'text' => [
                    'property' => 'textarea',
                    'name' => 'text',
                    'class' => 'form-control',
                    'label' => translate('form_label_review'),
                    'placeholder' => translate('form_label_review'),
                    'value' => (set_value('text')) ? set_value('text') : $review->text,
                    'validation' => ['rules' => 'required']
                ]
            ];
            $this->data['selected_rating'] = (set_value('rating')) ? set_value('rating') : $review->rating;
            
            if($this->data['form_field']['general']['product_id']['selected']){
                $this->data['form_field']['general']['product_id']['selected_text'] = $this->get_selected_element('product_id',$this->data['form_field']['general']['product_id']['selected']);
            }
            
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
                        'customer_id'  => (int)$this->input->post('customer_id'),
                        'rating'  => (int)$this->input->post('rating'),
                        'product_id'  => (int)$this->input->post('product_id'),
                        'status'  => $this->input->post('status'),
                        'text'  => $this->input->post('text'),
                        'author'  => $this->input->post('author')
                    ];

                    $this->{$this->model}->update($general,['id' => $id]);

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
}
