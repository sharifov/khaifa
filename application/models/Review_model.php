<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Review_model extends Core_Model
{

    public $table = 'review';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
        $this->authors = false;
    }
}