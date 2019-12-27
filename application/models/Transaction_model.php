<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends Core_Model
{

	public $table = 'transaction';
	public $primary_key = 'id';
	public $protected = [];

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
	}
}