<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Extension extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->config('menu_icon');
        $this->load->config('rules');
        $this->load->config('datatype');
        $this->load->config('elements');
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


        // Table Column
        $this->data['fields'] = [
            'id',
            'name',
            'icon'
        ];

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
                'name' => 'slug',
                'class' => 'form-control',
                'value' => $this->input->get('slug'),
                'placeholder' => translate('search_placeholder', true),
            ]
        ];

        // Filters for banned and not specified name
        $filter = [];
        if ($this->input->get('status') != null) {
            $filter['status'] = $this->input->get('status');
        }
        if ($this->input->get('slug') != null) {
            $filter['slug'] = $this->input->get('slug');
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
            $per_page = (int)$this->input->get('per_page');

            ${$this->controller . '_per_page'} = (int)$this->input->get('per_page');
            $this->session->set_userdata($this->controller . '_per_page', ${$this->controller . '_per_page'});
        } elseif ($this->session->has_userdata($this->controller . '_per_page')) {
            $per_page = $this->session->userdata($this->controller . '_per_page');
        } else {
            $per_page = 100;
        }

        $this->data['message'] = ($this->session->flashdata('message')) ? $this->session->flashdata('message') : '';


        // Gets all records from database with given criterias
        $total_rows = $this->{$this->model}->count_rows($filter);
        $rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->order_by($sort['column'],
            $sort['order'])->limit($per_page, $page - 1)->all();

        //echo $this->db->last_query();die();

        // Sets action button options
        $action_buttons = [
            'edit' => true
        ];

        // Sets custom row's data options
        $custom_rows_data = [
            [
                'column' => 'name',
                'callback' => 'get_name',
                'params' => $this->data['current_lang']
            ],
            [
                'column' => 'status',
                'callback' => 'get_status',
                'params' => ''
            ],
            [
                'column' => 'icon',
                'callback' => 'get_icon',
                'params' => ''
            ],
            [
                'column' => 'multilingual',
                'data' => [
                    '0' => '',
                    '1' => '<i class="icon-checkmark"></i>'
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

       

        $this->template->render();
    }



    /**
     * public function edit($id)
     * Gets row record from database which id equals to $id and fills proper fields. Sets form fields for data update (and buttons, breadcrumb links). Also cathces submitted form, validates and performs update operation.
     * @param integer $id
     */
    public function edit($id = false)
    {
        if ($id) {

            $this->data['title'] = translate('edit_title');
            $this->data['subtitle'] = translate('edit_description');

            // Get module data
            $row = $this->{$this->model}->fields()->filter(['id' => $id])->one();
            $this->data['all_table'] = $this->{$this->model}->get_table();

            //Get All Callback Method in Custom_model
            $methods = get_class_methods('Custom_model');

            if ($methods) {
                foreach ($methods as $method) {
                    if (strpos($method, 'callback_') !== false) {
                        $this->data['methods'][] = str_replace('callback_', '', $method);
                    }
                }
            }

            // Get All Languages
            if ($this->data['languages']) {
                $language_options[0] = translate('please_select');
                foreach ($this->data['languages'] as $language) {
                    $language_options[$language['id']] = $language['name'];
                }
            } else {
                $language_options = [];
            }

            // Get All Active Modules
            $modules = $this->{$this->model}->fields(['id', 'name'])->filter(['status' => 1])->all();

            if ($modules) {
                $module_options[0] = translate('please_select');
                foreach ($modules as $module) {
                    if ($module->id != $id) {
                        $module_options[$module->id] = json_decode($module->name)->index->title->{$this->data['current_lang']};
                    }
                }
            } else {
                $module_options = [];
            }


            // Module default sort data
            $default_sort = json_decode($row->default_sort);


            $this->data['text'] = json_decode($row->name, true);
            $this->data['fields'] = json_decode($row->fields, true);
           
            /* End validation set rules */


            $this->data['mysql_data_types'] = $this->config->item('list');
            $this->data['rules'] = $this->config->item('rules');
            $this->data['elements'] = $this->config->item('elements');

            /* Database fields general */
            $db_fields_generals = $this->db->field_data($row->slug);
            $protected_fields_for_general = [
                'created_at',
                'created_by',
                'updated_at',
                'updated_by',
                'deleted_at',
                'deleted_by'
            ];

            foreach ($db_fields_generals as $db_fields_general) {
                if (!in_array($db_fields_general->name, $protected_fields_for_general)) {
                    $this->data['db_fields_general'][] = $db_fields_general;
                }
            }


            if ($row->multilingual == 1) {
                /* Database fields translation */
                $protected_fields_for_translation = [$row->slug . '_id', 'language_id'];
                $db_fields_translations = $this->db->field_data($row->slug . '_translation');

                foreach ($db_fields_translations as $db_fields_translation) {
                    if (!in_array($db_fields_translation->name, $protected_fields_for_translation)) {
                        $this->data['db_fields_translation'][] = $db_fields_translation;
                    }
                }
            }


            //Buttons
            $this->data['buttons'][] = [
                'type' => 'button',
                'text' => translate('form_button_save'),
                'class' => 'btn btn-primary btn-labeled heading-btn',
                'id' => 'save',
                'icon' => 'icon-floppy-disk',
                'additional' => [
                    'onclick' => "confirm('Are you sure?') ? $('#form-save').submit() : false;",
                    'form' => 'form-save',
                    'formaction' => current_url()
                ]
            ];

            

            if ($this->input->method() == 'post') {

                $fields = json_decode($row->fields, true);

                $general_fields = $fields['general'];
                
                $translation_fields = (isset($fields['translation'])) ? $fields['translation'] : false;

                foreach($general_fields as $key => $g_field)
                {
                    $g_field['table'] = $this->input->post('general')[$key]['table'];
                    $g_field['label'] = $this->input->post('general')[$key]['label'];
                    $g_field['placeholder'] = $this->input->post('general')[$key]['placeholder'];

                    $form_fields['general'][$key] = $g_field;
                }

                if($translation_fields)
                {
                    foreach($translation_fields as $key => $t_field)
                    {
                        $t_field['table'] = $this->input->post('translation')[$key]['table'];
                        $t_field['label'] = $this->input->post('translation')[$key]['label'];
                        $t_field['placeholder'] = $this->input->post('translation')[$key]['placeholder'];

                        $form_fields['translation'][$key] = $t_field;
                    }
                }
                

                
                    $extension_data = [
                        'name' => json_encode($this->input->post('text')),
                        'fields' => json_encode($form_fields)
                    ];

                    $this->{$this->model}->update($extension_data, ['id' => $id]);

                    redirect('administrator/extension');
                
            }

            $this->template->render($this->controller . '/form');
        } else {
            show_404();
        }
    }
}
