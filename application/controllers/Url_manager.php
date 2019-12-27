<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Url_manager extends Site_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Product_model');
        $this->load->model('Review_model');
        $this->load->model('Customers_model');
        $this->load->model('Option_model');
        $this->load->model('Attribute_model');
        $this->load->model('modules/Brand_model');
        $this->load->model('Product_special_model');
        $this->load->model('modules/Stock_status_model');
        $this->load->model('modules/Country_model');
        $this->load->model('modules/Zone_model');

        $this->load->model('Product_model');
        $this->load->model('Review_model');
        $this->load->model('Option_model');
        $this->load->model('Attribute_model');
        $this->load->model('Product_special_model');
        $this->load->model('modules/Category_model');
        $this->load->model('modules/Brand_model');
        $this->load->model('modules/Page_model');
        $this->load->model('modules/Seo_links_model');
        $this->load->model('modules/Seo_url_redirect_model');

        $this->load->library('google_seo');
		
    }

    public function index($slug, $page = 1, $hidden = false, $options = [])
    {
        $product = $this->Product_model->filter(['slug' => urldecode($slug), '(status=1 OR status=9)' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->with_translation()->one();

        if($product) {

            $this->data['addresses'] = [];

            $this->data['countries'] = $this->Country_model->with_translation()->all();

            $product_seo = $this->Product_model->get_products(['status' => 1, 'id' => $product->id])[0];

            $price      = $this->currency->convertCurrencyString($product_seo['price']);


			$seo_links   = $this->Seo_links_model->filter(['status' => 1, 'name!=' => ''])->with_translation()->all();
            $categories  = $this->Category_model->filter(['status' => 1])->with_translation()->all();

            foreach ($seo_links as $link) {
                $this->data['seo_links'][] = [
                    'name'  =>  $link->name,
                    'slug'  =>  $link->slug,
                ];
            }

            foreach ($categories as $c) {
                $this->data['seo_links'][] = [
                    'name'  =>  $c->name,
                    'slug'  =>  $c->slug,
                ];
            }


            $price_usd  = $this->currency->convert($price['value'], 'USD', strtoupper($price['code']));

            if(isset($product_seo['manufacturer_id']) && $product_seo['manufacturer_id']) {
                $brand      = $this->Brand_model->filter(['id' => $product_seo['manufacturer_id'], 'status' => 1])->one();
            }

            $category_name = $this->db->query('SELECT CT.name FROM wc_product_to_category PC
                LEFT JOIN wc_category_translation CT ON PC.category_id=CT.category_id
                LEFT JOIN wc_category C ON CT.category_id=C.id
                WHERE PC.product_id='. $product->id .' AND CT.language_id='.$this->data['current_lang_id'].' AND C.parent=0')
                ->row();

            $this->data['dataLayer']['ecommerce']['detail']['products'][] = [
                'id'        =>  $product->id,
                'name'      =>  $product->name,
                'price'     =>  round($price_usd),
                'brand'     =>  $brand->name ?? '',
                'category'  =>  $category_name->name ?? '',
            ];

            $this->data["months"] = array("","January","February","March","April","May","June","July",
            "August","September","October","November","December");

            if(isset($this->data['customer']->id) && $this->data['customer']->id) {
                $this->load->model('modules/Address_model');

                $addresses = $this->Address_model->filter(['customer_id' => $this->data['customer']->id])->with_trashed()->all();

                $x = 0;

                if ($addresses) {
                    foreach ($addresses as $address) {
                        $this->data['addresses'][] = [
                            'id' => $address->id,
                            'firstname' => $address->firstname,
                            'lastname' => $address->lastname,
                            'company' => $address->company,
                            'address1' => $address->address_1,
                            'address2' => $address->address_2,
                            'city' => $address->city,
                            'postcode' => $address->postcode,
                            'phone' => $address->phone,
                            'country' => $this->Country_model->filter(['id' => $address->country_id])->with_translation()->one()->name,
                            'zone' => $this->Zone_model->filter(['id' => $address->zone_id])->one()->name ?? '',
                            'default'   =>  ($this->data['customer']->address_id == $address->id || (!$this->data['customer']->address_id && !$x))
                        ];

                        $x = 1;
                    }
                }
            }



            //Recently viewed
            $this->data['similar_products'] = [];

            $product_category = $this->Product_model->get_additional_data('product_to_category','*',['product_id' => $product->id]);

            $product_categories = [];

            if(is_iterable($product_category)) {
                foreach ($product_category as $value) {
                    $product_categories[] = $value->category_id;
                }
            }

            if($product_categories) {
                $product_category = $this->Product_model->get_additional_data('product_to_category','*',['category_id IN('.implode(",",$product_categories).')' => null]);
            }


            if(is_iterable($product_category) && isset($product_category)) {
                foreach ($product_category as $value) {
                    $similar_products[] = $value->product_id;
                }
            }

            $parsed_product_name = explode(' ', $product->name);

            $similar_products = $this->Product_model->get_products_extended([], false, $parsed_product_name[0] . ' ' . ($parsed_product_name[1] ?? '') . ' ' . ($parsed_product_name[2] ?? ''));

            if($similar_products) {
                foreach($similar_products as $s_product) {
                    $similar_products_ids[] = $s_product['id'];
                }
            }

            if(isset($similar_products_ids)) {
                $this->data['similar_products'] = $this->Product_model->get_products(['id IN('.implode(",",$similar_products_ids).')' => null, 'status' => 1], 10, ['FIELD(id, '.implode(",",$similar_products_ids).')', false]);

                $this->google_seo->dataLayerImpressions($this->data['similar_products'], 'Similar product');
            }


            //Language Link
            foreach($this->data['languages'] as $key => $value)
            {
                $temp_slug = $this->Product_model->filter(['id' => $product->id, 'status' => 1])->with_translation($value['id'])->one();
                $link = ($temp_slug) ? site_url(($this->data['current_lang'] == $key?'':$key).'/'.$temp_slug->slug) : "/";
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

            $this->data['title'] = $product->name;

            //Add to recently viewed
            $recently_viewed = $this->session->userdata('recently_viewed');
            if(!$recently_viewed) {
                $recently_viewed = [$product->id];
            } else {
                if(!in_array($product->id, $recently_viewed)) {
                    $recently_viewed[] = $product->id;
                }
            }
            $this->session->set_userdata('recently_viewed', $recently_viewed);
            //Get product category
            $category = $this->Product_model->get_additional_data('product_to_category','category_id',['product_id' => $product->id],true);
            if($category) {
                $category = $this->Category_model->filter(['category_id' => $category->category_id])->with_translation()->one();
                if($category) {
                    $product->category = $category->name;
                    $product->category_slug = $category->slug;
                }
            }

            //Get product manufacturer
            if($product->manufacturer_id) {
                $manufacturer = $this->Brand_model->fields('name')->filter(['id' => $product->manufacturer_id])->one();
                if($manufacturer) {
                    $product->manufacturer_name = $manufacturer->name;
                }
            }

            //Get product stock status
            if($product->stock_status_id) {
                $stock_status = $this->Stock_status_model->fields('name')->filter(['id' => $product->stock_status_id])->with_translation()->one();
                if($stock_status) {
                    $product->stock_status_name = $stock_status->name;
                }
            }

            //Get product Country name
            $product->country_name = "";
            if($product->country_id) {
                $country = $this->Country_model->fields('name')->filter(['id' => $product->country_id])->with_translation()->one();
                if($country) {
                    $product->country_name = $country->name;
                }
            }

            $product->region_name = "";
            if($product->region_id) {
                $region = $this->Zone_model->fields('name')->filter(['id' => $product->region_id])->one();
                if($region) {
                    $product->region_name = $region->name;
                }
            }

            //Get product rating
            $product->rating = round($this->Product_model->get_product_review($product->id));

            //Get product images
            $images = $this->Product_model->get_additional_data('product_images','*',['product_id' => $product->id], false, false, ['sort' => 'ASC']);
            if($images) {
                if(empty($product->image)) {
                    $product->image = $images[0]->image;
                } else {
                    foreach($images as $image) {
                        $product->images[]  = [
                            'url' => base_url('uploads/'.$image->image),
                            'alt_image' =>  $image->alt_image
                        ];
                    }
                }
            }

            if(empty($product->image)) {
                $product->image = base_url('uploads/catalog/nophoto.png');
            } else {
                $product->image = base_url('uploads/'.$product->image);
            }

            // Get product reviews
            $this->data['reviews'] = [];
            $reviews = $this->Review_model->filter(['product_id' => $product->id, 'status' => 1])->all();
            if($reviews) {
                foreach($reviews as $review) {
                    $user =	$this->Customers_model->filter(['id' => $review->customer_id, 'status' => 1])->one();
                    if($user) {
                        $review->user_name = $user->firstname.' '.ucfirst(substr($user->lastname,0,1));
                        $review->created_at = date('d M, Y',strtotime($review->created_at));
                        $this->data['reviews'][] = $review;
                    }
                }
            }

            $this->data['product_options'] = $this->Product_model->get_product_options_and_values($product);

            $this->data['shipping_list']  = [];


            if($this->data['current_country_id'] == $product->country_id && $product->country_id == 221)
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

			if(isset($_GET['a'])){
				print_R($this->data['shipping_list']);
			}
			
			
			foreach ($this->data['shipping_list'] as &$shipping_list) {
                $shipping_list['currency'] = 'USD';
            }

			if($product->country_id == $this->data['current_country_id']){
				$this->load->model('modules/Local_delivery_model');
				/*   $local_shipping = $this->db->query('
                        SELECT LD.* FROM
                        wc_users U
                        LEFT JOIN wc_local_delivery LD ON FIND_IN_SET(U.country, LD.countries)
                        WHERE LD.status=1 AND
                        U.country='.$this->data['current_country_id'].' AND
                        U.id=' . $product->created_by . ' ORDER BY LD.id DESC');  */
						//print 'SELECT LD.* FROM wc_local_delivery LD WHERE FIND_IN_SET('.$this->data['current_country_id'].', LD.countries) AND LD.status=1 ORDER BY LD.id DESC';
				$local_shipping = $this->db->query('SELECT LD.* FROM wc_local_delivery LD WHERE FIND_IN_SET('.$this->data['current_country_id'].', LD.countries) AND LD.status=1 ORDER BY LD.id DESC');

				if($local_shipping->num_rows()) {

                    $this->data['shipping_list'] = [];

					foreach ($local_shipping->result() as $_row){
						$currency = $this->currency->getCode($_row->currency_id);
						array_unshift($this->data['shipping_list'], [
							'id'      =>  $_row->id,
							'name'      =>  $_row->name,
							'code'      =>  'local_shipping',
							'price'     =>  $_row->price,
							'show_price'=>  $this->currency->convert($_row->price, $this->data['current_currency'], $currency) . ' ' . $this->data['current_currency'],
							'currency'  =>  $currency,
						]);
					}

                }
			}

            // Packaging details
            $this->load->model('modules/Length_class_model');
            $length_class = $this->Length_class_model->filter(['length_class_id' => $product->length_class_id])->with_translation()->one();
            $product->length_class_name = false;
            $product->length_class_unit = false;
            if($length_class) {
                $product->length_class_name = $length_class->name;
                $product->length_class_unit = $length_class->unit;
            }

            // Product price
            $product_price = $this->Product_model->get_price($product);

            $product->special = $product_price['special'];
            $product->price   = $product_price['price'];

            $product->price_simple   = $price["value"];
            $product->special_date_end  = ($product_price['special_date_end'] == '0000-00-00' || $product_price['special_date_end'] == null) ? false : date('Y-m-d', strtotime($product_price['special_date_end'] . ' 23:59:59')) . 'T23:59:59';

            /*if( $product_price['special_date_end'] < time() ) {
                $product->special_date_end = false;
            }*/


            // Copied Product
            //$this->data['copied_products'] = $this->Product_model->filter(['copied_product_id' => $product->id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->all();
            $this->data['copied_product_count'] = (int) $this->Product_model->filter(['copied_product_id' => $product->id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->count_rows();

            if($product->copied_product_id > 0) {
                $this->data['copied_product_count'] += 1;
                $this->data['copied_product_count'] += (int) $this->Product_model->filter([ 'id != "'.$product->id.'"' => null, 'copied_product_id' => $product->copied_product_id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->count_rows();
            }

            //echo $product->id;die();


            // Get Product Relation
            $this->data['product_relations'] = $this->get_product_relations($product->id);
            $this->data['product'] = $product;
            $this->data['attributes'] = $this->Product_model->get_product_attributes($product->id);


//            var_dump($this->data['shipping_list']);die;

            $this->template->render('product-single');

        }elseif ($this->data['category'] = $this->Category_model->filter(['slug' => trim($slug), 'status' => 1])->with_translation()->one()) {

            if(strpos($_SERVER['REQUEST_URI'], $slug . '/') === false && $hidden === false) {
                $url = str_replace($slug, $slug . '/', $_SERVER['REQUEST_URI']);

                redirect(site_url($url), 'auto', 301);
            }

            $this->data['custom_title'] = $this->data['category']->title;
            $this->data['meta_keywords'] = $this->data['category']->meta_title;
            $this->data['meta_description'] = $this->data['category']->meta_description;

            if($hidden == true) {
                $this->data['category']->h1 = $options['h1'];
                $this->data['category']->top_text = $options['top_text'];
                $this->data['category']->bottom_text = $options['bottom_text'];
                $this->data['category']->top_text_h1 = $options['top_text_h1'];
                $this->data['category']->bottom_text_h1 = $options['bottom_text_h1'];

                $this->data['custom_title']     = $options['title'];
                $this->data['meta_keywords']    = $options['meta_keywords'];
                $this->data['meta_description'] = $options['meta_description'];
            }


            $seo_links   = $this->Seo_links_model->filter(['status' => 1, 'name!=' => ''])->with_translation()->all();
            $categories  = $this->Category_model->filter(['status' => 1])->with_translation()->all();

            foreach ($seo_links as $link) {
                $this->data['seo_links'][] = [
                    'name'  =>  $link->name,
                    'slug'  =>  $link->slug,
                ];
            }

            foreach ($categories as $c) {
                $this->data['seo_links'][] = [
                    'name'  =>  $c->name,
                    'slug'  =>  $c->slug,
                ];
            }

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

            //Language Link
            foreach($this->data['languages'] as $key => $value)
            {
                $temp_slug = $this->Category_model->filter(['id' => $this->data['category']->id, 'status' => 1])->with_translation($value['id'])->one();
                $link = ($temp_slug) ? site_url(($this->data['current_lang'] == $key?'':$key).'/'.$temp_slug->slug) : "/";

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


            $this->data['title'] = $this->data['category']->name;



            if($this->data['category']->parent == 0) {



                $category_products = $this->Product_model->get_additional_data('product_to_category','*',['category_id' => $this->data['category']->id]);

                $this->data['products'] = [];
                $this->data['total_rows'] = 0;
                $this->data['brands'] = [];
                $brand_ids = [];
                if($category_products) {
                    $ids = [];
                    foreach($category_products as $top_product) {
                        $ids[] = $top_product->product_id;
                    }
                    $ids = implode(',',$ids);

                    $this->data['products']   = $this->Product_model->get_products(['id in('.$ids.')' => null, 'status' => 1], [12, $page - 1]);
                    $this->data['total_rows'] = $this->Product_model->get_product_count(['id in('.$ids.')' => null, 'status' => 1]);
                    $this->data['pagination'] = $this->get_pagination($slug,$this->data['total_rows']);
                }

                //Get sub categories
                $this->data['sub_categories'] = [];
                $sub_categories = $this->Category_model->fields('id, parent, name, slug')->with_translation()->filter(['parent' => $this->data['category']->id])->all();

                if($sub_categories) {
                    foreach($sub_categories as $sub_category) {
                        //$product_to_categories = $this->Category_model->get_additional_data('product_to_category','Count(product_id) as product_count',['category_id' => $sub_category->id],true);
                        //$product_count = ($product_to_categories) ? $product_to_categories->product_count : 0;
                        $this->data['sub_categories'][$sub_category->id] = [
                            'category_id' 	=> $sub_category->id,
                            'name' 			=> $sub_category->name,
                            'slug' 			=> $sub_category->slug,
                            'quantity' 		=> $this->Category_model->get_category_product_count($sub_category->id)
                        ];
                    }
                }

                $this->data['category_products'] = [];
                if($this->data['sub_categories']) {
                    $i = 0;
                    foreach($this->data['sub_categories'] as $sub_category) {
                        if($i == 2) {
                            break;
                        }
                        $category_products = $this->Category_model->get_additional_data('product_to_category','*',['category_id' => $sub_category['category_id']]);
                        if($category_products) {
                            $this->data['category_products'][$i]['category_name'] = $sub_category['name'];
                            $this->data['category_products'][$i]['category_slug'] = $sub_category['slug'];
                            $this->data['category_products'][$i]['products'] = [];
                            $ids = [];
                            foreach ($category_products as $product_category) {
                                $ids[] = $product_category->product_id;
                            }
                            $ids = implode(',',$ids);

                            $products = $this->Product_model->filter(['id IN ('.$ids.')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null, 'status' => 1])->with_translation()->limit(15)->all();
                            if($products) {
                                foreach($products as $product) {
                                    $brand_ids[] = $product->manufacturer_id;

                                    $image = $product->image;
                                    if(!empty(trim($product->image))) {
                                        $image = $this->Model_tool_image->resize($image, 167, 167);
                                    }
                                    if(!$image) {
                                        $product_images = $this->Product_model->get_product_images($product->id);
                                        if($product_images) {
                                            foreach($product_images as $product_image) {
                                                if($image) {
                                                    break;
                                                } else {
                                                    $image = $this->Model_tool_image->resize($product_image, 167, 167);
                                                }
                                            }
                                        }
                                    }
                                    if(!$image) {
                                        $image = $this->Model_tool_image->resize('nophoto.png', 167, 167);
                                    }
                                    // End image

                                    // Get product_price
                                    $product_price = $this->Product_model->get_price($product);

                                    $this->data['category_products'][$i]['products'][] = [
                                        'id' 			=> $product->id,
                                        'name'		 	=> $product->name,
                                        'slug'		 	=> $product->slug,
                                        'alt_image'		 	=> $product->alt_image,
                                        'link'			=> site_url_multi('product/'.$product->slug),
                                        'image'		 	=> $image,
                                        'price'			=> $product_price['price'],
                                        'special_price' => $product_price['special'],
                                        'rating'	 	=> round($this->Product_model->get_product_review($product->id))
                                    ];
                                }
                            }
                        }

                        $i++;
                    }

                }






                // Get sub Categories new
                $this->data['sub_categories'] = [];
                $sub_categories = $this->Category_model->fields('id, name, slug')->filter(['parent' => $this->data['category']->id])->with_translation()->all();
                if($sub_categories) {
                    foreach($sub_categories as $sub_category) {
                        $this->data['sub_categories'][$sub_category->id] = [
                            'name' => $sub_category->name,
                            'slug' => $sub_category->slug,
                            'sub_categories' => $this->get_sub_categories_and_product_counts($sub_category->id)
                        ];

                    }
                }

                // Get brands
                if($brand_ids) {
                    $brands = $this->Brand_model->filter(['id IN ('.implode(",",$brand_ids).')' => null, 'status' => 1])->order_by('sort', 'ASC')->all();
                    foreach($brands as $brand) {
                        $this->data['brands'][] = [
                            'id' => $brand->id,
                            'name' => $brand->name
                        ];
                    }
                }

                // Get Special products
                $this->data['special_products'] = [];
                $special_products = $this->Product_model->get_additional_data('featured_product','id, percent, products, start_date, expired_date',['type' => 'category', 'category_id' => $this->data['category']->id, 'status' => 1],true);
                if($special_products && !empty($special_products->products)) {
                    if($special_products->start_date == null || ($special_products->start_date < date('Y-m-d H:i:s') && $special_products->expired_date > date('Y-m-d H:i:s'))) {
                        $this->data['special_products']['expired_date'] = ($special_products->expired_date != '0000-00-00' || $special_products->expired_date != null) ? $special_products->expired_date : false;
                        $this->data['special_products']['percent'] 		= $special_products->percent;
                        $this->data['special_products']['products'] 	= $this->Product_model->get_products(['id in('.$special_products->products.')' => null, 'status' => 1], 10);
                    }
                }


                $this->google_seo->dataLayerImpressions($this->data['products'], $this->data['category']->name);

                $this->template->render('category_main');
            } else {

                //$this->data['price_range'] = $this->Product_model->get_price_range_by_category($this->data['category']->id);

                // Set default values
                $this->data['total_rows'] = 0;
                $this->data['products'] = [];
                $this->data['option_data'] = [];
                $this->data['attribute_data'] = [];
                $this->data['brands'] = [];
                $product_price_list = [];

                $brand_ids 	= [];

                $options = [];
                $attributes = [];
                $product_ids = [];
                $category_products = $this->Product_model->get_additional_data('product_to_category','*',['category_id' => $this->data['category']->id]);
                if($category_products){
                    foreach($category_products as $category_product) {
                        $product_ids[] = $category_product->product_id;
                    }
                }

				$max_product_id = $product_ids;

                if($product_ids) {
                    // Get filters
                    if($this->input->method() == 'get') {

                        if(isset($_GET['review']) && $_GET['review']) {
                            $selected_reviews = $_GET['review'];
                            $reviews = $this->Review_model->fields('AVG(rating) as avg_rating, product_id')->filter(['product_id IN('.implode(",",$product_ids).')' => null])->all();
                            $product_ids = [];
                            if($reviews) {
                                foreach ($reviews as $review) {
                                    if(in_array(ceil($review->avg_rating), $selected_reviews)) {
                                        $product_ids[] = $review->product_id;
                                    }
                                }
                            }
                        }

                        if($product_ids) {


                            $products = $this->Product_model->filter(['id IN('.implode(",",$product_ids).')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->all();

                            $product_ids = [];
                            if($products) {
                                foreach ($products as $product) {
                                    $price = $this->Product_model->get_price($product)['price'];
                                    $price = (float)$price;
                                    $price_to   = $_GET['price_to'] ?? null;
                                    $price_from = $_GET['price_from'] ?? null;
                                    if($price_to != NULL && $price_from != NULL)
                                    {
                                        if($price <= (int)str_replace(' ', '', $price_to) && $price >= $price_from)
                                        {
                                            $product_ids[] = $product->id;
                                        }
                                    }
                                    else
                                    {
                                        $product_ids[] = $product->id;
                                    }
                                }
                            }
                        }

                        $option = $_GET['option'] ?? null;

                        if($product_ids && $option) {
                            foreach($option as $option_id => $option_value_ids) {

                                $products = $this->Category_model->get_additional_data('product_option_value','product_id',['option_id' => $option_id, 'product_id IN('.implode(",",$product_ids).')' => null, 'option_value_id IN('.implode(",",$option_value_ids).')' => null]);
                                $product_ids = [];
                                if($products) {
                                    foreach($products as $product) {
                                        $product_ids[] = $product->product_id;
                                    }
                                }

                            }
                        }

                        $get_attribute = $_GET['attribute'] ?? null;

                        if($product_ids && $get_attribute) {
                            foreach($get_attribute as $attribute_id => $attribute_value_ids) {
                                $products = $this->Category_model->get_additional_data('product_attribute_value','product_id',['attribute_id' => $attribute_id, 'product_id IN('.implode(",",$product_ids).')' => null, 'attribute_value_id IN('.implode(",",$attribute_value_ids).')' => null]);
                                $product_ids = [];
                                if($products) {
                                    foreach($products as $product) {
                                        $product_ids[] = $product->product_id;
                                    }
                                }

                            }
                        }

                    }
                }

                if($product_ids) {
                    $ids = implode(',',$product_ids);
                    $product_options = $this->Category_model->get_additional_data('product_option_value','*',['product_id IN('.$ids.')' => null]);

                    if($product_options) {
                        foreach($product_options as $product_option) {
                            $options[$product_option->option_id][$product_option->option_value_id] = [
                                'option_id' => $product_option->option_id,
                                'option_value_id' => $product_option->option_value_id,
                            ];
                        }
                    }

                    // Get Products attributes
                    $product_attributes = $this->Category_model->get_additional_data('product_attribute_value','*',['product_id IN('.$ids.')' => null]);
                    if($product_attributes) {
                        foreach($product_attributes as $product_attribute) {
                            $attributes[$product_attribute->attribute_id][$product_attribute->attribute_value_id] = [
                                'attribute_id' => $product_attribute->attribute_id,
                                'attribute_value_id' => $product_attribute->attribute_value_id,
                            ];
                        }
                    }
                }

                if($options) {
                    foreach($options as $option_id => $option_values) {
                        $option_data  = $this->Option_model->filter(['id' => $option_id, 'status' => 1])->with_translation()->one();
                        if($option_data) {
                            if($option_values) {
                                $value_ids = [];
                                foreach($option_values as $option_value) {
                                    $value_ids[] = $option_value['option_value_id'];
                                }
                                $general_data = $this->Category_model->get_additional_data('option_value','*',['id IN('.implode(',',$value_ids).')' => null]);
                                $translation_data = $this->Category_model->get_additional_data('option_value_description','*',['option_value_id IN('.implode(',',$value_ids).')' => null, 'language_id' => $this->data['current_lang_id']]);

                                $data = [
                                    'id' 	=> $option_data->id,
                                    'type'  => $option_data->type,
                                    'name'  => $option_data->name,
                                    'values' => []
                                ];

                                foreach($general_data as $general) {
                                    $data['values'][$general->id]['image'] = $general->image;
                                    $data['values'][$general->id]['value'] = $general->value;
                                }

                                foreach($translation_data as $translation) {
                                    $data['values'][$translation->option_value_id]['name'] = $translation->name;
                                }
                                $this->data['option_data'][] = $data;
                            }

                        }


                    }
                }



                if($attributes) {
                    foreach($attributes as $attribute_id => $attribute_values) {

                        if($this->data['category']->attribute_group_id)
                        {
                            $attribute_group_id = $this->data['category']->attribute_group_id;
                        }
                        else
                        {
                            $parent_attribute_group = $this->Category_model->filter(['id' => $this->data['category']->parent])->one();
                            if($parent_attribute_group)
                            {
                                $attribute_group_id = $parent_attribute_group->attribute_group_id;
                            }
                            else
                            {
                                $attribute_group_id = 0;
                            }
                        }

                        $attribute_data  = $this->Attribute_model->filter(['id' => $attribute_id, 'status' => 1, 'custom' => 0, 'FIND_IN_SET('.$attribute_group_id.', attribute_group_id)' => null])->order_by('sort', 'ASC')->with_translation()->one();
                        if($attribute_data) {
                            if($attribute_values) {
                                $value_ids = [];
                                foreach($attribute_values as $attribute_value) {
                                    $value_ids[] = $attribute_value['attribute_value_id'];
                                }
                                $attribute_valuess = $this->Category_model->get_additional_data('attribute_value','*',['id IN('.implode(',',$value_ids).')' => null, 'custom' => 0]);
                                if($attribute_valuess)
                                {
                                    foreach($attribute_valuess as $attribute_valuesx)
                                    {
                                        if($attribute_valuesx->custom == 0)
                                        {
                                            $value_ids[] = $attribute_valuesx->id;
                                        }
                                    }
                                    $translation_data = $this->Category_model->get_additional_data('attribute_value_description','*',['attribute_value_id IN('.implode(',',$value_ids).')' => null, 'language_id' => $this->data['current_lang_id']]);

                                    $data = [
                                        'id' 	=> $attribute_data->id,
                                        'name'  => $attribute_data->name,
                                        'values' => []
                                    ];
                                    foreach($translation_data as $translation) {
                                        $data['values'][$translation->attribute_value_id]['name'] = $translation->name;
                                    }

                                    $this->data['attribute_data'][] = $data;
                                }


                            }
                        }
                    }
                }


                $this->data['sub_categories'] = [];
                $sub_categories = $this->Category_model->fields('id, parent, name, slug')->with_translation()->filter(['parent' => $this->data['category']->id])->all();

                if($sub_categories) {
                    foreach($sub_categories as $sub_category) {
                        //$product_to_categories = $this->Category_model->get_additional_data('product_to_category','Count(product_id) as product_count',['category_id' => $sub_category->id],true);
                        //$product_count = ($product_to_categories) ? $product_to_categories->product_count : 0;
                        $this->data['sub_categories'][$sub_category->id] = [
                            'category_id' 	=> $sub_category->id,
                            'name' 			=> $sub_category->name,
                            'slug' 			=> $sub_category->slug,
                            'quantity' 		=> $this->Category_model->get_category_product_count($sub_category->id)
                        ];
                    }
                }


                // Get filtered products
                if($product_ids) {

                    //var_dump($product_ids);die();
                    $order_by = ['created_at' => 'ASC'];
                    $get_sort = $_GET['sort'] ?? null;
                    if($get_sort) {
                        $sort = explode('_',$get_sort);
                        if(count($sort) == 2) {

                            $order_by = [$sort[0] => $sort[1]];
                        }
                    }
                    $per_page = (isset($_GET['per_page'])) ? (int)$_GET['per_page'] : 20;
                    $products = $this->Product_model->filter(['id in('.implode(",",$product_ids).')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null, 'status' => 1])->with_translation()->order_by($order_by)->limit($per_page, $page - 1)->all();

                    $this->data['total_rows'] = $this->Product_model->filter(['id in('.$ids.')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null, 'status' => 1])->with_translation()->count_rows();


					$max_product_price = $this->db->query('SELECT P.price, C.code as currency FROM wc_product P LEFT JOIN wc_currency C ON P.currency=C.id WHERE P.id IN (' . implode(",", $max_product_id) . ') ORDER BY P.price DESC LIMIT 1')->row();

					if (isset($max_product_price->price)) {
						$max_product_price = $this->currency->formatter_without_symbol($max_product_price->price, $max_product_price->currency, $this->data['current_currency']);
					}


                    if($products) {
                        foreach($products as $product) {

                            $get_brand = $_GET['brand'] ?? null;

                            if(($get_brand && in_array($product->manufacturer_id,$get_brand)) || !$get_brand){
                                $brand_ids[] = $product->manufacturer_id;

                                $image = $product->image;
                                if(!empty(trim($product->image))) {
                                    $image = $this->Model_tool_image->resize($image, 167, 167);
                                }
                                if(!$image) {
                                    $product_images = $this->Product_model->get_product_images($product->id);
                                    if($product_images) {
                                        foreach($product_images as $product_image) {
                                            if($image) {
                                                break;
                                            } else {
                                                $image = $this->Model_tool_image->resize($product_image, 167, 167);
                                            }
                                        }
                                    }
                                }
                                if(!$image) {
                                    $image = $this->Model_tool_image->resize('nophoto.png', 167, 167);
                                }
                                // End image

                                // Get product_price
                                $product_price = $this->Product_model->get_price($product);
                                $temp_currency_code = $this->currency->getCode($product->currency);
                                $temp_price = $this->currency->formatter_without_symbol($product->price,$temp_currency_code, $this->data['current_currency']);
                                $product_price_list[] = round($temp_price);

                                $this->data['products'][] = [
                                    'id' 			    => $product->id,
                                    'name'		 	    => $product->name,
                                    'slug'		 	    => $product->slug,
                                    'alt_image'		    => $product->alt_image??$product->name,
                                    'link'			    => site_url_multi('product/'.$product->slug),
                                    'image'		 	    => $image,
                                    'manufacturer_id'   => $product->manufacturer_id,
                                    'price'			    => $product_price['price'],
                                    'special_price'     => $product_price['special'],
                                    'rating'	 	    => round($this->Product_model->get_product_review($product->id))
                                ];

                            }

                        }



                        $this->data['pagination'] = $this->get_pagination($slug,$this->data['total_rows'], $per_page);

                    }
                }

				if(isset($order_by['price'])){

					$_b = [];
					array_map(function($val)use(&$_b){
						$_b[intval($val['price'])] = $val;
					}, $this->data['products']);

					if($order_by['price'] == 'ASC')
						ksort($_b);
					else
						krsort($_b);

					$this->data['products'] = $_b;
				}


                if($brand_ids) {
                    $brands = $this->Brand_model->filter(['id IN ('.implode(",",$brand_ids).')' => null, 'status' => 1])->all();
                    if($brands) {
                        foreach($brands as $brand) {
                            $this->data['brands'][] = [
                                'id' => $brand->id,
                                'name' => $brand->name
                            ];
                        }
                    }
                }

                if($product_price_list) {
                    $this->data['price_range'] = ['min' => 0, 'max' => $max_product_price];
                } else {
                    $this->data['price_range'] = ['min' => 0, 'max' => 100];
                }

                $this->google_seo->dataLayerImpressions($this->data['products'], $this->data['category']->name);

                $this->template->render('category');
            }
        }
        elseif ($page = $this->Page_model->filter(['slug' => urldecode($slug)])->with_translation()->one()) {




            if($page->page_id == 4) {
                if( ! $this->customer->is_loggedin()) {
                    redirect(site_url_multi('account/login?redirect=' . $page->slug));

                }
                else {
                    redirect(site_url_multi('account/orders'));
                }
            }


            //Language Link

            foreach($this->data['languages'] as $key => $value)
            {
                $temp_slug = $this->Page_model->filter(['id' => $page->id, 'status' => 1])->with_translation($value['id'])->one();
                $link = ($temp_slug) ? site_url(($this->data['current_lang'] == $key?'':$key).'/'.$temp_slug->slug) : "/";
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

            $this->data['title'] = $page->name;
            $this->data['image'] = $page->image;
            $this->data['description'] = $page->description;
            $this->template->render('page');
        }
        elseif ($seo_url = $this->Seo_links_model->filter(['slug' => urldecode($slug), 'status' => 1])->with_translation()->one()) {


			if(isset($_GET['seo'])){
                print_r($seo_url); die;
            }
            if(strpos($_SERVER['REQUEST_URI'], $slug . '/') === false && $hidden === false) {
                $url = str_replace($slug, $slug . '/', $_SERVER['REQUEST_URI']);

                redirect(site_url($url), 'auto', 301);
            }

            $url = $seo_url->url;

            $seo_slug_array = explode('/', $url);

            $seo_slug = current($seo_slug_array);

            if(in_array($seo_slug, ['az', 'en', 'ru'])) {
                $seo_slug = next($seo_slug_array);
            }

            parse_str(str_replace([$seo_slug . '/', '?', $this->data['current_lang'] . '/'], '', $url), $parsed);

            $_GET = array_merge($parsed, $_GET);

            return $this->index($seo_slug, 1, true, [
                'title'                 =>  $seo_url->title,
                'meta_title'            =>  $seo_url->meta_title,
                'meta_keywords'         =>  $seo_url->meta_keywords,
                'meta_description'      =>  $seo_url->meta_description,
                'h1'                    =>  $seo_url->h1,
                'top_text'              =>  $seo_url->top_text,
                'bottom_text'           =>  $seo_url->bottom_text,
                'top_text_h1'           =>  $seo_url->top_text_h1,
                'bottom_text_h1'        =>  $seo_url->bottom_text_h1,
            ]);

        }
        elseif ($seo_redirect_url = $this->Seo_url_redirect_model->filter(['newurl' => urldecode(trim($slug))])->one()) {

            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $seo_redirect_url->oldurl);
            exit();

        }
        else {
            show_404();
        }
    }

    public function add_credit_request(){

        $data=array();
        $data["ip"]=$_SERVER["REMOTE_ADDR"];

        $data["product_name"]=$this->input->post("product_name");
        $data["loan_period"]=$this->input->post("muddet");
        $data["salary"]=$this->input->post("gelir");
        $data["rate"]=22;
        $data["staj"]=$this->input->post("resmi_is");
        $data["staj_muddeti"]=$this->input->post("staj_time")." ".$this->input->post("staj_sec");
        $data["product_salary"]=$this->input->post("product_salary");
        $data["delivery_type"]=$this->input->post("shipping_name");
        $data["delivery_cost"]=$this->input->post("shipping_price");
        $data["count"]=$this->input->post("product_count");
        $data["monthly_payment"]=$this->input->post("pmt");
        $data["username"]=$this->input->post("crdt_username");
        $data["personality_no"]=$this->input->post("crdt_ser");
        $data["personality_fin_no"]=$this->input->post("crdt_pincode");
        $data["birth"]=$this->input->post("birthday");
        $data["address"]=$this->input->post("crdt_location");
        $data["phone"]=$this->input->post("crdt_phone");
        $data["home_phone"]=$this->input->post("crdt_phn");
        $data["e_mail"]=$this->input->post("crdt_e_mail");

        $this->db->insert("wc_credit_request",$data);
     //   echo $post;

    }

    public function get_product_relations($id)
    {
        $this->load->model('Relation_model');
        $response = [];
        $product_relations = $this->Product_model->get_additional_data('product_relation','*',['product_id' => $id]);
        if($product_relations) {
            foreach($product_relations as $product_relation) {
                $temp_data = [];
                // Get product relation values
                $product_relation_values = $this->Product_model->get_additional_data('product_relation_value','*,id as product_relation_value_id',['product_relation_id' => $product_relation->id],false,true);
                if($product_relation_values) {
                    $relation = $this->Relation_model->filter(['id' => $product_relation->relation_id])->with_translation()->one();
                    if($relation) {
                        $temp_data = [
                            'product_relation_id' => $product_relation->id,
                            'name' => $relation->name,
                            'relation_id' => $product_relation->relation_id,
                            'product_relation_value' => [],
                        ];

                        foreach($product_relation_values as $key => $prv) {
                            $relation_value_description = $this->Relation_model->get_additional_data('relation_value_description','name', ['relation_value_id' => $prv['relation_value_id'], 'language_id' => $this->data['current_lang_id']],true);
                            if($relation_value_description) {
                                $data = [
                                    'name'				=> $relation_value_description->name,
                                    'current'			=> $prv['current']
                                ];
                            }

                            $valid = true;
                            if($prv['relation_id'] != 0) {
                                $relation_product = $this->Product_model->fields('name, slug')->filter(['id' => $prv['relation_id'], 'status' => 1])->with_translation()->one();
                                if($relation_product) {
                                    $data['link'] = site_url_multi('product/'.$relation_product->slug);
                                } else {
                                    $valid = false;
                                }
                            } else {
                                $data['link'] = current_url();
                            }
                            if($valid) {
                                $temp_data['product_relation_value'][] = $data;
                            }
                        }

                        $response[] = $temp_data;
                    }

                }
            }
        }
        return $response;
    }

    private function get_pagination($slug, $total_rows, $per_page = 20)
    {
        // Sets Pagination options and initialize
        $config['base_url'] = site_url_multi('/').$slug;
        $config['reuse_query_string'] = true;
        $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['full_tag_open']            = '<div class="text-center"><div class="pagination main-pagination">';
        $config['full_tag_close']           = '</div></div>';
        $config['first_link'] 				= '&laquo;';
        $config['first_tag_open'] 			= '';
        $config['first_tag_close']			= '';
        $config['last_link'] 				= '&raquo;';
        $config['last_tag_open'] 			= '';
        $config['last_tag_close'] 			= '';
        $config['next_link'] 				= '&rarr;';
        $config['next_tag_open'] 			= '';
        $config['next_tag_close'] 			= '';
        $config['prev_link'] 				= '&larr;';
        $config['prev_tag_open'] 			= '';
        $config['prev_tag_close'] 			= '';
        $config['cur_tag_open'] 			= '<a class="active">';
        $config['cur_tag_close'] 			= '</a>';
        $config['num_tag_open'] 			= '';
        $config['num_tag_close'] 			= '';

        $this->pagination->initialize($config);
        return  $this->pagination->create_links();
    }

    public function get_sub_categories_and_product_counts($category_id)
    {
        $result = [];
        $sub_categories = $this->Category_model->fields('id, name, slug')->filter(['parent' => $category_id])->with_translation()->order_by('sort', 'ASC')->all();
        if($sub_categories) {
            foreach($sub_categories as $sub_category) {
                $product_to_categories = $this->Category_model->get_additional_data('product_to_category','Count(product_id) as product_count',['category_id' => $sub_category->id],true);
                $product_count = ($product_to_categories) ? $product_to_categories->product_count : 0;
                $result[$sub_category->id] = [
                    'category_id' => $sub_category->id,
                    'name' => $sub_category->name,
                    'slug' => $sub_category->slug,
                    'quantity' => $product_count
                ];
            }
        }
        return $result;
    }

    public function redirect($to = '/')
    {
        redirect($to, 'auto', 301);
    }

    public function fake()
    {
        return self::index('mimelons-policies');

        $url = $_SERVER['REQUEST_URI'] = '/electronics/';

        $exploded = explode('/', $url);

        $slug = ($exploded[1] ?? '');
        $page = ($exploded[2] ?? 1) ?: 1;

        var_dump($slug, $page);

        die;

        return $this->index($slug, $page);
    }


}
