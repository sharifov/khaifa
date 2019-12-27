<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->model('Review_model');
		$this->load->model('Product_special_model');
		$this->load->model('modules/Category_model');

	}

	public function index($slug = false, $page = 1, $seller = false) 
	{
        foreach($this->data['languages'] as $key => $value)
        {	
            
            $this->data['languages'][$key] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'code' => $value['code'],
                'slug' => $value['slug'],
                'admin' => $value['admin'],
                'directory' => $value['directory'],
                'dir' => $value['dir'],
                'link' => site_url($key.'/products/'.$slug)
            ];				
        }

		if(!empty($slug)) {
            $this->data['products'] = [];
            $products = [];
			$this->data['option_data'] = [];
			$this->data['attribute_data'] = [];
			$this->data['brands'] = [];
            $this->data['price_range'] = '';

            if($slug == 'new_products') {
                $products = $this->Product_model->fields('*')->filter(['status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                $this->data['total_rows'] = $this->Product_model->filter(['status' => 1])->order_by('created_at', 'DESC')->with_translation()->count_rows();
            } else if($slug == 'top_products') {
                $product_ids = $this->Product_model->get_top_products(10);
                $tp_ids = [];
                if($product_ids) {
                    foreach($product_ids as $product) {
                        $tp_ids[] = $product->product_id;
                    }
                    $products = $this->Product_model->fields('*')->filter(['id IN ('.implode(",",$tp_ids).')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                    $this->data['total_rows'] = $this->Product_model->filter(['id IN ('.implode(",",$tp_ids).')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->count_rows();
                }
            } else if($slug == 'recently_viewed') {
                $recently_viewed = $this->session->userdata('recently_viewed');
                if($recently_viewed) {
                    $products = $this->Product_model->fields('*')->filter(['id IN('.implode(",",$recently_viewed).')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                    $this->data['total_rows'] = $this->Product_model->filter(['id IN('.implode(",",$recently_viewed).')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->count_rows();
                }
            } else if($slug == 'featured_1') {
                $featured_products_1 = $this->Product_model->get_additional_data('featured_product','id, percent, products, start_date, expired_date',['type' => 'featured_1', 'status' => 1], true);
                if($featured_products_1 && !empty($featured_products_1->products)) {
                    if($featured_products_1->start_date == null || ($featured_products_1->start_date < date('Y-m-d H:i:s') && $featured_products_1->expired_date > date('Y-m-d H:i:s'))) {
                        $product_ids = $featured_products_1->products;
                        $products = $this->Product_model->fields('*')->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                        $this->data['total_rows'] = $this->Product_model->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->with_translation()->count_rows();
                    }
                }
            } else if($slug == 'featured_2') {
                $featured_products_2 = $this->Product_model->get_additional_data('featured_product','id, percent, products, start_date, expired_date',['type' => 'featured_2', 'status' => 1], true);
                if($featured_products_2 && !empty($featured_products_2->products)) {
                    if($featured_products_2->start_date == null || ($featured_products_2->start_date < date('Y-m-d H:i:s') && $featured_products_2->expired_date > date('Y-m-d H:i:s'))) {
                        $product_ids = $featured_products_2->products;
                        $products = $this->Product_model->fields('*')->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                        $this->data['total_rows'] = $this->Product_model->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->with_translation()->count_rows();
                    }
                }
            } else if($slug == 'sale') {
                                
                $product_special = $this->Product_model->get_additional_data('product_special','DISTINCT(product_id) as product_id',['(date_start = "0000-00-00" or date_start <= "'.date('Y-m-d').'")' => null , '(date_end = "0000-00-00" or date_end >= "'.date('Y-m-d').'")' => null]);


                if($product_special) {
                    foreach($product_special as $row) {
                        $ids[] = $row->product_id;
                    }
                    $product_ids = implode(",",$ids);
                    $products = $this->Product_model->fields('*')->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                    $this->data['total_rows'] = $this->Product_model->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->with_translation()->count_rows();
                }
            } else if($slug == 'seller') {        
                $this->data['title'] = get_seller($this->input->get('id'));
                $product_seller = $this->Product_model->filter(['created_by' => $this->input->get('id')])->all();
                if($product_seller) {
                    foreach($product_seller as $row) {
                        $ids[] = $row->id;
                    }
                    $product_ids = implode(",",$ids);
                    $products = $this->Product_model->fields('*')->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                    $this->data['total_rows'] = $this->Product_model->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->with_translation()->count_rows();
                }
            }  else if($slug == 'brand') {                
                $product_seller = $this->Product_model->filter(['manufacturer_id' => $this->input->get('id')])->all();
                if($product_seller) {
                    foreach($product_seller as $row) {
                        $ids[] = $row->id;
                    }
                    $product_ids = implode(",",$ids);
                    $products = $this->Product_model->fields('*')->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                    $this->data['total_rows'] = $this->Product_model->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->with_translation()->count_rows();
                }
            }

            else if($slug == 'old') {

                $products = $this->Product_model->fields('*')->filter(['status' => 9])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                $this->data['total_rows'] = $this->Product_model->filter(['status' => 9])->with_translation()->count_rows();
            }


            else {
                $category = $this->Category_model->fields('id, name')->filter(['slug' => $slug, 'status' => 1])->with_translation()->one();
                if($category) {
                    $category_products = $this->Product_model->get_additional_data('featured_product','id, percent, products, start_date, expired_date',['type' => 'category', 'category_id' => $category->id, 'status' => 1], true);
                    if($category_products && !empty($category_products->products)) {
                        if($category_products->start_date == null || ($category_products->start_date < date('Y-m-d H:i:s') && $category_products->expired_date > date('Y-m-d H:i:s'))) {
                            $product_ids = $category_products->products;
                            $products = $this->Product_model->fields('*')->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->order_by('created_at', 'DESC')->with_translation()->limit(20, $page - 1)->all();
                            $this->data['total_rows'] = $this->Product_model->filter(['id IN('.$product_ids.')' => null, 'status' => 1])->with_translation()->count_rows();
                        }
                    }   
                }
            }
            
            if($products) {
                foreach($products as $product) {
                    $image = $product->image;
                    if(!empty(trim($product->image))) {
                        $image = $this->Model_tool_image->resize($image, 167, 167);
                    }
					
                    if(!$image/* && $slug != 'old'*/) {
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
                    /*
                    @$old_image = explode('.', $product->image);

                    @$old_image = $old_image[0] . '-190x190.' . $old_image[1];

//                    $old_image = base_url('uploads/old/' . $old_image);

                    if( ! file_exists(dirname(__DIR__, 2) . '/uploads/old/' . $old_image) ) {

                        $old_image = $this->Model_tool_image->resize('nophoto.png', 167, 167);

                    }
                    else {
                        $old_image = base_url('uploads/old/' . $old_image);
                    }*/
                    
                    $this->data['products'][] = [
                        'id' 	        => $product->id,
                        'name'		 	=> $product->name,
                        'slug'		 	=> $product->slug,
                        'link'			=> site_url_multi('product/'.$product->slug),
                        'alt_image'	    => $product->alt_image,
                        'image'		 	=> /*$slug == 'old' ?  $old_image : */$image,
                        'price'			=> $slug == 'old' ? translate('out_of_stock', true) : $product_price['price'],
                        'special_price' => $product_price['special'],
                        'rating'	 	=> round($this->Product_model->get_product_review($product->id))
                    ];
                }
            } else {
                $this->data['products'] = [];
            }
            
            $this->data['pagination'] = $this->get_pagination($slug,$this->data['total_rows']);
            $this->template->render('products');
		} else {
            show_404();
        }
	}

    private function get_pagination($slug, $total_rows, $per_page = 20) 
    {
		// Sets Pagination options and initialize
		$config['base_url'] = site_url_multi('products/').$slug;
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