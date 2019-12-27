<?php

if (!function_exists('get_setting')) {
    function get_setting($key = false, $custom = false)
    {
        $CI =& get_instance();

        if ($key) {
            $CI->db->where('key', $key);
            $query = $CI->db->get('settings');
            if ($query->num_rows() == 1) {
                $setting = $query->row();
                if ($setting->json == 1) {
                    if ($custom) {
                        $json = json_decode($setting->value);
                        return (isset($json->$custom)) ? $json->$custom : 'UNKNOWN';
                    } else {
                        return json_decode($setting->value);
                    }
                } else {
                    return $setting->value;
                }
            }
            return false;
        }
        return false;
    }
}

if (!function_exists('get_settings')) {
    function get_settings($type = false)
    {
        $CI =& get_instance();

        if ($type) {
            $CI->db->where('type', $type);
            $query = $CI->db->get('settings');
            if ($query->num_rows() > 0) {
                return $query->result();
            }
            return false;
        }
        return false;
    }
}

if (!function_exists('currency_formatter')) {
    function currency_formatter($price, $from, $to)
    {
        $CI =& get_instance();
        $price = $CI->currency->formatter($price, $from, $to);
        
        return $price;
    }
}

if (!function_exists('currency_code')) {
    function currency_code($currency_id)
    {
        $CI =& get_instance();
        $code = $CI->currency->getCode($currency_id);
        
        return $code;
    }
}

if (!function_exists('vendor_product_status')) {
    function vendor_product_status($status_id)
    {
        if($status_id == 0)
		{
            return "<span class='label label-warning'>"  . translate('pending', true) . "</span>";
		}
		elseif($status_id == 1)
        {
            return "<span class='label label-success'>"  . translate('enable', true) . "</span>";
        }
        elseif($status_id == 2)
        {
            return "<span class='label label-danger'>"  . translate('disable', true) . "</span>";
        }
    }
}
