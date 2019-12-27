<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bank {

	public $CI;

	public $order_status_id = 1;

	public function __construct()
	{
		$this->CI = &get_instance();
	}

	public function process($order_id)
	{
		$this->CI->load->model('Order_model');
		$this->CI->Order_model->change_order_status($order_id, $this->order_status_id);
		
		return site_url_multi('checkout/success');
	}
}