<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_translation_model extends Core_Model
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


    public function get_menu_data($table_name) {
        $this->db->select('id, name');
        $query = $this->db->get($table_name);
        if($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function update_menu_data($table_name, $where, $data) {
        $this->db->where($where);
        $this->db->update($table_name, $data);
    }

}