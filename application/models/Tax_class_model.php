<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_class_model extends Core_Model
{

    public $table = 'tax_class';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = '';
    public $table_translation_key = '';
    public $table_language_key = '';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

}