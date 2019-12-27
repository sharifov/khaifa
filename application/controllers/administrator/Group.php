<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Group extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Permission_model');
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
        $this->data['fields'] = ['id', 'name', 'description'];

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


        $total_rows = $this->{$this->model}->where($filter)->count_rows();
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
            'label_value' => $this->{$this->model}->count_rows(),
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
        $this->data['title'] 	= translate('create_title');
        $this->data['subtitle'] = translate('create_description');

        $this->data['form_field']['general'] = [
            'name'		=> [
                'property'		=> 'text',
                'id'       		=> 'name',
                'name'          => 'name',
                'class'			=> 'form-control',
                'value'         => set_value('name'),
                'label'			=> translate('form_label_name'),
                'placeholder'	=> translate('form_placeholder_name'),
                'validation'	=> ['rules' => 'required']
            ],
            'description'		=> [
                'property'		=> 'textarea',
                'name'          => 'description',
                'class'			=> 'form-control',
                'value'         => set_value('description'),
                'label'			=> translate('form_label_description'),
                'placeholder'	=> translate('form_placeholder_description'),
                'validation'	=> ['rules' => 'required']
            ]

        ];

        foreach ($this->data['form_field']['general'] as $key => $value) {
            $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
        }

        //Get permissions
        $rows = $this->Permission_model->fields(['id','name','controller'])->order_by('controller', 'ASC')->as_array()->all();
        $this->data['permissions'] = [];
        $permissions = [];
        if($rows){
            foreach($rows as $row){
                if(!in_array($row['controller'],$this->data['permissions'])) {
                    $this->data['permissions'][] = $row['controller'];
                }
                $permissions[$row['controller']][] = $row;
            }
        }
        
        $this->data['permission_groups'] = array_chunk($permissions,4);
        
        $this->data['selected_permissions'] = (!empty(set_value('permissions')))  ? set_value('permissions') : [];

        $this->data['buttons'][] = [
            'type'		=> 'button',
            'text'		=> translate('form_button_save', true),
            'class'		=> 'btn btn-primary btn-labeled heading-btn',
            'id'		=> 'save',
            'icon'		=> 'icon-floppy-disk',
            'additional' => [
                'onclick'	=> "confirm('Are you sure?') ? $('#form-save').submit() : false;",
                'form' 		=> 'form-save',
                'formaction'=> current_url()
            ]
        ];

        $this->breadcrumbs->push(translate('create_title'), $this->directory.$this->controller.'/create');

        if ($this->input->method() == 'post') {
            if ($this->form_validation->run() == true) {
                //var_dump($this->input->post('permissions'));die();
                $general = [
                    'name'	=> $this->input->post('name'),
                    'description'	=> $this->input->post('description')
                ];

                $group_id = $this->{$this->model}->insert($general);

                //insert permission to group
                foreach ($this->input->post('permissions') as $permission_id) {
                    $this->{$this->model}->set_permission_to_group(['permission_id' => $permission_id, 'group_id' => $group_id]);
                }
                

                redirect(site_url_multi($this->directory.$this->controller));
            } else {
                $this->data['message'] = translate('error_warning', true);
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
        if ($id && ctype_digit($id)) {
            $this->data['general'] = $this->{$this->model}->filter(['id' => $id])->one();
            if ($this->data['general']) {
                //Set title & description
                $this->data['title'] 	= translate('edit_title');
                $this->data['subtitle'] = translate('edit_description');

                // Set General Form Field
                $this->data['form_field']['general'] = [
                    'name'		=> [
                        'property'		=> 'text',
                        'id'       		=> 'name',
                        'name'          => 'name',
                        'class'			=> 'form-control',
                        'value'         => (set_value('name')) ? set_value('name') : $this->data['general']->name,
                        'label'			=> translate('form_label_name'),
                        'placeholder'	=> translate('form_placeholder_name'),
                        'validation'	=> ['rules' => 'required']
                    ],
                    'description'		=> [
                        'property'		=> 'textarea',
                        'name'          => 'description',
                        'class'			=> 'form-control',
                        'value'         => (set_value('description')) ? set_value('description') : $this->data['general']->description,
                        'label'			=> translate('form_label_description'),
                        'placeholder'	=> translate('form_placeholder_description'),
                        'validation'	=> ['rules' => 'required']
                    ]

                ];
                // Set Form Validation General Form Field
                foreach ($this->data['form_field']['general'] as $key => $value) {
                    $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
                }

                //Get permissions
                $rows = $this->Permission_model->fields(['id','name','controller'])->order_by('controller', 'ASC')->as_array()->all();
                $this->data['permissions'] = [];
                $permissions = [];
                if($rows){
                    foreach($rows as $row){
                        if(!in_array($row['controller'],$this->data['permissions'])) {
                            $this->data['permissions'][] = $row['controller'];
                        }
                        $permissions[$row['controller']][] = $row;
                    }
                }
                $this->data['permission_groups'] = array_chunk($permissions,round((count($permissions)/4)));

                //Get selected permissions
                $selected_permissions = $this->{$this->model}->get_group_permissions($id);
                $this->data['selected_permissions'] = [];
                if (!empty($this->input->post('permissions'))) {
                    $this->data['selected_permissions'] = $this->input->post('permissions');
                } else {
                    foreach ($selected_permissions as $permission) {
                        $this->data['selected_permissions'][] = $permission->permission_id;
                    }
                }


                $this->data['buttons'][] = [
                    'type'		=> 'button',
                    'text'		=> translate('form_button_save', true),
                    'class'		=> 'btn btn-primary btn-labeled heading-btn',
                    'id'		=> 'save',
                    'icon'		=> 'icon-floppy-disk',
                    'additional' => [
                        'onclick'	=> "confirm('Are you sure?') ? $('#form-save').submit() : false;",
                        'form' 		=> 'form-save',
                        'formaction'=> current_url()
                    ]
                ];


                $this->breadcrumbs->push(translate('edit_title'), $this->directory.$this->controller.'/edit');

                if ($this->input->method() == 'post' && $this->form_validation->run() == true) {
                    $general = [
                        'name'	=> $this->input->post('name'),
                        'description'	=> $this->input->post('description')
                    ];
                    $this->{$this->model}->update($general, ['id' => $id]);

                    //update permissions
                    $this->{$this->model}->delete_group_permissions($id);

                    foreach ($this->input->post('permissions') as $permission_id) {
                        $this->{$this->model}->set_permission_to_group(['permission_id' => $permission_id, 'group_id' => $id]);
                    }

                    redirect(site_url_multi($this->directory.$this->controller));
                } else {
                    $this->data['message'] = translate('error_warning');
                    $this->template->render($this->controller.'/form');
                }
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
        $this->{$this->model}->delete($id);
        echo json_encode(['success' => 1]);
    }
}
