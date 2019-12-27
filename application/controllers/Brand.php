<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Brand extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->model('Review_model');
		$this->load->model('Option_model');
		$this->load->model('Attribute_model');
		$this->load->model('Product_special_model');
		$this->load->model('modules/Category_model');
		$this->load->model('modules/Brand_model');
	}

	
	public function index($slug = false, $page = 1) 
	{
        
	}
}