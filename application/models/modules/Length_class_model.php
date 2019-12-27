<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Length_class_model extends Core_Model
{

    public $table = 'length_class';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'length_class_translation';
    public $table_translation_key = 'length_class_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}