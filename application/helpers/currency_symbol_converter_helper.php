<?php

if( ! function_exists('currency_symbol_converter') ) {

    function currency_symbol_converter($string){

        $currencies = [
            '$' =>  '<i title="USD" class="fa fa-usd"></i>',
            '₼' =>  '<i title="AZN" class="fa fa-azn">m</i>',
            '€' =>  '<i title="EURO" class="fa fa-euro"></i>',
        ];

        foreach ($currencies as $currency => $class) {

            if(strpos($string, $currency) !== false) {

                return str_replace($currency, $class, $string);

            }
        }

        return $string;
    }
}

if( ! function_exists('currency_clear') ) {

    function currency_clear($data){
        
        return intval(str_replace(['$', '₼', '€', 'AZN', 'USD', 'AED'], '', $data));

    }
}

if( ! function_exists('currency_current') ) {

    function currency_current(){
        $CI = &get_instance();
        return $CI->data['current_currency'];
    }
}