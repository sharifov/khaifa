<?php

if (!function_exists('translate')) {
    function translate($key = false, $common = false, $replace = false)
    {
        $CI =& get_instance();
        if ($key) {
            $controller = $CI->controller;

            if ($common) {
				$_t = $CI->lang->translate('main_' . $key);
                return $replace?sprintf($_t, $replace):$_t;
            }
            return $CI->lang->translate($controller . '_' . $key);
        }

        return 'undefined';
    }
}

if (!function_exists('trans')) {
    function trans($key = false)
    {
        if ($key) {
            $CI =& get_instance();
            return $CI->lang->translate($CI->module_name . '_' . $key);
        }
        return false;
    }
}


if (!function_exists('__')) {
	
	function __($sef){
		$CI =& get_instance();
		$CI->db->select('b.slug');
		$CI->db->from('page_translation a');
		$CI->db->join('page_translation b', 'b.page_id=a.page_id', 'inner');
		$CI->db->where(['a.language_id'=>1, 'b.language_id'=>$CI->data['current_lang_id'], 'a.slug' => 'terms-and-conditions']);
		$query = $CI->db->get()->row();
		return $query?$query->slug:$sef;
	}
	
}