<?php

if (!function_exists('form_status')) {
    function form_status($name)
    {
        $options[0] = translate('deactive');
        $options[1] = translate('deactive');
        return form_dropdown($name, $options, 'default');
    }
}

if( ! function_exists('custom_parse_phone') ) {

    function custom_parse_phone($phone_string) {

        $phones = '';

        $phone_array = explode(',', $phone_string);

        foreach ($phone_array as $phone) {

            $phones .= '<a href="tel: '.$phone.'">'.$phone.'</a>';

        }

        return $phones;
    }


}