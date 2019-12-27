<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_status_model extends Core_Model
{

    public $table = 'order_status';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'order_status_translation';
    public $table_translation_key = 'order_status_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}