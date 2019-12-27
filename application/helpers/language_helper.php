<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('valid_lang')) {
    function valid_lang($variable = false)
    {
        if($variable)
        {
            $CI =& get_instance();

            if(isset($variable->{$CI->data['current_lang']}))
            {
                return $variable->{$CI->data['current_lang']};
            }
            elseif(isset($variable->{$CI->data['default_language']}))
            {
                return $variable->{$CI->data['default_language']};
            }
            else
            {
                return false;
            }
        }
        return false;
    }
}