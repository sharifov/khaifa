<?php

class Seo_url_redirect_model extends  Core_Model{

    public $table = 'seo_url_redirect';
    public $primary_key = 'id';
    public $protected = [];


    public function __construct()
    {
        parent::__construct();
    }

}