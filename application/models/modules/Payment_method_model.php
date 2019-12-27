<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_method_model extends Core_Model
{

    public $table = 'payment_method';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'payment_method_translation';
    public $table_translation_key = 'payment_method_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function availablePaymentMethods()
    {
        $paymentMethods = $this->Payment_method_model
            ->filter(['status' => 1, 'FIND_IN_SET("'. $this->data['current_country_id'] .'", countries)' => null])
            ->with_translation()
            ->order_by('sort', 'ASC')
            ->all();

        return $paymentMethods;

    }
}