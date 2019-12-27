<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_menu_model extends Core_Model
{

    public $table = 'admin_menu';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'admin_menu_translation';
    public $table_translation_key = 'admin_menu_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}