<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group_model extends Core_Model
{

    public $table = 'groups';
    public $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function set_permission_to_group($data = [])
    {
        if ($data) {
            $this->db->insert('permission_to_group',
                ['group_id' => $data['group_id'], 'permission_id' => $data['permission_id']]);
        }
    }

    public function get_group_permissions($group_id = false)
    {
        if ($group_id) {
            return $this->db->get_where('permission_to_group', ['group_id' => $group_id])->result();
        }
        return false;
    }

    public function delete_group_permissions($group_id = false)
    {
        if ($group_id) {
            $this->db->delete('permission_to_group', ['group_id' => $group_id]);
        }
    }

}