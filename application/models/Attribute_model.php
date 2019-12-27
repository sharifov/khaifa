<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attribute_model extends Core_Model
{

	public $table = 'attribute';
	public $primary_key = 'id';
	public $protected = [];
	public $table_translation = 'attribute_translation';
	public $table_translation_key = 'attribute_id';
	public $table_language_key = 'language_id';

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
	}

	public function force_delete_attribute($id){
		if($id == 'all') {
			$this->database->delete('attribute');
			$this->database->delete('attribute_translation');
			$this->database->delete('attribute_value');
			$this->database->delete('attribute_value_description');
		} else {
			$this->database->where('id', $id);
			$this->database->delete('attribute');

			$this->database->where('attribute_id', $id);
			$this->database->delete('attribute_translation');

			$this->database->where('attribute_id', $id);
			$this->database->delete('attribute_value');

			$this->database->where('attribute_id', $id);
			$this->database->delete('attribute_value_description');
		}
		
	}
	public function insert_attribute_value($data) {
		if($data) {
			$this->db->insert('attribute_value',$data);
			return $this->db->insert_id();
		}
		return false;
	}

	public function insert_attribute_value_translation($data) {
		$this->db->insert('attribute_value_description',$data);
	}

	public function get_attribute_value($attribute_id){
		$this->db->where(['attribute_id' => $attribute_id]);
		$query = $this->db->get('attribute_value');

		if($query->num_rows() > 0){
			return $query->result();
		}
		return false;
	}

	public function get_attribute_value_translation($attribute_value_id){
		$this->db->where(['attribute_value_id' => $attribute_value_id]);
		$query = $this->db->get('attribute_value_description');
		if($query->num_rows() > 0){
			return $query->result();
		}
		return false;
	}
	
	public function delete_attribute_value($attribute_id){
		$this->database->where('attribute_id', $attribute_id);
		$this->database->delete('attribute_value');
	}

	public function delete_attribute_value_translation($attribute_id){
		$this->database->where('attribute_id', $attribute_id);
		$this->database->delete('attribute_value_description');
	}

	public function get_attribute_values($attribute_id = false, $language_id =  false)
	{
		if($attribute_id) {
			$language_id = ($language_id) ? $language_id : $this->data['current_lang_id'];
			$this->db->select('*');
			$this->db->from('attribute_value');
			$this->db->join('attribute_value_description', 'attribute_value.id = attribute_value_description.attribute_value_id');
			$this->db->where(['attribute_value.attribute_id' => $attribute_id, 'attribute_value_description.language_id' => $language_id]);
			$query = $this->db->get();
			return $query->result_array();

		}
	}
	
}