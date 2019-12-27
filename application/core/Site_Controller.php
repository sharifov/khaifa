<?php defined('BASEPATH') or exit('No direct script access allowed');

class Site_Controller extends Core_Controller
{
	public function __construct()
	{
		parent::__construct();

        
        // asdf

		$this->data['css'] = '';
		//$this->data['title'] = 'HomePage';
		$this->data['js'] = '';
		$this->data['title'] = '';
		$this->data['description'] = '';
		$this->data['meta_title'] = '';
		$this->data['meta_description'] = '';
		$this->data['meta_keywords'] = '';
		$this->data['current_country_id'] = get_country_id();
		$this->module_name = $this->uri->segment(2);

		$this->load->model('modules/Favorite_model');

		$this->theme = get_setting('site_theme');

		$this->data['customer'] = $this->customer->get_customer();

		if (!$this->input->is_ajax_request()) {
			if(isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'redirect=') !== false){
				$this->session->set_userdata('URI_SCRIPT', base_url().(explode('redirect=', $_SERVER['REQUEST_URI'])[1]));
			}elseif(isset($_SERVER['SCRIPT_URI']) && $_SERVER['SCRIPT_URI'] && strpos($_SERVER['REQUEST_URI'], 'facebook_login') === false && strpos($_SERVER['REQUEST_URI'], 'google_login') === false){
				$this->session->set_userdata('URI_SCRIPT', $_SERVER['SCRIPT_URI']);
			}	
		}
			

		//Sell enable disable
		$this->data['sell_enable'] = false;
		$this->load->model('modules/Country_model');
		$country = $this->Country_model->filter(['id' => get_country_id()])->one();
		if($country)
		{
			$this->data['sell_enable'] = $country->sell;
		}

		//Currency
		$this->load->model('modules/Currency_model');

		//Default currency

        if($this->data['current_country_id'] == 15) {
            $this->data['default_currency'] = 'AZN';
            $this->data['default_currency_id'] = 2;
        }
        elseif($this->data['current_country_id'] == 221) {
            $this->data['default_currency'] = 'AED';
            $this->data['default_currency_id'] = 3;
        }
        else {
            $this->data['default_currency'] = 'USD';
            $this->data['default_currency_id'] = 1;
        }

		$this->data['currencies'] = $this->Currency_model->filter(['status' => 1])->all();

		if($this->session->has_userdata('currency'))
		{
			$this->data['current_currency'] = $this->session->userdata('currency');
		}
		else
		{
			if($country)
			{
				if($country->currency)
				{
					$this->data['current_currency'] = $this->Currency_model->filter(['id' => $country->currency, 'status' => 1])->one()->code;
				}
				else
				{
					$this->data['current_currency'] = 'USD';
				}
			}
			else
			{
				$this->data['current_currency'] = 'USD';
			}
			
		}

		$this->load->library('Cart_lib');
		$this->load->library('Tax');
		$this->load->library('Weight');
		$this->load->library('Length');

		// Set tax rates
		$this->tax->setStoreAddress(15, 216);

		// Get all categories
		$this->load->model('modules/Category_model');
		$this->data['categories'] = $this->get_categories();
		$this->data['all_categories']  = $this->Category_model->filter(['status' => 1, 'parent' => 0, 'top' => 1])->with_translation()->order_by(['parent' => 'ASC', 'sort' => 'ASC'])->all();
		
		$this->data['favorite_ids'] = [];
		$this->data['faqs_for_user'] = [];
		if($this->data['customer'])
		{
			$this->data['favorite_count'] = $this->Favorite_model->filter(['customer_id' => $this->data['customer']->id])->with_trashed()->count_rows();
		

			$favorites = $this->Favorite_model->filter(['customer_id' => $this->data['customer']->id])->with_trashed()->all();
			if($favorites)
			{
				foreach($favorites as $favorite)
				{
					$this->data['favorite_ids'][] = $favorite->product_id;
				}
			}
			
			$this->load->model('modules/Faq_category_model');
			$this->data['faqs_for_user'] = $this->Faq_category_model->filter(['status' => 1])->with_translation()->all();
		
		}

		// Discount ended
		if(true) {

            $discounted_products = $this->db
                ->query("SELECT GROUP_CONCAT(products) as products FROM `wc_discounts` WHERE fake=1 AND (start_date !='0000-00-00' AND end_date !='0000-00-00') AND end_date < DATE_FORMAT(NOW(), '%Y-%m-%d') AND deleted_at IS NULL")
                ->result_array();

            if(isset($discounted_products[0]['products'])) {

                $discounted_products = $discounted_products[0]['products'];

                $this->db->query("UPDATE wc_product SET price=old_price, old_price=NULL WHERE id IN ($discounted_products) AND old_price IS NOT NULL;");
            }
		}




        /*
        SET @products = (SELECT products FROM `wc_discounts`
        WHERE
        (start_date !='0000-00-00' AND end_date !='0000-00-00') AND
        end_date < NOW() AND deleted_at IS NULL);

        SELECT price, old_price FROM wc_product WHERE id IN (@products) AND old_price IS NOT NULL;

        */

        $this->data['dataLayerPosition'] = 0;

		
	}

	public function get_categories() {
        $categories = $this->Category_model->fields('id, name, slug')->filter(['status' => 1, 'parent' => 0, 'top' => 1])->with_translation()->order_by(['sort' => 'ASC'])->as_array()->all();
				$i=0;
				if($categories) {
					foreach($categories as $category) {
							$categories[$i]['sub_categories'] = $this->sub_categories($category['id']);
							$i++;
					}
				}
        return $categories;
    }

    public function sub_categories($id) {
		$categories = $this->Category_model->fields('id, name, slug')->filter(['status' => 1, 'parent' => $id, 'top' => 1])->with_translation()->order_by(['sort' => 'ASC'])->as_array()->all();
		$i=0;
		if($categories)
		{
			foreach($categories as $category) {
				$categories[$i]['sub_categories'] = $this->sub_categories($category['id']);
				$i++;
			}
			return $categories;   
		} else {
			return [];
		}
        
        
    }
}
