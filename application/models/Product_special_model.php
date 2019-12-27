<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_special_model extends Core_Model
{

    public $table = 'product_special';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}