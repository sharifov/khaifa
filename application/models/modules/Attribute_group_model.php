<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attribute_group_model extends Core_Model
{

    public $table = 'attribute_group';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}