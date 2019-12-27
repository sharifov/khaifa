<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends Core_Model
{

    public $table = 'permissions';
    public $primary_key = 'id';
    public $protected = [];
    public $rules = [];

    public function __construct()
    {
        parent::__construct();
    }

}