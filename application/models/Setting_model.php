<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends Core_Model
{

    public $table = 'settings';
    public $primary_key = 'id';
    public $fillable = [];
    public $protected = [];

    public function __construct()
    {
        $this->soft_deletes = false;
        parent::__construct();
    }

    public function update_setting($setting_data = [])
    {
        if (!empty($setting_data)) {
            foreach ($setting_data as $setting_key => $setting_value) {
                $this->db->where('key', $setting_key);
                $query = $this->db->get($this->table);

                if ($query->num_rows() == 0) {
                    $this->db->insert($this->table, ['key' => $setting_key, 'value' => $setting_value]);
                } elseif ($query->num_rows() == 1) {
                    $this->db->where('key', $setting_key);
                    $this->db->update($this->table, ['value' => $setting_value]);
                } else {
                    $this->db->delete($this->table, ['key' => $setting_key]);
                    $this->db->insert($this->table, ['key' => $setting_key, 'value' => $setting_value]);
                }
            }
            return true;
        }
        return false;
    }


    public function  get_settings(){
        $this->db->select('*');
        $this->db->from('wc_settings');
        return $this->db->get()->result_array();
    }

}