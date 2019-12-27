<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_account_model extends Core_Model
{

	public $table = 'bank_account';
	public $primary_key = 'id';
	public $protected = [];

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
	}
}