<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->model('Review_model');
		$this->load->model('Option_model');
		$this->load->model('Attribute_model');
		$this->load->model('Product_special_model');
		$this->load->model('modules/Category_model');
		$this->load->model('modules/Brand_model');
	}

	
	public function index($slug = false, $page = 1) 
	{
		if($slug) {
		    
            redirect(site_url_multi($slug), 'auto', 301);
            die;

			$slug = urldecode($slug);
			$this->data['category'] = $this->Category_model->filter(['slug' => trim($slug), 'status' => 1])->with_translation()->one();

			

			if($this->data['category']) {

				//Language Link
				foreach($this->data['languages'] as $key => $value)
				{	
					$temp_slug = $this->Category_model->filter(['id' => $this->data['category']->id, 'status' => 1])->with_translation($value['id'])->one();
					$link = ($temp_slug) ? site_url($key.'/category/'.$temp_slug->slug) : "/";
					
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
					
					if($product_ids) {		
						// Get filters
						if($this->input->method() == 'get') {
							if($this->input->get('review')) {
								$selected_reviews = $this->input->get('review');
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
										if($this->input->get('price_to') != NULL && $this->input->get('price_from') != NULL)
										{
											if($price <= (int)str_replace(' ', '', $this->input->get('price_to')) && $price >= $this->input->get('price_from'))
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

							if($product_ids && $this->input->get('option')) {
								foreach($this->input->get('option') as $option_id => $option_value_ids) {
									
									$products = $this->Category_model->get_additional_data('product_option_value','product_id',['option_id' => $option_id, 'product_id IN('.implode(",",$product_ids).')' => null, 'option_value_id IN('.implode(",",$option_value_ids).')' => null]);
									$product_ids = [];
									if($products) {
										foreach($products as $product) {
											$product_ids[] = $product->product_id;
										}
									}
									
								}
							}

							if($product_ids && $this->input->get('attribute')) {
								foreach($this->input->get('attribute') as $attribute_id => $attribute_value_ids) {
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
						if($this->input->get('sort')) {
							$sort = explode('_',$this->input->get('sort'));
							if(count($sort) == 2) {
								
								$order_by = [$sort[0] => $sort[1]];
							}
						}
						$per_page = ($this->input->get('per_page')) ? (int)$this->input->get('per_page') : 20;
						$products = $this->Product_model->filter(['id in('.implode(",",$product_ids).')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null, 'status' => 1])->with_translation()->order_by($order_by)->limit($per_page, $page - 1)->all();
						$this->data['total_rows'] = $this->Product_model->filter(['id in('.$ids.')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null, 'status' => 1])->with_translation()->count_rows();
						if($products) {
							foreach($products as $product) {
								if(($this->input->get('brand') && in_array($product->manufacturer_id,$this->input->get('brand'))) || !$this->input->get('brand')){
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
										'id' 			=> $product->id,
										'name'		 	=> $product->name,
										'alt_image'		=> isset($product->alt_image)?$product->alt_image:$product->name,
										'slug'		 	=> $product->slug,
										'link'			=> site_url_multi('product/'.$product->slug),
										'image'		 	=> $image,
										'price'			=> $product_price['price'],
										'special_price' => $product_price['special'],
										'rating'	 	=> round($this->Product_model->get_product_review($product->id))
									];

								}
								
							}

							$this->data['pagination'] = $this->get_pagination($slug,$this->data['total_rows'], $per_page);

						}
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
						$this->data['price_range'] = ['min' => 0, 'max' => max($product_price_list)];
					} else {
						$this->data['price_range'] = ['min' => 0, 'max' => 100];
					}


					
					$this->template->render('category');
				}
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}

	public function view($slug = false) 
	{
		if(!empty($slug))
		{
			$slugs = ['top_products','recently_viewed','new_products'];
			if(in_array($slug, $slugs)) 
			{
				if($slug == 'new_products') {
					
				}
			}
		}
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

	private function get_pagination($slug, $total_rows, $per_page = 20) 
	{
		// Sets Pagination options and initialize
		$config['base_url'] = site_url_multi('category/').$slug;
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
}