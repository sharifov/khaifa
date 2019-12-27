<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address_model extends Core_Model
{

    public $table = 'address';
    public $primary_key = 'id';
    public $protected = [];
    public $authors = false;
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
    }
}