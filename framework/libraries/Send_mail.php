<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Send_mail
{
    public $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function send($option)
    {
        $this->CI->email->from($option['from'], $option['from_name']);
        $this->CI->email->to($option['to']);
        $this->CI->email->subject($option['subject']);
        $this->CI->email->message($option['message']);
        $this->CI->email->set_newline("\r\n");
        return $this->CI->email->send();
    }

}