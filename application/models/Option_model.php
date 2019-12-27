<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Option_model extends Core_Model
{

    public $table = 'option';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'option_translation';
    public $table_translation_key = 'option_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function force_delete_option($id){
        if($id == 'all') {
            $this->database->delete('option');
            $this->database->delete('option_translation');
            $this->database->delete('option_value');
            $this->database->delete('option_value_description');
        } else {
            $this->database->where('id', $id);
            $this->database->delete('option');

            $this->database->where('option_id', $id);
            $this->database->delete('option_translation');

            $this->database->where('option_id', $id);
            $this->database->delete('option_value');

            $this->database->where('option_id', $id);
            $this->database->delete('option_value_description');
        }
        
    }
    public function insert_option_value($data) {
        if($data) {
            $this->db->insert('option_value',$data);
            return $this->db->insert_id();
        }
        return false;
    }

    public function insert_option_value_translation($data) {
        $this->db->insert('option_value_description',$data);
    }

    public function get_option_value($option_id){
        $this->db->where(['option_id' => $option_id]);
        $query = $this->db->get('option_value');

        if($query->num_rows() > 0){
            return $query->result();
        }
        return false;
    }

    public function get_option_value_translation($option_value_id){
        $this->db->where(['option_value_id' => $option_value_id]);
        $query = $this->db->get('option_value_description');
        if($query->num_rows() > 0){
            return $query->result();
        }
        return false;
    }
    
    public function delete_option_value($option_id){
        $this->database->where('option_id', $option_id);
        $this->database->delete('option_value');
    }

    public function delete_option_value_translation($option_id){
        $this->database->where('option_id', $option_id);
        $this->database->delete('option_value_description');
    }

    public function get_option_values($option_id = false, $language_id =  false)
    {
        if($option_id) {
            $language_id = ($language_id) ? $language_id : $this->data['current_lang_id'];
            $this->db->select('*');
            $this->db->from('option_value');
            $this->db->join('option_value_description', 'option_value.id = option_value_description.option_value_id');
            $this->db->where(['option_value.option_id' => $option_id, 'option_value_description.language_id' => $language_id]);
            $query = $this->db->get();
            return $query->result_array();

        }
    }
    
}