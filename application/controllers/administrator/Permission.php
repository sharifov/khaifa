<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permission extends Administrator_Controller
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


        // Table Column
        $this->data['fields'] = ['id', 'name','controller','method'];

        if ($this->data['fields']) {
            foreach ($this->data['fields'] as $field) {
                $this->data['columns'][$field] = [
                    'table' => [
                        $this->data['current_lang'] => translate('table_head_' . $field)
                    ]
                ];
            }
        }
       
        //Show Fields
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

        //Filter
        $filter = [];
        if ($this->input->get('status') != null) {
            $filter['status'] = $this->input->get('status');
        }
        if ($this->input->get('name') != null) {
            $filter['name'] = $this->input->get('name');
        }


        $sort = [
            'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
            'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC'
        ];

        $this->data['total_rows'] = $this->{$this->model}->filter($filter)->count_rows();
        $segment_array = $this->uri->segment_array();
        $page = (ctype_digit(end($segment_array))) ? end($segment_array) : 1;

        if ($this->input->get('per_page')) {
            $this->data['per_page'] = (int) $this->input->get('per_page');

            ${$this->controller . '_per_page'} = (int) $this->input->get('per_page');
            $this->session->set_userdata($this->controller . '_per_page', ${$this->controller . '_per_page'});
        } elseif ($this->session->has_userdata($this->controller . '_per_page')) {
            $this->data['per_page'] = $this->session->userdata($this->controller . '_per_page');
        } else {
            $this->data['per_page'] = 10;
        }

        $this->data['message'] = ($this->session->flashdata('message')) ? $this->session->flashdata('message') : '';



        $total_rows = $this->{$this->model}->filter($filter)->count_rows();
        $rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page-1)->all();


        $action_buttons = [
            'edit' => true,
            'delete' => true
        ];


        $custom_rows_data = [];

        $this->wc_table->set_module(false);
        $this->wc_table->set_columns($columns);
        $this->wc_table->set_rows($rows);
        $this->wc_table->set_custom_rows($custom_rows_data);
        $this->wc_table->set_action($action_buttons);
        $this->data['table'] = $this->wc_table->generate();


        //Pagination
        $config['base_url'] = site_url_multi($this->directory . $this->controller . '/index');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->data['per_page'];
        $config['reuse_query_string'] = true;
        $config['use_page_numbers'] = true;

        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        
        $this->data['breadcrumb_links'][] = [
            'text' => translate('breadcrumb_link_all', true),
            'href' => site_url($this->directory . $this->controller),
            'icon_class' => 'icon-database position-left',
            'label_value' => $this->{$this->model}->where()->count_rows(),
            'label_class' => 'label label-primary position-right'
        ];

        $this->data['breadcrumb_links'][] = [
            'text' => translate('breadcrumb_link_trash', true),
            'href' => site_url($this->directory . $this->controller . '/trash'),
            'icon_class' => 'icon-trash position-left',
            'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
            'label_class' => 'label label-danger position-right'
        ];

        $this->template->render();
    }

    /**
     * public function create()
     * Sets form fields for new data insertion to database (and buttons, breadcrumb links). Also cathces submitted form, validates and performs insert operation.
     */
    public function create()
    {
        $this->data['title'] 		= translate('create_title');
        $this->data['subtitle'] 	= translate('create_description');

        //Buttons
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

        $this->breadcrumbs->push(translate('create_title'), $this->directory.$this->controller.'/create');


        // Form Fields
        $this->data['form_field']['general'] = [
            'name' => [
                'property' 		=> 'text',
                'id' 			=> 'name',
                'name'          => 'name',
                'class'			=> 'form-control',
                'value'         => set_value('name'),
                'label'			=> translate('form_label_name'),
                'placeholder'	=> translate('form_placeholder_name'),
                'validation' 	=> ['rules' => 'required']
            ],
            'controller' => [
                'property' 		=> 'text',
                'id' 			=> 'name',
                'name'          => 'controller',
                'class'			=> 'form-control',
                'value'         => set_value('controller'),
                'label'			=> translate('form_label_controller'),
                'placeholder'	=> translate('form_placeholder_controller'),
                'validation' 	=> ['rules' => 'required']
            ],
            'method' => [
                'property' 		=> 'text',
                'id' 			=> 'name',
                'name'          => 'method',
                'class'			=> 'form-control',
                'value'         => set_value('method'),
                'label'			=> translate('form_label_method'),
                'placeholder'	=> translate('form_placeholder_method'),
                'validation' 	=> ['rules' => 'required']
            ]
        ];

        // Set Form Validation General Form Field
        foreach ($this->data['form_field']['general'] as $key => $value) {
            $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
        }

        if ($this->input->method() == 'post') {
            if ($this->form_validation->run()) {
                $general = [
                    'controller' => $this->input->post('controller'),
                    'method' => $this->input->post('method'),
                    'name' => $this->input->post('name')
                ];
                $insert = $this->{$this->model}->insert($general);

                if ($insert == true) {
                    $this->session->set_flashdata('message', translate('form_success_create'));
                    redirect(site_url_multi($this->directory.$this->controller), 'refresh');
                }
            } else {
                $this->data['message'] 	= (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            }
        }


        $this->template->render($this->controller.'/form');
    }

    /**
     * public function edit($id)
     * Gets row record from database which id equals to $id and fills proper fields. Sets form fields for data update (and buttons, breadcrumb links). Also cathces submitted form, validates and performs update operation.
     * @param integer $id
     */
    public function edit($id)
    {
        $this->data['title'] 		= translate('edit_title');
        $this->data['subtitle'] 	= translate('edit_description');

        // Form Buttons
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

        $this->breadcrumbs->push(translate('edit_title'), $this->directory.$this->controller.'/edit/'.$id);

        $row = $this->{$this->model}->filter(['id'=>$id])->one();

        $this->data['form_field']['general'] = [
            'name' => [
                'property' 		=> 'text',
                'id' 			=> 'name',
                'name'          => 'name',
                'class'			=> 'form-control',
                'value'         => (set_value('name')) ? set_value('name') : $row->name,
                'label'			=> translate('form_label_name'),
                'placeholder'	=> translate('form_placeholder_name'),
                'validation' 	=> ['rules' => 'required']
            ],
            'controller' => [
                'property' 		=> 'text',
                'id' 			=> 'name',
                'name'          => 'controller',
                'class'			=> 'form-control',
                'value'         => (set_value('controller')) ? set_value('controller') : $row->controller,
                'label'			=> translate('form_label_controller'),
                'placeholder'	=> translate('form_placeholder_controller'),
                'validation' 	=> ['rules' => 'required']
            ],
            'method' => [
                'property' 		=> 'text',
                'id' 			=> 'name',
                'name'          => 'method',
                'class'			=> 'form-control',
                'value'         => (set_value('method')) ? set_value('method') : $row->method,
                'label'			=> translate('form_label_method'),
                'placeholder'	=> translate('form_placeholder_method'),
                'validation' 	=> ['rules' => 'required']
            ]
        ];

        // Set Form Validation General Form Field
        foreach ($this->data['form_field']['general'] as $key => $value) {
            $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
        }

        if ($this->input->method() == 'post') {
            if ($this->form_validation->run()) {
                $general = [
                    'controller'	=> $this->input->post('controller'),
                    'method' 		=> $this->input->post('method'),
                    'name' 			=> $this->input->post('name')
                ];
                
                $update = $this->{$this->model}->update($general, ['id' => $id]);
                
                if ($update) {
                    $this->session->set_flashdata('message', translate('form_success_edit'));
                    redirect(site_url_multi($this->directory . $this->controller), 'refresh');
                }
            } else {
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            }
        }
        

        $this->template->render($this->controller.'/form');
    }

    /**
     * public function delete($id)
     * Deletes row record from database which id equals to $id.
     * @param integer $id
     */
    public function delete($id)
    {
        $this->{$this->model}->delete($id);
        echo json_encode(['success' => 1]);
    }
}
