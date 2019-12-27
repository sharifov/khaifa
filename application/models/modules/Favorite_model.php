<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Favorite_model extends Core_Model
{

	public $table = 'favorite';
	public $primary_key = 'id';
	public $protected = [];
	public $timestamps = false;
	public $authors = false;

	public function __construct()
	{
		parent::__construct();
	}
}