<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Language extends Administrator_Controller
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

        // Sets Table columns
        $this->data['fields'] = ['id', 'name', 'slug', 'code', 'directory', 'direction', 'status'];

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

        // Sets action button options
        $action_buttons = [
            'edit' => true,
            'delete' => true,
            'custom' => [
                [
                    'href_value' => 'directory',
                    'icon' => 'icon-folder',
                    'text' => 'Translate',
                    'href' => site_url_multi($this->admin_url.'/translation/directory/'),
                ],
            ],
        ];

        // Sets custom row's data options
        $custom_rows_data = [
            [
                'column' => 'status',
                'callback' => 'get_status',
                'params' => '',
            ],
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
     * public function index()
     * Runs as default when this controller requested if any other method is not specified in route file.
     * Collects all data (buttons, table columns, fields, pagination config, breadcrumb links) which will be displayed on index page of this controller (generally it contains rows of database result). At final sends data to target template.
     */

    public function trash()
    {
        $this->data['title'] = translate('trash_title');
        $this->data['subtitle'] = translate('trash_description');

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

        // Sets Table columns
        $this->data['fields'] = ['id', 'name', 'slug', 'code', 'directory', 'direction', 'status'];

        if ($this->data['fields']) {
            foreach ($this->data['fields'] as $field) {
                $this->data['columns'][$field] = [
                    'table' => [$this->data['current_lang'] => translate('table_head_' . $field),
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

        // Sets action button options
        $action_buttons = [
            'remove'    => true,
            'restore'   => true
        ];

        // Sets custom row's data options
        $custom_rows_data = [
            [
                'column' => 'status',
                'callback' => 'get_status',
                'params' => '',
            ],
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

        $this->breadcrumbs->push(translate('create_title'), $this->directory . $this->controller . '/create');

        // Form Fields
        $this->data['form_field']['general'] = [
            'name' => [
                'property' => 'text',
                'id' => 'name',
                'name' => 'name',
                'id' => 'name',
                'class' => 'form-control',
                'value' => set_value('name'),
                'label' => translate('form_placeholder_name'),
                'placeholder' => translate('form_placeholder_name'),
                'validation' => ['rules' => 'required'],
            ],
            'directory' => [
                'property' => 'text',
                'id' => 'directory',
                'name' => 'directory',
                'id' => 'directory',
                'class' => 'form-control',
                'value' => set_value('directory'),
                'label' => translate('form_placeholder_directory'),
                'placeholder' => translate('form_placeholder_directory'),
                'validation' => ['rules' => 'required'],
            ],
            'slug' => [
                'property' => 'text',
                'name' => 'slug',
                'class' => 'form-control',
                'value' => set_value('slug'),
                'label' => translate('form_label_slug'),
                'placeholder' => translate('form_placeholder_slug'),
                'validation' => ['rules' => 'required'],
            ],
            'code' => [
                'property' => 'text',
                'id' => 'code',
                'name' => 'code',
                'id' => 'code',
                'class' => 'form-control',
                'value' => set_value('code'),
                'label' => translate('form_placeholder_code'),
                'placeholder' => translate('form_placeholder_code'),
                'validation' => ['rules' => 'required'],
            ],
            'direction' => [
                'property' => 'text',
                'id' => 'direction',
                'name' => 'direction',
                'id' => 'direction',
                'class' => 'form-control',
                'value' => set_value('direction'),
                'label' => translate('form_placeholder_direction'),
                'placeholder' => translate('form_placeholder_direction'),
                'validation' => [],
            ],
            'admin' => [
                'property' => 'checkbox',
                'name' => 'admin',
                'id' => 'admin',
                'class' => 'styled',
                'value' => 1,
                'checked' => (set_value('admin')) ? true : false,
                'label' => translate('form_label_admin'),
                'validation' => [],
            ],
            'default' => [
                'property' => 'checkbox',
                'name' => 'default',
                'id' => 'default',
                'class' => 'styled',
                'value' => 1,
                'checked' => (set_value('default')) ? true : false,
                'label' => translate('form_label_default'),
                'validation' => [],
            ],
            'sort' => [
                'property' => 'number',
                'min' => '0',
                'id' => 'sort',
                'name' => 'sort',
                'id' => 'sort',
                'class' => 'form-control',
                'value' => (set_value('sort')) ? set_value('sort') : 0,
                'label' => translate('form_placeholder_sort'),
                'placeholder' => translate('form_placeholder_sort'),
                'validation' => [],
            ],

            'status' => [
                'property' => 'dropdown',
                'name' => 'status',
                'id' => 'status',
                'label' => translate('form_label_status'),
                'class' => 'select2 select-search',
                'options' => [translate('disable', true), translate('enable', true)],
                'selected' => set_value('status'),
                'validation' => ['rules' => 'required'],
            ],
        ];

        // Set form validation rules
        foreach ($this->data['form_field']['general'] as $key => $value)
        {   
            if($value['validation']){
                $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
            }
        }
        
        if ($this->input->method() == 'post') {
            if ($this->form_validation->run() == true) {
            
                $language_data = [];
                foreach ($this->input->post() as $key => $language) {
                    if (is_array($language)) {
                        $language_data[$key] = json_encode($language);
                    } else {
                        $language_data[$key] = $language;
                    }
                }
                
                //Admin and default lang
                if(!$this->input->post('admin')) {
                    $language_data['admin'] = 0;
                }
                if(!$this->input->post('default')) {
                    $language_data['default'] = 0;
                }
                
                $insert = $this->{$this->model}->insert($language_data);

                if ($insert == true) {
                    $this->session->set_flashdata('message', translate('form_success_create'));
                    redirect(site_url_multi($this->directory . $this->controller), 'refresh');
                }

            }  else {
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
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

        $this->breadcrumbs->push(translate('edit_title'), $this->directory . $this->controller . '/edit/' . $id);

        $row = $this->{$this->model}->filter(['id' => $id])->one();

        $this->data['form_field']['general'] = [
            'name' => [
                'property' => 'text',
                'id' => 'name',
                'name' => 'name',
                'id' => 'name',
                'class' => 'form-control',
                'value' => (set_value('name')) ? set_value('name') : $row->name,
                'label' => translate('form_placeholder_name'),
                'placeholder' => translate('form_placeholder_name'),
                'validation' => ['rules' => 'required']
            ],
            'directory' => [
                'property' => 'text',
                'id' => 'directory',
                'name' => 'directory',
                'id' => 'directory',
                'class' => 'form-control',
                'value' => (set_value('directory')) ? set_value('directory') : $row->directory,
                'label' => translate('form_placeholder_directory'),
                'placeholder' => translate('form_placeholder_directory'),
                'validation' => ['rules' => 'required']
            ],
            'slug' => [
                'property' => 'text',
                'name' => 'slug',
                'class' => 'form-control',
                'value' => (set_value('slug')) ? set_value('slug') : $row->slug,
                'label' => translate('form_label_slug'),
                'placeholder' => translate('form_placeholder_slug'),
                'validation' => ['rules' => 'required']
            ],
            'code' => [
                'property' => 'text',
                'id' => 'code',
                'name' => 'code',
                'id' => 'code',
                'class' => 'form-control',
                'value' => (set_value('code')) ? set_value('code') : $row->code,
                'label' => translate('form_placeholder_code'),
                'placeholder' => translate('form_placeholder_code'),
                'validation' => ['rules' => 'required'],
            ],
            'direction' => [
                'property' => 'text',
                'id' => 'direction',
                'name' => 'direction',
                'id' => 'direction',
                'class' => 'form-control',
                'value' => (set_value('direction')) ? set_value('direction') : $row->direction,
                'label' => translate('form_placeholder_direction'),
                'placeholder' => translate('form_placeholder_direction'),
                'validation' => ['rules' => 'required'],
            ],
            'admin' => [
                'property' => 'checkbox',
                'name' => 'admin',
                'id' => 'admin',
                'class' => 'styled',
                'value' => 1,
                'checked' => (set_value('admin')) ? true : $row->admin,
                'label' => translate('form_label_admin'),
                'validation' => [],
            ],
            'default' => [
                'property' => 'checkbox',
                'name' => 'default',
                'id' => 'default',
                'class' => 'styled',
                'value' => 1,
                'checked' => (set_value('default')) ? true : $row->default,
                'label' => translate('form_label_default'),
                'validation' => [],
            ],
            'sort' => [
                'property' => 'number',
                'min'  => '0',
                'id' => 'sort',
                'name' => 'sort',
                'id' => 'sort',
                'class' => 'form-control',
                'value' => (set_value('sort')) ? set_value('sort') : $row->sort,
                'label' => translate('form_placeholder_sort'),
                'placeholder' => translate('form_placeholder_sort'),
                'validation' => ['rules' => 'required'],
            ],
            'status' => [
                'property' => 'dropdown',
                'name' => 'status',
                'id' => 'status',
                'label' => translate('form_label_status'),
                'class' => 'select2 select-search',
                'options' => [translate('disable', true), translate('enable', true)],
                'selected' => (set_value('status')) ? set_value('status') : $row->status,
                'validation' => ['rules' => 'required'],
            ],
        ];

        // Set form validation rules
        foreach ($this->data['form_field']['general'] as $key => $value)
        {   
            if($value['validation']){
                $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
            }
        }

        // Form Buttons
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

        // Form Select Options
        $this->data['options'] = [
            'status' => [
                translate('common_disable', true),
                translate('common_enable', true),
            ],
        ];

        if ($this->input->method() == 'post') {
            if ($this->form_validation->run() == true) {

                $language_data = [];
                foreach ($this->input->post() as $key => $language) {
                    if (is_array($language)) {
                        $language_data[$key] = json_encode($language);
                    } else {
                        $language_data[$key] = $language;
                    }
                }

                //Admin and default lang
                if(!$this->input->post('admin')){
                    $language_data['admin'] = 0;
                }
                if(!$this->input->post('default')){
                    $language_data['default'] = 0;
                }
                
                $update = $this->{$this->model}->update($language_data, ['id' => $id]);

                if ($update == true) {
                    $this->session->set_flashdata('message', translate('form_success_edit'));
                    redirect(site_url_multi($this->directory . $this->controller), 'refresh');
                }
            }
            else {
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            }
                
        }

        $this->template->render($this->controller . '/form');
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
			$this->{$this->model}->force_delete($id);
			$this->template->json(['success' => 1]);
		} else {
			if ($this->input->method() == 'post') {
                $response  = ['success' => false, 'message' => translate('couldnt_remove_message',true)];
				if ($this->input->post('selected')) {
					foreach ($this->input->post('selected') as $id) {
						$this->{$this->model}->force_delete($id);
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
		$this->{$this->model}->force_delete(['deleted_at !=' => null]);
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
