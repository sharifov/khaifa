<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cod {

	public $CI;

	public $order_status_id = 1;
    public $config_vars;

	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->config->load('auth');
        $this->config_vars = $this->CI->config->item('auth');
	}

	public function process($order_id)
	{
		$this->CI->load->model('Order_model');
		$this->CI->Order_model->change_order_status($order_id, $this->order_status_id);

        if (isset($this->config_vars['email_config']) && is_array($this->config_vars['email_config'])) {
            $this->CI->email->initialize($this->config_vars['email_config']);
        }

        $this->CI->email->from($this->config_vars['email'], $this->config_vars['name']);
        $this->CI->email->to($this->CI->data['customer']->email);
        $this->CI->email->subject("You paid successfully");
        $this->CI->email->message("You paid successfully");
        $this->CI->email->set_newline("\r\n");
        $send = $this->CI->email->send();


        return site_url_multi('checkout/cod');
	}
}