<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_method_model extends Core_Model
{

    public $table = 'shipping_method';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'shipping_method_translation';
    public $table_translation_key = 'shipping_method_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}