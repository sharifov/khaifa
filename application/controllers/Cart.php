<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->model('Option_model');

	}

	public function get_categories(){
        $categories = $this->Category_model->fields('id, name, slug')->filter(['status' => 1, 'parent' => 0])->with_translation()->order_by(['sort' => 'ASC'])->as_array()->all();
        $i=0;
        foreach($categories as $p_cat){
            $categories[$i]['sub'] = $this->sub_categories($p_cat['id']);
            $i++;
        }
        return $categories;
    }

    public function sub_categories($id){
		$categories = $this->Category_model->fields('id, name, slug')->filter(['status' => 1, 'parent' => $id])->with_translation()->order_by(['sort' => 'ASC'])->as_array()->all();
		$i=0;
		if($categories)
		{
			foreach($categories as $p_cat){
				$categories[$i]['sub'] = $this->sub_categories($p_cat['id']);
				$i++;
			}
			return $categories;
		} else {
			return [];
		}


    }

	public function find_category($arr = null, $parent_id = null) {
        /*echo $this->cart_lib->getTotalPrice();
        echo $this->cart_lib->getPaymentTotal();*/
	}

	public function info()
	{
		$response = ['html' => false,'quantity' => 0];
		$products = $this->cart_lib->getProducts();

		
		$this->data['total'] = $this->currency->formatter($this->cart_lib->getTotalPrice()+$this->cart_lib->getPaymentTotal(), $this->data['current_currency'], $this->data['current_currency']);

		$this->data['coupon'] = '';
		if($this->session->has_userdata('coupon'))
		{	
			$this->data['coupon'] = $this->session->userdata('coupon');
			$this->load->model('modules/Coupon_model');
			$this->data['coupon_total'] = $this->currency->formatter($this->Coupon_model->getTotal($this->cart_lib->getTotalPrice()), $this->data['current_currency'], $this->data['current_currency']);
			$this->data['total'] = $this->currency->formatter($this->cart_lib->getTotalPrice()+$this->cart_lib->getPaymentTotal()-$this->Coupon_model->getTotal($this->cart_lib->getTotalPrice()), $this->data['current_currency'], $this->data['current_currency']);
		}

		if($products) {
			$total_price = 0;
			$html  = '';
			$html .= '	<div class="container-fluid shopping-cart-cover">';
			$html .= '		<div class="dropdown-head shopping-head">';
			$html .= '			<div class="h4">'. translate('my', true) .' <span>'. translate('bag', true) .': '.count($products).' '. translate('items', true) .'</span></div>';
			$html .= '		</div>';
			$html .= '		<div class="col-md-12 carts-cover">';
			foreach ($products as $product) {
				$html .= '		<div class="row shopping-item">';
				$html .= '			<div class="col-md-6 shopping-cart-img">';
				$html .= '				<div class="shopping-img">';
				$html .= '					<a href="'.site_url_multi("product/".$product['slug']).'">';
				$html .= '						<img src="'.base_url('uploads/'.$product['image']).'" alt="">';
				$html .= '					</a>';
				$html .= '				</div>';
				$html .= '			</div>';
				$html .= '			<div class="col-md-6 shopping-cart-details">';
				$html .= '				<div class="product-caption-price">';
				$html .= '					<span class="product-caption-price-new">';
				$html .= 						currency_symbol_converter($this->currency->formatter($product['price'], $product['currency'], $this->data['current_currency']));
				$html .= '					</span>';
				$html .= '				</div>';
				$html .= '				<div class="cart-txt-detail">';
				$html .= 				$product['name'];
				$html .= '				</div>';
				$html .= '				<div class="qty text-left">';
				$html .= '					<ul>';
				$html .= '						<li> <strong>'. translate('qty', true) .'</strong> '.$product['quantity'].' </li>';
				if($product['option']) {
					foreach($product['option'] as $option) {
						$html.= '				<li> <strong>'.$option['name'].':</strong> '.$option['value'].' </li>';
					}
				}
				$html .= '					</ul>';
				$html .= '				</div>';
				$html .= '				<div class="qty text-left">';
				$html .= '					<ul>';
				if($product['shipping']) {
					foreach($product['shipping'] as $shipping) {
						$html.= '				<li> <strong>'.$shipping['name'].':</strong> '.currency_symbol_converter($this->currency->formatter($shipping['price'], $shipping['currency'], $this->data['current_currency'])).' </li>';
					}
				}
				$html .= '					</ul>';
				$html .= '				</div>';
				$html .= '				<button class="cart-item-btn" onclick="removeCart('.$product["cart_id"].', false)">';
				$html .= '					<img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">';
				$html .= '				</button>';
				$html .= '			</div>';
				$html .= '		</div>';
			}
			$html .= '				<div class="row cart-total">';
            $html .= '	                <div class="cart-total-content">';
            $html .= '	                	<p class="total-txt">'.translate('sub_total', true).'<p/>';
            $html .= '	                    <p class="product-caption-price">';
            $html .= '	                     	<span class="product-caption-price-new">';
			$html .= '	                        	'.currency_symbol_converter($this->currency->formatter($this->cart_lib->getSubTotalPrice(), $this->data['current_currency'], $this->data['current_currency']));//$this->currency->frm($this->cart_lib->getSubTotalPrice());
			$html .= '	                         </span>';
            $html .= '	                     </p>';
            $html .= '	               	</div>';
			$html .= '	            </div>';
			$html .= '				<div class="row cart-total">';
            $html .= '	                <div class="cart-total-content">';
            $html .= '	                	<p class="total-txt">'.translate('shipping_total', true).'<p/>';
            $html .= '	                    <p class="product-caption-price">';
            $html .= '	                     	<span class="product-caption-price-new">';
			$html .= '	                        	'.currency_symbol_converter($this->currency->formatter($this->cart_lib->getShippingTotal(), $this->data['current_currency'], $this->data['current_currency']));//$this->currency->frm($this->cart_lib->getSubTotalPrice());
			$html .= '	                         </span>';
            $html .= '	                     </p>';
            $html .= '	               	</div>';
			$html .= '	            </div>';
			if($this->session->has_userdata('payment_method')) {
				if($this->cart_lib->getPaymentTotal() > 0)
				{
					$html .= '				<div class="row cart-total">';
					$html .= '	                <div class="cart-total-content">';
					$html .= '	                	<p class="total-txt">'.translate('payment', true).'<p/>';
					$html .= '	                    <p class="product-caption-price">';
					$html .= '	                     	<span class="product-caption-price-new">';
					$html .= '	                        	'.currency_symbol_converter($this->currency->formatter($this->cart_lib->getPaymentTotal(), $this->data['current_currency'], $this->data['current_currency']));//$this->currency->frm($this->cart_lib->getSubTotalPrice());
					$html .= '	                         </span>';
					$html .= '	                     </p>';
					$html .= '	               	</div>';
					$html .= '	            </div>';
				}
			}
			if($this->session->has_userdata('coupon'))
			{
				$html .= '				<div class="row cart-total">';
				$html .= '	                <div class="cart-total-content">';
				$html .= '	                	<p class="total-txt">'.translate('coupon', true).'<p/>';
				$html .= '	                    <p class="product-caption-price">';
				$html .= '	                     	<span class="product-caption-price-new">';
				$html .= '	                        	'.currency_symbol_converter($this->data['coupon_total']);//$this->currency->frm($this->cart_lib->getSubTotalPrice());
				$html .= '	                         </span>';
				$html .= '	                     </p>';
				$html .= '	               	</div>';
				$html .= '	            </div>';
			}
			$html .= '				<div class="row cart-total">';
            $html .= '	                <div class="cart-total-content">';
            $html .= '	                	<p class="total-txt">'.translate('total', true).'<p/>';
            $html .= '	                    <p class="product-caption-price">';
			$html .= '	                     	<span class="product-caption-price-new">';
			$html .= '	                        	'.currency_symbol_converter($this->data['total']);
			$html .= '	                         </span>';
            $html .= '	                     </p>';
            $html .= '	               	</div>';
            $html .= '	            </div>';
			$html .= '	            <div class="col-md-12 total-btns flex">';
			$html .= '	                <a href="'.site_url_multi("checkout/cart").'" class="btn reviews-btn">'. translate('view_bag', true) .'</a>';
			$html .= '	                <a href="'.site_url_multi("checkout").'" class="btn reviews-btn green_btn">'. translate('checkout', true) .'</a>';
			$html .= '	            </div>';
			$html .= '			</div>';
			$html .= '		</div>';

			$response['html'] = $html;
			$response['quantity'] = count($products);
		}

		if(true) {

            $parcels = $this->cart_lib->getShippingTotalWithMethod();

            $this->data['cart'] = $this->cart_lib->getProducts();

            $shipments = [];

            foreach ($parcels as $parcel) {

                $product_id = current($parcel['products']);

                $this->load->model('Product_model');

                $product = $this->Product_model->get_products(['id' => $product_id]);

                $seller = get_seller($product[0]['created_by'] ?? 1);

                $shipments[] = [
                    'seller_name'       =>  $seller,
                    'shipment_name'     =>  $parcel['name'],
                    'price'             =>  $parcel['show_price']
                ];
            }

            $this->data['shipments'] = $shipments;

            $vendors = [];

            foreach ($this->data['cart'] as $cart) {

                $vendors[get_seller($cart['created_by'])][$cart['product_id']] = $cart;

            }

            $this->data['vendors'] = $vendors;

            $response['html'] = '<div class="container-fluid shopping-cart-cover bag-shop-top44">
        <div class="dropdown-head shopping-head shead "><div class="h4">My <span>bag: '. count($this->data['cart']) .' Items</span></div></div>
        <div class="col-sm-12 col-xs-12 carts-cover ncarts_cover">

           	<div class="col-xs-12 col-sm-12 carts_types">';

            foreach ($vendors as $vendor => $products) {
                $response['html'] .= '
                <div class="row carts_items crt-type1">
                    <div class="div-xs-12 col-sm-12 plr10">
                        <h4 class="carts_type_name text-left">'.translate('seller', true).' : '. $vendor .'</h4>
                    </div>';

                foreach ($products as $product) {
                    $response['html'] .= '
		           	<div class="col-xs-12 col-sm-12 shopping-item">
		                <div class="col-md-6 shopping-cart-img">
		                    <div class="shopping-img">
		                        <a href="'.site_url_multi($product['slug']).'">
		                            <img src="'.base_url('uploads/') . '/' . $product['image'] .'" alt="'.$product['name'].'">
		                        </a>
		                    </div>
		                </div>
		                <div class="col-md-6 shopping-cart-details">
		                    <div class="product-caption-price">
		                        <span class="product-caption-price-new">'.$product['price'].'---'.currency_formatter($product['price'], $product['currency'], $this->data['current_currency']).'</span>
                            <button class="cart-item-btn" onclick="removeCart('.$product['cart_id'].', false)">
                              <img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">
                            </button>
		                    </div>
		                    <div class="cart-txt-detail">'.$product['name'].'
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Qty:</strong> '. $product['quantity'] .'</li>
		                        </ul>
		                    </div>
		                </div>
		            </div>';
                }

                $response['html'] .= '
	           </div>';
            }



            $response['html'] .= '
           	</div>

            <!-- starts cart content -->
           <div class="col-xs-12 col-sm-12 cart_cnt_top">
                <div class="bb crt_total">
                    <div class="cart-total">
                        <div class="cart-total-content crt_item crt_sum">
                            <p class="total-txt">'. translate('total', true) .' :</p>
                            <p class="product-caption-price">
                                <span class="product-caption-price-new"> ' . currency_symbol_converter($this->data['total']) . '
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="total-btns flex">
                    <a href="'. site_url_multi('checkout/cart') .'" id="gcid_view_bag" class="btn reviews-btn nreviews-btn">'. translate('view_bag', true) .'</a>
                    <a href="'. site_url_multi('checkout') .'" id="gcid_checkout" class="btn reviews-btn nreviews-btn green_btn">'. translate('checkout', true) .'</a>
                </div>
           </div>
           <!-- ends cart content -->
        </div>
    </div>';

        }

		$this->template->json($response);
    }

	public function add()
	{
		$response = [];
		$this->form_validation->set_rules('product_id',translate('label_product',true),'trim|required|is_natural_no_zero');
		if($this->input->method() == 'post'){
			if($this->form_validation->run()) {
				$product_id  = $this->input->post('product_id');
				$options = $this->input->post('option');

				$shipping = $this->input->post('shipping');

                $this->data['shipping_list']  = [];

                $product = $this->Product_model->filter(['id' => $product_id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->with_translation()->one();

                /* if($product->country_id == 221)
                { */
                    if($this->data['current_country_id'] == 221)
                    {
                        $this->load->library('shipping/Free');
                        $this->data['shipping_list'] = $this->free->calculate();
                    }
                    else
                    {
                        $this->load->library('shipping/Ems');
                        if($product->length_class_id != 1)
                        {
                            $this->load->library('Length');
                            $Width = $this->length->convert($product->width, $product->length_class_id, 1);
                            $height = $this->length->convert($product->height, $product->length_class_id, 1);
                            $length =  $this->length->convert($product->length, $product->length_class_id, 1);
                        }
                        else
                        {
                            $Width = $product->width;
                            $height = $product->height;
                            $length = $product->length;
                        }

                        $this->load->library('Weight');
                        $weight = $weight = $this->weight->weight_to_gram_converter($product->weight, $product->weight_class_id);

                        /*if($product->weight_class_id != 2)
                        {
                            $this->load->library('Weight');
                            $weight = $this->weight->convert($product->weight, $product->weight_class_id, 2);
                        }
                        else
                        {
                            $weight = $product->weight;
                        }*/

                        $this->data['shipping_list'] = $this->ems->calculate($Width, $height, $length, $weight);
                    }
               // }

				foreach ($this->data['shipping_list'] as &$shipping_list) {

                    $shipping_list['currency'] = 'USD';

                }

				if($product->country_id == $this->data['current_country_id']){
					$this->load->model('modules/Local_delivery_model');
					$local_shipping = $this->db->query('SELECT LD.* FROM wc_local_delivery LD WHERE FIND_IN_SET('.$this->data['current_country_id'].', LD.countries) AND LD.status=1 ORDER BY LD.id DESC');

					if($local_shipping->num_rows()) {

						$this->data['shipping_list'] = [];

						$_shipping = json_decode($shipping, true);

						foreach ($local_shipping->result() as $_row){
							if($_row->id == $_shipping['id']){
								$currency = $this->currency->getCode($_row->currency_id);
								$this->data['shipping_list'][] = [
									'id'      =>  $_row->id,
									'name'      =>  $_row->name,
									'code'      =>  'local_shipping',
									'price'     =>  $_row->price,
									'show_price'=>  $this->currency->convert($_row->price, $this->data['current_currency'], $currency) . ' ' . $this->data['current_currency'],
									'currency'  =>  $currency,
								];
							}
						}
					}
				}

				$quantity = (int) $this->input->post('quantity');
				if($quantity > 0) {
					$product = $this->Product_model->filter(['id' => $product_id, 'status' => 1])->with_translation()->one();
					if($product->quantity >= $quantity) {
						if($product) {
							$product_options = $this->Product_model->get_product_options($product_id);
							if($product_options) {
								foreach($product_options as $product_option) {
									if($product_option['required'] && empty($options[$product_option['product_option_id']])) {
										$response['error']['option'][$product_option['product_option_id']] = sprintf(translate('cart_error_required',true), $product_option['name']);
									}
								}
							}

							if(!$shipping)
							{
								$response['error']['shipping'] = sprintf(translate('cart_error_required',true), 'shipping');
							}

							if(!$response) {

							    if(in_array(json_decode($shipping, true), $this->data['shipping_list'])) {
                                    $this->cart_lib->add($product_id, $quantity, $options, 0, $shipping);
                                    $response['success'] = translate('cart_success_add',true);
                                }
							    else{
                                    $response['error']['shipping'] = sprintf(translate('cart_error_required',true), 'shipping');
                                }

								//Total

							} else {
								$response['redirect'] = site_url_multi('product/'.$product->slug);
							}
						}
					} else {
						$response['error']['quantity'] = 'Quantity is not valid';
					}
				} else {
					$response['error']['quantity'] = 'Quantity is not valid';
				}
			} else {
				$response['error'] = 'Product_id required!';
			}
		}

		$this->template->json($response);
	}

	public function edit()
	{
		$response = [];
		$this->form_validation->set_rules('cart_id','cart','trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('quantity','quantity','trim|required');
		if($this->input->method() == 'post'){
			if($this->form_validation->run()) {
				$this->cart_lib->update($this->input->post('cart_id'),$this->input->post('quantity'));
				$response['success'] = translate('cart_success_update',true);
			}
		}
		$this->template->json($response);
	}

	public function remove()
	{
	    $this->load->model('modules/Brand_model');

		$response = [];
		$this->form_validation->set_rules('cart_id','cart_id','trim|required|is_natural_no_zero');
		if($this->input->method() == 'post'){
			if($this->form_validation->run()) {

			    $cart = $this->db->from('cart')
                    ->where('cart_id', $this->input->post('cart_id'))
                    ->get()
                    ->row();

                $product_seo = $this->Product_model->get_products(['status' => 1, 'id' => $cart->product_id])[0];

                $price      = $this->currency->convertCurrencyString($product_seo['price']);

                $price_usd  = $this->currency->convert($price['value'], 'USD', strtoupper($price['code']));



                if(isset($product_seo['manufacturer_id']) && $product_seo['manufacturer_id']) {
                    $brand      = $this->Brand_model->filter(['id' => $product_seo['manufacturer_id'], 'status' => 1])->one();
                }

                $category_name = $this->db->query('SELECT CT.name FROM wc_product_to_category PC
                LEFT JOIN wc_category_translation CT ON PC.category_id=CT.category_id
                LEFT JOIN wc_category C ON CT.category_id=C.id
                WHERE PC.product_id='. $cart->product_id .' AND CT.language_id='.$this->data['current_lang_id'].' AND C.parent=0')
                    ->row();

                $this->data['dataLayer']['ecommerce']['detail']['products'][] = [
                    'id'        =>  $cart->product_id,
                    'name'      =>  $product_seo['name'],
                    'price'     =>  round($price_usd),
                    'brand'     =>  $brand->name ?? '',
                    'category'  =>  $category_name->name ?? '',
                ];

                $response['js'] = [
                    'id'        =>  $cart->product_id,
                    'name'      =>  $product_seo['name'],
                    'price'     =>  round($price_usd),
                    'brand'     =>  $brand->name ?? '',
                    'category'  =>  $category_name->name ?? '',
                ];

				$this->cart_lib->remove($this->input->post('cart_id'));
				$response['success'] = translate('cart_success_update',true);
			}
		}
		$this->template->json($response);
	}



}
