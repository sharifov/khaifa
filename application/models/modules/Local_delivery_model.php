<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Local_delivery_model extends Core_Model
{
    public $table = 'local_delivery';

    public $primary_key = 'id';

    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}