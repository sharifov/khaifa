<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coupon extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('modules/Coupon_model');
	}

	public function index()
	{

		if($this->input->method() == 'post')
		{
			$coupon = $this->input->post('coupon');
			$coupon_info = $this->Coupon_model->getCoupon($coupon);

			if (empty($this->input->post('coupon')))
			{
				$json['error'] = translate('error_empty');

				$this->session->unset_userdata('coupon');
			} 
			elseif ($coupon_info) 
			{
				$this->session->set_userdata('coupon', $coupon);
				$this->session->set_userdata('coupon_info', $coupon_info);
				
				$json['success'] = translate('text_success');
				$json['redirect'] =  $_SERVER['HTTP_REFERER'];
			} 
			else 
			{
				$json['error'] = translate('error_coupon');
			}

			$this->template->json($json);
		}
	}
}