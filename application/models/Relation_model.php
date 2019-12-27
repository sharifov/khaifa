<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Relation_model extends Core_Model
{

    public $table = 'relation';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'relation_translation';
    public $table_translation_key = 'relation_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function force_delete_relation($id){
        if($id == 'all') {
            $this->database->delete('relation');
            $this->database->delete('relation_translation');
            $this->database->delete('relation_value');
            $this->database->delete('relation_value_description');
        } else {
            $this->database->where('id', $id);
            $this->database->delete('relation');

            $this->database->where('relation_id', $id);
            $this->database->delete('relation_translation');

            $this->database->where('relation_id', $id);
            $this->database->delete('relation_value');

            $this->database->where('relation_id', $id);
            $this->database->delete('relation_value_description');
        }
        
    }
    public function insert_relation_value($data) {
        if($data) {
            $this->db->insert('relation_value',$data);
            return $this->db->insert_id();
        }
        return false;
    }

    public function insert_relation_value_translation($data) {
        $this->db->insert('relation_value_description',$data);
    }

    public function get_relation_value($relation_id){
        $this->db->where(['relation_id' => $relation_id]);
        $query = $this->db->get('relation_value');

        if($query->num_rows() > 0){
            return $query->result();
        }
        return false;
    }

    public function get_relation_value_translation($relation_value_id){
        $this->db->where(['relation_value_id' => $relation_value_id]);
        $query = $this->db->get('relation_value_description');
        if($query->num_rows() > 0){
            return $query->result();
        }
        return false;
    }
    
    public function delete_relation_value($relation_id){
        $this->database->where('relation_id', $relation_id);
        $this->database->delete('relation_value');
    }

    public function delete_relation_value_translation($relation_id){
        $this->database->where('relation_id', $relation_id);
        $this->database->delete('relation_value_description');
    }

    public function get_relation_values($relation_id = false, $language_id =  false)
    {
        if($relation_id) {
            $language_id = ($language_id) ? $language_id : $this->data['current_lang_id'];
            $this->db->select('*');
            $this->db->from('relation_value');
            $this->db->join('relation_value_description', 'relation_value.id = relation_value_description.relation_value_id');
            $this->db->where(['relation_value.relation_id' => $relation_id, 'relation_value_description.language_id' => $language_id]);
            $query = $this->db->get();
            return $query->result_array();

        }
    }
    
}