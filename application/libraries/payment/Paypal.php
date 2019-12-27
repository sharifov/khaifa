<?php

class Paypal {

	public $CI;
    public $config_vars;
	protected $sandbox = false;
	protected $email;
	protected $transaction = 1;
	protected $order_status = [
		'pending'					=> 1,
		'canceled_reversal'			=> 6,
		'completed'					=> 4,
		'denied'					=> 5,
		'expired'					=> 14,
		'failed'					=> 7,
		'processed'					=> 13,
		'refunded'					=> 8,
		'reversed'					=> 9,
		'voided'					=> 12
	];
	

	public function __construct()
	{
		$this->CI = &get_instance();

		$this->email = get_setting('paypal_email');

        $this->CI->config->load('auth');
        $this->config_vars = $this->CI->config->item('auth');
	}

	public function process($order_id)
	{
		if (!$this->sandbox)
		{
			$paypal_data['action'] = 'https://www.paypal.com/cgi-bin/webscr&pal=V4T754QB63XXL';
		}
		else
		{
			$paypal_data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr&pal=V4T754QB63XXL';
		}

		$this->CI->load->model('Order_model');

		$order_info = $this->CI->Order_model->getOrder($order_id);

		if ($order_info)
		{
			$paypal_data['business'] = $this->email;
			$paypal_data['item_name'] = html_entity_decode('Mimelon.com', ENT_QUOTES, 'UTF-8');

			$paypal_data['products'] = [];

            $total = 0;

			foreach ($this->CI->cart_lib->getProducts() as $product)
			{
				$option_data = [];

				foreach ($product['option'] as $option)
				{
					$option_data[] = [
						'name'  => $option['name'],
						'value' => (mb_strlen($option['value']) > 20 ? mb_strlen($option['value'], 0, 20) . '..' : $option['value'])
					];
				}

				$product_raw_price = trim(str_replace('$', '', $this->CI->currency->formatter($product['price'], $product['currency'], 'USD')));

                $product_shipping_price = (int) $product['shipping'][0]['price'];

                $product_total_price = ceil($product_raw_price)/* + $product_shipping_price*/;

                $total += $product_total_price * $product['quantity'];

                /*$session_products[] = [
                    'name'      =>  $product['name'],
                    'model'      => $product['model'],
                    'option'     => $session_option_data,
                    'quantity'   => $product['quantity'],
                    'price'      => $product_total_price,
                    'weight'     => $product['weight'],
                ];*/

				$paypal_data['products'][] = [
					'name'     => htmlspecialchars($product['name']),
					'model'    => htmlspecialchars($product['model']),
					'price'    => $product_total_price,
					'quantity' => $product['quantity'],
					'option'   => $option_data,
					'weight'   => $product['weight']
				];
			}

            $paypal_data['products'][] = [
                'name'     => 'Shipping total',
                'model'    => 'Shipping total',
                'price'    => trim(str_replace('$', '', $this->CI->currency->formatter($this->CI->cart_lib->getShippingTotal(), $this->CI->data['current_currency'], 'USD'))),
                'quantity' => '',
                'option'   => [],
                'weight'   => ''
            ];

            $total_with_payment_fee = trim(str_replace('$', '', $this->CI->currency->formatter($this->CI->cart_lib->getTotalPrice()+$this->CI->cart_lib->getPaymentTotal(), $this->CI->data['current_currency'], 'USD')));

            $shipping = $this->CI->currency->formatter_without_symbol($this->CI->cart_lib->getShippingTotal(), $this->CI->data['current_currency'], 'USD');


//            var_dump($total_with_payment_fee, $total, $shipping);die;

            $paypal_data['products'][] = [
                'name'     => 'Payment fee',
                'model'    => 'Payment fee',
                'price'    => round($total_with_payment_fee - $total - $shipping),
                'quantity' => '',
                'option'   => [],
                'weight'   => ''
            ];


            $paypal_data['discount_amount_cart'] = 0;

//			$total = $this->CI->currency->formatter_without_symbol($order_info['total'] - $this->CI->cart_lib->getSubTotal(), $order_info['currency_code'], false, false);

			/*if ($total > 0)
			{
				$paypal_data['products'][] = [
					'name'     => 'Total',
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => [],
					'weight'   => 0
				];
			}
			else
			{
				$paypal_data['discount_amount_cart'] -= $this->CI->currency->formatter_without_symbol($total, 'USD',  $order_info['currency_code']);
			}*/

			$this->CI->load->model('modules/Address_model');
			$address = $this->CI->Address_model->filter(['id' => $order_info['address_id']])->with_trashed()->one();

			if($address)
			{
				$firstname = $address->firstname;
				$lastname = $address->lastname;
				$address_1 = $address->address_1;
				$address_2 = $address->address_2;
				$city = $address->city;
				$postcode = $address->postcode;
				$country = $this->CI->Country_model->filter(['id' => $address->country_id])->one()->iso_code_2;
			}

			$paypal_data['currency_code'] = 'USD';
			$paypal_data['first_name'] = html_entity_decode($firstname, ENT_QUOTES, 'UTF-8');
			$paypal_data['last_name'] = html_entity_decode($lastname, ENT_QUOTES, 'UTF-8');
			$paypal_data['address1'] = html_entity_decode($address_1, ENT_QUOTES, 'UTF-8');
			$paypal_data['address2'] = html_entity_decode($address_2, ENT_QUOTES, 'UTF-8');
			$paypal_data['city'] = html_entity_decode($city, ENT_QUOTES, 'UTF-8');
			$paypal_data['zip'] = html_entity_decode($postcode, ENT_QUOTES, 'UTF-8');
			$paypal_data['country'] = $country;
			$paypal_data['email'] = $this->CI->data['customer']->email;
			$paypal_data['invoice'] = $order_id . ' - ' . html_entity_decode($firstname, ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($lastname, ENT_QUOTES, 'UTF-8');
			$paypal_data['lc'] = $this->CI->data['current_lang'];
			$paypal_data['return'] = site_url_multi('checkout/success');
			$paypal_data['notify_url'] = site_url_multi('checkout/callback');
			$paypal_data['cancel_return'] = site_url_multi('checkout');

			if (!$this->transaction)
			{
				$paypal_data['paymentaction'] = 'authorization';
			}
			else
			{
				$paypal_data['paymentaction'] = 'sale';
			}

			$paypal_data['custom'] = $order_id;

			$this->CI->session->set_userdata('paypal_data', $paypal_data);

			return site_url_multi('checkout/paypal');
		}
	}

	public function callback()
	{
        if ($this->CI->input->post('custom'))
		{
			$order_id = $this->CI->input->post('custom');
		}
		else
		{
			$order_id = 0;
		}

        $this->CI->load->model('Order_model');

		$order_info = $this->CI->Order_model->getOrder($order_id);

		if ($order_info)
		{
			$request = 'cmd=_notify-validate';

            foreach ($this->CI->input->post() as $key => $value)
			{
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}

			if (!$this->sandbox)
			{
				$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			}
			else
			{
				$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}
			
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($curl);

			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/tet', print_R($response, true));
			
			
			if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && $this->CI->input->post('payment_status'))
			{
				$order_status_id = $this->order_status['pending'];

                switch($this->CI->input->post('payment_status'))
				{
					case 'Canceled_Reversal':
						$order_status_id = $this->order_status['canceled_reversal'];
						break;


					case 'Completed':

                        $receiver_match = (strtolower($this->CI->input->post('receiver_email')) == strtolower($this->email));

						$total_paid_match = ((float)$this->CI->input->post('mc_gross') == $this->CI->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));

						if ($receiver_match && $total_paid_match)
						{
                            $order_status_id = $this->order_status['completed'];

                            if (isset($this->config_vars['email_config']) && is_array($this->config_vars['email_config'])) {
                                $this->CI->email->initialize($this->config_vars['email_config']);
                            }

                            $this->CI->email->from($this->config_vars['email'], $this->config_vars['name']);
                            $this->CI->email->to($this->email);
                            $this->CI->email->subject(translate('payment_successfully_subject', true));
                            $this->CI->email->message(translate('payment_successfully_text', true));
                            $this->CI->email->set_newline("\r\n");
                            $send = $this->CI->email->send();

                            $this->CI->db->where('id', $order_id);

                            $this->CI->db->update('order', ['order_status_id'  =>  1]);


                            $order_products = $this->db->from('order_product')
                                ->where('order_id', $order_id)
                                ->get()
                                ->result_array();

                            foreach ($order_products as $order) {

                                $this->db->query('UPDATE wc_product SET quantity=quantity-'. $order['quantity'] .' WHERE id=' . $order['product_id']);

                            }

                            $this->session->set_userdata('order_id', $order_id);


                        }
						break;
					case 'Denied':
						$order_status_id =  $this->order_status['denied'];
						break;
					case 'Expired':
						$order_status_id =  $this->order_status['expired'];
						break;
					case 'Failed':
						$order_status_id =  $this->order_status['failed'];
						break;
					case 'Pending':
						$order_status_id = $this->order_status['pending'];
						break;
					case 'Processed':
						$order_status_id =  $this->order_status['processed'];
						break;
					case 'Refunded':
						$order_status_id =  $this->order_status['refunded'];
						break;
					case 'Reversed':
						$order_status_id =  $this->order_status['reversed'];
						break;
					case 'Voided':
						$order_status_id =  $this->order_status['voided'];
						break;
				}

                $this->CI->Order_model->change_order_status($order_id, $order_status_id);
			}
			
			curl_close($curl);
		}
	}
}