<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Weight_class_model extends Core_Model
{

    public $table = 'weight_class';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'weight_class_translation';
    public $table_translation_key = 'weight_class_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}