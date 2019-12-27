<?php

if (!function_exists('module_setting')) {
	function module_setting($module_name)
	{
		$CI =& get_instance();
		
		$CI->db->where('slug', $module_name);
		$query = $CI->db->get('modules');

		if ($query->num_rows() === 1) {
			return $query->row();
		} else {
			return false;
		}
	}
}

if (!function_exists('get_module')) {
	function get_module($module_name = false, $params = [])
	{
		if ($module_name) {
			$module_setting = module_setting($module_name);
			if ($module_setting) {
				if ($module_setting->status == 1) {
					$CI =& get_instance();

					$CI->load->model($module_name.'_model');

					$default = [
						'page' 					=> 1,
						'per_page'				=> 10,
						'fields'				=> '*',
						'filter'				=> [],
						'sort_column'			=> 'created_at',
						'sort_order'			=> 'DESC',
					];

					foreach ($default as $key => $value) {
						$params[$key] = (isset($params[$key])) ? $params['$key'] : $default[$key];
					}
					
					if ($module_setting->multilingual == 1) {
						$row = $CI->{$module_name.'_model'}->fields($params['fields'])->with_translation()->filter($params['filter'])->order_by($params['sort_column'], $params['sort_order'])->limit($params['per_page'], $params['page']-1)->all();
					} else {
						$row = $CI->{$module_name.'_model'}->fields($params['fields'])->filter($params['filter'])->order_by($params['sort_column'], $params['sort_order'])->limit($params['per_page'], $params['page']-1)->all();
					}

					if ($row) {
						return $row;
					} else {
						return false;
					}
				} else {
					return 'Module disable';
				}
			} else {
				return 'Module not exists';
			}
		}

		return 'Error';
	}
}

if (!function_exists('get_date')) {
	function get_date($format = 'Y-m-d H:i:s', $date = false)
	{
		if ($date) {
			return date($format, strtotime($date));
		}
		return false;
	}
}

if (!function_exists('get_short_description')) {
	function get_short_description($description = false, $length = '270', $end = '...')
	{
		if ($description) {
			return mb_substr(trim(strip_tags($description)), 0, $length).$end;
		}
		return false;
	}
}


if (!function_exists('get_product_image')) {
	function get_product_image($product_id)
	{
		$CI =& get_instance();
		
		$CI->db->where('id', $product_id);
		$query = $CI->db->get('product');

		if ($query->num_rows() == 1)
		{
			return $CI->Model_tool_image->resize($query->row()->image, 120, 120);
		}
		return false;
	}
}

if (!function_exists('get_product_link')) {
	function get_product_link($product_id)
	{
		$CI =& get_instance();
		$CI->load->model('Product_model');

		$product = $CI->Product_model->filter(['id' => $product_id])->with_translation()->one();

		if ($product)
		{
			return site_url_multi('product/'.$product->slug);
		}
		return false;
	}
}

if (!function_exists('get_order_status_name')) {
	function get_order_status_name($order_status_id)
	{
		$CI =& get_instance();
		$CI->load->model('modules/Order_status_model');

		$order_status = $CI->Order_status_model->filter(['id' => $order_status_id])->with_translation()->one();

		if ($order_status)
		{
			return $order_status->name;
		}
		return false;
	}
}

