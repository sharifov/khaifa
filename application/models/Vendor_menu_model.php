<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_menu_model extends Core_Model
{

	public $table = 'vendor_menu';
	public $primary_key = 'id';
	public $protected = [];

	public function __construct()
	{
		parent::__construct();
	}

}