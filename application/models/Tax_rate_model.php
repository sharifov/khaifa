<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_rate_model extends Core_Model
{

    public $table = 'tax_rate';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}