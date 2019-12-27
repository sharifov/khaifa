<?php

class Free {

	protected $CI;
	protected $from_countries = [221];
	
	public function __construct()
	{
		$this->CI = &get_instance();
	}

	public function calculate()
	{
		$country_id = (int)$this->CI->data['current_country_id'];
		
		$response = [];

		if(isset($this->CI->auth->get_user()->id) && $this->CI->auth->is_admin(isset($this->CI->auth->get_user()->id)))
		{
			$price = get_setting('free_shipping_price');
		}
		else
		{
			$price = get_setting('free_shipping_price_vendor');
		}

		if(in_array($country_id, $this->from_countries))
		{
			$response[] = [
				'price'		=> $this->CI->currency->formatter_without_symbol($price, 'AED', 'USD'),
				'show_price' => $this->CI->currency->formatter($price, 'AED', $this->CI->data['current_currency']),
				'currency' => $this->CI->data['current_currency'],
				'code'	=> 'free',
				'name' => translate('free_shipping', true)
			];

		}

		return $response;
	}
}
