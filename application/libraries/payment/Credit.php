<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/third_party/checkout.com/vendor/autoload.php';

use Checkout\CheckoutApi;
use Checkout\Models\Tokens\Card;
use Checkout\Models\Payments\TokenSource;
use Checkout\Models\Payments\Payment;
use Checkout\Models\Payments\ThreeDs;
use Checkout\Models\Payments\Refund;

class Credit {

	public $CI;

	public $order_status_id = 1;
    public $config_vars;

	public function __construct()
	{
		$this->CI = &get_instance();

		$this->CI->load->model('Currency_model');

        $this->CI->config->load('auth');

        $this->config_vars = $this->CI->config->item('auth');
	}

	public function currency_to_usd($currency_id, $amount)
    {
        if($currency_id == 1)
            return $amount;

        $currencies = $this->CI->Currency_model->filter(['status' => 1])->all();

        $currencyArray = [];

        foreach ($currencies as $currency) {
            $currencyArray[$currency->id] = $currency;
        }

        $converted_amount = ($amount * $currencyArray[$currency_id]->value) / $currencyArray[1]->value;

        return $converted_amount;
    }

	public function currency_to_aed($currency_id, $amount)
    {
        if($currency_id == 3)
            return $amount;

        $currencies = $this->CI->Currency_model->filter(['status' => 1])->all();

        $currencyArray = [];

        foreach ($currencies as $currency) {
            $currencyArray[$currency->id] = $currency;
        }

        $converted_amount = ($amount * $currencyArray[$currency_id]->value) / $currencyArray[3]->value;

        return $converted_amount;
    }

	public function process($order_id)
	{	
	
        /*if (isset($this->config_vars['email_config']) && is_array($this->config_vars['email_config'])) {
            $this->CI->email->initialize($this->config_vars['email_config']);
        }
//        $this->CI->email->initialize(['smtp_crypto' => 'tls']);
        $this->CI->email->from($this->config_vars['email'], $this->config_vars['name']);
        $this->CI->email->to('ibehbudov21@mail.ru');
        $this->CI->email->subject("You paid successfully");
        $this->CI->email->message("You paid successfully");
        $this->CI->email->set_newline("\r\n");
        $send = $this->CI->email->send();

        var_dump($send);die;*/
		

		$token = $this->CI->input->post('cko-card-token');
		$this->CI->load->model('Order_model');


		$order_info = $this->CI->Order_model->getOrder($order_id);

//		var_export($order_info);die;

		$secretKey = 'sk_d5f00eb9-3cca-431d-aea9-0d5927d0ea3b';
		

		// Initialize the Checkout API
		$checkout           = new CheckoutApi($secretKey);

		$method             = new TokenSource($token);

		$payment            = new Payment($method, 'AED');

        $payment->threeDs   = new ThreeDs(TRUE);

		if($order_info['customer_id'] == 54) {

            $order_info['currency_id'] = 2;
            $order_info['total'] = 0.1;

            /*var_dump($payment);
            die;*/

        }
		
		$payment->amount = (int) ($this->currency_to_aed($order_info['currency_id'], $order_info['total'])*100);

		/*$payment->{'3ds'} = (object) [
		    'enabled'       =>  true,
//            'attempt_n3d'   =>  true,
        ];*/

	 	$payment->customer = (object)( [
			//'id' => $order_info['customer_id'],
			'email'=> $this->CI->data['customer']->email,
			'name'=> $this->CI->data['customer']->firstname.' '.$this->CI->data['customer']->lastname,
		]);
		
        $details = $checkout->payments()->request($payment);

        file_put_contents(__DIR__ . '/' . time() . '.json', json_encode($details));

        if(isset($details->_links['redirect']['href'])) {

            $redirect_link = $details->_links['redirect']['href'];

            $data = array(
                'sid'           =>  end(explode('/', $redirect_link)),
                'order_id'      =>  $order_id,
                'amount'        =>  $payment->amount,
                'c_payment_id'  =>  $details->id,
            );

            $this->CI->db->insert('checkoutcom_pending', $data);

            header('Location: ' . $details->_links['redirect']['href']);
            die;
        }

		if($details->approved == true)
		{
			$this->CI->db->insert('checkoutcom', [
			    'payment_id' => $details->id,
			    'c_payment_id' => $details->id,
                'order_id' => $order_id,
                'amount'    =>  $payment->amount,
            ]);

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

            send_custom_mail([
                'to' => $this->CI->data['customer']->email,
                'subject'   =>  translate('payment_successfully_subject', true),
                'message'   =>  translate('payment_successfully_text', true),
            ]);

			$redirect = 'checkout/success';
		}
		else
		{
			$redirect = 'checkout/error';
		}


		return redirect($redirect);
	}

	public function refund($order_id, $amount)
	{
            $secretKey = 'sk_d5f00eb9-3cca-431d-aea9-0d5927d0ea3b';
		// Initialize the Checkout API
		$checkout = new CheckoutApi($secretKey);

		$this->CI->db->where('order_id', $order_id);
		$query = $this->CI->db->get('checkoutcom');
		if($query->num_rows())
		{
			$payment_Row = $query->row();
			$payment_id = $payment_Row->payment_id;

			$details = $checkout->payments()->refund(new Refund($payment_id, $amount*100));
			return $details;
		}
		
	}
}