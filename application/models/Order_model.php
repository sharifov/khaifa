<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends Core_Model
{

	public $table = 'order';
	public $primary_key = 'id';
	public $protected = [];
	public $authors = false;

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
	}

	public function addOrder($data)
	{
        $products = $data['products'];
		$totals = $data['totals'];
		unset($data['products']);
		unset($data['totals']);

		$this->insert($data);

		$order_id = $this->db->insert_id();

		// Products
		if (isset($products)) {
			foreach ($products as $product)
			{
				$order_product = [
					'order_id'			=> $order_id,
					'product_id'		=> $product['product_id'],
					'name'				=> $product['name'],
					'model'				=> $product['model'],
					'quantity'			=> $product['quantity'],
					'price'				=> $product['price'],
					'total'				=> $product['total'],
					'price_original'	=> $product['price_original'],
					'total_original'	=> $product['total_original'],
					'currency_original'	=> $product['currency_original'],
					'tax'				=> $product['tax'],
					'shipping'			=> $product['shipping'],
				];

				$this->db->insert('order_product', $order_product);

				$order_product_id = $this->db->insert_id();

				foreach ($product['option'] as $option)
				{
					$order_option = [
						'order_id'						=> $order_id,
						'order_product_id'				=> $order_product_id,
						'product_option_id'				=> $option['product_option_id'],
						'product_option_value_id'		=> $option['product_option_value_id'],
						'name'							=> $option['name'],
						'value'							=> $option['value'],
						'type'							=> $option['type']
					];

					$this->db->insert('order_option', $order_option);
				}
			}
		}

		// Totals
		if (isset($totals))
		{
			foreach ($totals as $total)
			{
				$order_total = [
					'order_id'		=> $order_id,
					'code'			=> $total['code'],
					'title'			=> $total['title'],
					'value'			=> $total['value'],
					'sort_order'	=> $total['sort_order']
				];
				
				$this->db->insert('order_total', $order_total);

			}
		}

		return $order_id;
	}

	public function change_order_status($order_id, $order_status_id, $comment = '', $notify = false, $override = false)
	{
		$order_info = $this->getOrder($order_id);
		if ($order_info)
		{
		
			if (!in_array($order_info['order_status_id'], array(1, 2)) && in_array($order_status_id, array(1, 2)))
			{

			

				$order_totals = $this->getOrderTotals($order_id);

				foreach ($order_totals as $order_total)
				{

					$CI = &get_instance();
					$model_name = ucfirst($order_total['code']).'_model';
					$CI->load->model('modules/' . $model_name);

					if (property_exists($CI->{$model_name}, 'confirm'))
					{
						$CI->{$model_name}->confirm($order_id);
					}
				}

				$order_products = $this->getOrderProducts($order_id);

				foreach ($order_products as $order_product)
				{
					$this->db->query("UPDATE wc_product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");
					$order_options = $this->getOrderOptions($order_id, $order_product['product_id']);

					foreach ($order_options as $order_option)
					{
						$this->db->query("UPDATE wc_product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
					}
				}
			}

			// Update the DB with the new statuses
			$this->db->query("UPDATE `wc_order` SET order_status_id = '" . (int)$order_status_id . "', updated_at = NOW() WHERE id = '" . (int)$order_id . "'");

			$this->db->query("INSERT INTO wc_order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $comment . "', created_at = NOW()");

			
			if (in_array($order_info['order_status_id'], array(1, 2)) && !in_array($order_status_id, array(1, 2))) {
				// Restock
				$order_products = $this->getOrderProducts($order_id);

				foreach($order_products as $order_product)
				{
					$this->db->query("UPDATE `wc_product` SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

					$order_options = $this->getOrderOptions($order_id, $order_product['order_product_id']);

					foreach ($order_options as $order_option)
					{
						$this->db->query("UPDATE wc_product_option_value SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
					}
				}

				$order_totals = $this->getOrderTotals($order_id);
				
				foreach ($order_totals as $order_total)
				{
					$CI = &get_instance();
					$model_name = ucfirst($order_total['code']).'_model';
					$CI->load->model('modules/' . $model_name);

					if (property_exists($CI->{$model_name}, 'unconfirm'))
					{
						$CI->{$model_name}->unconfirm($order_id);
					}
				}
			}
		}
	}

	public function getOrderProducts($order_id)
	{
		$query = $this->db->query("SELECT * FROM wc_order_product WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->result_array();
	}
	
	public function getOrderOptions($order_id, $order_product_id)
	{
		$query = $this->db->query("SELECT * FROM wc_order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
		
		return $query->result_array();
	}

	public function getOrderTotals($order_id)
	{
		$query = $this->db->query("SELECT * FROM `wc_order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
	
		return $query->result_array();
	}

	public function getOrder($order_id)
	{
		$order_query = $this->db->query("SELECT * FROM `wc_order` o WHERE o.id = '" . (int)$order_id . "'");

		if ($order_query->num_rows())
		{
			return [
				'id'                	  => $order_query->row()->id,
				'customer_id'             => $order_query->row()->customer_id,
				'address_id'               => $order_query->row()->address_id,
				'payment_method'          => $order_query->row()->payment_method,
				'payment_code'            => $order_query->row()->payment_code,
				'shipping_method'         => $order_query->row()->shipping_method,
				'shipping_code'           => $order_query->row()->shipping_code,
				'total'                   => $order_query->row()->total,
				'order_status_id'         => $order_query->row()->order_status_id,
				'language_id'             => $order_query->row()->language_id,
				'currency_id'             => $order_query->row()->currency_id,
				'currency_code'           => $order_query->row()->currency_code,
				'currency_value'          => $order_query->row()->currency_value,
				'ip'                      => $order_query->row()->ip,
				'forwarded_ip'            => $order_query->row()->forwarded_ip,
				'user_agent'              => $order_query->row()->user_agent,
				'accept_language'         => $order_query->row()->accept_language,
				'created_at'              => $order_query->row()->created_at,
				'updated_at'           	  => $order_query->row()->updated_at
			];
		}
		else
		{
			return false;
		}
	}
}