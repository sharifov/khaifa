<?php

if (!function_exists('check_permission')) {
    function check_permission($controller = false, $method = false)
    {
        $CI =& get_instance();

        if (!$CI->input->is_ajax_request()) {
           

            if (!$controller) {
                $controller = $CI->controller;
            }


            if (!$method) {
                $method = $CI->method;
            }

            $CI->db->where('controller', $controller);
            $CI->db->where('method', $method);
            $query = $CI->db->get('permissions');
            if ($query->num_rows() == 1) {
                $permission = $query->row();

                $user_groups = $CI->auth->get_user_groups();
                foreach ($user_groups as $user_group) {
                    $CI->db->where('group_id', $user_group->id);
                    $CI->db->where('permission_id', $permission->id);
                    $query2 = $CI->db->get('permission_to_group');
                    //echo $CI->db->last_query();
                    //var_dump($query2);
                    if ($query2->num_rows() == 1) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            return true;
        }
    }
}
