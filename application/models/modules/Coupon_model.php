<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon_model extends Core_Model
{
	public $CI;
	public $table = 'coupon';
	public $primary_key = 'id';
	public $protected = [];

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
		$this->CI = &get_instance();
	}

	public function getCoupon($code) {
		$status = true;

		$coupon_query = $this->db->query("SELECT * FROM `wc_coupon` WHERE code = '" . $code . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");

		if ($coupon_query->num_rows()) {
			if ($coupon_query->row()->total > $this->CI->cart_lib->getSubTotal()) {
				$status = false;
			}

			$coupon_total = $this->getTotalCouponHistoriesByCoupon($code);

			if ($coupon_query->row()->uses_total > 0 && ($coupon_total >= $coupon_query->row()->uses_total))
			{
				$status = false;
			}

			if ($coupon_query->row()->logged && !$this->CI->data['customer'])
			{
				$status = false;
			}

			if ($this->CI->data['customer'])
			{
				$customer_total = $this->getTotalCouponHistoriesByCustomerId($code, $this->CI->data['customer']->id);
				
				if ($coupon_query->row()->uses_customer > 0 && ($customer_total >= $coupon_query->row()->uses_customer))
				{
					$status = false;
				}
			}

			// Products
			$coupon_product_data = [];

			if($coupon_query->row()->products)
			{
				$coupon_product_query = explode(',', $coupon_query->row()->products);
				foreach ($coupon_product_query as $product)
				{
					$coupon_product_data[] = $product;
				}
			}
			

			// Categories
			$coupon_category_data = [];
			if($coupon_query->row()->categories)
			{
				$coupon_category_query =  explode(',', $coupon_query->row()->categories);

				foreach ($coupon_category_query as $category)
				{
					$coupon_category_data[] = $category;
				}
			}
			
			$product_data = array();
			
			if ($coupon_product_data || $coupon_category_data) {
				foreach ($this->CI->cart_lib->getProducts() as $product) {
					if (in_array($product['product_id'], $coupon_product_data)) {
						$product_data[] = $product['product_id'];

						continue;
					}

					foreach ($coupon_category_data as $category_id) {
						$coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `wc_product_to_category` WHERE `product_id` = '" . (int)$product['product_id'] . "' AND category_id = '" . (int)$category_id . "'");

						if ($coupon_category_query->row()->total) {
							$product_data[] = $product['product_id'];

							continue;
						}
					}
				}
				var_dump($product_data);
				if (!$product_data) {
					$status = false;
				}
			}
		} else {
			$status = false;
		}

		if ($status) {
			return [
				'coupon_id'     => $coupon_query->row()->id,
				'code'          => $coupon_query->row()->code,
				'name'          => $coupon_query->row()->name,
				'type'          => $coupon_query->row()->type,
				'discount'      => $coupon_query->row()->discount,
				'shipping'      => $coupon_query->row()->shipping,
				'total'         => $coupon_query->row()->total,
				'product'       => $product_data,
				'date_start'    => $coupon_query->row()->date_start,
				'date_end'      => $coupon_query->row()->date_end,
				'uses_total'    => $coupon_query->row()->uses_total,
				'uses_customer' => $coupon_query->row()->uses_customer,
				'status'        => $coupon_query->row()->status,
				'date_added'    => $coupon_query->row()->created_at
			];
		}
	}

	public function getTotal($total) {
		if ($this->CI->session->has_userdata('coupon')) {

			$coupon_info = $this->getCoupon($this->CI->session->userdata('coupon'));

			if ($coupon_info) {
				$discount_total = 0;

				if (!$coupon_info['product']) {
					$sub_total = $this->CI->cart_lib->getSubTotal();
				} else {
					$sub_total = 0;

					foreach ($this->CI->cart_lib->getProducts() as $product) {
						if (in_array($product['product_id'], $coupon_info['product'])) {
							$sub_total += $product['total'];
						}
					}
				}

				if ($coupon_info['type'] == 'F') {
					$coupon_info['discount'] = min($coupon_info['discount'], $sub_total);
				}

				foreach ($this->CI->cart_lib->getProducts() as $product) {
					$discount = 0;

					if (!$coupon_info['product']) {
						$status = true;
					} else {
						$status = in_array($product['product_id'], $coupon_info['product']);
					}

					if ($status) {
						if ($coupon_info['type'] == 'F') {
							$discount = $coupon_info['discount'] * ($product['total'] / $sub_total);
						} elseif ($coupon_info['type'] == 'P') {
							$discount = $product['total'] / 100 * $coupon_info['discount'];
						}

						if ($product['tax_class_id']) {
							$tax_rates = $this->CI->tax->getRates($product['total'] - ($product['total'] - $discount), $product['tax_class_id']);

							foreach ($tax_rates as $tax_rate) {
								if ($tax_rate['type'] == 'P') {
									$total['taxes'][$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
								}
							}
						}
					}
					$discount_total += $this->currency->formatter_without_symbol($discount, $this->currency->getCode($product['currency']), $this->data['current_currency']);
				}

				// if ($coupon_info['shipping'] && $this->CI->session->has_userdata('shipping_method')) {
				// 	if (!empty($this->session->data['shipping_method']['tax_class_id'])) {
				// 		$tax_rates = $this->CI->tax->getRates($this->CI->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

				// 		foreach ($tax_rates as $tax_rate) {
				// 			if ($tax_rate['type'] == 'P') {
				// 				$total['taxes'][$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
				// 			}
				// 		}
				// 	}

				// 	$discount_total += $this->session->data['shipping_method']['cost'];
				// }

				// If discount greater than total
				if ($discount_total > $total) {
					$discount_total = $total;
				}
				return $discount_total;
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end)
		{
			$code = substr($order_total['title'], $start, $end - $start);
		}

		if ($code)
		{
			$status = true;
			
			$coupon_query = $this->db->query("SELECT * FROM `wc_coupon` WHERE code = '" .$code . "' AND status = '1'");

			if ($coupon_query->num_rows())
			{
				$coupon_total = $this->getTotalCouponHistoriesByCoupon($code);
	
				if ($coupon_query->row()->uses_total > 0 && ($coupon_total >= $coupon_query->row()->uses_total)) {
					$status = false;
				}
				
				if ($order_info['customer_id'])
				{
					$customer_total = $this->getTotalCouponHistoriesByCustomerId($code, $order_info['customer_id']);
					
					if ($coupon_query->row()->uses_customer > 0 && ($customer_total >= $coupon_query->row()->uses_customer))
					{
						$status = false;
					}
				}
			}
			else
			{
				$status = false;	
			}

			if ($status)
			{
				$this->db->query("INSERT INTO `wc_coupon_history` SET coupon_id = '" . (int)$coupon_query->row()->id . "', order_id = '" . (int)$order_info['order_id'] . "', customer_id = '" . (int)$order_info['customer_id'] . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
			}
		}
	}

	public function unconfirm($order_id)
	{
		$this->db->query("DELETE FROM `wc_coupon_history` WHERE order_id = '" . (int)$order_id . "'");
	}
	
	public function getTotalCouponHistoriesByCoupon($coupon)
	{
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `wc_coupon_history` ch LEFT JOIN `wc_coupon` c ON (ch.coupon_id = c.id) WHERE c.code = '" . $coupon . "'");	
		
		return $query->row()->total;
	}
	
	public function getTotalCouponHistoriesByCustomerId($coupon, $customer_id)
	{
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `wc_coupon_history` ch LEFT JOIN `wc_coupon` c ON (ch.coupon_id = c.id) WHERE c.code = '" . $coupon . "' AND ch.customer_id = '" . (int)$customer_id . "'");
		
		return $query->row()->total;
	}
}