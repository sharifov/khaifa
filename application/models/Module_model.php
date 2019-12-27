<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Module_model extends Core_Model
{

	public $table = 'modules';
	public $primary_key = 'id';
	public $protected = ['id'];
	public $rules = [];


	public function __construct()
	{
		parent::__construct();
	}

	public function generate_option($table, $key, $value, $multi = false)
	{
		$this->db->select([$key, $value]);
		$query = $this->db->get($table);		
		$options = [];
		if($query->num_rows() > 0)
		{
			if(!$multi)
			{
				$options[0] = translate('select', true);
			}
			$rows = $query->result();
			foreach($rows as $row)
			{
				$options[$row->{$key}] = $row->{$value};
			}
		}
		return $options;
	}

	public function get_selected_element($module, $key, $columns, $selected_element, $translation = false) 
	{
		if($module) {
			$columns = explode(',',$columns);
			$select = "";
			if(count($columns) > 1){
				$select = "CONCAT(";
				for ($i=0; $i <= count($columns) - 1; $i++) { 
					if($i == count($columns)-1) {
						$select .= ",".$columns[$i];
					} else {
						$select .= $columns[$i].",'-'";
					}
				}
				$select .= ")";
				
			} elseif(count($columns) == 1){
				$select = $columns[0];
			}

			echo $select;
			
			$this->db->select([$select.' as value']);

			if($translation) {

			} else {
				$this->db->where([$key => $selected_element, 'deleted_at is null' => null]);
			}
			$query = $this->db->get($module);
			if($query->num_rows() > 0){
				return $query->row()->value;
			}
			echo $this->db->last_query();
			var_dump($query->result_array());
			die();
			
		}
		
	}

}