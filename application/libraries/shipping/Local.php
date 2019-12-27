<?php

class Local {

	protected $CI;
	
	public function __construct()
	{
		$this->CI = &get_instance();
	}

	public function calculate($products = [])
	{	
		$response = [];	
		
		$_q = $this->CI->db->query("SELECT shipping FROM wc_cart WHERE session_id = '" . $this->CI->session->session_id . "' AND api_id = '0' AND customer_id = '" . (int)$this->CI->data['customer']->id . "' ORDER BY cart_id DESC");
		
		if($_q && $_q->num_rows() && isset($_q->row()->shipping)){
			
			$_shipping = json_decode($_q->row()->shipping, true);
		
			if($_shipping['code'] == 'local_shipping'){
				$response[] = [
					'price'		=> $this->CI->currency->formatter_without_symbol($_shipping['price'], $_shipping['currency'], 'USD'),
					'show_price' => $this->CI->currency->formatter($_shipping['price'], $_shipping['currency'], $this->CI->data['current_currency']),
					'currency' => $_shipping['currency'],
					'code'	=> 'local_shipping',
					'name' => $_shipping['name']
				];
			}
		}
		return $response;
	}
}
