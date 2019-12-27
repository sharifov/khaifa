<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer_approval extends Administrator_Controller
{
    public function __construct()
    {
        parent::__construct();
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
                'name' => 'firstname',
                'class' => 'form-control',
                'value' => $this->input->get('firstname'),
                'placeholder' => translate('search_placeholder', true),
            ],
        ];

        // Sets Table columns
        $this->data['fields'] = ['id', 'firstname', 'lastname', 'telephone', 'email'];

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
        $filter = ['approval is null' => null];
        if ($this->input->get('firstname') != null) {
            $filter['firstname LIKE "%' . $this->input->get('firstname') . '%"'] = null;
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
        
        $action_buttons['custom'] = [
            [
                'href' => site_url($this->directory . $this->controller).'/customerApproval/1/',
                'href_value' => 'id',
                'text'  => 'Approve',
                'icon' => 'icon-checkmark4'
            ],
            [
                'href' => site_url($this->directory . $this->controller).'/customerApproval/0/',
                'href_value' => 'id',
                'text'  => 'Refuse',
                'icon' => 'icon-cross2'
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

        // Sets Breadcrumb links
        $this->data['breadcrumb_links'][] = [
            'text' => translate('breadcrumb_link_all', true),
            'href' => site_url($this->directory . $this->controller),
            'icon_class' => 'icon-database position-left',
            'label_value' => $this->{$this->model}->filter($filter)->count_rows(),
            'label_class' => 'label label-primary position-right',
        ];

        $this->template->render();
    }

    public function customerApproval($approve, $user_id)
	{
        $this->{$this->model}->update(['approval' => $approve],['id' => $user_id]);
		$this->template->json(['status' => true]);
	}
    
}
