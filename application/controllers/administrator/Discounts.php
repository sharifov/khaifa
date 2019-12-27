<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Discounts extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('modules/Category_model');
        $this->load->model('Discounts_model');
    }

    public function index()
    {
        $this->data['title'] = 'Discounts';
        $this->data['subtitle'] = 'Discounts for products';

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



        //Content Language
        if ($this->input->get('language_id')) {
            $language_id = (int) $this->input->get('language_id');
            $option_language_id = (int) $this->input->get('language_id');
            $this->session->set_userdata('option_language_id', $option_language_id);
        } elseif ($this->session->has_userdata('option_language_id')) {
            $language_id = (int) $this->session->userdata('option_language_id');
        } else {
            $language_id = $this->data['current_lang_id'];
        }
        // Sets Table columns
        $this->data['fields'] = ['id', 'title', 'discount','start_date', 'end_date'];

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

        // Sorts by column and order
        $sort = [
            'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
            'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC',
        ];

        $this->data['language_list_holder'] = [];

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
        $rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->with_translation($language_id)->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page - 1)->all();

        // Sets custom row's data options
        $custom_rows_data = [
            [
                'column' => 'status',
                'callback' => 'get_status',
                'params'    => []
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

        /*$this->data['buttons'][] = [
            'type' => 'button',
            'text' => translate('header_button_delete', true),
            'class' => 'btn btn-danger btn-labeled heading-btn',
            'id' => 'deleteSelectedItems',
            'icon' => 'icon-trash',
            'additional' => [
                'data-href' => site_url($this->directory . $this->controller . '/delete')
            ]
        ];*/

        // Sets Breadcrumb links
        $this->data['breadcrumb_links'][] = [
            'text' => translate('breadcrumb_link_all', true),
            'href' => site_url($this->directory . $this->controller),
            'icon_class' => 'icon-database position-left',
            'label_value' => $this->{$this->model}->count_rows(),
            'label_class' => 'label label-primary position-right',
        ];

/*        $this->data['breadcrumb_links'][] = [
            'text' => translate('breadcrumb_link_trash', true),
            'href' => site_url($this->directory . $this->controller . '/trash'),
            'icon_class' => 'icon-trash position-left',
            'label_value' => $this->{$this->model}->only_trashed()->count_rows(),
            'label_class' => 'label label-danger position-right',
        ];*/

        $this->template->render();
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

    public function getCategoryProducts($category_id)
    {
        $response = [];


        $productsQuery = $this->db->from('product_to_category')
            ->where('category_id', $category_id)
            ->get()
            ->result();


        $product_ids = [];

        foreach ($productsQuery as $value) {

            $product_ids[] = $value->product_id;

        }

        if( ! count($product_ids) ){

            header('Content-type: application/json');

            echo json_encode([]);

            die;
        }

                $where = [
                    'copied_product_id' => 0,
                    'status!=9' => null,
                    'id IN ('. implode(',', $product_ids).')'=> null
                ];
                $this->load->model('Product_model');
                $rows = $this->Product_model->fields("id,model")
                    ->filter($where)
                    ->with_translation($this->data['current_lang_id'])
                    ->as_array()
                    ->all();

                if($rows) {
                    $response = $rows;
                }

        $this->template->json($response);
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
        $this->data['fields'] = ['id', 'name', 'rate', 'type', 'geo_zone_id'];

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
                'column' => 'geo_zone_id',
                'callback' => 'get_option',
                'params' => [
                    'table' => 'geo_zone',
                    'key'   => 'id',
                    'value' => 'name'
                ]
            ],
            [
                'column' => 'type',
                'callback' => 'get_custom_data',
                'params' => ['P' => "Percentage",'F' => "Fixed Amount"]
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

    public function create()
    {

        $this->data['title']    = 'Discount create';
        $this->data['subtitle'] = 'Discount create';

        $tmp = ['Please select category'];
        $parents = $this->get_categories();

        foreach ($parents as $parent) {

            $tmp[$parent['id']] = $parent['name'];

            if(isset($parent['sub_categories']) && count($parent['sub_categories'])) {

                foreach ($parent['sub_categories'] as $child) {

                    $tmp[$child['id']] = str_repeat('&nbsp;', 3) . '-' . $child['name'];

                }

            }

        }




        // General Form Fields
        $this->data['form_field']['general'] = [
            'name' => [
                'property' => 'text',
                'name' => 'name',
                'class' => 'form-control',
                'label' => 'Discount name',
                'placeholder' => 'Discount name',
                'value' => set_value('name'),
                'validation' => ['rules' => 'required']
            ],
            'discount' => [
                'property' => 'number',
                'name' => 'discount',
                'class' => 'form-control',
                'label' => 'Discount price',
                'placeholder' => 'Discount price',
                'value' => set_value('discount'),
                'validation' => ['rules' => 'required']
            ],
            'discount_type' => [
                'property' => 'dropdown',
                'name' => 'discount_type',
                'class' => 'form-control',
                'label' => 'By price or percent (%)',
                'placeholder' => 'Discount price',
                'options' => [
                    1 => 'By price',
                    2 => 'By percent (%)',
                ],
                'validation' => ['rules' => 'required']
            ],
            'fake' => [
                'property' => 'dropdown',
                'name' => 'fake',
                'class' => 'form-control',
                'label' => 'Fake ?',
                'placeholder' => 'Fake ?',
                'options' => [
                    0 => 'No',
                    1 => 'Yes',
                ],
                'validation' => ['rules' => 'required']
            ],
            'start_date' => [
                'property' => 'date',
                'name' => 'start_date',
                'class' => 'form-control',
                'label' => 'Start date',
                'placeholder' => 'Start date',
                'validation' => []
            ],
            'end_date' => [
                'property' => 'date',
                'name' => 'end_date',
                'class' => 'form-control',
                'label' => 'End date',
                'placeholder' => 'End date',
                'validation' => []
            ],
            'categories' => [
                'property' => 'dropdown',
                'name' => 'categories',
                'class' => 'form-control',
                'label' => 'Categories',
                'placeholder' => 'categories',
                'options' => $tmp,
                'validation' => ['rules' => 'required']
            ],
        ];


        foreach ($this->data['languages'] as $language) {

            $this->data['form_field']['translation'][$language['id']]['title'] = [
                'property'    	=> "text",
                'name'        	=> 'translation[' . $language['id'] . '][title]',
                'class'       	=> 'form-control',
                'value'       	=> set_value('translation[' . $language['id'] . '][title]') ? set_value('translation[' . $language['id'] . '][title]') : "",
                'label'       	=> translate("form_label_name"),
                'placeholder' 	=> translate("form_label_name"),
                'validation'    => ['rules' => 'required']
            ];
        }


        $this->data['categories'] = $tmp;

        // Set form validation rules
        foreach ($this->data['form_field']['general'] as $key => $value)
        {
            if(isset($value['validation']) && $value['validation']) {
                $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
            }
        }

        if($this->input->post('password')) {
            $this->form_validation->set_rules('confirm_password', translate('form_label_confirm_password'), 'required|matches[password]');
        }



        $this->data['buttons'][] = [
            'type'       => 'button',
            'text'       => translate('form_button_save', true),
            'class'      => 'btn btn-primary btn-labeled heading-btn',
            'id'         => 'save',
            'icon'       => 'icon-floppy-disk',
            'additional' => [
                'onclick'    => "if(confirm('".translate('are_you_sure', true)."'))
                { 
                $('#form-save').submit();
                return false;
                 }else{ 
                 return false; 
                 }",
                'form'       => 'form-save',
                'formaction' => current_url()
            ]
        ];

        if ($this->input->method() == 'post') {
            if($this->form_validation->run() == true) {

                //print_r($_POST); die;
                $products_id = is_array($this->input->post('products')) ? implode(',', $this->input->post('products')) : '';




                $general = [
                    'name'  => $this->input->post('name'),
                    'discount'  => $price = $this->input->post('discount'),
                    'discount_type'  => $type = $this->input->post('discount_type'),
                    'fake'  =>  $fake = $this->input->post('fake'),
                    'products'  => $products_id,
                    'start_date' => $start_date = $this->input->post('start_date'),
                    'end_date' => $end_date = $this->input->post('end_date'),
                ];


                $id = $this->{$this->model}->insert($general);

                foreach($this->input->post('translation') as $disId => $value){
                        $data = [
                            'title'       => $value['title'],
                            'discount_id' => $id,
                            'language_id' => $disId
                        ];
                        $this->db->insert('wc_discount_translation',$data);
                }


                if($products_id) {

                    $this->db->query('UPDATE wc_product SET old_price=price WHERE id IN ('.$products_id.')');

                    if($id && $type == 1) {

                        if($fake) {
                            $this->db->query('UPDATE wc_product SET price=price+'. $price .' WHERE id IN ('.$products_id.')');
                        }

                        $this->db->query('INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price-'. $price .', "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');

                    }

                    elseif ($id && $type == 2){

                        if($fake) {
                            $this->db->query('UPDATE wc_product SET price=(price+(price * '. $price .') / 100) WHERE id IN ('.$products_id.')');

                            $this->db->query('INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price/( 1 + '. $price .' / 100 ), "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');

                        }
                        else {
                            $this->db->query('INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price-(price / 100)*'. $price .', "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');
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
        $this->data['title']    = 'Discount edit';
        $this->data['subtitle'] = 'Discount edit';

        //Content Language
        if ($this->input->get('language_id')) {
            $language_id = (int) $this->input->get('language_id');
            $option_language_id = (int) $this->input->get('language_id');
            $this->session->set_userdata('option_language_id', $option_language_id);
        } elseif ($this->session->has_userdata('option_language_id')) {
            $language_id = (int) $this->session->userdata('option_language_id');
        } else {
            $language_id = $this->data['current_lang_id'];
        }

        $discount = $this->{$this->model}->filter(['id' => $id])->with_translation($language_id)->one();



        if($discount) {

            $tmp = ['Please select category'];


            $parents = $this->get_categories();

            foreach ($parents as $parent) {

                $tmp[$parent['id']] = $parent['name'];

                if(isset($parent['sub_categories']) && count($parent['sub_categories'])) {

                    foreach ($parent['sub_categories'] as $child) {

                        $tmp[$child['id']] = str_repeat('&nbsp;', 3) . '-' . $child['name'];

                    }

                }

            }


            // General Form Fields
            $this->data['form_field']['general'] = [
                'name' => [
                    'property' => 'text',
                    'name' => 'name',
                    'class' => 'form-control',
                    'label' => 'Discount name',
                    'placeholder' => 'Discount name',
                    'value' => (set_value('name')) ? set_value('name') : $discount->name,
                    'validation' => ['rules' => 'required']
                ],
                'discount' => [
                    'property' => 'text',
                    'name' => 'discount',
                    'class' => 'form-control',
                    'label' => 'Discount price',
                    'placeholder' => 'Discount price',
                    'value' => (set_value('discount')) ? set_value('discount') : $discount->discount,
                    'validation' => ['rules' => 'required']
                ],
                'discount_type' => [
                    'property' => 'dropdown',
                    'name' => 'discount_type',
                    'class' => 'form-control',
                    'label' => 'By price or percent (%)',
                    'placeholder' => 'Discount price',
                    'options' => [
                        1 => 'By price',
                        2 => 'By percent',
                    ],
                    'selected' => (set_value('discount_type')) ? set_value('discount_type') : $discount->discount_type,
                    'validation' => ['rules' => 'required']
                ],
                'fake' => [
                    'property' => 'dropdown',
                    'name' => 'fake',
                    'class' => 'form-control',
                    'label' => 'Fake ?',
                    'placeholder' => 'Fake ?',
                    'options' => [
                        0 => 'No',
                        1 => 'Yes',
                    ],
                    'selected' => (set_value('fake')) ? set_value('fake') : $discount->fake,
                    'validation' => ['rules' => 'required']
                ],
                'start_date' => [
                    'property' => 'date',
                    'name' => 'start_date',
                    'class' => 'form-control',
                    'label' => 'Start date',
                    'value' => (set_value('start_date')) ? set_value('start_date') : $discount->start_date,
                    'placeholder' => 'Start date',
                    'validation' => []
                ],
                'end_date' => [
                    'property' => 'date',
                    'name' => 'end_date',
                    'class' => 'form-control',
                    'label' => 'End date',
                    'placeholder' => 'End date',
                    'value' => (set_value('end_date')) ? set_value('end_date') : $discount->end_date,
                    'validation' => []
                ],
                'categories' => [
                    'property' => 'dropdown',
                    'name' => 'categories',
                    'class' => 'form-control',
                    'label' => '',
                    'placeholder' => 'categories',
                    'options' => $tmp,
                    'validation' => ['rules' => 'required']
                ],
            ];

            foreach ($this->data['languages'] as $language) {

                $row_translation = $this->{$this->model}->fields('*')->filter(['discount_id' => $id])->with_translation($language['id'])->one();

                $this->data['form_field']['translation'][$language['id']]['title'] = [
                    'property'    	=> "text",
                    'name'        	=> 'translation[' . $language['id'] . '][title]',
                    'class'       	=> 'form-control',
                    'value'       	=> set_value('translation[' . $language['id'] . '][title]') ? set_value('translation[' . $language['id'] . '][title]') : $row_translation->title,
                    'label'       	=> translate("form_label_name"),
                    'placeholder' 	=> translate("form_label_name"),
                    'validation'    => ['rules' => 'required']
                ];
            }

            // Set form validation rules
            foreach ($this->data['form_field']['general'] as $key => $value)
            {
                if(isset($value['validation']) && $value['validation']) {
                    $this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
                }
            }

            if($this->input->post('password')) {
                $this->form_validation->set_rules('password', translate('form_label_confirm_password'), 'required|min_length[5]');
                $this->form_validation->set_rules('confirm_password', translate('form_label_confirm_password'), 'required|matches[password]');
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

                    $products_id = is_array($this->input->post('products')) ? implode(',', $this->input->post('products')) : '';

                    $general = [
                        'name'  => $this->input->post('name'),
                        'discount'  => $price = $this->input->post('discount'),
                        'discount_type'  => $type = $this->input->post('discount_type'),
                        'fake'  => $fake = $this->input->post('fake'),
                        'products'  => $products_id,
                        'start_date' => $start_date = $this->input->post('start_date'),
                        'end_date' => $end_date = $this->input->post('end_date'),
                    ];


                    foreach($this->input->post('translation') as $langid => $langvalue){
                            $data = [
                              'title' => $langvalue['title']
                            ];
                            $this->db->where('discount_id',$id);
                            $this->db->where('language_id',$langid);
                            $this->db->update('wc_discount_translation',$data);
                    }


                    $this->db->delete('product_special', array('discount_id' => $id));

                    if($discount->fake) {

                        $this->db->query('UPDATE wc_product SET price=IFNULL(old_price, price) WHERE id IN ('.$discount->products.')');
                        $this->db->query('UPDATE wc_product SET old_price=NULL WHERE id IN ('.$discount->products.')');

                    }

                    if($products_id) {


                        if($id && $type == 1) {

                            if($fake) {

                                $this->db->query('UPDATE wc_product SET old_price=price WHERE id IN ('.$products_id.')');

                                $this->db->query('UPDATE wc_product SET price=price+'. $price .' WHERE id IN ('.$products_id.')');
                            }

                            $this->db->query('INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price-'. $price .', "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');

                        }

                        elseif ($id && $type == 2){

                            if($fake) {

                                $this->db->query('UPDATE wc_product SET old_price=price WHERE id IN ('.$products_id.')');

                                $this->db->query('UPDATE wc_product SET price=(price+(price * '. $price .') / 100) WHERE id IN ('.$products_id.')');

                                $this->db->query('INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price/( 1 + '. $price .' / 100 ), "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');

                            }
                            else {
                                $this->db->query('INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price-(price / 100)*'. $price .', "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');
                            }
                        }
                    }

                    /*if($products_id) {

                        if($discount->discount_type == 1 && $discount->fake) {

                            $this->db->query('UPDATE wc_product SET price=price-'. $discount->discount .' WHERE id IN ('.rtrim($discount->products, ',').')');

                        }
                        elseif($discount->discount_type == 2 && $discount->fake) {

                            $this->db->query('UPDATE wc_product SET price=price-(price / 100)*'. $discount->discount .' WHERE id IN ('.rtrim($discount->products, ',').')');

                        }

                        if($id && $type == 1) {

                            if($fake) {
                                $this->db->query('UPDATE wc_product SET price=price+'. $price .' WHERE id IN ('.$products_id.')');
                            }

                            $this->db->query($sql = 'INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price-'. $price .', "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');

                        }
                        elseif ($id && $type == 2){

                            if($fake) {
                                $this->db->query('UPDATE wc_product SET price=price+(price / 100)*'. $price .' WHERE id IN ('.$products_id.')');
                                $this->db->query($sql = 'INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price-(price-(price / (1 + '. $price .'/ 100))), "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');
                            }
                            else{
                                $this->db->query($sql = 'INSERT INTO wc_product_special (product_id, discount_id, customer_group_id, priority, price, date_start, date_end) SELECT id, '. $id .', 1, 0, price-(price / 100)*'. $price .', "'. $start_date .'", "'. $end_date .'" FROM wc_product WHERE id IN ('.$products_id.')');
                            }
                        }
                    }*/

                    $this->{$this->model}->update($general,['id' => $id]);


                    $this->session->set_flashdata('message', translate('form_success_edit'));
                    redirect(site_url_multi($this->directory . $this->controller), 'refresh');
                } else {

                    $this->data['message'] = translate('error_warning', true);
                }
            }


            $this->load->model('Product_model');


            if($discount->products) {
                $this->data['products'] = $this->Product_model->fields("id,model")
                    ->filter(['id IN ('. $discount->products .')' => null])
                    ->with_translation($this->data['current_lang_id'])
                    ->as_array()
                    ->all();
            }
            else {
                $this->data['products'] = [];
            }



            $this->template->render($this->controller . '/form');


        } else {
            show_404();
        }
    }

    public function delete($id = false)
    {
        if ($id) {

            $discount = $this->{$this->model}->filter(['id' => $id])->one();

            if($discount->products && $discount->fake) {

                $this->db->query('UPDATE wc_product SET price=old_price WHERE (id IN ('.rtrim($discount->products, ',').') AND old_price IS NOT NULL)');
                $this->db->query('UPDATE wc_product SET old_price=NULL  WHERE (id IN ('.rtrim($discount->products, ',').') AND old_price IS NOT NULL)');

                /*if($discount->discount_type == 1 && $discount->fake) {

                    $this->db->query('UPDATE wc_product SET price=price-'. $discount->discount .' WHERE id IN ('.rtrim($discount->products, ',').')');

                }
                elseif($discount->discount_type == 2 && $discount->fake) {

                    $this->db->query('UPDATE wc_product SET price=price-(price-(price / (1 + '. $discount->discount .'/ 100))) WHERE id IN ('.rtrim($discount->products, ',').')');

                }*/
            }


            $this->db->delete('product_special', array('discount_id' => $id));

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


            $discount = $this->{$this->model}->filter(['id' => $id])->one();

            if($discount->products) {

                if($discount->discount_type == 1 && $discount->fake) {

                    $this->db->query('UPDATE wc_product SET price=price-'. $discount->discount .', old_price=NULL WHERE id IN ('.rtrim($discount->products, ',').')');

                }
                elseif($discount->discount_type == 2 && $discount->fake) {

                    $this->db->query('UPDATE wc_product SET price=price-(price-(price / (1 + '. $discount->discount .'/ 100))), old_price=NULL WHERE id IN ('.rtrim($discount->products, ',').')');

                }
            }

            $this->db->delete('product_special', array('discount_id' => $id));
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
//        $this->{$this->model}->force_delete_option('all');
        redirect(site_url_multi($this->directory . $this->module_name));
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
