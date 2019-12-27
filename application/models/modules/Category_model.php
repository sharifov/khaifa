<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends Core_Model
{

    public $table = 'category';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'category_translation';
    public $table_translation_key = 'category_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_product_to_category_sum($ids = []){
        if($ids) {
            $this->db->select('wc_product_to_category.category_id,COUNT(product_id) as quantity, wc_category_translation.name,wc_category_translation.slug');
            $this->db->join('category','product_to_category.category_id = category.id','Left');
            $this->db->join('category_translation','product_to_category.category_id = category_translation.category_id','Left');
            $this->db->where('product_to_category.category_id IN('.implode(",",$ids).')', null);
            $this->db->where('category.status', 1);
            $this->db->where('category_translation.language_id', $this->data['current_lang_id']);
            $this->db->group_by('product_to_category.category_id');
            $query = $this->db->get('product_to_category');
            if($query->num_rows() > 0) {
                return $query->result();
            }
        }
        return false;
    } 


    public function get_category_product_count($category_id) {
        $this->db->select('COUNT(wc_product.id) as product_count');
        $this->db->join('wc_product', 'wc_product_to_category.product_id = wc_product.id','RIGHT');
        $this->db->where([
            'wc_product_to_category.category_id' => $category_id, 
            'wc_product.status' => 1, 
            'wc_product.quantity > 0' => null, 
            'wc_product.deleted_at is null' => null
        ]);
        $query = $this->db->get('wc_product_to_category');
        if($query->num_rows() > 0) {
            return $query->row()->product_count;
        }
        return 0;
    }
}