<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extension_model extends Core_Model
{

    public $table = 'modules';

    public $primary_key = 'id';

    public $protected = [];

    public function __construct()
    {
        parent::__construct();
    }


    public function get_table()
    {
        $query = $this->db->query("SELECT table_name FROM information_schema.tables where table_schema='mimelon'");
        return $query->result_array();
    }

    public function table_field($table_name)
    {
        return $this->db->list_fields($table_name);
    }
}