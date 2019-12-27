<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_service_model extends Core_Model
{

	public $table = 'shipping_service';
	public $primary_key = 'id';
	public $protected = [];

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
	}
}