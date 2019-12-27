<?php

if (!function_exists('is_loggedin')) {
	function is_loggedin()
	{
		$CI =& get_instance();
		return $CI->auth->is_loggedin();
	}
}

if (!function_exists('get_user')) {
	function get_user($user_id = false)
	{
		$CI =& get_instance();
		return $CI->auth->get_user($user_id)->firstname.' '.$CI->auth->get_user($user_id)->lastname;
	}
}

if (!function_exists('get_user_id')) {
	function get_user_id()
	{
		if(is_loggedin())
		{
			$CI =& get_instance();
			return $CI->auth->get_user()->id;
		}
		return false;
		
	}
}

if (!function_exists('is_member')) {
	function is_member($group)
	{
		$CI =& get_instance();
		return $CI->auth->is_member($group);
	}
}

if (!function_exists('is_admin')) {
	function is_admin()
	{
		$CI =& get_instance();
		return $CI->auth->is_admin();
	}
}

if (!function_exists('get_seller')) {
	function get_seller($user_id)
	{
		$CI =& get_instance();
		$groups = $CI->auth->get_user_groups($user_id);

		if($groups[0]->name == 'vendor')
		{
			return get_seller_store_name($user_id);
		}
		else
		{
			return get_setting('default_seller_name');
		}
		
	}
}


if (!function_exists('get_seller_store_name')) {
	function get_seller_store_name($user_id = false)
	{
		$CI =& get_instance();
		$seller =  $CI->auth->get_user($user_id);
		return ($seller) ? $seller->brand : 'UNKNOWN';
	}
}


