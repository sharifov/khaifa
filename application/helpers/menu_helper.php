<?php

if (!function_exists('get_menu_by_id')) {
    function get_menu_by_id($id = false)
    {
        $CI =& get_instance();
        $CI->load->library('menu_generator');
        $CI->load->model("modules/Menu_model");
        $CI->load->model("Menu_items_model", "menu_item");

        if ($id) {
            $CI->db->where('id', $id);
            $CI->db->where('status', 1);
            $query = $CI->db->get('menu');
            if ($query->num_rows() == 1) {
                $menu_row = $query->row();
                $items = $CI->Menu_model->get_items($menu_row->id, $CI->data['current_lang_id'], 1);
                $CI->menu_generator->set_items($items);

                $config = [
                    'nav_tag_open' => $menu_row->nav_tag_open,
                    'nav_tag_close' => $menu_row->nav_tag_close,
                    'item_tag_open' => $menu_row->item_tag_open,
                    'item_tag_close' => $menu_row->item_tag_close,
                    'parent_tag_open' => $menu_row->parent_tag_open,
                    'parent_tag_close' => $menu_row->parent_tag_close,
                    'parentl1_tag_open' => $menu_row->parentl1_tag_open,
                    'parentl1_tag_close' => $menu_row->parentl1_tag_close,
                    'parent_anchor' => $menu_row->parent_anchor,
                    'children_tag_open' => $menu_row->children_tag_open,
                    'children_tag_close' => $menu_row->children_tag_close,
                    'parentl1_anchor' => $menu_row->parentl1_anchor
                ];

                $CI->menu_generator->initialize($config);
                return $CI->menu_generator->render();
            }
            return false;
        }
        return false;
    }
}

if (!function_exists('get_menu_by_name')) {
    function get_menu_by_name($name = false)
    {
        $CI =& get_instance();
        $CI->load->library('menu_generator');
        $CI->load->model("modules/Menu_model");
        $CI->load->model("modules/Menu_item_model", "menu_item");

        if ($name) {
            $CI->db->where('slug', $name);
            $CI->db->where('status', 1);
            $query = $CI->db->get('menu');
            if ($query->num_rows() == 1) {
                $menu_row = $query->row();
                $items = $CI->Menu_model->get_items($menu_row->id, $CI->data['current_lang_id'], 1);
                $CI->menu_generator->set_items($items);

                $config = [
                    'nav_tag_open' => $menu_row->nav_tag_open,
                    'nav_tag_close' => $menu_row->nav_tag_close,
                    'item_tag_open' => $menu_row->item_tag_open,
                    'item_tag_close' => $menu_row->item_tag_close,
                    'parent_tag_open' => $menu_row->parent_tag_open,
                    'parent_tag_close' => $menu_row->parent_tag_close,
                    'parentl1_tag_open' => $menu_row->parentl1_tag_open,
                    'parentl1_tag_close' => $menu_row->parentl1_tag_close,
                    'parent_anchor' => $menu_row->parent_anchor,
                    'children_tag_open' => $menu_row->children_tag_open,
                    'children_tag_close' => $menu_row->children_tag_close,
                    'parentl1_anchor' => $menu_row->parentl1_anchor
                ];

                $CI->menu_generator->initialize($config);
                return $CI->menu_generator->render();
            }
            return false;
        }
        return false;
    }
}
