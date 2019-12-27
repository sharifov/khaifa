<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends Site_Controller
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
    }

    public function index($slug = false)
    {
        redirect(site_url_multi($slug), 'auto', 301);
        die;

        if ($slug) {
            $product = $this->Product_model->filter(['slug' => urldecode($slug), '(status=1 OR status=9)' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->with_translation()->one();

            if ($product) {


                //Language Link
                foreach ($this->data['languages'] as $key => $value) {
                    $temp_slug = $this->Product_model->filter(['id' => $product->id, 'status' => 1])->with_translation($value['id'])->one();
                    $link = ($temp_slug) ? site_url($key . '/product/' . $temp_slug->slug) : "/";
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

				if(!$product->alt_image) $product->alt_image =  $product->name;

                //Add to recently viewed
                $recently_viewed = $this->session->userdata('recently_viewed');
                if (!$recently_viewed) {
                    $recently_viewed = [$product->id];
                } else {
                    if (!in_array($product->id, $recently_viewed)) {
                        $recently_viewed[] = $product->id;
                    }
                }
                $this->session->set_userdata('recently_viewed', $recently_viewed);

                //Get product category
                $category = $this->Product_model->get_additional_data('product_to_category', 'category_id', ['product_id' => $product->id], true);
                if ($category) {
                    $category = $this->Category_model->filter(['category_id' => $category->category_id])->with_translation()->one();
                    if ($category) {
                        $product->category = $category->name;
                        $product->category_slug = $category->slug;
                    }
                }

                //Get product manufacturer
                if ($product->manufacturer_id) {
                    $manufacturer = $this->Brand_model->fields('name')->filter(['id' => $product->manufacturer_id])->one();
                    if ($manufacturer) {
                        $product->manufacturer_name = $manufacturer->name;
                    }
                }

                //Get product stock status
                if ($product->stock_status_id) {
                    $stock_status = $this->Stock_status_model->fields('name')->filter(['id' => $product->stock_status_id])->with_translation()->one();
                    if ($stock_status) {
                        $product->stock_status_name = $stock_status->name;
                    }
                }

                //Get product Country name
                $product->country_name = "";
                if ($product->country_id) {
                    $country = $this->Country_model->fields('name')->filter(['id' => $product->country_id])->with_translation()->one();
                    if ($country) {
                        $product->country_name = $country->name;
                    }
                }

                $product->region_name = "";
                if ($product->region_id) {
                    $region = $this->Zone_model->fields('name')->filter(['id' => $product->region_id])->one();
                    if ($region) {
                        $product->region_name = $region->name;
                    }
                }

                //Get product rating
                $product->rating = round($this->Product_model->get_product_review($product->id));

                //Get product images
                $images = $this->Product_model->get_additional_data('product_images', '*', ['product_id' => $product->id], false, false, ['sort' => 'ASC']);
                if ($images) {
                    if (empty($product->image)) {
                        $product->image = $images[0]->image;
                    } else {
                        foreach ($images as $image) {
                            $product->images[] = ['url' => base_url('uploads/' . $image->image)];
                        }
                    }
                }
                if (empty($product->image)) {
                    $product->image = base_url('uploads/catalog/nophoto.png');
                } else {
                    $product->image = base_url('uploads/' . $product->image);
                }

                // Get product reviews
                $this->data['reviews'] = [];
                $reviews = $this->Review_model->filter(['product_id' => $product->id, 'status' => 1])->all();
                if ($reviews) {
                    foreach ($reviews as $review) {
						if($review->customer_id){
							$user = $this->Customers_model->filter(['id' => $review->customer_id, 'status' => 1])->one();
							$review->user_name = $user->firstname . ' ' . ucfirst(substr($user->lastname, 0, 1));
						}
						else $review->user_name = translate('guest');

						$review->created_at = date('d M, Y', strtotime($review->created_at));
						$this->data['reviews'][] = $review;
                    }
                }

                $this->data['product_options'] = $this->Product_model->get_product_options_and_values($product);

                $this->data['shipping_list'] = [];

                if ($this->data['current_country_id'] == $product->country_id && $product->country_id == 221) {
                    $this->load->library('shipping/Free');
                    $this->data['shipping_list'] = $this->free->calculate();
                } else {
                    $this->load->library('shipping/Ems');
                    if ($product->length_class_id != 1) {
                        $this->load->library('Length');
                        $Width = $this->length->convert($product->width, $product->length_class_id, 1);
                        $height = $this->length->convert($product->height, $product->length_class_id, 1);
                        $length = $this->length->convert($product->length, $product->length_class_id, 1);
                    } else {
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

//				var_dump($this->data['shipping_list']);die;

                foreach ($this->data['shipping_list'] as &$shipping_list) {

//                    preg_match('!\d+!', $shipping_list['show_price'], $price);

//                    $shipping_list['currency']  = $this->data['current_currency'];
//                    $shipping_list['price']     = $price[0];
                    $shipping_list['currency'] = 'USD';

                }


                // Packaging details
                $this->load->model('modules/Length_class_model');
                $length_class = $this->Length_class_model->filter(['length_class_id' => $product->length_class_id])->with_translation()->one();
                $product->length_class_name = false;
                $product->length_class_unit = false;
                if ($length_class) {
                    $product->length_class_name = $length_class->name;
                    $product->length_class_unit = $length_class->unit;
                }


                // Product price
                $product_price = $this->Product_model->get_price($product);

                $product->special = $product_price['special'];
                $product->price = $product_price['price'];
                $product->special_date_end = ($product_price['special_date_end'] == '0000-00-00' || $product_price['special_date_end'] == null) ? false : date('Y-m-d H:i:s', strtotime($product_price['special_date_end']));

                // Copied Product
                //$this->data['copied_products'] = $this->Product_model->filter(['copied_product_id' => $product->id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->all();
                $this->data['copied_product_count'] = (int)$this->Product_model->filter(['copied_product_id' => $product->id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->count_rows();

                if ($product->copied_product_id > 0) {
                    $this->data['copied_product_count'] += 1;
                    $this->data['copied_product_count'] += (int)$this->Product_model->filter(['id != "' . $product->id . '"' => null, 'copied_product_id' => $product->copied_product_id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->count_rows();
                }

                //echo $product->id;die();


                // Get Product Relation
                $this->data['product_relations'] = $this->get_product_relations($product->id);
                $this->data['product'] = $product;
                $this->data['attributes'] = $this->Product_model->get_product_attributes($product->id);


            } else {
                show_404();
            }

        } else {
            show_404();
        }

        $this->template->render('product-single');
    }

    public function clone($slug = false)
    {
        if ($slug) {
            $product = $this->Product_model->filter(['slug' => urldecode($slug), 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->with_translation()->one();

            if ($product) {


                //Language Link
                foreach ($this->data['languages'] as $key => $value) {
                    $temp_slug = $this->Product_model->filter(['id' => $product->id, 'status' => 1])->with_translation($value['id'])->one();
                    $link = ($temp_slug) ? site_url($key . '/product/' . $temp_slug->slug) : "/";
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

				if(!$product->alt_image) $product->alt_image =  $product->name;

                //Add to recently viewed
                $recently_viewed = $this->session->userdata('recently_viewed');
                if (!$recently_viewed) {
                    $recently_viewed = [$product->id];
                } else {
                    if (!in_array($product->id, $recently_viewed)) {
                        $recently_viewed[] = $product->id;
                    }
                }
                $this->session->set_userdata('recently_viewed', $recently_viewed);

                //Get product category
                $category = $this->Product_model->get_additional_data('product_to_category', 'category_id', ['product_id' => $product->id], true);
                if ($category) {
                    $category = $this->Category_model->filter(['category_id' => $category->category_id])->with_translation()->one();
                    if ($category) {
                        $product->category = $category->name;
                        $product->category_slug = $category->slug;
                    }
                }

                //Get product manufacturer
                if ($product->manufacturer_id) {
                    $manufacturer = $this->Brand_model->fields('name')->filter(['id' => $product->manufacturer_id])->one();
                    if ($manufacturer) {
                        $product->manufacturer_name = $manufacturer->name;
                    }
                }

                //Get product stock status
                if ($product->stock_status_id) {
                    $stock_status = $this->Stock_status_model->fields('name')->filter(['id' => $product->stock_status_id])->with_translation()->one();
                    if ($stock_status) {
                        $product->stock_status_name = $stock_status->name;
                    }
                }

                //Get product Country name
                $product->country_name = "";
                if ($product->country_id) {
                    $country = $this->Country_model->fields('name')->filter(['id' => $product->country_id])->with_translation()->one();
                    if ($country) {
                        $product->country_name = $country->name;
                    }
                }

                $product->region_name = "";
                if ($product->region_id) {
                    $region = $this->Zone_model->fields('name')->filter(['id' => $product->region_id])->one();
                    if ($region) {
                        $product->region_name = $region->name;
                    }
                }

                //Get product rating
                $product->rating = round($this->Product_model->get_product_review($product->id));

                //Get product images
                $images = $this->Product_model->get_additional_data('product_images', '*', ['product_id' => $product->id], false, false, ['sort' => 'ASC']);
                if ($images) {
                    if (empty($product->image)) {
                        $product->image = $images[0]->image;
                    } else {
                        foreach ($images as $image) {
                            $product->images[] = ['url' => base_url('uploads/' . $image->image)];
                        }
                    }
                }
                if (empty($product->image)) {
                    $product->image = base_url('uploads/catalog/nophoto.png');
                } else {
                    $product->image = base_url('uploads/' . $product->image);
                }

                // Get product reviews
                $this->data['reviews'] = [];
                $reviews = $this->Review_model->filter(['product_id' => $product->id, 'status' => 1])->all();
                if ($reviews) {
                    foreach ($reviews as $review) {
						if($review->customer_id){
							$user = $this->Customers_model->filter(['id' => $review->customer_id, 'status' => 1])->one();
							$review->user_name = $user->firstname . ' ' . ucfirst(substr($user->lastname, 0, 1));
						}
						else $review->user_name = translate('guest');
                    }
                }

                $this->data['product_options'] = $this->Product_model->get_product_options_and_values($product);

                $this->data['shipping_list'] = [];

                if ($this->data['current_country_id'] == $product->country_id && $product->country_id == 221) {
                    $this->load->library('shipping/Free');
                    $this->data['shipping_list'] = $this->free->calculate();
                } else {
                    $this->load->library('shipping/Ems');
                    if ($product->length_class_id != 1) {
                        $this->load->library('Length');
                        $Width = $this->length->convert($product->width, $product->length_class_id, 1);
                        $height = $this->length->convert($product->height, $product->length_class_id, 1);
                        $length = $this->length->convert($product->length, $product->length_class_id, 1);
                    } else {
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


                // Packaging details
                $this->load->model('modules/Length_class_model');
                $length_class = $this->Length_class_model->filter(['length_class_id' => $product->length_class_id])->with_translation()->one();
                $product->length_class_name = false;
                $product->length_class_unit = false;
                if ($length_class) {
                    $product->length_class_name = $length_class->name;
                    $product->length_class_unit = $length_class->unit;
                }


                // Product price
                $product_price = $this->Product_model->get_price($product);

                $product->special = $product_price['special'];
                $product->price = $product_price['price'];
                $product->special_date_end = ($product_price['special_date_end'] == '0000-00-00' || $product_price['special_date_end'] == null) ? false : date('Y-m-d H:i:s', strtotime($product_price['special_date_end']));


                // Copied Product
                //$this->data['copied_products'] = $this->Product_model->filter(['copied_product_id' => $product->id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->all();
                $this->data['copied_product_count'] = (int)$this->Product_model->filter(['copied_product_id' => $product->id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->count_rows();

                if ($product->copied_product_id > 0) {
                    $this->data['copied_product_count'] += 1;
                    $this->data['copied_product_count'] += (int)$this->Product_model->filter(['id != "' . $product->id . '"' => null, 'copied_product_id' => $product->copied_product_id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->count_rows();
                }

                //echo $product->id;die();


                // Get Product Relation
                $this->data['product_relations'] = $this->get_product_relations($product->id);
                $this->data['product'] = $product;
                $this->data['attributes'] = $this->Product_model->get_product_attributes($product->id);

            } else {
                show_404();
            }

        } else {
            show_404();
        }

        $this->template->render('product-single2');
    }

    public function review()
    {
        $response = ['success' => false, 'message' => ''];
        if ($this->customer->is_loggedin()) {
            $this->load->model('Review_model');
            $this->form_validation->set_rules('product', translate('form_label_product'), 'trim|required');
            $this->form_validation->set_rules('rating', translate('form_label_rating'), 'trim|required');
            $this->form_validation->set_rules('subject', translate('form_label_subject'), 'trim|required|min_length[3]|max_length[64]');
            $this->form_validation->set_rules('text', translate('form_label_review'), 'trim|required|min_length[10]|max_length[1000]');
            if ($this->input->method() == 'post') {
                if ($this->form_validation->run() == true) {
                    $review = [
                        'customer_id' => (int)$this->data['customer']->id,
                        'product_id' => $this->input->post('product'),
                        'subject' => $this->input->post('subject'),
                        'text' => $this->input->post('text'),
                        'rating' => $this->input->post('rating')
                    ];
                    $this->Review_model->insert($review);
                    $response['success'] = true;
                    $response['message'] = translate('success_message_thanks_for_review', true);
                } else {
                    $response['message'] = validation_errors();
                }
            }

		} else {
            $response['message'] = translate('error_message_please_login_first', true);
        }
        $this->template->json($response);
    }


    public function search($page = 1)
    {

        //$product = $this->Product_model->filter(['slug' => urldecode($slug), '(status=1 OR status=9)' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->with_translation()->one();


        if (false && $this->input->get('query') && $this->input->is_ajax_request()) {
            $query = (string)$this->input->get('query');

            $filter = [];
            $filter['status'] = 1;
            $filter['status!=9'] = null;

			$_ex = explode(' ', $query);

			foreach($_ex as $_qx){
				$filter['name LIKE "%' . $_qx . '%"'] = NULL;
			}

            if ($this->input->get('category_id')) {
                $category_id = (int)$this->input->get('category_id');
                $products_id = $this->Product_model->get_additional_data('product_to_category', '*', ['category_id' => $category_id]);

                if ($products_id) {
                    $ids = [];
                    foreach ($products_id as $product) {
                        $ids[] = $product->product_id;
                    }
                    $ids = implode(',', $ids);
                    $filter['id in(' . $ids . ')'] = NULL;

                    $order_by = ['FIELD(id, ' . $ids . ')', false]; //added new
                } else {
                    $filter['id in(99999999999999999)'] = NULL;
                }

                /*$products = $this->Product_model->get_products_extended($filter, false, $query);

				$filter = [];

                if($products)
                {
                    $ids = [];
                    foreach($products_id as $product) {
                        $ids[] = $product->product_id;
                    }
                    $ids = implode(',',$ids);
                    $filter['id in('.$ids.')'] = NULL;
                }*/

            }

//			$this->data['products'] = $this->Product_model->get_products($filter,10);
            // $this->data['products'] = $this->Product_model->get_products_extended([], false, $query);


            /*
                    if($this->data['products'])
                    {
                        $ids = [];
                        foreach($this->data['products'] as $product) {
                            $ids[] = $product['id'];
                        }
                        $ids = implode(',',$ids);
                        $filter = [];
                        $filter['id in('.$ids.')'] = NULL;

                        $order_by = ['FIELD(id, '.$ids.')' , false];
                    }

            */


            $this->data['products'] = $this->Product_model->get_products($filter, 10, $order_by ?? []);
            $this->template->json($this->data['products']);
        } else {

            $query = (string)$this->input->get('query');
            $this->data['total_rows'] = 0;
            $this->data['products'] = [];
            $this->data['option_data'] = [];
            $this->data['attribute_data'] = [];
            $this->data['brands'] = [];
            $brand_ids = [];
            $options = [];
            $attributes = [];
            $product_ids = [];
            $product_price_list = [];



            $filter['status'] = 1;
            $filter['status!=9'] = null;

            /* if ($this->input->get('copied_product_id')) {
                //$filter['copied_product_id'] = $this->input->get('copied_product_id');
                $filtered_product = $this->Product_model->fields('copied_product_id')->filter(['id' => $this->input->get('copied_product_id')])->one();
                if ($filtered_product) {
                    $temp_product_ids = [];
                    $temp_products = $this->Product_model->fields('id')->filter(['copied_product_id' => $this->input->get('copied_product_id'), 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->all();
                    if ($temp_products) {
                        foreach ($temp_products as $temp_product) {
                            $temp_product_ids[] = $temp_product->id;
                        }
                    }

                    if ($filtered_product->copied_product_id > 0) {
                        $temp_products = $this->Product_model->filter(['copied_product_id' => $filtered_product->copied_product_id, 'status' => 1, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->all();
                        if ($temp_products) {
                            foreach ($temp_products as $temp_product) {
                                $temp_product_ids[] = $temp_product->id;
                            }
                        }
                    }

                    if ($temp_product_ids) {
                        $filter['id in(' . implode(',', $temp_product_ids) . ')'] = null;
                    } else {
                        $filter['id'] = 99999999;
                    }

                } else {
                    $filter['id'] = 99999999;
                }

            } else {
                $filter['name LIKE "%' . $query . '%"'] = NULL;
            } */

			$this->load->library('Algolia');

			$max_product_price = 0;
			
			$per_page = 20;

			$category_id = (int)$this->input->get('category_id');

			foreach ($this->data['languages'] as $key => $value) {

				$link = site_url($key . '/product/search?query=' . $query . '&category_id=' . $category_id);
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
			
			$_searchQueries = [];
			
			if ($category_id) {
				$_searchQueries['categories'] = $category_id;
			}
			
			$_max_res_search = $this->algolia->maximum('products', $query, $_searchQueries);
				
			$_catParents = [];
			
			if(isset($_GET['aaa'])){
				
			//	print_R(array_slice($_max_res_search['hits'], 0 , 3));
				
				function reCount(&$data, $_t){
					
					foreach($data as &$d){
						$d['count']++;
						if(isset($d['children']) && $d['children'])
							reCount($d['children'], $_t);
					}
					
				}
				
				function catcurs($_filtered, &$data){
					
					if(isset($_filtered[0])){
						$_t = $_filtered[0];
	
						//reCount($data, $_t);
						
						array_shift($_filtered);
				
						if(isset($_filtered[0])){
							$data[$_t]['children'] = [];
							catcurs($_filtered, $data[$_t]['children']);
						}
					}
			
				}
				
				array_map(function($val)use(&$_catParents){
				
						$_filtered = array_filter($val['categories']);
						foreach($_filtered as $filt){
							print_R($filt);
							print '-->';
						}
						
				
					//catcurs(array_filter($val['categories']), $_catParents);  
				
					
				}, array_slice($_max_res_search['hits'], 0 , 2));

				
				die;
					
			}
			
			$_catParents = [];
			
			array_map(function($val)use(&$_catParents){
				
				$_filtered = array_filter($val['categories']);
				
				foreach($_filtered as $kctx=>$ctx){
					
					if(!in_array($ctx, $_catParents[$_filtered[$kctx-1]]['children']) && $kctx-1>-1)
						$_catParents[$_filtered[$kctx-1]]['children'][$ctx]['count']++;	
					
					$_catParents[$ctx]['count']++;
					
				}
				
			}, $_max_res_search['hits']);
			
			ksort($_catParents);
			
			if($_catParents){
				$trCats = $this->Category_model->filter(['id IN(' . implode(",", array_keys($_catParents)) . ')' => null])->with_translation()->all();
				
				array_map(function($val)use(&$_catParents){
					$_catParents[$val->id]['name'] = $val->name;
				}, (array)$trCats);
			}
			
			$this->data['algolia_categories'] = $_catParents;
			
			array_map(function($val)use(&$max_product_price){
				$val['price'] = floatval($val['price']);
				if($val['price'] > $max_product_price){
					$max_product_price = $val['price'];
				}
			}, $_max_res_search['hits']);

			$_res_search = array_slice($_max_res_search['hits'], ($page-1)*$per_page, $per_page);
			
			if($_max_res_search['nbHits'])
			{

				$_ids = [];
				
				$product_ids =  array_map(function($val)use(&$_ids){ 
					$_ids[] = "'{$val['id']}'";
					return $val['id']; 
				}, $_res_search);
				
				$_ids = implode(',', $_ids);

				if($_ids)
					$filter['id IN('.$_ids.')'] = NULL;

	//			$products = $this->Product_model->get_products($filter,10); OLD
			  /*   if ($this->input->get('copied_product_id')) {
					$products = $this->Product_model->get_products($filter);
				} else {
					$products = $this->Product_model->get_products_extended([], false, $query);
				} */

				$products = $this->Product_model->get_products($filter);


				// search codes
				if ($product_ids) {
					// Get filters
					if ($this->input->method() == 'get') {

						if ($this->input->get('review')) {
							$selected_reviews = $this->input->get('review');
							$reviews = $this->Review_model->fields('AVG(rating) as avg_rating, product_id')->filter(['product_id IN(' . implode(",", $product_ids) . ')' => null])->all();
							$product_ids = [];
							if ($reviews) {
								foreach ($reviews as $review) {
									if (in_array(ceil($review->avg_rating), $selected_reviews)) {
										$product_ids[] = $review->product_id;
									}
								}
							}
						}


						if ($product_ids /*&& false*/) { // false ni sildim, bu formada idi -> ($product_ids && false)

							$order_by = ['FIELD (id, ' . implode(",", $product_ids) . ')' => false];
							$products = $this->Product_model->filter(['id IN(' . implode(",", $product_ids) . ')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null])->order_by($order_by)->all();
							$product_ids = [];

							if ($products) {
								foreach ($products as $product) {
									$price = $this->Product_model->get_price($product)['price'];
									$price = str_replace('$', '', $price);
									$price = (float)$price;

									if ($this->input->get('price_to') != NULL && $this->input->get('price_from') != NULL) {
										if ($price <= (int)str_replace(' ', '', $this->input->get('price_to')) && $price >= $this->input->get('price_from')) {
											$product_ids[] = $product->id;
										}
									} else {
										$product_ids[] = $product->id;
									}
								}
							}
						}


						if ($product_ids && $this->input->get('option')) {
							foreach ($this->input->get('option') as $option_id => $option_value_ids) {

								$products = $this->Category_model->get_additional_data('product_option_value', 'product_id', ['option_id' => $option_id, 'product_id IN(' . implode(",", $product_ids) . ')' => null, 'option_value_id IN(' . implode(",", $option_value_ids) . ')' => null]);
								$product_ids = [];
								if ($products) {
									foreach ($products as $product) {
										$product_ids[] = $product->product_id;
									}
								}

							}
						}

						if ($product_ids && $this->input->get('attribute')) {
							foreach ($this->input->get('attribute') as $attribute_id => $attribute_value_ids) {
								$products = $this->Category_model->get_additional_data('product_attribute_value', 'product_id', ['attribute_id' => $attribute_id, 'product_id IN(' . implode(",", $product_ids) . ')' => null, 'attribute_value_id IN(' . implode(",", $attribute_value_ids) . ')' => null]);
								$product_ids = [];
								if ($products) {
									foreach ($products as $product) {
										$product_ids[] = $product->product_id;
									}
								}

							}
						}

					}
				}


				if ($product_ids) {
					$ids = implode(',', $product_ids);
					$product_options = $this->Category_model->get_additional_data('product_option_value', '*', ['product_id IN(' . $ids . ')' => null]);

					if ($product_options) {
						foreach ($product_options as $product_option) {
							$options[$product_option->option_id][$product_option->option_value_id] = [
								'option_id' => $product_option->option_id,
								'option_value_id' => $product_option->option_value_id,
							];
						}
					}

					// Get Products attributes
					$product_attributes = $this->Category_model->get_additional_data('product_attribute_value', '*', ['product_id IN(' . $ids . ')' => null]);
					if ($product_attributes) {
						foreach ($product_attributes as $product_attribute) {
							$attributes[$product_attribute->attribute_id][$product_attribute->attribute_value_id] = [
								'attribute_id' => $product_attribute->attribute_id,
								'attribute_value_id' => $product_attribute->attribute_value_id,
							];
						}
					}
				}


				if ($options) {
					foreach ($options as $option_id => $option_values) {
						$option_data = $this->Option_model->filter(['id' => $option_id, 'status' => 1])->with_translation()->one();
						if ($option_data) {
							if ($option_values) {
								$value_ids = [];
								foreach ($option_values as $option_value) {
									$value_ids[] = $option_value['option_value_id'];
								}
								$general_data = $this->Category_model->get_additional_data('option_value', '*', ['id IN(' . implode(',', $value_ids) . ')' => null]);
								$translation_data = $this->Category_model->get_additional_data('option_value_description', '*', ['option_value_id IN(' . implode(',', $value_ids) . ')' => null, 'language_id' => $this->data['current_lang_id']]);

								$data = [
									'id' => $option_data->id,
									'type' => $option_data->type,
									'name' => $option_data->name,
									'values' => []
								];

								foreach ($general_data as $general) {
									$data['values'][$general->id]['image'] = $general->image;
									$data['values'][$general->id]['value'] = $general->value;
								}

								foreach ($translation_data as $translation) {
									$data['values'][$translation->option_value_id]['name'] = $translation->name;
								}
								$this->data['option_data'][] = $data;
							}
						}
					}
				}

				// Get filtered products
				if ($product_ids && !empty($product_ids)) { // added new !empty()
	//				$order_by = ['created_at' => 'ASC'];
					//$order_by = ['FIELD(id, ' . implode(",", $product_ids) . ')' => false];
				   if($_ids)
					   $order_by = ['FIELD(id, ' .$_ids. ')' => false];
					if ($this->input->get('sort')) {
						$sort = explode('_', $this->input->get('sort'));
						if (count($sort) == 2) {

							$order_by = [$sort[0] => $sort[1]];
						}
					}

					//$products = $this->Product_model->filter(['id in('.implode(",",$product_ids).')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null, 'status' => 1])->with_translation()->order_by($order_by)->limit($per_page, $page - 1)->all();

					$productss = $this->Product_model->filter(['id in(' . implode(",", $product_ids) . ')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null, 'status' => 1])->with_translation()->order_by($order_by)->all();


					if ($this->input->get('brand')) {
						$brand_id = $this->input->get('brand');
						$ids = [];
						if ($productss) {
							foreach ($productss as $product) {
								$brand_ids[] = $product->manufacturer_id;
								if (in_array($product->manufacturer_id, $brand_id)) {
									$ids[] = $product->id;
								}
							}
						}

					} else {

						$ids = [];
						if ($productss) {
							foreach ($productss as $product) {
								$brand_ids[] = $product->manufacturer_id;
								$ids[] = $product->id;
							}
						}
					}


				   /*  $this->data['total_rows'] = $this->Product_model->filter(['id in(' . implode(",", $ids) . ')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "' . date('Y-m-d') . '"))' => null, 'status' => 1])->with_translation()->count_rows(); */

					$this->data['total_rows'] = $_max_res_search['nbHits'];

					$products = $this->Product_model->filter(['id IN(' .$_ids. ')' => null])->with_translation()->order_by($order_by)->all();

					if ($products) {
						foreach ($products as $product) {

							$image = $product->image;
							if (!empty(trim($product->image))) {
								$image = $this->Model_tool_image->resize($image, 167, 167);
							}

							if (!$image) {
								$product_images = $this->Product_model->get_product_images($product->id);
								if ($product_images) {
									foreach ($product_images as $product_image) {
										if ($image) {
											break;
										} else {
											$image = $this->Model_tool_image->resize($product_image, 167, 167);
										}
									}
								}
							}

							if (!$image) {
								$image = $this->Model_tool_image->resize('nophoto.png', 167, 167);
							}
							// End image

							// Get product_price
							$product_price = $this->Product_model->get_price($product);
							$temp_currency_code = $this->currency->getCode($product->currency);
							$temp_price = $this->currency->formatter_without_symbol($product->price, $temp_currency_code, $this->data['current_currency']);
							$product_price_list[] = round($temp_price);

							$this->data['products'][] = [
								'id' => $product->id,
								'name' => $product->name,
								'slug' => $product->slug,
								'link' => site_url_multi('product/' . $product->slug),
								'image' => $image,
								'price' => $product_price['price'],
								'special_price' => $product_price['special'],
								'rating' => round($this->Product_model->get_product_review($product->id))
							];

						}
					}
				}

				$this->data['pagination'] = $this->get_pagination($this->data['total_rows'], $per_page ?? 10);


				if ($brand_ids) {
					$brands = $this->Brand_model->filter(['id IN (' . implode(",", $brand_ids) . ')' => null, 'status' => 1])->all();
					if ($brands) {
						foreach ($brands as $brand) {
							$this->data['brands'][] = [
								'id' => $brand->id,
								'name' => $brand->name
							];
						}
					}
				}

				/*if($product_price_list) {
					$this->data['price_range'] = ['min' => 0, 'max' => max($product_price_list)];
				} else {
					$this->data['price_range'] = ['min' => 0, 'max' => 100];
				}*/
			}


            $this->data['price_range'] = ['min' => 0, 'max' => round($max_product_price ?? 100)];

            if ($this->input->is_ajax_request()) {
                $this->template->json($this->data['products']);
            } else {
                $this->template->render('product_search_result');
            }
        }
    }

    public function get_product_relations($id)
    {
        $this->load->model('Relation_model');
        $response = [];
        $product_relations = $this->Product_model->get_additional_data('product_relation', '*', ['product_id' => $id]);
        if ($product_relations) {
            foreach ($product_relations as $product_relation) {
                $temp_data = [];
                // Get product relation values
                $product_relation_values = $this->Product_model->get_additional_data('product_relation_value', '*,id as product_relation_value_id', ['product_relation_id' => $product_relation->id], false, true);
                if ($product_relation_values) {
                    $relation = $this->Relation_model->filter(['id' => $product_relation->relation_id])->with_translation()->one();
                    if ($relation) {
                        $temp_data = [
                            'product_relation_id' => $product_relation->id,
                            'name' => $relation->name,
                            'relation_id' => $product_relation->relation_id,
                            'product_relation_value' => [],
                        ];

                        foreach ($product_relation_values as $key => $prv) {
                            $relation_value_description = $this->Relation_model->get_additional_data('relation_value_description', 'name', ['relation_value_id' => $prv['relation_value_id'], 'language_id' => $this->data['current_lang_id']], true);
                            if ($relation_value_description) {
                                $data = [
                                    'name' => $relation_value_description->name,
                                    'current' => $prv['current']
                                ];
                            }

                            $valid = true;
                            if ($prv['relation_id'] != 0) {
                                $relation_product = $this->Product_model->fields('name, slug')->filter(['id' => $prv['relation_id'], 'status' => 1])->with_translation()->one();
                                if ($relation_product) {
                                    $data['link'] = site_url_multi('product/' . $relation_product->slug);
                                } else {
                                    $valid = false;
                                }
                            } else {
                                $data['link'] = current_url();
                            }
                            if ($valid) {
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

    private function get_pagination($total_rows, $per_page = 20)
    {
        // Sets Pagination options and initialize
        $config['base_url'] = site_url_multi('product/search');
        $config['reuse_query_string'] = true;
//		$config['suffix'] 		= '?' . http_build_query($_GET, '', "&");

        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['full_tag_open'] = '<div class="text-center"><div class="pagination main-pagination">';
        $config['full_tag_close'] = '</div></div>';
        $config['first_link'] = '&laquo;';
        $config['first_tag_open'] = '';
        $config['first_tag_close'] = '';
        $config['last_link'] = '&raquo;';
        $config['last_tag_open'] = '';
        $config['last_tag_close'] = '';
        $config['next_link'] = '&rarr;';
        $config['next_tag_open'] = '';
        $config['next_tag_close'] = '';
        $config['prev_link'] = '&larr;';
        $config['prev_tag_open'] = '';
        $config['prev_tag_close'] = '';
        $config['cur_tag_open'] = '<a class="active">';
        $config['cur_tag_close'] = '</a>';
        $config['num_tag_open'] = '';
        $config['num_tag_close'] = '';

        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }

    public function stock_notifier()
    {
        $this->form_validation->set_rules('email', translate('email'), 'required|trim');
        $this->form_validation->set_rules('product_id', translate('product_id'), 'required|trim');

        if ($this->form_validation->run()) {
            $stock_notifier = [
                'product_id' => (int)$this->input->post('product_id'),
                'email' => (string)$this->input->post('email'),
                'notified' => 0
            ];

            $this->load->model('modules/Stock_notifier_model');
            $stock_notifier_id = $this->Stock_notifier_model->insert($stock_notifier);

            if ($stock_notifier_id) {
                $response = [
                    'success' => true,
                    'message' => translate('successfully_added')
                ];


            } else {
                $response = [
                    'success' => false,
                    'message' => translate('unsuccess')
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => validation_errors()
            ];
        }

        $this->template->json($response);
    }

}
