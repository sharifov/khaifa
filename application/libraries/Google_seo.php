<?php

class Google_seo
{
    protected   $CI;

    public      $dataLayerPosition = 0;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('modules/Brand_model');
        $this->CI->load->library('Currency');

        $this->CI->data['dataLayer']['ecommerce']['currencyCode'] = 'USD';
    }

    public function dataLayerImpressions($products, $listName)
    {
        foreach ($products as $product) {

            $price      = $this->CI->currency->convertCurrencyString($product['price']);

            $price_usd  = $this->CI->currency->convert($price['value'], 'USD', strtoupper($price['code']));

            if(isset($product['manufacturer_id']) && $product['manufacturer_id']) {
                $brand      = $this->CI->Brand_model->filter(['id' => $product['manufacturer_id'], 'status' => 1])->one();
            }
            
            $category_name = $this->CI->db->query('SELECT CT.name FROM wc_product_to_category PC
                    LEFT JOIN wc_category_translation CT ON PC.category_id=CT.category_id
                    LEFT JOIN wc_category C ON CT.category_id=C.id
                    WHERE PC.product_id='. $product['id'] .' AND CT.language_id='.$this->CI->data['current_lang_id'].' AND C.parent=0')
                ->row();

            $this->CI->data['dataLayer']['ecommerce']['impressions'][$product['id']] = [
                'id'        =>  $product['id'],
                'name'      =>  $product['name'],
                'price'     =>  round($price_usd),
                'brand'     =>  $brand->name ?? '',
                'category'  =>  $category_name->name ?? '',
                'position'  =>  $this->dataLayerPosition++,
                'list'      =>  $listName
            ];

        }
    }

}