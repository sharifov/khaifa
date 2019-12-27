<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_group_model extends Core_Model
{

    public $table = 'customer_group';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'customer_group_translation';
    public $table_translation_key = 'customer_group_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}