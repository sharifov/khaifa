<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transaction extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	

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

		// Table Column
		$this->data['fields'] = ['id', 'status', 'amount', 'user_id', 'bank_account', 'comment'];

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
		if ($this->input->get('id') != null) {
			$filter['id'] = $this->input->get('id');
		}

		if (!$this->auth->is_admin())
		{
			$filter['user_id'] = $this->auth->get_user()->id;
		}


		$sort = [
			'column' => ($this->input->get('column')) ? $this->input->get('column') : 'created_at',
			'order' => ($this->input->get('order')) ? $this->input->get('order') : 'DESC'
		];

		$this->data['total_rows'] = $this->{$this->model}->filter($filter)->with_trashed()->count_rows();
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


		$total_rows = $this->{$this->model}->where($filter)->with_trashed()->count_rows();
		$rows = $this->{$this->model}->fields($this->data['fields'])->filter($filter)->with_trashed()->order_by($sort['column'], $sort['order'])->limit($this->data['per_page'], $page-1)->all();


		if(is_admin()) {
			$action_buttons['custom'] =  [
				[
					'href_value' => 'id',
					'href_value2' => 'status',
					'icon' => 'icon-check',
					'text' => translate('confirm'),
					'href' => site_url_multi($this->directory.$this->controller.'/confirm/'),
				],
				[
					'href_value' => 'id',
					'href_value2' => 'status',
					'icon' => 'icon-cross',
					'text' => translate('cancel'),
					'href' => site_url_multi($this->directory.$this->controller.'/cancel/'),
				]
			];	
		}
		else
		{
			$action_buttons = [];
		}
		

		$custom_rows_data = [
			[
				'column'	=> 'user_id',
				'callback'	=> 'get_seller',
				'params'	=> []
			],
			[
				'column'	=> 'amount',
				'callback'	=> 'currency_format',
				'params'	=> ['currency' => get_setting('currency')]
			],
			[
				'column'	=> 'status',
				'callback'	=> 'get_tr_status',
				'params'	=> []
			],
			[
				'column'	=> 'bank_account',
				'callback'	=> 'get_bank_account',
				'params'	=> []
			],

		];

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

		$this->template->render();
	}


	public function create()
	{
		$this->data['title'] 	= translate('create_title');
		$this->data['subtitle'] = translate('create_description');


		
		$this->load->model('modules/Bank_account_model');;
		$bank_accounts = $this->Bank_account_model->filter(['status' => 1, 'created_by' => $this->auth->get_user()->id])->all();
		$bank_account_options[0] = translate('cash');
		if($bank_accounts)
		{
		   foreach($bank_accounts as $bank_account)
		   {
			   $bank_account_options[$bank_account->id] = $bank_account->account_holder.' '.$bank_account->bank_name. ' ('.$bank_account->account_number.')'; 
		   }
		}

		$this->data['form_field']['general'] = [
			'amount'		=> [
				'property'		=> 'text',
				'id'       		=> 'amount',
				'name'          => 'amount',
				'class'			=> 'form-control',
				'value'         => set_value('amount'),
				'label'			=> translate('form_label_amount'),
				'placeholder'	=> translate('form_placeholder_amount'),
				'validation'	=> ['rules' => 'required']
			],
			'comment'		=> [
				'property'		=> 'textarea',
				'name'          => 'comment',
				'class'			=> 'form-control',
				'value'         => set_value('comment'),
				'label'			=> translate('form_label_comment'),
				'placeholder'	=> translate('form_placeholder_comment'),
				'validation'	=> ['rules' => '']
			]
		];

		$this->data['form_field']['general']['bank_account'] = [
			'property'		=> 'dropdown',
			'name'          => 'bank_account',
			'class'			=> 'form-control',
			'selected'      => set_value('bank_account'),
			'label'			=> translate('form_label_bank_account'),
			'options'		=> $bank_account_options,
			'validation'	=> ['rules' => '']
		];

		if($this->auth->is_admin())
		{
			$this->load->model('User_model');
			$users = $this->User_model->filter(['banned' => 0])->all();
			$this->load->library('Currency');
			 if($users)
			 {
				foreach($users as $user)
				{
					$user_options[$user->id] = $user->firstname.' '.$user->lastname. ' ('.$user->email.') - '.$this->currency->format($user->balance, get_setting('currency')); 
				}
			 }
			 
			


			$this->data['form_field']['general']['user_id'] = [
				'property'		=> 'dropdown',
				'name'          => 'user_id',
				'class'			=> 'form-control',
				'selected'      => set_value('user_id'),
				'label'			=> translate('form_label_user_id'),
				'options'		=> $user_options,
				'validation'	=> ['rules' => 'required']
			];

			$status_options[0] = translate('pending');
			$status_options[1] = translate('complete');
			$status_options[2] = translate('canceled');

		
			$this->data['form_field']['general']['status'] = [
				'property'		=> 'dropdown',
				'name'          => 'status',
				'class'			=> 'form-control',
				'selected'      => set_value('status'),
				'label'			=> translate('form_label_status'),
				'options'		=> $status_options,
				'validation'	=> ['rules' => 'required']
			];
		}

		foreach ($this->data['form_field']['general'] as $key => $value) {
			$this->form_validation->set_rules($value['name'], $value['label'], $value['validation']['rules']);
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

		$this->breadcrumbs->push(translate('create_title'), $this->directory.$this->controller.'/create');

		if ($this->input->method() == 'post') {
			if ($this->form_validation->run() == true) {
				$general = [
					'bank_account'	=> $this->input->post('bank_account'),
					'amount'		=> $this->input->post('amount'),
					'user_id'		=> ($this->auth->is_admin()) ? $this->input->post('user_id') : $this->auth->get_user()->id,
					'comment'		=> $this->input->post('comment'),
					'status'		=> 0,
				];

				
				if($this->auth->get_user($general['user_id'])->balance >= $this->input->post('amount'))
				{
					$this->{$this->model}->insert($general);

					
						$amount = $this->auth->get_user($general['user_id'])->balance-$this->input->post('amount');
						$this->db->where('id', $general['user_id']);
						$this->db->update('users', ['balance' => $amount]);
						redirect(site_url_multi($this->directory.$this->controller));
				}
				else
				{
					$this->data['message'] = translate('low_balance');
				}
				
				

				
			} else {
				$this->data['message'] = translate('error_warning', true);
			}
		}

		$this->template->render($this->controller.'/form');
	}

	public function confirm($id = false)
	{
		if($id)
		{
			$transaction = $this->{$this->model}->filter(['id' => $id, 'status !=' => 1])->with_trashed()->one();
			if($transaction)
			{
				$this->{$this->model}->update(['status' => 1], $id);
				redirect(site_url_multi($this->directory.$this->controller));
				
			}
		}
	}

	public function cancel($id = false)
	{
		if($id)
		{
			$transaction = $this->{$this->model}->filter(['id' => $id, 'status !=' => 1])->with_trashed()->one();
			if($transaction)
			{
				$amount = $this->auth->get_user($transaction->user_id)->balance+$transaction->amount;
				$this->db->where('id', $transaction->user_id);
				$this->db->update('users', ['balance' => $amount]);

				$this->{$this->model}->update(['status' => 2], $id);
				redirect(site_url_multi($this->directory.$this->controller));
				
			}
		}
	}

}