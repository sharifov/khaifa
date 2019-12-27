<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function callback_get_image($data, $params, $id= false)
	{
		if (!empty($data)) {
			return "<img src='" . $this->Model_tool_image->resize($data, $params['width'],
					$params['height']) . "' width='" . $params['width'] . "' height='" . $params['height'] . "'>";
		}
		return;
	}

	public function callback_get_status($data, $params = false, $id = false)
	{
		if($data == 2)
		{
			return "<span class='label label-warning'>"  . translate('waiting', true) . "</span>";
		}
		else
		{
			return '<div class="checkbox checkbox-switch">
				<label>
					<input type="checkbox" class="switch changeStatus" data-on-text="On" data-off-text="Off" data-on-color="success" data-size="mini" data-off-color="danger" data-id="'.$id.'" data-url="'.current_url().'" '.(($data) ? "checked=checked" : "").'>
				</label>
			</div>';
		}
	}

	public function callback_status_vendor($data, $params = false, $id = false)
	{
		if($data == 0)
		{
			return "<a href='".site_url_multi('administrator/order_product/status/'.$id.'/1')."'><i class='icon-check'></i> ".translate('confirm')."</a> <a href='".site_url_multi('administrator/order_product/status/'.$id.'/2')."'><i class='icon-cross'></i>".translate('cancel')."</a>";
		}
		else
		{
			if($data == 2)
			{
				return "<span class='label label-danger'>"  . translate('disable', true) . "</span>";
			}
			elseif($data == 1)
			{
				return "<span class='label label-success'>"  . translate('enable', true) . "</span>";
			}
			elseif($data == 3)
			{
				return "<span class='label label-danger'>Refunded</span>";
			}
			
		}
	}

	public function callback_get_name($data, $params = false, $id = false)
	{
		if (!empty($data)) {
			return (isset(json_decode($data)->index->title->{$this->data['current_lang']})) ? json_decode($data)->index->title->{$this->data['current_lang']} : json_decode($data)->index->title->{$this->data['default_language']};
		}
		return;
	}

	public function callback_get_icon($data, $params = false, $id = false)
	{
		if (!empty($data)) {
			return "<i class='" . $data . "'></i>";
		}
		return;
	}

	public function callback_get_status_label($data)
	{
		if($data) {
			return "<span class='label label-success'>" . translate('enable', true) . "</span>";
		} else {
			return "<span class='label label-danger'>" . translate('disable', true) . "</span>";
		}
	}

	public function callback_get_user_banned($data)
	{
		if($data) {
			return "<span class='label label-danger'>" . translate('disable', true) . "</span>";
		} else {
			return "<span class='label label-success'>" . translate('enable', true) . "</span>";
		}
	}

	public function callback_get_custom_data($data, $params = false)
	{
		if($data && $params) {
			return $params[$data];
		}
		
	}

	public function callback_get_file_label($data)
	{
		if(!empty($data)) {
			return base_url('uploads/'.$data);
		}
		return;
	}

	public function callback_get_image_label($data, $params = ['width' => 200, 'height' => 200]) 
	{
		if($data) {
			$image = $this->Model_tool_image->resize($data, $params['width'], $params['height']);
			if($image) {
				return '<img src="'.$image.'" width="'.$params["width"].'" height="'.$params["height"].'">';
			} else {
				return '<img src="'.$this->Model_tool_image->resize('no-photo.png', $params['width'], $params['height']).'" width="'.$params["width"].'" height="'.$params["height"].'">';
			}
		}

	}

	// public function callback_get_option($data, $params = [])
	// {
	// 	$query = $this->db->get_where($params['table'], [$params['key'] => $data]);
	// 	if($query->num_rows() > 0)
	// 	{
	// 		$row = $query->row();
	// 		return $row->{$params['value']};
	// 	}
	// }

	public function callback_get_option($data, $params = [])
	{
		if(isset($params['module']) && $params['module'])
		{
			if($params['module']['dynamic'])
			{
				$this->load->model('modules/'.$params['module']['name'].'_model');
			}
			else
			{
				$this->load->model($params['module']['name'].'_model');
			}

			$where[$params['module']['key']] = $data;
			if($params['module']['where'])
			{
				foreach($params['module']['where'] as $row)
				{
					$where[$row['key']] = $row['value'];
				}
			}

			if($params['module']['translation'])
			{
				$row = $this->{$params['module']['name'].'_model'}->filter($where)->with_translation()->one();
			}
			else
			{
				$row = $this->{$params['module']['name'].'_model'}->filter($where)->one();
			}

			$columns = explode(',', $params['module']['columns']);

			if($row)
			{
				$result = '';
				foreach($columns as $column)
				{
					$result .= $row->{$column}.' ';
				}

				return trim($result);
			}

			return false;

		}
	}

	public function callback_get_rating($rating, $params = [])
	{
		$stars = '';
		if($rating > 0) {
			for ($i=0; $i < $rating; $i++) { 
				$stars .= '<i class="icon-star-full2" style="color:#ebc733;"></i>';
			}
		}	
		return $stars;
	}

	public function callback_get_multiselect_label($data, $params = [])
	{
		$rows = [];
		if($data)
		{
			$explode = explode(',', $data);
			$this->db->where_in($params['key'], $explode);
			$query = $this->db->get($params['table']);			
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					$rows[] = $row->{$params['value']};
				}
			}
		}

		return implode(',', $rows);
	}

	public function callback_get_customer($data)
	{
		$this->db->where('id', $data);
		$query = $this->db->get('customer');
		if($query->num_rows() == 1)
		{
			return $query->row()->firstname.' '.$query->row()->lastname;
		}
		return false;
	}

	public function callback_get_order_status($data)
	{
		$CI = &get_instance();
		$CI->load->model('modules/Order_status_model');
		$order_status = $CI->Order_status_model->filter(['id' => $data])->with_translation()->one();
		if($order_status)
		{
			return $order_status->name;
		}
		return false;
	}

	public function callback_get_seller($data)
	{
		$CI = &get_instance();
		$seller = $CI->auth->get_user($data);

		return ($seller) ? $seller->firstname.' '.$seller->lastname.' ('.$seller->email.') <span class="label label-success">'.$seller->balance.'$</span>' : '';
	}

	public function callback_get_seller_name($data)
	{
		$CI = &get_instance();
		$seller = get_seller($data);

		return "<a href='".site_url_multi('administrator/user/show/').$data."'>".$seller."</a>";
	}

	

	public function callback_get_bank_account($data)
	{
		$CI = &get_instance();
		$CI->load->model('modules/Bank_account_model');

		if($data == 0)
		{
			return translate('cash');
		} else {
			$bank_account = $CI->Bank_account_model->filter(['id' => $data])->one();
			if($bank_account)
			{
				return '<a href="'.site_url_multi('administrator/bank_account/show/').$data.'">'.$bank_account->bank_name.' '.$bank_account->account_number.'</a>';
			}
		}

		return ($seller) ? $seller->firstname.' '.$seller->lastname.' ('.$seller->email.')' : '';
	}

	public function callback_currency_format($data, $params)
	{
		$CI = &get_instance();
		$CI->load->library('Currency');
		$amount = $CI->currency->formatter($data, $params['currency'], $params['currency']);

		return $amount;
	}

	public function callback_product_currency_format($data, $params, $id = false)
	{
		$CI = &get_instance();
		$CI->load->model('Product_model');
		$product = $CI->Product_model->filter(['id' => $id])->one();
		if($product)
		{
			$currency = $CI->currency->getCode($product->currency);
		}
		else
		{
			$currency = '';
		}
		$amount = ceil($data).' '.$currency;

		return $amount;
	}

	public function callback_currency_formatter($data)
	{
		$CI = &get_instance();
		$CI->load->library('Currency');
		$code = $CI->currency->getCode($data);

		return $code;
	}


	public function callback_get_tr_status($data, $params = false, $id = false)
	{
		$rows = [
			'0' => "<span class='label label-warning' >"  . translate('pending') . "</span>",
			'1' => "<span class='label label-success' >"  . translate('complete') . "</span>",
			'2' => "<span class='label label-danger'>" . translate('canceled') . "</span>",
			'3' => "<span class='label label-danger'>Refunded</span>",
		];

		return $rows[$data];
	}


	public function callback_get_accept($id){
        if($id == 1){
            return "<span class='label label-success'>" . "Qebul edilib" . "</span>";
        }elseif ($id == 2){
            return "<span class='label label-danger'>" ."Legv edilib". "</span>";
        }else{
            return "<span class='label label-info'>" . "Gozleyir" . "</span>";
        }
    }
}