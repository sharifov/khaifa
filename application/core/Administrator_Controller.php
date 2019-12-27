<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administrator_Controller extends Core_Controller
{
	public $admin_url;

	public function __construct()
	{
		parent::__construct();

		//Set Admin URL
		$this->admin_url = get_setting('admin_url');
		$this->data['admin_url'] = $this->admin_url;

		$this->load->library('Wc_table');
		$this->load->library('Sidebar');

		$this->data['per_page'] = get_setting('per_page');

		//Per Page Options
		$per_page_lists = get_setting('per_page_list');

		if ($per_page_lists) {
			foreach ($per_page_lists as $per_page_list) {
				$this->data['per_page_lists'][$per_page_list] = $per_page_list;
			}
		}

		if ($this->controller != 'authentication') {
			if (!$this->auth->is_loggedin()) {
				redirect($this->admin_url.'/authentication/login');
				exit();
			}
			
			if (!check_permission()) {
				show_error('Your are not permitted');
				exit();
			}
		}


		

		$this->data['user'] = $this->auth->get_user();

		if ($this->uri->segment(1) . '/' == $this->directory) {
			$this->module_name = $this->uri->segment(2);
		} else {
			$this->module_name = $this->uri->segment(3);
		}

		
		$this->data['sidebar_menus'] = $this->sidebar->getMenu();
		if($this->auth->is_member('vendor')) {
			$this->data['sidebar_menus'] = $this->sidebar->getMenuVendor();
		}
		

		/* Load Breadcrumb Home Link */
		$this->breadcrumbs->push(translate('breadcrumb_home', true), $this->directory . 'dashboard');

		if ($this->directory != 'admin' && $this->controller != 'module') {
			$this->breadcrumbs->push(translate('index_title'), $this->directory . $this->controller);
		}

	   

		//Default Error Delimiters
		$this->form_validation->set_error_delimiters('<span class="help-block">', '</span>');

		$this->data['copyright'] = sprintf(translate('footer_copyright', true), date('Y'), VERSION);
		$this->data['elapsed_time'] = $this->benchmark->elapsed_time();
		$this->data['memory_usage'] = $this->benchmark->memory_usage();

		$this->theme = get_setting('admin_theme');
		$this->data['admin_theme'] = base_url('templates/'.$this->admin_url.'/'.$this->theme);

		//Load Model Per Controller
		$this->load->model($this->model);
	}
}
