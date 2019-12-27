<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Extension_model');
		$this->load->model('Product_model');
		$this->load->model('Order_product_model');
	}
	
	/**
	* public function index()
	* Runs as default when this controller requested if any other method is not specified in route file.
	* Sets all data which will be displayed on index page of this controller. At final sends data to target template.
	*/
	
	public function index()
	{
		$this->data['title']			= translate('index_title');
		$this->data['subtitle']			= translate('index_description');
		
		if($this->auth->is_member('admin'))
		{
			$this->data['title']			= translate('index_title');
			$this->data['subtitle']			= translate('index_description');

			$modules = $this->Extension_model->filter(['status' => 1])->all();

			$data = [];
			if ($modules) {

				
				foreach ($modules as $module) {

					$name = (isset(json_decode($module->name)->index->title->{$this->data['current_lang']})) ? json_decode($module->name)->index->title->{$this->data['current_lang']} : json_decode($module->name)->index->title->{$this->data['default_language']};

					$model = $module->slug.'_model';
					$this->load->model('modules/'.$model);
					$mod = new stdClass();
					$mod->name = $name;
					$mod->icon = $module->icon;
					$mod->link = site_url_multi($this->admin_url.'/'.$module->slug);
					$mod->active_count = $this->{$model}->filter(['status' => 1])->count_rows();
					$mod->deactive_count = $this->{$model}->filter(['status' => 0])->count_rows();

					$data[] = $mod;
				}
			}

			$this->data['modules'] = $data;

			$this->template->render();
		}
		else
		{
			$this->data['title']			= translate('vendor_title');
			$this->data['subtitle']			= translate('vendor_description');

			// Filters for banned and not specified name
			$filter = [];

			$products = $this->Product_model->filter(['created_by' => $this->auth->get_user()->id])->all();

			$product_ids = [];
			if($products)
			{
				foreach($products as $product)
				{
					$product_ids[] = $product->id;
				}
			}

			

			
			if ($this->input->get('id') != null) {
				$filter['id LIKE "%' . $this->input->get('id') . '%"'] = null;
			}
			if(!empty($product_ids))
			{
				$filter['product_id IN (' . implode(',', $product_ids) . ')'] = null;
			}
			else
			{
				$filter['product_id IN (999999999999999999999)'] = null;
			}

			$total_rows = $this->Order_product_model->where($filter)->with_trashed()->count_rows();

			$this->data['balance']			= $this->auth->get_user()->balance;
			$this->data['total_order']		= $total_rows;

			$this->load->model('Product_model');
			$this->data['product_count'] = $this->Product_model->filter(['status' => 1, 'created_by' => $this->auth->get_user()->id])->with_translation()->count_rows();
			$this->data['product_count_waiting'] = $this->Product_model->filter(['status' => 2, 'created_by' => $this->auth->get_user()->id])->with_translation()->count_rows();
			$this->data['product_count_deactive'] = $this->Product_model->filter(['status' => 0, 'created_by' => $this->auth->get_user()->id])->with_translation()->count_rows();

			$this->template->render('dashboard/vendor');
		}
	}
}
