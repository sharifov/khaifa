<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends Core_Model
{

	public $table = 'product';
	public $primary_key = 'id';
	public $protected = [];
	public $table_translation = 'product_translation';
	public $table_translation_key = 'product_id';
	public $table_language_key = 'language_id';

	public $timestamps = true;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_product_options($product_id = false) {
		if($product_id) {
			$response  = [];
			$this->db->join('option_translation', 'product_option.option_id = option_translation.option_id');
			$this->db->where('product_id' , $product_id);
			$this->db->where('language_id' , $this->data['current_lang_id']);
			$query = $this->db->get('product_option');
			if($query->num_rows() > 0) {
				$product_options = $query->result();
				foreach($product_options as $product_option) {
					$this->db->where('product_option_id' , $product_option->id);
					$query = $this->db->get('product_option_value');
					$response[] = [
						'product_option_id' => $product_option->id,
						'name'  => $product_option->name,
						'value' => $product_option->value,
						'required' => $product_option->required,
						'option_id' => $product_option->option_id,
						'option_values' => $query->result_array()
					];
				}
				return $response;
			}
		}
		return false;
	} 

	public function force_delete_product_relation_data($product_id = 0)
	{
		if($product_id > 0) {
			
		}
	}
	
	public function get_attributes($product_id)
	{
		$attributes = [];
		$this->db->select(['attribute_id', 'text']);
		$this->db->from('product_attribute');
		$this->db->join('product_attribute_translation', 'product_attribute.id=product_attribute_translation.product_attribute_id');
		$this->db->where('product_id', $product_id);
		$this->db->where('language_id', $this->data['current_lang_id']);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			if($result)
			{
				foreach($result as $row)
				{
					$this->db->select(['name']);
					$this->db->from('attribute');
					$this->db->join('attribute_translation', 'attribute.id=attribute_translation.attribute_id');
					$this->db->where('language_id', $this->data['current_lang_id']);
					$this->db->where('id', $row->attribute_id);
					$query = $this->db->get();
				
					if($query->num_rows() == 1)
					{
						$attribute_name = $query->row()->name;
					}
					else
					{
						$attribute_name = '';
					}

					$data = new stdClass();
					$data->name = $attribute_name;
					$data->value = $row->text;
					$attributes[] = $data;
				}
			}
		}

		return $attributes;
	}

	public function get_product_attributes($product_id) {
		$this->db->select('attribute_translation.name as name, attribute_value_description.name as value');
		$this->db->join('attribute_translation', 'product_attribute_value.attribute_id = attribute_translation.attribute_id', 'LEFT');
		$this->db->join('attribute_value_description', 'product_attribute_value.attribute_value_id = attribute_value_description.attribute_value_id', 'LEFT');
		$this->db->where(['product_attribute_value.product_id' => $product_id, 'attribute_translation.language_id' => $this->data['current_lang_id'], 'attribute_value_description.language_id' => $this->data['current_lang_id']]);
		$query = $this->db->get('product_attribute_value');
		if($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	public function get_top_products($limit = 0) 
	{
		$this->db->group_by('product_id');
		$this->db->order_by('product_id', 'DESC');
		$this->db->limit($limit);
		$query = $this->db->get('order_product');
		if($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	public function get_product_images($product_id) 
	{
		$this->db->where(['product_id' => $product_id]);
		$query = $this->db->get('product_images');
		if($query->num_rows() > 0) {
			return $query->result();
		}
		return false;
	}

	public function get_product_review($product_id) 
	{
		$this->db->select('AVG(rating) as rating');
		$this->db->where(['product_id' => $product_id, 'status' => 1]);
		$query = $this->db->get('review');
		if($query->num_rows() > 0) {
			return $query->row()->rating;
		}
		return 0;
	}

	public function get_product_special_price($product_id) 
	{
		$where['product_id'] = $product_id;
		$where['customer_group_id'] = get_setting('default_customer_group_id');
		$where['(date_start = "0000-00-00" or date_start is null or date_start <= "'.date('Y-m-d').'")'] = null;
		$where['(date_end = "0000-00-00" or date_end is null or date_end >= "'.date('Y-m-d').'")'] = null;
		$this->db->order_by('priority','ASC');
		$this->db->where($where);
		$query = $this->db->get('product_special');
		if($query->num_rows() > 0) {
			return $query->row()->price;
		}
		return false;
	}
	
	public function get_product_special($product_id) 
	{
		$where['product_id'] = $product_id;
		$where['customer_group_id'] = get_setting('default_customer_group_id');
		$where['(date_start = "0000-00-00" or date_start is null or date_start <= "'.date('Y-m-d').'")'] = null;
		$where['(date_end = "0000-00-00" or date_end is null or date_end >= "'.date('Y-m-d').'")'] = null;
		$this->db->order_by('priority','ASC');
		$this->db->where($where);
		$this->db->limit(1,0);
		$query = $this->db->get('product_special');
		if($query->num_rows() > 0) {
			return $query->row();
		}
		return false;
	}

	public function get_products($where = [], $limit = false, $ordering = [])
	{
		$response = [];
		if($where) {
			$this->db->join('product_translation', 'product.id = product_translation.product_id', 'LEFT');
			$where['language_id'] = $this->data['current_lang_id'];
			//$where['quantity > 0'] = null;
			//$where['((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))'] = null;
			
			$this->db->where($where);
			if($ordering) {
                $this->db->order_by($ordering[0], $ordering[1]);
            }
			else {
                $this->db->order_by("created_at", "DESC");
            }
			if($limit) {
				if(is_array($limit)) {
					$this->db->limit($limit[0], $limit[1]);
				} else {
					$this->db->limit($limit);
				}
			}
			$query = $this->db->get('product');
			if($query->num_rows() > 0) {
				foreach($query->result() as $product) {
					// Image
					$image = $product->image;
					if(!empty(trim($product->image))) {
						$image = $this->Model_tool_image->resize($image, 167, 167);
					}
					/*if(!$image) {
						$product_images = $this->get_product_images($product->id);
						if($product_images) {
							foreach($product_images as $product_image) {
								if($image) {
									break;
								} else {
									$image = $this->Model_tool_image->resize($product_image, 167, 167);
								}
							}
						}
					}*/
					if(!$image) {
						$image = $this->Model_tool_image->resize('nophoto.png', 167, 167);
					}
					// End image

					// Get price
					$price = $this->get_price($product);

					$response[] = [
						'id' 			=> $product->id,
						'name'		 	=> $product->name,
						'slug'		 	=> $product->slug,
						'tax_class_id'	=> $product->tax_class_id,
						'currency'		=> $product->currency,
						'created_by'    => $product->created_by,
						'manufacturer_id' => $product->manufacturer_id,
                        'alt_image'     => $product->alt_image,
						'link'			=> site_url_multi($product->slug),
						'image'		 	=> $image,
						'price'			=> $price['price'], //$product_price,
						'special_price' => $price['special'], //$special_price ? $this->currency->formatter($tax_price, $currency_code, $this->data['current_currency']) : false,
						'rating'	 	=> round($this->get_product_review($product->id))
					];

				}
				
			}
		}

		return $response;
	}

	public function get_products_extended($where = [], $limit = false, $query = false, $percentage = 0)
	{
		$products = $this->db
            ->select('product_id as id, name', false)
            ->from('product P')
            ->join('product_translation PT', 'PT.product_id=P.id', 'left')
            ->where(array_merge(['status' => 1], $where))
            ->limit($limit)
            ->get()
            ->result_array();

		$result = [];

		$query          = strtolower($query);

		$query_length   = strlen($query);

		$query_pieces   = explode(' ', $query);

		foreach ($products as $key => $product) {

		    $pieces             = explode(' ', strtolower($product['name']));

		    $name_length        = strlen($product['name']);

		    $name               = strtolower($product['name']);

		    $similarity_percent_summary = 0;

            $x = 0;

		    foreach ($query_pieces as $query_piece) {

                foreach ($pieces as $piece) {

                    similar_text($query_piece, $piece, $similarity_percent);

                    if($percentage > 0)  {

                        if($similarity_percent > $percentage) {

                            $similarity_percent_summary += $similarity_percent;

                        }

                    }
                    else {
                        if($similarity_percent == 100 || $similarity_percent > 80) {

                            $similarity_percent_summary += $similarity_percent * strlen($query_piece) * ($x == 0 ? 3 : 1);

                        }
                        else {
                            $similarity_percent_summary += $similarity_percent * 0;
                        }
                    }


                }

                $x++;

            }


/*            $similarity_percent_summary = 0;

		    foreach ($query_pieces as $query_piece) {
                foreach ($pieces as $piece) {

                    similar_text($query_piece, $piece, $similarity_percent);

                    if($similarity_percent > 70) {

                        $similarity_percent_summary += $similarity_percent;

                    }
                }
            }*/



		    $percent = count($query_pieces) * 50;

            if($similarity_percent_summary > $percent) {

                $product['sort']    = $similarity_percent_summary;

                $result[] = $product;

            }
        }


            usort($result, function ($array1, $array2){
		    return $array1['sort'] < $array2['sort'];
        });

            return $result;
	}

	public function get_product_count($where = [])
	{
		$this->db->join('product_translation', 'product.id = product_translation.product_id', 'LEFT');
		$where['language_id'] = $this->data['current_lang_id'];
		//$where['quantity > 0'] = null;
		$where['((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))'] = null;
		$this->db->where($where);
		$query = $this->db->get('product');
		return $query->num_rows();
		
	}

	public function get_product_image_from_gallery($product_id) 
	{

	}

	public function get_price($product)
	{
		// Calculate country group price
		$country_group_id = get_country_group_id();
		$this->db->where(['country_group_id' => $country_group_id, 'product_id' => $product->id]);
		$query = $this->db->get('product_country_group');
		$country_group_percent = 0;
		if($query->num_rows() > 0) {
			$country_group_percent  = $query->row()->percent;
		} else {
			$this->db->where(['id' => $country_group_id]);
			$country_group_query = $this->db->get('country_group');
			
			if($country_group_query->num_rows() > 0) {
				$country_group_percent  = $country_group_query->row()->percent;
			}
		}

		$product->price = (int)$product->price;

		if($country_group_percent > 0) {
			$price = (int)(($product->price * $country_group_percent) / 100);
			$product->price += $price;
		}
	

		$tax_price = $this->tax->calculate($product->price, $product->tax_class_id, get_setting('tax'));

		$special = $this->get_product_special($product->id);
		if($special && $country_group_percent > 0) {
			$special->price += (int)(($special->price * $country_group_percent) / 100);
		}

		$special_price = $special ? $this->tax->calculate($special->price, $product->tax_class_id, get_setting('tax')) : 0;
		$currency_code = $this->currency->getCode($product->currency);

		$product_price = $this->currency->formatter($special_price ? $special_price : $tax_price, $currency_code, $this->data['current_currency']);
		return [
			'price'		=> $product_price,
			'currency'	=> $currency_code,
			'special' 	=> $special_price ? $this->currency->formatter($tax_price, $currency_code, $this->data['current_currency']) : false,
			'special_date_end' => ($special) ? $special->date_end : false
		];
	}

	public function get_product_options_and_values($product) 
	{
		$product_id = $product->id;

		$response = [];
		
		$this->db->select('product_option.id as product_option_id, product_option.option_id as option_id, product_option.value, product_option.required, product_option_value.id as product_option_value_id, product_option_value.*, option.type as option_type, option_translation.name as option_name, option_value_description.name as option_value_name');
		$this->db->join("product_option_value", "product_option.id = product_option_value.product_option_id", "LEFT");
		$this->db->join("option", "product_option.option_id = option.id", "LEFT");
		$this->db->join("option_translation", "product_option.option_id = option_translation.option_id", "LEFT");
		$this->db->join("option_value_description", "product_option_value.option_value_id = option_value_description.option_value_id", "LEFT");
		
		$where = [];
		$where["product_option.product_id"] = $product_id;
		$where["option.status"] = 1;
		$where["option_translation.language_id"] = $this->data['current_lang_id'];
		$where["option_value_description.language_id"] = $this->data['current_lang_id'];
		$this->db->where($where);

		$query = $this->db->get("product_option");
		if($query->num_rows() > 0) {
			foreach($query->result() as $product_option) {
				if(array_key_exists($product_option->product_option_id, $response)) {
					$response[$product_option->product_option_id]['option_values'][] = [
						'product_option_value_id' => $product_option->product_option_value_id, 
						'option_value_id' => $product_option->option_value_id, 
						'country_group_id' => $product_option->country_group_id, 
						'name' => $product_option->option_value_name, 
						'price' => $product_option->price, 
						'option_value_price' => $this->calculate_option_value_price($product, $product_option->price),
						'price_prefix' => $product_option->price_prefix, 
						'quantity' => $product_option->quantity, 
						'subtract' => $product_option->subtract 
					];
				} else {
					$response[$product_option->product_option_id] = [
						'product_option_id' => $product_option->product_option_id,
						'option_id' 		=> $product_option->option_id,
						'name' 				=> $product_option->option_name,
						'value' 			=> $product_option->value,
						'required' 			=> $product_option->required,
						'type'				=> $product_option->option_type,
						'option_values'		=> [
							[
								'product_option_value_id' => $product_option->product_option_value_id, 
								'option_value_id' => $product_option->option_value_id, 
								'country_group_id' => $product_option->country_group_id, 
								'name' => $product_option->option_value_name, 
								'price' => $product_option->price, 
								'option_value_price' => $this->calculate_option_value_price($product, $product_option->price),
								'price_prefix' => $product_option->price_prefix, 
								'quantity' => $product_option->quantity, 
								'subtract' => $product_option->subtract 
							]
						]
					];
				}				
			}
		}

		return $response;
	}

	function calculate_option_value_price($product, $price)
	{
		$option_value_price = (double)$price;
		if($option_value_price > 0) {
			$option_value_price = $this->tax->calculate($option_value_price, $product->tax_class_id, get_setting('tax'));
			return $this->currency->formatter($option_value_price, $this->currency->getCode($product->currency), $this->data['current_currency']);
		} else {
			return false;
		}
	}

	function get_price_range_by_category($category_id) {
		
		if($category_id) {
			$category_products = $this->get_additional_data('product_to_category','*',['category_id' => $category_id]);
			if($category_products){
				foreach($category_products as $category_product) {
					$product_ids[] = $category_product->product_id;
				}

				$products = $this->filter(['id IN('.implode(",",$product_ids).')' => null, '((date_available = "0000-00-00") or (date_available is null) or (date_available <= "'.date('Y-m-d').'"))' => null])->all();
			
				$max_price = 0;
				if($products) {
					foreach($products as $product) {
						$price = (float)$this->get_price($product)['price'];
						if($max_price < $price)
						{
							$max_price = $price;
						}

						
					}
					return ['min' => 0, 'max' => ceil($max_price)];
				}
			}
			
		}	
		return false;
	}

	public function get_products_static($where)
    {
        $result = [];
		
        $products = $this->db
            ->select('P.id, PT.name, P.image, PT.slug')
            ->from('product P')
            ->join('product_translation PT', 'PT.product_id=P.id', 'left')
            ->where($where)
            ->get()
            ->result_array();
			
        foreach ($products as $product) {

            $result[] = [
                'objectID'  =>  $product['id'],
                'id'        =>  $product['id'],
                'name'      =>  $product['name'],
                'image'     =>  $product['image'] ? $this->Model_tool_image->resize($product['image'], 167, 167) : $this->Model_tool_image->resize('nophoto.png', 167, 167),
                'slug'      =>  $product['slug'],
                'links'     =>  [
                    'az'    =>  site_url('')
                ]
            ];
        }
		
		return $result;
    }

}