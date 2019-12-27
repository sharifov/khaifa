<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends Core_Model
{

    public $table = 'menu';
    public $primary_key = 'id';
    public $protected = [];

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_items($menu_id, $lang_id, $status)
    {
        $this->db->where('menu_item.menu_id', $menu_id);
        $this->db->where('menu_item_translation.language_id', $lang_id);
        $this->db->where('menu_item.status', $status);
        $this->db->join('menu_item_translation', 'menu_item.id = menu_item_translation.menu_item_id');
        $this->db->order_by("menu_item.order", "ASC");
        return $this->db->get('menu_item')->result_array();
    }
}