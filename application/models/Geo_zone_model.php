<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Geo_zone_model extends Core_Model
{

    public $table = 'geo_zone';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}