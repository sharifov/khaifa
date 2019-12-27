<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends Core_Model
{

    public $table = 'groups';
    public $primary_key = 'id';
    public $protected = [];

    public function __construct()
    {
        parent::__construct();
    }

}