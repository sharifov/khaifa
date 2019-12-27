<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Favorite extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('modules/Favorite_model');
		$this->load->model('Product_model');
		$this->load->model('Review_model');
		if(!$this->data['customer'])
		{
			redirect(site_url_multi('/'));
		}
	}

	public function index($page = 1)
	{
		$this->data['title'] = translate('title');
		$this->breadcrumbs->push(translate('title'), 'favorite');

		$customer_id = $this->data['customer']->id;

		$favorites = $this->Favorite_model->filter(['customer_id' => $customer_id])->with_trashed()->all();
		
		$this->data['products'] = [];
		
		if($favorites)
		{
			foreach($favorites as $favorite)
			{
				$favorite_ids[] = $favorite->product_id;
			}
			
			$products = $this->Product_model->filter(['id IN ('.implode(',', $favorite_ids).')' => NULL ])->with_translation()->order_by('created_at', 'DESC')->limit(12, $page-1)->all();
			$this->data['total_rows'] = $this->Product_model->filter(['id IN ('.implode(',', $favorite_ids).')' => NULL ])->with_translation()->count_rows();
			if($products)
			{
				foreach($products as $product)
				{

					$reviews = $this->Review_model->filter(['product_id' => $product->id, 'status' => 1])->all();
					$review_count = $this->Review_model->filter(['product_id' => $product->id, 'status' => 1])->count_rows();
					$total_review = 0;
					if($reviews) {
						foreach($reviews as $review) {
							$total_review += $review->rating;
						}
					}
					

					$data = new stdClass();
					$data->id = $product->id;
					$data->name = $product->name;
				
					$data->price = $this->Product_model->get_price($product)['price'];
					
					$data->link = site_url_multi('product/'.$product->slug);
					$image = $this->Model_tool_image->resize($product->image, 167, 167);
					if(!$image) {
						$image = $this->Model_tool_image->resize('nophoto.png', 167, 167);
                    }
					$data->image = $image;
					$data->rating = ($total_review > 0) ? ceil($total_review/$review_count) : 0;

					$this->data['products'][] = $data; 
				}

				// Sets Pagination options and initialize
				$config['base_url'] = site_url_multi('favorite/index');
				$config['total_rows'] = $this->data['total_rows'];
				$config['per_page'] = 12;
				$config['full_tag_open']            = '<div class="text-center"><div class="pagination main-pagination text-center">';
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
				$this->data['pagination'] = $this->pagination->create_links();
			}

		}

		$this->template->render('favorite');
	}
	
	public function add()
	{
		$response = ['success' => false, 'type'	=> 'other', "message" => ""];

		if($this->input->method() == 'post') 
		{
			$this->form_validation->set_rules('product_id', 'product_id', 'is_natural_no_zero');
			if($this->form_validation->run())
			{
				$id = $this->input->post('product_id');
				if($this->auth->is_loggedin())
				{
					$favorite = [
						'product_id'	=> $id,
						'customer_id'	=> $this->data['customer']->id

					];

					$row = $this->Favorite_model->filter($favorite)->with_trashed()->one();
					
					if($row) {
						$this->Favorite_model->force_delete($favorite);
						$response = [
							'success' 	=> true,
							'type'		=> 'deleted',
							'message'	=> translate('successfully_removed_from_favorite'),
							'count'		=> $this->Favorite_model->filter(['customer_id' => $this->data['customer']->id])->with_trashed()->count_rows()
						];
					} else {
						$this->Favorite_model->insert($favorite);
						$response = [
							'success' 	=> true,
							'type'		=> 'inserted',
							'message'	=> translate('successfully_added_to_favorite'),
							'count'		=> $this->Favorite_model->filter(['customer_id' => $this->data['customer']->id])->with_trashed()->count_rows()
						];
					}
				}
				else
				{
					$response = [
						'success' 	=> false,
						'type' 		=> 'login',
						'message'	=> translate('please_login')
					];
				}
			}
			else
			{
				$response = [
					'success' 	=> false,
					'type'		=> 'other',
					'message'	=> translate('product_id_not_found')
				];
			}

		}

		$this->template->json($response);
	}

	public function remove()
	{
		if($this->input->method() == 'post')
		{
			$this->form_validation->set_rules('product_id', 'product_id', 'required|trim');
			
			if($this->form_validation->run())
			{
				$product_id = (int)$this->input->post('product_id');
				$customer_id = $this->data['customer']->id;
				$this->Favorite_model->force_delete(['product_id' => $product_id, 'customer_id' => $customer_id]);

				$this->data['response'] = [
					'success' => true,
					'message' => translate('favorite_successfully_deleted')
				];
			}
			else
			{
				$this->data['response'] = [
					'success' => false,
					'message' => validation_errors()
				];
			}
		}
		else
		{
			$this->data['response'] = [
				'success' => false,
				'message' => translate('only_post_request', true)
			];
		}

		$this->template->json($this->data['response']);
	}
}