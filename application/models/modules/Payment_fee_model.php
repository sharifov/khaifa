<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_fee_model extends Core_Model
{

    public $table = 'payment_fee';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}