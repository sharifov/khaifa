<?php

if (!function_exists('get_user_ip')) {
	function get_user_ip()
	{
		foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'REMOTE_ADDR') as $header)
		{
			if (!isset($_SERVER[$header]) || ($spoof = $_SERVER[$header]) === null) {
				continue;
			}
			sscanf($spoof, '%[^,]', $spoof);
			if (!filter_var($spoof, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
				$spoof = null;
			} else {
				return $spoof;
			}
		}
		return '0.0.0.0';
	}
}

if (!function_exists('get_country_id')) {
	function get_country_id()
	{
		$CI = &get_instance();

		if($CI->session->userdata('country')) {
		    return $CI->session->userdata('country');
        }

		if(true) {

		    if(isset($CI->session->customer_id) && $CI->session->customer_id) {
                $default_address = $CI->db
                    ->select('A.country_id')
                    ->from('customer C')
                    ->join('address A', 'C.address_id=A.id', 'left')
                    ->where('C.id', $CI->session->customer_id)
                    ->get()
                    ->row();

                if($default_address && isset($default_address->country_id)) {
                    return $default_address->country_id;
                }
            }

        }

		$CI->load->model('modules/Country_model');
		$country_code = geoip_country_code_by_name(get_user_ip());

		$country = $CI->Country_model->filter(['iso_code_2' => $country_code, 'status' => 1])->with_translation()->one();

		if($country)
		{
			return $country->id;
		}
		return false;
	}
}

if (!function_exists('get_country_group_id')) {
	function get_country_group_id()
	{
		$CI = &get_instance();
		$CI->load->model('modules/Country_group_model');

		$country_group = $CI->Country_group_model->filter(['FIND_IN_SET("'.get_country_id().'", countries)' => NULL, 'status' => 1])->one();

		if($country_group)
		{
			return $country_group->id;
		}
		return false;
	}
}


if (!function_exists('get_country_name')) {
	function get_country_name($country_id = 0)
	{
		if($country_id == 0)
		{
			$country_id = (int) get_country_id();
		}
		else
		{
			$country_id = (int)$country_id;
		}


		$CI = &get_instance();
		$CI->load->model('modules/Country_model');
		$country = $CI->Country_model->filter(['status' => 1, 'id' => $country_id])->with_translation()->one();
		if($country)
		{
			return $country->name;
		}
		return false;
	}
}

if (!function_exists('get_country_all')) {
	function get_country_all()
	{
		$CI = &get_instance();
		$CI->load->model('modules/Country_model');
		$countries = $CI->Country_model->filter(['status' => 1])->with_translation()->all();
		if($countries) return $countries;
	
		return false;
	}
}

if (!function_exists('get_region_name')) {
	function get_region_name($region_id = 0)
	{
		if($region_id)
		{
			$CI = &get_instance();
			$CI->load->model('modules/Zone_model');
			$zone = $CI->Zone_model->filter(['status' => 1, 'id' => $region_id])->one();
			if($zone)
			{
				return $zone->name;
			}
			return false;
		}
		return false;
	}
}


if (!function_exists('get_region_all')) {
	function get_region_all()
	{
		$CI = &get_instance();
		$CI->load->model('modules/Zone_model');
		$zones = $CI->Zone_model->filter(['status' => 1])->all();
		
		return $zones?$zones:false;
	}
}
