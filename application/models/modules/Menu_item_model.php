<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_item_model extends Core_Model
{

    public $table = 'menu_item';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'menu_item_translation';
    public $table_translation_key = 'menu_item_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}