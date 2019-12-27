<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Geo_zone extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('modules/Country_model');
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
        $this->data['fields'] = ['id', 'name', 'description'];

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
        $custom_rows_data = [];

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
        $this->data['title'] = translate('trash_title');
        $this->data['subtitle'] = translate('trash_description');

        // Sets Table columns
        $this->data['fields'] = ['id', 'name', 'description'];

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

        // Sets custom row's data options
        $custom_rows_data = [];

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

    public function ajax_get_zones()
    {
        $response = new stdClass();
        $country_id = $this->input->get('country_id');
        if($country_id) {

            $this->load->model('Country_model');
            $response = $this->Country_model->fields('id as country_id, name, iso_code_2, iso_code_3, postcode_required, status, address_format')->filter(['id' => $country_id])->with_translation()->one();
            
            $this->load->model('modules/Zone_model');
            $rows = $this->Zone_model->fields('id as zone_id, country_id, name, code, status')->filter(['country_id' => $country_id])->as_array()->all();
            $response->zone = $rows;
            
        }

        $this->template->json($response);
    }

    public function create()
    {

        $this->data['title']    = translate('create_title');
        $this->data['subtitle'] = translate('create_description');
        
        // Form data
        $this->data['countries'] = $this->Country_model->fields("id as country_id,name")->with_translation($this->data['current_lang_id'])->as_array()->all();
        
        $this->data['zone_to_geo_zones'] = set_value('zone_to_geo_zone');

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
            'description' => [
                'property' => 'textarea',
                'name' => 'descriprion',
                'class' => 'form-control',
                'label' => translate('form_label_description'),
                'placeholder' => translate('form_label_description'),
                'value' => set_value('description'),
                'validation' => []
            ]
        ];
        
        
        // Set form validation rules
        foreach ($this->data['form_field']['general'] as $key => $value)
        {   
            if($value['validation']) {
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
                    'description'  => $this->input->post('description')
                ];
                
                $id = $this->{$this->model}->insert($general);

                if($id) {
                    if($this->input->post('zone_to_geo_zone')) {
                        foreach($this->input->post('zone_to_geo_zone') as $geo_zone) {
                            $data[0] = [
                                'country_id' => $geo_zone['country_id'],
                                'zone_id' => $geo_zone['zone_id'],
                                'geo_zone_id' => $id
                            ];
    
                            $this->{$this->model}->insert_additional_data('zone_to_geo_zone',$data);
                        }
                    }
                }

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

        $geo_zone = $this->{$this->model}->filter(['id' => $id])->one();

        if($geo_zone) {

            // Form data
            $this->data['countries'] = $this->Country_model->fields("id as country_id,name")->with_translation($this->data['current_lang_id'])->as_array()->all();
            
            $zone_to_geo_zones = [];
            $rows = $this->{$this->model}->get_additional_data('zone_to_geo_zone','*',['geo_zone_id' => $id]);
            if($rows) {
                foreach($rows as $row) {
                    $zone_to_geo_zones[] = [
                        'country_id' => $row->country_id,
                        'zone_id' => $row->zone_id
                    ];
                }
            }

            $this->data['zone_to_geo_zones'] = (set_value('zone_to_geo_zone')) ? set_value('zone_to_geo_zone') : $zone_to_geo_zones;
            
             // General Form Fields
            $this->data['form_field']['general'] = [
                'name' => [
                    'property' => 'text',
                    'name' => 'name',
                    'class' => 'form-control',
                    'label' => translate('form_label_name'),
                    'placeholder' => translate('form_label_name'),
                    'value' => (set_value('name')) ? set_value('name') : $geo_zone->name,
                    'validation' => ['rules' => 'required']
                ],
                'description' => [
                    'property' => 'textarea',
                    'name' => 'description',
                    'class' => 'form-control',
                    'label' => translate('form_label_description'),
                    'placeholder' => translate('form_label_description'),
                    'value' => (set_value('description')) ? set_value('description') : $geo_zone->description,
                    'validation' => []
                ]
            ];
            
            
            // Set form validation rules
            foreach ($this->data['form_field']['general'] as $key => $value)
            {   
                if($value['validation']) {
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
                        'description'  => $this->input->post('description')
                    ];

                    $id = $this->{$this->model}->update($general,['id' => $id]);
                    
                    $this->{$this->model}->delete_additional_data('zone_to_geo_zone',['geo_zone_id' => $id]);

                    if($this->input->post('zone_to_geo_zone')) {
                        foreach($this->input->post('zone_to_geo_zone') as $geo_zone) {
                            $data[0] = [
                                'country_id' => $geo_zone['country_id'],
                                'zone_id' => $geo_zone['zone_id'],
                                'geo_zone_id' => $id
                            ];
    
                            $this->{$this->model}->insert_additional_data('zone_to_geo_zone',$data);
                        }
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
