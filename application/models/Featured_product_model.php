<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Featured_product_model extends Core_Model
{

    public $table = 'featured_product';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}