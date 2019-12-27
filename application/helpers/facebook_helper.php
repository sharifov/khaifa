<?php

if (!function_exists('get_facebook_login_url'))
{
	function get_facebook_login_url()
	{
		$CI =& get_instance();
		$CI->load->library('facebook');
		return $CI->facebook->login_url();
	}
}

if (!function_exists('get_facebook_logout_url'))
{
	function get_facebook_logout_url()
	{
		$CI =& get_instance();
		$CI->load->library('facebook');
		return $CI->facebook->logout_url();
	}
}

if (!function_exists('is_authenticated'))
{
	function is_authenticated()
	{
		$CI =& get_instance();
		$CI->load->library('facebook');
		return $CI->facebook->is_authenticated();
	}
}

if (!function_exists('get_google_login_url'))
{
	function get_google_login_url()
	{
		$CI =& get_instance();
		$CI->load->library('google');
		return $CI->google->get_login_url();
	}
}
