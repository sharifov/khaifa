<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->model('Review_model');
		$this->load->model('Product_special_model');
		$this->load->model('Discounts_model');
    }

	public function cron_algolia(){

		/* file_put_contents(__DIR__.'/testing', '-------------------', FILE_APPEND);
		file_put_contents(__DIR__.'/testing', print_R($_SERVER, true), FILE_APPEND);

		die; */

		$this->load->library('algolia');

		$this->algolia->clear('products');

        $products = $this->Product_model->get_products(['status' => 1, 'quantity >' => 0]);

        $all_products = [];

        foreach ($products as $product) {

            $product['objectID'] = $product['id'];

            $all_products[$product['id']] = $product;

        }

        foreach ($this->Product_model->get_additional_data('product_to_category', '*') as $category) {

            if(isset($all_products[$category->product_id])) {
                $all_products[$category->product_id]['categories'][] = (int) $category->category_id;
            }

        }

        $this->algolia->save('products', $all_products);
	}

	public function testshipping()
    {

		die;
        $this->session->set_userdata('order_id', 995);

        die;

        $parcels = $this->cart_lib->getShippingTotalWithMethod();

        var_dump( $products   = $this->cart_lib->getProducts());

		die;

        foreach ($parcels as &$parcel) {

            $parcel['products'] = $this->Product_model->get_products(['id IN ('. implode(',', $parcel['products']) .')' => null]);

            $parcel['seller'] = get_seller($parcel['products'][0]['created_by']);

        }

        var_dump($parcels);

        die;

        $this->load->library('algolia');

        /*var_dump($this->algolia->delete('products', 54));

        die;*/

		print_R($this->algolia->clear('products'));

        $products = $this->Product_model->get_products(['status' => 1, 'quantity >' => 0]);

        $all_products = [];

        foreach ($products as $product) {

            $product['objectID'] = $product['id'];

            $all_products[$product['id']] = $product;

        }

        foreach ($this->Product_model->get_additional_data('product_to_category', '*') as $category) {

            if(isset($all_products[$category->product_id])) {
                $all_products[$category->product_id]['categories'][] = (int) $category->category_id;
            }

        }

//        var_dump($all_products);

        $this->algolia->save('products', $all_products);
    }

	public function index()
	{
		
        // $this->load->library('payment/Credit');
		// $a = $this->credit->process($_POST['cko-card-token']);
		// var_dump($a);
		// die();
		
        if(isset($_GET['cko-session-id'])) {

            $curl = curl_init('https://api.checkout.com/payments/' . ($sid = $_GET['cko-session-id']));

            curl_setopt_array($curl, [
                CURLOPT_HTTPHEADER => [
                    'Authorization: sk_d5f00eb9-3cca-431d-aea9-0d5927d0ea3b',
                    'Content-Type: application/json'
                ],
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_FOLLOWLOCATION  => true,
            ]);

            $respond = json_decode($a = curl_exec($curl), true);

            if(isset($respond['approved']) && $respond['approved'] == true) {

                $pending = $this->db->from('checkoutcom_pending')
                    ->where('sid', $sid)
                    ->get()
                    ->row();

                if($pending) {

                    $data = array(
                        'payment_id'        =>  $pending->sid,
                        'order_id'          =>  $pending->order_id,
                        'amount'            =>  $pending->amount,
                        'c_payment_id'      =>  $pending->c_payment_id,
                    );

                    $this->db->insert('checkoutcom', $data);

                    $this->db->where('id', $pending->order_id);

                    $this->db->update('order', ['order_status_id'  =>  1]);

                    $this->db->where('sid', $sid);
                    $this->db->delete('checkoutcom_pending');

                    $order_products = $this->db->from('order_product')
                        ->where('order_id', $pending->order_id)
                        ->get()
                        ->result_array();

                    foreach ($order_products as $order) {

                        $this->db->query('UPDATE wc_product SET quantity=quantity-'. $order['quantity'] .' WHERE id=' . $order['product_id']);

                    }

                    $this->session->set_userdata('order_id', $pending->order_id);


                    send_custom_mail([
                        'to' => $this->data['customer']->email,
                        'subject'   =>  translate('payment_successfully_subject', true),
                        'message'   =>  translate('payment_successfully_text', true),
                    ]);

                    redirect(site_url_multi('checkout/success'));
                }
                else {
                    redirect(site_url_multi('checkout/error'));
                }
            }
            else {
                redirect(site_url_multi('checkout/error'));
            }
        }

		$this->data['title'] = get_setting('site_title', $this->data['current_lang']);

        $lang = $this->data['current_lang'];
        $this->load->model('Setting_model');

        $settings = $this->Setting_model->get_settings();

        foreach($settings as $setting){
            foreach($setting as $key=>$value) {
               if($value == 'meta_description'){
                   $json = json_decode($setting['value'],true);
                   $this->data['meta_description'] = $json[$lang];
               }

               if($value == 'meta_keywords'){
                   $json = json_decode($setting['value'],true);
                   $this->data['meta_keywords'] = $json[$lang];
               }
            }
        }

		//Top products
		$this->data['top_products'] = [];
		$product_ids = $this->Product_model->get_top_products(10);
		$tp_ids = [];
		if($product_ids) {
			foreach($product_ids as $product) {
				$tp_ids[] = $product->product_id;
			}

            $this->data['dataLayer']['ecommerce']['currencyCode'] = 'USD';

			$this->data['top_products'] = array_map(function(&$val){

			$val['alt_image'] = $val['alt_image']?$val['alt_image']:$val['name'];
			return $val;

		}, $this->Product_model->get_products(['id IN ('.implode(",",$tp_ids).')' => null, 'status' => 1], 10));

            $this->load->library('google_seo');

            $this->google_seo->dataLayerImpressions($this->data['top_products'], 'Top products');

		}

		//New products

			$this->data['new_products'] = array_map(function(&$val){

				$val['alt_image'] = $val['alt_image']?$val['alt_image']:$val['name'];
				return $val;

			}, $this->Product_model->get_products(['status' => 1], 10));



        $this->google_seo->dataLayerImpressions($this->data['new_products'], 'New products');

        $this->data['old_products'] = $this->db->from('old_products')->limit(20)->order_by('product_id', 'DESC')->get()->result_array();

        foreach ($this->data['old_products'] as &$old_product) {

            $raw_image = explode('.', $old_product['image']);

            $old_product['image'] = $raw_image[0] . '-190x190.' . $raw_image[1];

            if( ! file_exists('uploads/old/catalog/' . $old_product['image'])) {
                unset($old_product);
            }

        }


        $this->data['discounts'] = $this->db->select('id, name, products, discount, start_date, end_date')
            ->from('discounts')
            ->where([
                'discount_type'  =>  2,
                'start_date <=' => date('Y-m-d'),
                'end_date >=' => date('Y-m-d'),
                'deleted_at IS NULL' => null
            ])
            ->order_by('id', 'DESC')
            ->limit(2)
            ->get()
            ->result_array();


        $this->data['discounts'] = [];

            $discounts = $this->Discounts_model
                ->fields('*')
                ->filter([
                    'discount_type'  =>  2,
                    'start_date <=' => date('Y-m-d'),
                    'end_date >=' => date('Y-m-d'),
                    'deleted_at IS NULL' => null
                ])
                ->with_translation($this->data['current_lang_id'])
                ->all();


            $x = 4;
            if($discounts) {
                foreach ($discounts as $discount) {

                    $dStart = new DateTime(date('Y-m-d H:i:s'));

                    $dEnd  = new DateTime($discount->end_date);

                    $this->data['discounts'][] = [
                        'title'     =>  $discount->title,
                        'discount'  =>  $discount->discount,
                        'end_date'  =>  $discount->end_date . 'T23:59:59',
                        'products'  =>  $discount_products = array_map(function(&$val){
														$val['alt_image'] = $val['alt_image']?$val['alt_image']:$val['name'];
														return $val;
													}, $this->Product_model->get_products(['id IN ('. $discount->products.')' => null, 'status' => 1], 15)),
                        'x'         =>  $x
                    ];

                    $x++;

                    $this->google_seo->dataLayerImpressions($discount_products, 'Sale products');
                }
            }
			
			//print_R($this->data['discounts']);
			

		//Recently viewed
		$this->data['recently_viewed'] = [];
		$recently_viewed = $this->session->userdata('recently_viewed');

		if($recently_viewed) {
            ksort($recently_viewed, SORT_DESC);

        }

		    if($recently_viewed) {

                $r_v_names = '';

                $this->data['recently_viewed_tmp'] = $this->Product_model->get_products(['id IN('.implode(",",$recently_viewed).')' => null, 'status' => 1], 10, ['FIELD (id, '.implode(",",$recently_viewed).')', null]);


                foreach ($this->data['recently_viewed_tmp'] as $r_v) {

                    $r_v_names .= $r_v['name'] . ' ';

                }

                $parsed_product_name = explode(' ', $r_v_names);

                $similar_products = $this->Product_model->get_products_extended([], false, $parsed_product_name[0] . ' ' . ($parsed_product_name[1] ?? '') . ' ' . ($parsed_product_name[2] ?? ''), 40);

                $similar_products_array = [];

                if($similar_products) {
                    foreach($similar_products as $s_product) {
                        $similar_products_array[] = $s_product['id'];
                    }

                    shuffle($similar_products_array);

                    $recently_viewed = array_merge($recently_viewed, $similar_products_array);
                }
            }

        /*}*/

        if($recently_viewed) {
            $this->data['recently_viewed'] = $this->Product_model->get_products(['id IN('.implode(",",$recently_viewed).')' => null, 'status' => 1], 15, ['FIELD (id, '.implode(",",$recently_viewed).')', null]);
						$this->data['recently_viewed'] = array_map(function(&$val){
								$val['alt_image'] = $val['alt_image']?$val['alt_image']:$val['name'];
								return $val;
						}, $this->data['recently_viewed']);
        }


        //Featured Products (Sale)
		$this->data['featured_products_1'] = [];
		$featured_products_1 = $this->Product_model->get_additional_data('featured_product','id, percent, products, start_date, expired_date',['type' => 'featured_1', 'status' => 1],true);

		if($featured_products_1 && !empty($featured_products_1->products)) {
			if($featured_products_1->start_date == null || ($featured_products_1->start_date < date('Y-m-d H:i:s') && $featured_products_1->expired_date > date('Y-m-d H:i:s'))) {
				$this->data['featured_products_1']['expired_date'] = ($featured_products_1->expired_date != '0000-00-00' || $featured_products_1->expired_date != null) ? $featured_products_1->expired_date : false;
                $this->data['featured_products_1']['expired_date'] = date('Y-m-d', $this->data['featured_products_1']['expired_date']) . 'T' . date('H:i:s', $this->data['featured_products_1']['expired_date']);
				$this->data['featured_products_1']['percent'] = $featured_products_1->percent;
				$product_ids = $featured_products_1->products;
				$this->data['featured_products_1']['products'] = $this->Product_model->get_products(['id IN('.$product_ids.')' => null, 'status' => 1], 10);
			}
		}

		$this->data['featured_products_1'] = array_map(function(&$val){
				$val['alt_image'] = $val['alt_image']?$val['alt_image']:$val['name'];
				return $val;
		}, $this->data['featured_products_1']);

		$this->data['featured_products_2'] = [];
		$featured_products_2 = $this->Product_model->get_additional_data('featured_product','id, percent, products, start_date, expired_date',['type' => 'featured_2', 'status' => 1],true);
		if($featured_products_2 && !empty($featured_products_2->products)) {
			if($featured_products_2->start_date == null || ($featured_products_2->start_date < date('Y-m-d H:i:s') && $featured_products_2->expired_date > date('Y-m-d H:i:s'))) {
				$this->data['featured_products_2']['expired_date'] = ($featured_products_2->expired_date != '0000-00-00' || $featured_products_2->expired_date != null) ? $featured_products_2->expired_date : false;
                $this->data['featured_products_2']['expired_date'] = date('Y-m-d', $this->data['featured_products_2']['expired_date']) . 'T' . date('H:i:s', $this->data['featured_products_2']['expired_date']);
				$this->data['featured_products_2']['percent'] = $featured_products_2->percent;
				$product_ids = $featured_products_2->products;
				$this->data['featured_products_2']['products'] = $this->Product_model->get_products(['id IN('.$product_ids.')' => null, 'status' => 1], 10);
				$products = $this->Product_model->fields('id, name, slug, price, image, currency')->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(10)->all();
			}
		}

		$this->data['featured_products_2'] = array_map(function(&$val){
				$val['alt_image'] = $val['alt_image']?$val['alt_image']:$val['name'];
				return $val;
		}, $this->data['featured_products_2']);


		//Banner
		$this->data['banners'] = [
			'top'			=> get_banners('top'),
			'middle'		=> get_banner('middle'),
			'center'		=> [
				'top_left'		=> get_banner('center_top_left'),
				'top_right'		=> get_banner('center_top_right'),
				'bottom_left'	=> get_banner('center_bottom_left'),
				'bottom_right'	=> get_banner('center_bottom_right'),
			],
			'footer'		=> [
				'top_left'		=> get_banner('footer_top_left'),
				'top_right'		=> get_banner('footer_top_right'),
				'bottom_left'	=> get_banner('footer_bottom_left'),
				'bottom_right'	=> get_banner('footer_bottom_right'),
			]
		];

		$this->template->render('home');
	}

	public function set_currency()
	{
		if($this->input->get('code'))
		{
			if(isset($this->currency->currencies[$this->input->get('code')]))
			{
				$this->session->set_userdata('currency', $this->input->get('code'));
			}
			else
			{
				$this->session->set_userdata('currency', 'USD');
			}
		}
		else
		{
			$this->session->set_userdata('currency', 'USD');
		}
		redirect($_SERVER['HTTP_REFERER']);

	}

	public function change_country()
    {
        $country = $this->Country_model->filter(['id' => (int) $this->input->post('country')])->with_translation()->one();

        if(isset($country->name) && $country->name) {
            $this->session->set_userdata('country', $country->id);
        }

        echo json_encode(['success' => $this->session->userdata('country')]);
    }

}
