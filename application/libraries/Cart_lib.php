<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Cart_lib
{
	public $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->db->query("DELETE FROM wc_cart WHERE (api_id > '0' OR customer_id = '0') AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
		if($this->CI->data['customer'] ?? '')
		{
			$this->CI->db->query("UPDATE wc_cart SET session_id = '" . $this->CI->session->session_id . "' WHERE api_id = '0' AND customer_id = '" . (int)$this->CI->data['customer']->id . "'");

			// Once the customer is logged in we want to update the customers cart
			$cart_query = $this->CI->db->query("SELECT * FROM wc_cart WHERE api_id = '0' AND customer_id = '0' AND session_id = '" .$this->CI->session->session_id . "'");

			foreach ($cart_query->result_array() as $cart) {
				$this->CI->db->query("DELETE FROM wc_cart WHERE cart_id = '" . (int)$cart['cart_id'] . "'");

				// The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
				$this->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id'], $cart['shipping']);
			}
		}
		
	}
	
	public function add($product_id, $quantity = 1, $option = [], $recurring_id = 0, $shipping = '')
	{

		$customer_id = ($this->CI->data['customer']) ? $this->CI->data['customer']->id : 0;
		$where = [
			'customer_id' => $customer_id,
			'api_id' => 0,
			'session_id' => $this->CI->session->session_id,
			'product_id' => $product_id,
			'recurring_id' => 0,
			'option' => json_encode($option),
			'shipping' => $shipping
		];
		$this->CI->db->where($where);
		$total_rows = $this->CI->db->count_all_results('cart');
		if (!$total_rows)
		{
			$data = [
				'api_id' => 0,
				'customer_id' => (int)$customer_id,
				'session_id' => $this->CI->session->session_id,
				'product_id' => $product_id,
				'recurring_id' => 0,
				'option' => json_encode($option),
				'shipping' => $shipping,
				'quantity' => $quantity,
				'created_at' => date('Y-m-d H:i:s')
			];
			
			$this->CI->db->insert('cart',$data);
		}
		else
		{
			$this->CI->db->set('quantity', 'quantity + '.$quantity, FALSE);
			$this->CI->db->where($where);
			$this->CI->db->update('cart');
		}
		return true;
	}
	
	public function update($cart_id, $quantity)
	{
		$customer_id = ($this->CI->data['customer']) ? $this->CI->data['customer']->id : 0;
		$this->CI->db->set('quantity', $quantity);
		$where = [
			'customer_id' => $customer_id,
			'api_id' => 0,
			'session_id' => $this->CI->session->session_id,
			'cart_id'=> $cart_id
		];
		$this->CI->db->where($where);
		$this->CI->db->update('cart');
	}

	public function remove($cart_id)
	{

		$customer_id = ($this->CI->data['customer']) ? $this->CI->data['customer']->id : 0;
		$where = [
			'customer_id' => $customer_id,
			'api_id' => 0,
			'session_id' => $this->CI->session->session_id,
			'cart_id' => $cart_id
		];

		$this->CI->db->delete('cart',$where);
	}

	public function clear()
	{
		$customer_id = ($this->CI->data['customer']) ? $this->CI->data['customer']->id : 0;
		$where = [
			'customer_id' => $customer_id,
			'api_id' => 0,
			'session_id' => $this->CI->session->session_id
		];
		$this->CI->db->delete('cart',$where);
	}

	public function getProducts()
	{
		$product_data = array();
	
		$customer_id = ($this->CI->data['customer']) ? $this->CI->data['customer']->id : 0;
		$where = [
			'customer_id' => $customer_id,
			'api_id' => 0,
			'session_id' => $this->CI->session->session_id
		];
		$this->CI->db->where($where);
		
		$cart_query = $this->CI->db->get('cart');
		
		if($cart_query->num_rows() > 0) {
			foreach($cart_query->result() as $cart) {

				$stock = true;
				$this->CI->db->join('product_translation','product.id = product_translation.product_id');
				$this->CI->db->where(['product.id' => $cart->product_id]);
				$this->CI->db->where(['product_translation.language_id' => $this->CI->data['current_lang_id']]);
				$this->CI->db->where(['product.status' => 1]);
				$this->CI->db->where(['date_available <= "'.date('Y-m-d').'"' => null]);
				$product_query = $this->CI->db->get('product');
				if($product_query->num_rows() > 0 && ($cart->quantity > 0)) {
					$product = $product_query->row();
					
					$option_price = 0;
					$option_points = 0;
					$option_weight = 0;

				/* 	print_R($cart);
					die; */
					
					$option_data = array();
					if(!empty($cart->option) && $cart->option != "null") {
						foreach (json_decode($cart->option) as $product_option_id => $value) {
							$this->CI->db->join('option', 'product_option.option_id = option.id');
							$this->CI->db->join('option_translation', 'option.id = option_translation.option_id');
							$this->CI->db->where(['product_option.id' => $product_option_id]);
							$this->CI->db->where(['option_translation.language_id' => $this->CI->data['current_lang_id']]);
							$option_query = $this->CI->db->get('product_option');

							if ($option_query->num_rows() > 0) {
								$option = $option_query->row();
								if ($option->type == 'select' || $option->type == 'radio') {
									
									$this->CI->db->join('option_value', 'product_option_value.option_value_id = option_value.id');
									$this->CI->db->join('option_value_description', 'option_value.id = option_value_description.option_value_id');
									$this->CI->db->where(['product_option_value.product_option_id' => $product_option_id]);
									$this->CI->db->where(['product_option_value.id' => $value]);
									$this->CI->db->where(['option_value_description.language_id' => $this->CI->data['current_lang_id']]);
									$option_value_query = $this->CI->db->get('product_option_value');
									
									if ($option_value_query->num_rows() > 0) {
										$option_value_query = $option_value_query->row();
										
										if ($option_value_query->price_prefix == '+') {
											$option_price += $option_value_query->price;
										} elseif ($option_value_query->price_prefix == '-') {
											$option_price -= $option_value_query->price;
										}

										if ($option_value_query->points_prefix == '+') {
											$option_points += $option_value_query->points;
										} elseif ($option_value_query->points_prefix == '-') {
											$option_points -= $option_value_query->points;
										}

										if ($option_value_query->weight_prefix == '+') {
											$option_weight += $option_value_query->weight;
										} elseif ($option_value_query->weight_prefix == '-') {
											$option_weight -= $option_value_query->weight;
										}

										if ($option_value_query->subtract && (!$option_value_query->quantity || ($option_value_query->quantity < $cart->quantity))) {
											$stock = false;
										}

										$option_data[] = array(
											'product_option_id'       => $product_option_id,
											'product_option_value_id' => $value,
											'option_id'               => $option->id,
											'option_value_id'         => $option_value_query->option_value_id,
											'name'                    => $option->name,
											'value'                   => $option_value_query->name,
											'type'                    => $option->type,
											'quantity'                => $option_value_query->quantity,
											'subtract'                => $option_value_query->subtract,
											'price'                   => $option_value_query->price,
											'price_prefix'            => $option_value_query->price_prefix,
											'points'                  => $option_value_query->points,
											'points_prefix'           => $option_value_query->points_prefix,
											'weight'                  => $option_value_query->weight,
											'weight_prefix'           => $option_value_query->weight_prefix
										);
									}
								}
							}
						}
					}

					$currency = $this->CI->currency->getCode($product->currency);

					$shipping_price = 0;
					$shipping_data = array();
					if(!empty($cart->shipping) && $cart->shipping != "null")
					{
						$shipping = json_decode($cart->shipping);
						
							$shipping_data[] = array(
								'name'         => $shipping->name,
								'code'         => $shipping->code,
								'price'        => $shipping->price,
								'currency'     => $shipping->currency,
							);

							$shipping_price = $shipping->price;
							$shipping_currency = $shipping->currency;
					}

					
					// Product Discounts
					$discount_quantity = 0;

					foreach ($cart_query->result() as $cart_2) {
						if ($cart_2->product_id == $cart->product_id) {
							$discount_quantity += $cart_2->quantity;
						}
					}
					
					$this->CI->db->select('price');
					$this->CI->db->where(['product_id' => $cart->product_id]);
					$this->CI->db->where(['customer_group_id' => 1]);
					$this->CI->db->where(['quantity <= '.(int)$discount_quantity => null]);
					$this->CI->db->where(["((date_start = '0000-00-00' OR date_start < NOW())" => null]);
					$this->CI->db->where(["(date_end = '0000-00-00' OR date_end > NOW()))" => null]);
					$this->CI->db->order_by("quantity", "desc");
					$this->CI->db->order_by("priority", "asc");
					$this->CI->db->order_by("price", "asc");
					$this->CI->db->limit(1);
					$product_discount_query = $this->CI->db->get('product_discount');
					if ($product_discount_query->num_rows() > 0) {
						$price = $product_discount_query->row()->price;
					}
					
					// Product Specials
					$this->CI->db->select('price');
					$this->CI->db->where(['product_id' => $cart->product_id]);
					$this->CI->db->where(['customer_group_id' => 1]);
					$this->CI->db->where(["(date_start = '0000-00-00' OR date_start < NOW())" => null]);
					$this->CI->db->where(["(date_end = '0000-00-00' OR CONCAT(date_end, ' 23:59:59')  > NOW())" => null]);
					$this->CI->db->order_by("priority", "asc");
					$this->CI->db->order_by("price", "asc");
					$this->CI->db->limit(1);

					$product_special_query = $this->CI->db->get('product_special');
					
					if ($product_special_query->num_rows() > 0) {
						$price = $product_special_query->row()->price;
					}

					// Calculate country group price
					$price = $this->calculate_country_group_price($cart->product_id, $price);
		
					// Reward Points
					$reward = 0;

					// Downloads
					$download_data = array();
					// Stock
					if (!$product->quantity || ($product->quantity < $cart->quantity))
					{
						$stock = false;
					}

					$recurring = false;


					$product_data[] = array(
						'cart_id'         => $cart->cart_id,
						'country_id'      => $product->country_id,
						'product_id'      => $product->id,
						'currency'        => $currency,
						'name'            => $product->name,
						'description'     => $product->description,
						'slug'            => $product->slug,
						'model'           => $product->model,
						'image'           => $product->image,
						'created_by'      => $product->created_by,
						'option'          => $option_data,
						'shipping'        => $shipping_data,
						'download'        => $download_data,
						'quantity'        => $cart->quantity,
						'minimum'         => $product->min_quantity,
						'subtract'        => $product->subtract,
						'stock'           => $stock,
						'price'           => ($price + $option_price),
						'price_original'  => ($product->price+$option_price),
						'currency_original'  => $product->currency,
						'shipping_price' => $shipping_price,
						'shipping_currency' => $shipping_currency,
						'total'           => ceil(($price + $option_price) * $cart->quantity),
						'total_original'  => ceil(($product->price + $option_price) * $cart->quantity),
						'total_for_shipping'  => 34,
						//'total_price' 	  => ($price + $option_price) * $cart->quantity,
						'reward'          => $reward * $cart->quantity,
						'points'          => ($product->points ? ($product->points + $option_points) * $cart->quantity : 0),
						'tax_class_id'    => $product->tax_class_id,
						'weight'          => ($product->weight + $option_weight) * $cart->quantity,
						'weight_class_id' => $product->weight_class_id,
						'length'          => $product->length,
						'width'           => $product->width,
						'height'          => $product->height,
						'length_class_id' => $product->length_class_id,
						'recurring'       => $recurring
					);

				} else {
					$this->remove($cart->cart_id);
				}
			}
		}

		return $product_data;
	}

	public function calculate_country_group_price($product_id, $price)
	{

		if($product_id > 0 && $price > 0) {
			$country_group_id = get_country_group_id();
			$this->CI->db->where(['country_group_id' => $country_group_id, 'product_id' => $product_id]);
			$query = $this->CI->db->get('product_country_group');
			$country_group_percent = 0;
			if($query->num_rows() > 0) {
				$country_group_percent  = $query->row()->percent;
			} else {
				$this->CI->db->where(['id' => $country_group_id]);
				$country_group_query = $this->CI->db->get('country_group');
				if($country_group_query->num_rows() > 0) {
					$country_group_percent  = $country_group_query->row()->percent;
				}
			}

			if($country_group_percent > 0) {
				$price += (int)(($price * $country_group_percent) / 100);
				return $price;
			} 
		}
		return $price;
	}
	
	public function getWeight()
	{
		$weight = 0;

		foreach ($this->getProducts() as $product)
		{
			$weight += $this->CI->weight->convert($product['weight'], $product['weight_class_id'], get_setting('config_weight_class_id'));
		}

		return $weight;
	}

	public function getSubTotal()
	{
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getSubTotalPrice()
	{
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total +=$this->CI->currency->formatter_without_symbol($product['total'], $product['currency'], $this->CI->data['current_currency']);
		}

		return $total;
	}

	public function getTaxes()
	{
		$tax_data = [];

		foreach ($this->getProducts() as $product)
		{
			if ($product['tax_class_id'])
			{
				$tax_rates = $this->CI->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate)
				{
					if (!isset($tax_data[$tax_rate['tax_rate_id']]))
					{
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					}
					else
					{
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal()
	{
		$total = 0;

		foreach ($this->getProducts() as $product)
		{
			$total += (int)($this->CI->tax->calculate($product['price'], $product['tax_class_id'], 1) * $product['quantity']);
		}

		return $total;
	}

	public function getShippingTotal()
	{	
	    return self::getShippingTotal2()['show_price'];

		$total = 0;

		foreach ($this->getProducts() as $product)
		{
			//echo $product['shipping_price'].'--';
			//echo $product['shipping_currency'].'--';
			$price = $this->CI->currency->formatter_without_symbol($product['shipping_price'], $product['shipping_currency'], $this->CI->data['current_currency']) * $product['quantity'];
			//echo $price.'-';
			$total += $price;
		}

		return $total;
	}

	public function getShippingTotal2()
    {
        $summary        = 0;
        $summary_show   = 0;

        foreach (self::getShippingTotalWithMethod() as $value) {
            $summary += $value['price'] ?? 0;
            $value['show_price'] =  $value['show_price'] ?? 0;
            $summary_show += (int)(currency_clear($value['show_price']));
        }

        return ['price' => $summary, 'show_price' => $summary_show];
    }

	public function getShippingTotalWithMethod($order_id = null)
	{
		
		$total = 0;

        $this->CI->load->library('shipping/Free');
        $this->CI->load->library('shipping/Ems');
        $this->CI->load->library('shipping/Local');
        $this->CI->load->library('Weight');
        $this->CI->load->library('Length');

        $vendor_shipping_total = [];

        if($order_id) {
			
            $products = $this->CI->db->from('order_product OP')
            ->join('product P', 'OP.product_id=P.id')
            ->where('OP.order_id', $order_id)
            ->get()
            ->result_array();


            foreach ($products as &$product) {

                $product['shipping'] = json_decode($product['shipping'], true);

            }
        }
        else {
            $products = $this->getProducts();
        }
	

        foreach ($products as $product)
		{

		    $product_op = $this->CI->db->from('product')
                ->select('country_id, created_by')
                ->where('id', $product['product_id'])
                ->get()
                ->row();


            if($product_op->country_id == 221)
            {
                if(isset($this->CI->data['current_country_id']) && $this->CI->data['current_country_id'] == 221)
                {
                    $this->CI->data['shipping_list'] = $this->CI->free->calculate();
                }
                else
                {
                    if($product['length_class_id'] != 1)
                    {
                        $Width = $this->CI->length->convert($product['width'], $product['length_class_id'], 1);
                        $height = $this->CI->length->convert($product['height'], $product['length_class_id'], 1);
                        $length =  $this->CI->length->convert($product['length'], $product['length_class_id'], 1);
                    }
                    else
                    {
                        $Width = $product['width'];
                        $height = $product['height'];
                        $length = $product['length'];
                    }

                    $weight = $this->CI->weight->weight_to_gram_converter($product['weight'], $product['weight_class_id']);

                    $product_shipping = $product['shipping'][0]['code'];

                    if( ! isset($vendor_shipping_total[$product_op->created_by][$product_shipping]['width']) ) {
                        $vendor_shipping_total[$product_op->created_by][$product_shipping]['width'] = 0;
                    }

                    if( ! isset($vendor_shipping_total[$product_op->created_by][$product_shipping]['height']) ) {
                        $vendor_shipping_total[$product_op->created_by][$product_shipping]['height'] = 0;
                    }

                    if( ! isset($vendor_shipping_total[$product_op->created_by][$product_shipping]['length']) ) {
                        $vendor_shipping_total[$product_op->created_by][$product_shipping]['length'] = 0;
                    }

                    if( ! isset($vendor_shipping_total[$product_op->created_by][$product_shipping]['weight']) ) {
                        $vendor_shipping_total[$product_op->created_by][$product_shipping]['weight'] = 0;
                    }

                    $vendor_shipping_total[$product_op->created_by][$product_shipping]['width']  += $Width *  $product['quantity'];
                    $vendor_shipping_total[$product_op->created_by][$product_shipping]['height'] += $height * $product['quantity'];
                    $vendor_shipping_total[$product_op->created_by][$product_shipping]['length'] += $length * $product['quantity'];
                    $vendor_shipping_total[$product_op->created_by][$product_shipping]['weight'] += $weight * $product['quantity'];
                    $vendor_shipping_total[$product_op->created_by][$product_shipping]['products'][] = $product['product_id'];

                }
            }

		}

        $total = [];

        foreach ($vendor_shipping_total as $vendor_shipping) {

            foreach ($vendor_shipping as $name => $shipping) {

                $tmp = [];

                $ems_shippings = $this->CI->ems->calculate($shipping['width'], $shipping['height'], $shipping['length'], $shipping['weight']);

                foreach ($ems_shippings as $ems_shipping) {

                    if($ems_shipping['code'] == $name) {
                        $total[] = $tmp[] = array_merge($ems_shipping, [
                            'products'      =>  $shipping['products'],
                            'ems_options'   =>  [
                                'width'     =>  $shipping['width'],
                                'height'    =>  $shipping['height'],
                                'length'    =>  $shipping['length'],
                                'weight'    =>  $shipping['weight']
                            ]
                        ]);
                    }
                }

                if( ! $tmp ) {

                    $current_ems = is_array(current($ems_shippings)) ? current($ems_shippings) : [];

                    $total[] = array_merge($current_ems, [
                        'products'      =>  $shipping['products'],
                        'ems_options'   =>  [
                            'width'     =>  $shipping['width'],
                            'height'    =>  $shipping['height'],
                            'length'    =>  $shipping['length'],
                            'weight'    =>  $shipping['weight']
                        ]
                    ]);
                }

            }
        }

        $shipping_methods = [];

        foreach ($total as $item) {
            if(isset($item['code'])) {
                if(isset($shipping_methods[$item['code']])) {

                    $previousData = $shipping_methods[$item['code']];

                    $shipping_methods[$item['code']] = [
                        'products'      => array_unique(array_merge($previousData['products'], $item['products'])),
                        'ems_options'   =>  [
                            'width' =>  $previousData['ems_options']['width'] + $item['ems_options']['width'],
                            'height' =>  $previousData['ems_options']['height'] + $item['ems_options']['height'],
                            'length' =>  $previousData['ems_options']['length'] + $item['ems_options']['length'],
                            'weight' =>  $previousData['ems_options']['weight'] + $item['ems_options']['weight'],
                        ]
                    ];

                }

                else {
                    $shipping_methods[$item['code']] = [
                        'products'      => $item['products'],
                        'ems_options'   =>  [
                            'width' =>  $item['ems_options']['width'],
                            'height' => $item['ems_options']['height'],
                            'length' => $item['ems_options']['length'],
                            'weight' => $item['ems_options']['weight'],
                        ]
                    ];
                }
            }
        }
		
		$total = array_merge($total, $this->CI->local->calculate($products));

        if(false) {
            $total = [];

            foreach ($shipping_methods as $name => $shipping_method) {

                $tmp = [];

                $ems_shippings = $this->CI->ems->calculate($shipping_method['ems_options']['width'], $shipping_method['ems_options']['height'], $shipping_method['ems_options']['length'], $shipping_method['ems_options']['weight']);

                foreach ($ems_shippings as $ems_shipping) {
                    if($ems_shipping['code'] == $name) {
                        $total[] = $tmp[] = array_merge($ems_shipping, [
                            'products'      =>  $shipping_method['products'],
                            'ems_options'   =>  $shipping_method['ems_options']
                        ]);
                    }
                }

                if( ! $tmp ) {
                    $total[] = array_merge(current($ems_shippings), [
                        'products'      =>  $shipping_method['products'],
                        'ems_options'   =>  $shipping_method['ems_options']
                    ]);
                }
            }
        }
	
		
        return $total;
	}

	public function getPaymentTotal()
	{
		
		$total = $this->getTotalPrice();
		$country_group_id = get_country_group_id();

		if($this->CI->session->has_userdata('payment_method'))
		{
			$payment_method = $this->CI->session->userdata('payment_method');
		}
		else
		{
			$payment_method = get_setting('default_payment_method');
		}

		$this->CI->load->model('modules/Payment_fee_model');
		$fee = $this->CI->Payment_fee_model->filter(['status' => 1, 'country_group_id' => $country_group_id, 'payment_method' => $payment_method])->one();
		if($fee)
		{
			if($fee->type == 0)
			{
				$payment_fee = $this->CI->currency->formatter_without_symbol($fee->value, 'USD', $this->CI->data['current_currency']);
			}
			else
			{
				$payment_fee = (int)($total*$fee->value/100);
			}
		}
		else
		{
			$payment_fee = 0;
		}

		return $payment_fee;
	}


	// New
	public function getTotalPrice() 
	{
		
		$total = 0;
		
		$products = $this->getProducts();
		
		foreach ($products as $product)
		{
			$temp = (int)($this->CI->tax->calculate($product['price'], $product['tax_class_id'], 1) * $product['quantity']);
			
			$shipping_price = $this->CI->currency->formatter_without_symbol($product['shipping_price'], $product['shipping_currency'], $this->CI->data['current_currency'])*$product['quantity'];
			$product_price = $this->CI->currency->formatter_without_symbol($temp, $product['currency'], $this->CI->data['current_currency']);
			
			$total += $product_price;
			
		}
		
		$total += self::getShippingTotal();

		return $total;
	}

	public function countProducts()
	{
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product)
		{
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts()
	{
		return count($this->getProducts());
	}

	public function hasStock()
	{
		foreach ($this->getProducts() as $product)
		{
			if (!$product['stock'])
			{
				return false;
			}
		}

		return true;
	}

	public function hasShipping()
	{
		foreach ($this->getProducts() as $product)
		{
			if ($product['shipping'])
			{
				return true;
			}
		}
		return true;
	}
}