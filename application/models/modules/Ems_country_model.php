<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ems_country_model extends Core_Model
{

    public $table = 'ems_country';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}