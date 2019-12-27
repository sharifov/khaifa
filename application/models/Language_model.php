<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language_model extends Core_Model
{

    public $table = 'languages';
    public $primary_key = 'id';
    public $protected = ['id'];
    public $rules = [];


    public function __construct()
    {
        parent::__construct();
    }

}