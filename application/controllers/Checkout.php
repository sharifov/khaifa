<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checkout extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('modules/Payment_method_model');
		$this->load->model('modules/Shipping_method_model');
		$this->load->model('modules/Address_model');
		$this->load->model('modules/Country_model');
		$this->load->model('modules/Zone_model');
		$this->load->model('modules/Brand_model');
		$this->data['countries'] = $this->Country_model->filter(['status' => 1])->with_translation()->order_by('name', 'ASC')->all();
	}
	

	public function index()
	{
		
	    $this->data['country'] = $this->session->userdata('country');

        foreach($this->data['languages'] as $key => $value)
        {

            $link =  site_url($key.'/checkout');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }

		if(!$this->data['customer'])
		{
			redirect(site_url_multi('account/login?redirect=checkout/index'));
			exit();
		}

		if($this->session->has_userdata('payment_method'))
		{
			$this->data['payment_method_selected'] = $this->session->userdata('payment_method');
		}
		else
		{
			$this->data['payment_method_selected'] = get_setting('default_payment_method');
		}
		
		$this->data['title'] = translate('title');

		$this->data['cart'] = $this->cart_lib->getProducts();


		$this->data['subtotal'] = $this->currency->formatter($this->cart_lib->getSubTotalPrice(), $this->data['current_currency'], $this->data['current_currency']);
		$this->data['shipping_total'] = $this->currency->formatter($this->cart_lib->getShippingTotal(), $this->data['current_currency'], $this->data['current_currency']);
		$this->data['payment_total'] =  $this->currency->formatter($this->cart_lib->getPaymentTotal(), $this->data['current_currency'], $this->data['current_currency']);;


        $this->data['payment_total_price'] = str_replace('$', '', $this->data['payment_total']);

		$this->data['total'] = $this->currency->formatter($this->cart_lib->getTotalPrice()+$this->cart_lib->getPaymentTotal(), $this->data['current_currency'], $this->data['current_currency']);
		$this->data['coupon'] = '';
		if($this->session->has_userdata('coupon'))
		{
			$this->data['coupon'] = $this->session->userdata('coupon');
			$this->load->model('modules/Coupon_model');
			$this->data['coupon_total'] = $this->currency->formatter($this->Coupon_model->getTotal($this->cart_lib->getTotalPrice()), $this->data['current_currency'], $this->data['current_currency']);
			$this->data['total'] = $this->currency->formatter($this->cart_lib->getTotalPrice()+$this->cart_lib->getPaymentTotal()-$this->Coupon_model->getTotal($this->cart_lib->getTotalPrice()), $this->data['current_currency'], $this->data['current_currency']);
		}
		

		if(count($this->data['cart']) == 0)
		{
			redirect(site_url_multi('account/login'));
			exit();
		}

		$this->data['payment_methods'] = $this->Payment_method_model->availablePaymentMethods();
		$this->data['shipping_methods'] = $this->Shipping_method_model->filter(['status' => 1])->with_translation()->order_by('sort', 'ASC')->all();

		//Coupon
		$this->data['coupon'] = '';
		if($this->session->has_userdata('coupon'))
		{
			$this->data['coupon'] = $this->session->userdata('coupon');
		}


		$this->session->set_userdata('checkout_data', [
		    'total'     =>  $this->data['total'],
            'tax'       =>  $this->data['payment_total'],
            'shipping'  =>  $this->data['shipping_total'],
            'coupon'    =>  $this->data['coupon'],
            'products'  =>  $this->data['cart']
        ]);

		$addresses = $this->Address_model->filter(['customer_id' => $this->data['customer']->id])->with_trashed()->all();

		if($addresses)
		{
			foreach($addresses as $address)
			{
				$this->data['addresses'][] = [
					'id'					=> $address->id,
					'firstname'				=> $address->firstname,
					'lastname'				=> $address->lastname,
					'company'				=> $address->company,
					'address1'				=> $address->address_1,
					'address2'				=> $address->address_2,
					'city'					=> $address->city,
					'postcode'				=> $address->postcode,
					'phone'					=> $address->phone,
					'country'				=> $this->Country_model->filter(['id' => $address->country_id])->with_translation()->one()->name,
					'zone'					=> $this->Zone_model->filter(['id' => $address->zone_id])->one()->name ?? '',
				];
			}
				
		}

		if($error = $this->session->flashdata('error')) {

		    $this->data['error'] = $error;

        }

		if(true) {

            $parcels = $this->cart_lib->getShippingTotalWithMethod();

            $shipments = [];

            $this->data['dataLayer'] = [
                'event'     =>  'checkout',
                'ecommerce' =>  [
                    'checkout'  =>  [
                        'actionField' =>    [
                            'step'  =>  1
                        ]
                    ]
                ]
            ];

            $this->load->model('Product_model');

            foreach ($parcels as $parcel) {

                $product_id = current($parcel['products']);


                $product = $this->Product_model->get_products(['id' => $product_id]);






                $seller = get_seller($product[0]['created_by'] ?? 1);

                $shipments[] = [
                    'seller_name'       =>  $seller,
                    'shipment_name'     =>  $parcel['name'] ?? 'No shipment',
                    'price'             =>  $parcel['show_price'] ?? ''
                ];
            }

            $this->data['shipments'] = $shipments;

            $vendors = [];

            foreach ($this->data['cart'] as $cart) {

                $vendors[get_seller($cart['created_by'])][$cart['product_id']] = $cart;

                $product = $this->Product_model->get_products(['id' => $cart['product_id']]);

                $price      = $this->currency->convertCurrencyString($product[0]['price']);

                $price_usd  = $this->currency->convert($price['value'], 'USD', strtoupper($price['code']));



                if(isset($product[0]['manufacturer_id']) && $product[0]['manufacturer_id']) {
                    $brand      = $this->Brand_model->filter(['id' => $product[0]['manufacturer_id'], 'status' => 1])->one();
                }

                $category_name = $this->db->query('SELECT CT.name FROM wc_product_to_category PC
                LEFT JOIN wc_category_translation CT ON PC.category_id=CT.category_id
                LEFT JOIN wc_category C ON CT.category_id=C.id
                WHERE PC.product_id='. $product[0]['id'] .' AND CT.language_id='.$this->data['current_lang_id'].'/* AND C.parent=0*/')
                    ->row();

                $this->data['dataLayer']['ecommerce']['checkout']['products'][][] = [
                    'id'        =>  $product[0]['id'],
                    'name'      =>  $product[0]['name'],
                    'price'     =>  round($price_usd),
                    'brand'     =>  $brand->name ?? '',
                    'category'  =>  $category_name->name ?? '',
                    'quantity'  =>  (int) $cart['quantity']
                ];



            }

            $this->data['vendors'] = $vendors;

        }


		if(isset($_GET['test'])) {
            $this->template->render('checkout_2');
        }
		else {
            $this->template->render('checkout');
        }

	}

	public function confirm()
	{
		$redirect = '';
			
		// Validate if payment method has been set.
		if (!$this->input->post('payment_method'))
		{
			$redirect =  site_url_multi('checkout');
		}
			
		if(!$this->input->post('address_id'))
		{
		    $this->session->set_flashdata('error', translate('address_error', true));

			$redirect =  site_url_multi('checkout');	
		}

		if (!$this->cart_lib->hasProducts() || !$this->cart_lib->hasStock())
		{
			$redirect = site_url_multi('checkout');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart_lib->getProducts();

		foreach ($products as $product)
		{
			$product_total = 0;

			foreach ($products as $product_2)
			{
				if ($product_2['product_id'] == $product['product_id'])
				{
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$redirect = site_url_multi('checkout/cart');

				break;
			}
		}

		if (!$redirect)
		{
			$order_data = [];

			$totals = [];
			$taxes = $this->cart_lib->getTaxes();
			
			$total = $this->currency->formatter_without_symbol($this->cart_lib->getTotalPrice(), $this->data['current_currency'], $this->data['current_currency']);
			if($this->session->has_userdata('coupon'))
			{
				$this->data['coupon'] = $this->session->userdata('coupon');
				$this->load->model('modules/Coupon_model');
				$total = $this->currency->formatter_without_symbol($this->cart_lib->getTotalPrice()-$this->Coupon_model->getTotal($this->cart_lib->getTotalPrice()), $this->data['current_currency'], $this->data['current_currency']);
			}

			$order_data['totals'] = $totals;

			if ($this->data['customer'])
			{
				$order_data['customer_id'] = $this->data['customer']->id;
				$order_data['address_id'] = (int) $this->input->post('address_id');
			}


			if ($this->input->post('payment_method'))
			{
				$payment_method = $this->input->post('payment_method');
				$order_data['payment_method'] = $this->Payment_method_model->filter(['code' => $payment_method])->with_translation()->one()->name;
				$order_data['payment_code'] = $payment_method;
			}
			else
			{
				$order_data['payment_method'] = '';
			}


			if ($this->cart_lib->hasShipping())
			{
				if($this->input->post('shipping_method'))
				{
					$shipping_method =  $this->input->post('shipping_method');
					$order_data['shipping_method'] = $this->Shipping_method_model->filter(['code' => $shipping_method])->with_translation()->one()->name;
					$order_data['shipping_code'] = $this->input->post('shipping_method');
				} else
				{
					$order_data['shipping_method'] = '';
					$order_data['shipping_code'] = '';
				}
			}

			$order_data['products'] = [];

			foreach ($this->cart_lib->getProducts() as $product)
			{
				$option_data = [];
				$session_option_data = [];
				
				foreach ($product['option'] as $option)
				{
					$option_data[] = [
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					];

                    /*$session_option_data[] = [
                        'name'                    => $option['name'],
                        'value'                   => $option['value'],
                    ];*/
				}

				$order_data['products'][] = [
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'price_original'      => $product['price_original'],
					'total'      => $product['total'],
					'total_original'      => $product['total_original'],
					'currency_original'      => $product['currency_original'],
					'shipping'      => json_encode($product['shipping']),
					'tax'        => 0
				];

				$product_raw_price = trim(str_replace('$', '', $this->currency->formatter($product['price'], $product['currency'], 'USD')));

				$product_shipping_price = (int) $product['shipping'][0]['price'];

				$product_total_price = $product_raw_price + ceil($product_shipping_price);

				/*$session_products[] = [
				    'name'      =>  $product['name'],
                    'model'      => $product['model'],
                    'option'     => $session_option_data,
                    'quantity'   => $product['quantity'],
                    'price'      => $product_total_price,
                    'weight'     => $product['weight'],
                ];*/
			}

			/*if(isset($_GET['test'])) {

			    $paypal_data = $this->session->userdata('paypal_data');

			    $paypal_data['products'] = $session_products;

			    $this->session->set_userdata([
			        'paypal_data'   =>  $paypal_data
                ]);

			    var_dump($session_products);die;
            }*/


			//Fixle
			$order_data['total'] = $total;


			$order_data['language_id'] = $this->data['current_lang_id'];
			$order_data['currency_id'] = $this->currency->getId($this->data['current_currency']);
			$order_data['currency_code'] = $this->data['current_currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->data['current_currency']);
			$order_data['ip'] = $this->input->server('REMOTE_ADDR');

			if (!empty($this->input->server['HTTP_X_FORWARDED_FOR']))
			{
				$order_data['forwarded_ip'] = $this->input->server('HTTP_X_FORWARDED_FOR');
			}
			elseif (!empty($this->input->server['HTTP_CLIENT_IP']))
			{
				$order_data['forwarded_ip'] = $this->input->server('HTTP_CLIENT_IP');
			}
			else
			{
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->input->server['HTTP_USER_AGENT']))
			{
				$order_data['user_agent'] = $this->input->server('HTTP_USER_AGENT');
			}
			else
			{
				$order_data['user_agent'] = '';
			}

			if (isset($this->input->server['HTTP_ACCEPT_LANGUAGE']))
			{
				$order_data['accept_language'] = $this->input->server('HTTP_ACCEPT_LANGUAGE');
			}
			else
			{
				$order_data['accept_language'] = '';
			}

			$this->load->model('Order_model');
			
			if( (! isset($_POST['cko-card-token']) || ! $_POST['cko-card-token']) && $_POST['payment_method'] == 'credit' ) {
                redirect(site_url_multi('checkout'));
            }

			$order_data['combined_shipping'] = json_encode($this->cart_lib->getShippingTotalWithMethod());
			
			
            $order_id = $this->Order_model->addOrder($order_data);

            $this->session->set_userdata('order_id', $order_id);

            $this->load->library('payment/'.ucfirst($order_data['payment_code']));
			
            $redirect = $this->{$order_data['payment_code']}->process($order_id);

        }
		else
		{
			$redirect;
		}
		
		redirect($redirect);
	}
	
	public function cart() 
	{
        foreach($this->data['languages'] as $key => $value)
        {

            $link =  site_url($key.'/checkout/cart');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }

		if($this->input->post('quantity') && !empty($this->input->post('quantity'))){
			foreach ($this->input->post('quantity') as $cart_id => $quantity) {
				if($quantity > 0){
					$this->cart_lib->update($cart_id,$quantity);
				} else {
					$this->cart_lib->remove($cart_id);
				}
			}
		}

		$this->data['total'] =  $this->currency->formatter($this->cart_lib->getTotalPrice(), $this->data['current_currency'], $this->data['current_currency']);
		$this->data['shipping_price'] =  $this->currency->formatter($this->cart_lib->getShippingTotal(), $this->data['current_currency'], $this->data['current_currency']);
		$this->data['subtotal'] = $this->currency->formatter($this->cart_lib->getSubTotalPrice(), $this->data['current_currency'], $this->data['current_currency']);
		//Coupon
		$this->data['coupon'] = '';
		
		if($this->session->has_userdata('coupon'))
		{	
			$this->data['coupon'] = $this->session->userdata('coupon');
			$this->load->model('modules/Coupon_model');
			$this->data['coupon_total'] = $this->currency->formatter($this->Coupon_model->getTotal($this->cart_lib->getTotalPrice()), $this->data['current_currency'], $this->data['current_currency']);
			$this->data['total'] = $this->currency->formatter($this->cart_lib->getTotalPrice()-$this->Coupon_model->getTotal($this->cart_lib->getTotalPrice()), $this->data['current_currency'], $this->data['current_currency']);
		}
		$this->data['products'] = $this->cart_lib->getProducts();
		$this->template->render('cart');
	}

	public function success()
	{
		
		if($this->input->get('cko-session-id')){
			
			$_sid = $this->input->get('cko-session-id');
		
			$this->db->select('order_id, amount, c_payment_id');
			$this->db->where(['sid'=>$_sid, 'status'=>0]);
			$_q = $this->db->get('checkoutcom_pending')->row();
			
			if($_q){

				$this->db->insert('checkoutcom', [
					'payment_id' => $_q->c_payment_id,
					'c_payment_id' => $_q->c_payment_id,
					'order_id' => $_q->order_id,
					'amount'    =>  $_q->amount,
				]);

				$this->db->where('id', $_q->order_id);

				$this->db->update('order', ['order_status_id'  =>  1]);

				$order_products = $this->db->from('order_product')
					->where('order_id', $_q->order_id)
					->get()
					->result_array();

				foreach ($order_products as $order) {

					$this->db->query('UPDATE wc_product SET quantity=quantity-'. $order['quantity'] .' WHERE id=' . $order['product_id']);

				}

				$this->session->set_userdata('order_id', $_q->order_id);

				send_custom_mail([
					'to' => $this->data['customer']->email,
					'subject'   =>  translate('payment_successfully_subject', true),
					'message'   =>  translate('payment_successfully_text', true),
				]); 
				
				$this->db->set('status', 1);
				$this->db->where(['sid'=>$_sid, 'status'=>0]);
				$this->db->update('checkoutcom_pending');
				
				redirect(site_url_multi('checkout/success'));
				
			}
			
		}
		
		
		
	    if($order_id = $this->session->userdata('order_id')) {

	        $shipping = $this->cart_lib->getShippingTotalWithMethod($order_id);

	        $products = $this->db
                ->select('*, O.total as order_total')
                ->from('order O')
                ->join('order_product OP', 'O.id=OP.order_id', 'left')
                ->where('order_id', $order_id)
                ->get()
                ->result_array();



	        if(true) {

//	            $this->load->library('libraries/Currency');

                $checkoutData = $this->session->userdata('checkout_data');


                if(true) {

                    $total      = $this->currency->convertCurrencyString($checkoutData['total']);

                    $total_usd  = $this->currency->convert($total['value'], 'USD', strtoupper($total['code']));


                    $tax      = $this->currency->convertCurrencyString($checkoutData['tax']);

                    $tax_usd  = $this->currency->convert($tax['value'], 'USD', strtoupper($tax['code']));


                    $shipping      = $this->currency->convertCurrencyString($checkoutData['shipping']);

                    $shipping_usd  = $this->currency->convert($shipping['value'], 'USD', strtoupper($shipping['code']));


                    $coupon       = $this->currency->convertCurrencyString($checkoutData['coupon']);

                    $coupon_usd   = $this->currency->convert($coupon['value'], 'USD', strtoupper($coupon['code']));

                    $json = [
                        'event'         =>  'gtm4wp.orderCompletedEEC',
                        'ecommerce'     =>  [
                            'purchase'  =>  [
                                'actionField'   =>  [
                                    'id'            =>  $order_id,
                                    'affiliation'   =>  'Mimelon Store',
                                    'revenue'       =>  ceil($total_usd),
                                    'tax'           =>  $tax_usd,
                                    'shipping'      =>  intval($shipping_usd),
                                    'coupon'        =>  $coupon_usd,
                                    'currencyCode'  =>  'USD'
                                ]
                            ]
                        ]
                    ];





                    $this->load->model('Product_model');

                    foreach ($checkoutData['products'] as $product) {

                        $product_price      = $this->Product_model->get_products(['id' => $product['product_id']]);

                        $category_and_brand =
                            $this->db
                                ->select('B.name as brand_name, CT.name as category_name')
                                ->from('product P')
                                ->join('brand B', 'P.manufacturer_id=B.id', 'left')
                                ->join('product_to_category PC', 'P.id=PC.product_id', 'left')
                                ->join('category_translation CT', 'PC.category_id=CT.category_id', 'left')
                                ->where('P.id', $product['product_id'])
                                ->where('CT.language_id', $this->data['current_lang_id'])
                                ->limit(1)
                                ->get()
                                ->row();

                        /*$relation =
                            $this->db
                                ->from('product_relation_value')
                                ->where('product_id', $product['product_id'])
                                ->where('relation_id>', 0)
                                ->get()
                                ->result()[0];

                        $relation_value =
                            $this->db
                                ->from('relation_value_description')
                                ->where('relation_value_id', $relation->relation_value_id)
                                ->where('relation_id', $relation->relation_id)
                                ->get()
                                ->result_array();*/

                        $product_price = $product_price[0]['price'];

                        $product_price= $this->currency->convertCurrencyString($product_price);

                        $product_price_usd   = $this->currency->convert($product_price['value'], 'USD', strtoupper($product_price['code']));

                        $json['ecommerce']['purchase']['products'][] = [
                            'id'        =>  $product['product_id'],
                            'name'      =>  $product['model'],
                            'price'     =>  ceil($product_price_usd),
                            'brand'     =>  $category_and_brand->brand_name,
                            'category'  =>  $category_and_brand->category_name,
                            'variant'   =>  '',
                            'quantity'  =>  (int) $product['quantity'],
                            'coupon'    =>  '',
                            'currencyCode'  =>  'USD'
                        ];
                    }
                }

	            $this->data['json'] = json_encode($json);
            }

            $this->session->unset_userdata('order_id');

        }

        foreach($this->data['languages'] as $key => $value)
        {

            $link =  site_url($key.'/checkout/success');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }

		$this->data['title'] = translate('successfully_payment');
		$this->data['description'] = translate('successfully_payment_description');
		$this->cart_lib->clear();
		$this->template->render('success');
	}

	public function cod()
	{
        foreach($this->data['languages'] as $key => $value)
        {

            $link =  site_url($key.'/checkout/cod');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }
		$this->data['title'] = translate('cod_successfully_payment');
		$this->data['description'] = translate('cod_successfully_payment_description');
		$this->cart_lib->clear();
		$this->template->render('success');
	}

	public function error()
	{
        foreach($this->data['languages'] as $key => $value)
        {

            $link =  site_url($key.'/checkout/error');
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => $link
            ];
        }
		$this->data['title'] = translate('error_payment');
		$this->data['description'] = translate('error_payment_description');
		$this->template->render('success');
	}

	public function callback()
	{
	    if($this->input->post('custom')) {
            $this->load->library('payment/Paypal');
            $this->paypal->callback();
        }
		
		$this->cart_lib->clear();
	}

	public function paypal()
	{
		if(!$this->session->has_userdata('paypal_data'))
		{
			redirect(site_url_multi('checkout'));
		}
		else
		{
			$this->data['title'] = translate('paypal');
			$this->data['description'] = translate('paypal_description');
			
			$this->data['paypal'] = $this->session->userdata('paypal_data');
			$this->template->render('paypal');
		}
		
	}


	public function payment_method()
	{
		if($this->input->method() == 'post')
		{
			$this->form_validation->set_rules('payment_method', translate('payment_method'), 'required|trim');

			if($this->form_validation->run())
			{
				$this->session->set_userdata('payment_method', $this->input->post('payment_method'));
				$response = true;
			}
			else
			{
				$response = false;
			}
		}
		else
		{
			$response = false;
		}

		$this->template->json($response);
	}


    public function test(){

        $this->template->render('test');
    }

    public function test2()
    {
        $this->template->render('checkout_2');
    }


}