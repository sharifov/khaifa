<?php

if (!function_exists('get_banner')) {
	function get_banner($key = false)
	{
		$CI =& get_instance();
		$CI->load->model('modules/Banner_model');
		$banner = $CI->Banner_model->filter(['key' => $key, '(country_group_id = '.get_country_group_id().' OR country_group_id = 0)' => NULL, 'status' => 1])->with_translation()->one();
		return $banner;
	}
}

if (!function_exists('get_banners')) {
	function get_banners($key = false)
	{
		$CI =& get_instance();
		$CI->load->model('modules/Banner_model');
		$banners = $CI->Banner_model->filter(['key' => $key, '(country_group_id = '.get_country_group_id().' OR country_group_id = 0)' => NULL, 'status' => 1])->with_translation()->order_by('sort', 'ASC')->all();
		return $banners;
	}
}