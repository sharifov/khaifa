<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_option_model extends Core_Model
{

	public $table = 'order_option';
	public $primary_key = 'id';
	public $protected = [];
	public $authors = false;

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
	}
}