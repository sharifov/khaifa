<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication_model extends Core_Model
{

    public $table = 'users';
    public $primary_key = 'id';
    public $protected = ['id'];
    public $rules = [];


    public function __construct()
    {

    }
}