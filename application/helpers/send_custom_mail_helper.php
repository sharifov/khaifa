<?php

if( ! function_exists('send_custom_mail') ) {

    function send_custom_mail($option)
    {
        $ci = &get_instance();

        $ci->email->initialize([
            'smtp_crypto' => 'tls'
        ]);

        $option['from'] ?? ($option['from'] = 'support@mimelon.com');

        $ci->email->from($option['from'], $option['from_name']);
        $ci->email->to($option['to']);
        $ci->email->subject($option['subject']);
        $ci->email->message($option['message']);
        $ci->email->set_newline("\r\n");
        return $ci->email->send();
    }
}