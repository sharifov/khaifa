<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends Administrator_Controller
{
	public $time_zones;
	public $date_format;
	public $time_format;

	public function __construct()
	{
		parent::__construct();
		$this->time_zones = $this->config->item("time_zones");
		$this->date_format = $this->config->item("date_format");
		$this->time_format = $this->config->item("time_format");
	}

	/**
	 * public function index() - Here, index function plays role as edit
	 * Gets all setting records from database and fills proper fields. Sets form fields for data update (and buttons, breadcrumb links). Also cathces submitted form, validates and performs update operation.
	 */
	public function index()
	{
		$this->data['title'] = translate('index_title');
		$this->data['subtitle'] = translate('index_description');

		$this->data['buttons'][] = [
			'type' => 'button',
			'text' => translate('form_button_save', 1),
			'class' => 'btn btn-primary btn-labeled heading-btn',
			'id' => 'save',
			'icon' => 'icon-floppy-disk',
			'additional' => [
				'onclick' => "confirm('Are you sure?') ? $('#form-save').submit() : false;",
				'form' => 'form-save',
				'formaction' => current_url()
			]
		];

		$this->load->model('modules/Currency_model');
		$currencies = $this->Currency_model->filter(['status' => 1])->all();

		if($currencies)
		{
			foreach($currencies as $currency)
			{
				$currency_options[$currency->code] = $currency->name;
			}
		}

		$this->load->model('modules/Payment_method_model');
		$payment_methods = $this->Payment_method_model->filter(['status' => 1])->with_translation()->all();

		if($payment_methods)
		{
			foreach($payment_methods as $payment_method)
			{
				$payment_method_options[$payment_method->code] = $payment_method->name;
			}
		}

		$this->load->model('modules/Order_status_model');
		$order_statuses = $this->Order_status_model->with_translation()->all();

		if($order_statuses)
		{
			foreach($order_statuses as $order_status)
			{
				$order_status_options[$order_status->id] = $order_status->name;
			}
		}

		$this->load->model('modules/Stock_status_model');
		$stock_statuses = $this->Stock_status_model->with_translation()->all();

		if($stock_statuses)
		{
			foreach($stock_statuses as $stock_status)
			{
				$stock_status_options[$stock_status->id] = $stock_status->name;
			}
		}

		
		$this->data['tabs'] = [
			'general' => [
				'icon' => 'icon-menu7',
				'label' => translate('tab_general'),
				'active' => true,
				'fields' => [
					'default_seller_name' => [
						'property' => 'text',
						'name' => 'default_seller_name',
						'class' => 'form-control',
						'value' => (set_value('default_seller_name')) ? set_value('default_seller_name') : get_setting('default_seller_name'),
						'label' => translate('default_seller_name'),
						'placeholder' => translate('default_seller_name_placeholder'),
						'validation' => ['rules' => '']
					],
					'time_zone' => [
						'property' => 'dropdown',
						'name' => 'time_zone',
						'id' => 'time_zone',
						'label' => translate('time_zone'),
						'class' => 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'options' => [
							"0" => translate('time_zone_select'),
							$this->time_zones
						],
						'selected' => (set_value('time_zone')) ? set_value('time_zone') : get_setting('time_zone'),
						'validation' => ['rules' => '']
					],
					'date_format' => [
						'property' => 'dropdown',
						'name' => 'date_format',
						'id' => 'date_format',
						'label' => translate('date_format'),
						'class' => 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'options' => [
							"0" => translate('date_format_select'),
							$this->date_format
						],
						'selected' => (set_value('date_format')) ? set_value('date_format') : get_setting('date_format'),
						'validation' => ['rules' => '']
					],
					'time_format' => [
						'property' => 'dropdown',
						'name' => 'time_format',
						'id' => 'time_format',
						'label' => translate('time_format'),
						'class' => 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'options' => [
							"0" => translate('time_format_select'),
							$this->time_format
						],
						'selected' => (set_value('time_format')) ? set_value('time_format') : get_setting('time_format'),
						'validation' => ['rules' => '']
					],
					'gl_analytic_code' => [
						'property' => 'textarea',
						'name' => 'gl_analytic_code',
						'class' => 'form-control',
						'value' => (set_value('gl_analytic_code')) ? set_value('gl_analytic_code') : get_setting('gl_analytic_code'),
						'label' => translate('gl_analytic_code'),
						'placeholder' => translate('gl_analytic_code_placeholder'),
						'validation' => ['rules' => ''],
						'icon' => 'icon-pin',
						'xss_filtering' => false
					],
					'custom_js' => [
						'property' => 'textarea',
						'name' => 'custom_js',
						'class' => 'form-control',
						'value' => (set_value('custom_js')) ? set_value('custom_js') : get_setting('custom_js'),
						'label' => translate('custom_js'),
						'placeholder' => translate('custom_js_placeholder'),
						'validation' => ['rules' => ''],
						'icon' => 'icon-pin',
						'xss_filtering' => false
					],
					'site_title' => [
						'property' => 'text',
						'name' => 'site_title',
						'class' => 'form-control',
						'value' => (set_value('site_title')) ? set_value('site_title') : get_setting('site_title'),
						'label' => translate('site_title'),
						'placeholder' => translate('site_title_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'site_description' => [
						'property' => 'text',
						'name' => 'site_description',
						'class' => 'form-control',
						'value' => (set_value('site_description')) ? set_value('site_description') : get_setting('site_description'),
						'label' => translate('site_description'),
						'placeholder' => translate('site_description_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'meta_title' => [
						'property' => 'text',
						'name' => 'meta_title',
						'class' => 'form-control',
						'value' => (set_value('meta_title')) ? set_value('meta_title') : get_setting('meta_title'),
						'label' => translate('meta_title'),
						'placeholder' => translate('meta_title_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
                    'meta_keywords' => [
                        'property' => 'text',
                        'name' => 'meta_keywords',
                        'class' => 'form-control',
                        'value' => (set_value('meta_keywords')) ? set_value('meta_keywords') : get_setting('meta_keywords'),
                        'label' => translate('meta_keywords'),
                        'placeholder' => translate('meta_keywords_placeholder'),
                        'validation' => ['rules' => ''],
                        'translate' => true
                    ],
					'meta_description' => [
						'property' => 'text',
						'name' => 'meta_description',
						'class' => 'form-control',
						'value' => (set_value('meta_description')) ? set_value('meta_description') : get_setting('meta_description'),
						'label' => translate('meta_description'),
						'placeholder' => translate('meta_description_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],

				]
			],
			'filemanager' => [
				'icon' => 'icon-box',
				'label' => translate('tab_filemanager'),
				'fields' => [
					'permitted_file' => [
						'property' => 'text',
						'name' => 'permitted_file',
						'class' => 'form-control',
						'value' => (set_value('permitted_file')) ? set_value('permitted_file') : get_setting('permitted_file'),
						'label' => translate('filemanager_permitted_file'),
						'placeholder' => translate('filemanager_permitted_file_placeholder'),
						'validation' => ['rules' => '']
					]
				]
			],
			'shipping' => [
				'icon' => 'icon-truck',
				'label' => translate('tab_shipping'),
				'fields' => [
					'free_shipping_price' => [
						'property' => 'text',
						'name' => 'free_shipping_price',
						'class' => 'form-control',
						'value' => (set_value('free_shipping_price')) ? set_value('free_shipping_price') : get_setting('free_shipping_price'),
						'label' => translate('free_shipping_price'),
						'placeholder' => 0,
						'validation' => ['rules' => '']
					],
					'free_shipping_price_vendor' => [
						'property' => 'text',
						'name' => 'free_shipping_price_vendor',
						'class' => 'form-control',
						'value' => (set_value('free_shipping_price_vendor')) ? set_value('free_shipping_price_vendor') : get_setting('free_shipping_price_vendor'),
						'label' => translate('free_shipping_price_vendor'),
						'placeholder' => 0,
						'validation' => ['rules' => '']
					]
				]
			],
			'order_status' => [
				'icon' => 'icon-coin-dollar',
				'label' => translate('tab_order_status'),
				'fields' => [
					'order_status_id' => [
						'property' => 'dropdown',
						'name' => 'order_status_id',
						'class' => 'form-control',
						'options' => $order_status_options,
						'selected' => (set_value('order_status_id')) ? set_value('order_status_id') : get_setting('order_status_id'),
						'label' => translate('order_status_id'),
						'validation' => ['rules' => '']
					],
					'complete_order_status_id' => [
						'property' => 'dropdown',
						'name' => 'complete_order_status_id',
						'class' => 'form-control',
						'options' => $order_status_options,
						'selected' => (set_value('complete_order_status_id')) ? set_value('complete_order_status_id') : get_setting('complete_order_status_id'),
						'label' => translate('complete_order_status_id'),
						'validation' => ['rules' => '']
					],
					'processing_order_status_id' => [
						'property' => 'dropdown',
						'name' => 'processing_order_status_id',
						'class' => 'form-control',
						'options' => $order_status_options,
						'selected' => (set_value('processing_order_status_id')) ? set_value('processing_order_status_id') : get_setting('processing_order_status_id'),
						'label' => translate('processing_order_status_id'),
						'validation' => ['rules' => '']
					],
					'stock_limit' => [
						'property' => 'text',
						'name' => 'stock_limit',
						'class' => 'form-control',
						'value' => (set_value('stock_limit')) ? set_value('stock_limit') : get_setting('stock_limit'),
						'label' => translate('stock_limit'),
						'placeholder' => 0,
						'validation' => ['rules' => ''],
					],
				]
			],
			'currency' => [
				'icon' => 'icon-coin-dollar',
				'label' => translate('tab_currency'),
				'fields' => [
                    'paypal_email' => [
                        'property' => 'text',
                        'name' => 'paypal_email',
                        'icon' => 'icon-paypal',
                        'class' => 'form-control',
                        'value' => (set_value('paypal_email')) ? set_value('paypal_email') : get_setting('paypal_email'),
                        'label' => 'Paypal email',
                        'placeholder' => 'Paypal email',
                        'validation' => ['rules' => '']
                    ],
					'currency' => [
						'property' => 'dropdown',
						'name' => 'currency',
						'class' => 'form-control',
						'options' => $currency_options,
						'selected' => (set_value('currency')) ? set_value('currency') : get_setting('currency'),
						'label' => translate('currency'),
						'validation' => ['rules' => '']
					],
					'default_payment_method' => [
						'property' => 'dropdown',
						'name' => 'default_payment_method',
						'class' => 'form-control',
						'options' => $payment_method_options,
						'selected' => (set_value('default_payment_method')) ? set_value('default_payment_method') : get_setting('default_payment_method'),
						'label' => translate('default_payment_method'),
						'validation' => ['rules' => '']
					],
					'currency_auto' => [
						'property' => 'status',
						'name' => 'currency_auto',
						'class' => 'form-control',
						'selected' => (set_value('currency_auto')) ? set_value('currency_auto') : get_setting('currency_auto'),
						'label' => translate('currency_auto'),
						'validation' => ['rules' => '']
					]
				]
			],
			'social' => [
				'icon' => 'icon-facebook2',
				'label' => translate('tab_social'),
				'fields' => [
					'facebook' => [
						'property' => 'text',
						'name' => 'facebook',
						'icon' => 'icon-facebook',
						'class' => 'form-control',
						'value' => (set_value('facebook')) ? set_value('facebook') : get_setting('facebook'),
						'label' => translate('facebook'),
						'placeholder' => translate('facebook_placeholder'),
						'validation' => ['rules' => '']
					],
					'twitter' => [
						'property' => 'text',
						'name' => 'twitter',
						'icon' => 'icon-twitter',
						'class' => 'form-control',
						'value' => (set_value('twitter')) ? set_value('twitter') : get_setting('twitter'),
						'label' => translate('twitter'),
						'placeholder' => translate('twitter_placeholder'),
						'validation' => ['rules' => '']
					],
					'whatsapp' => [
						'property' => 'text',
						'name' => 'whatsapp',
						'icon' => 'fa fa-whatsapp',
						'class' => 'form-control',
						'value' => (set_value('whatsapp')) ? set_value('whatsapp') : get_setting('whatsapp'),
						'label' => translate('whatsapp'),
						'placeholder' => translate('whatsapp_placeholder'),
						'validation' => ['rules' => '']
					],
					'instagram' => [
						'property' => 'text',
						'name' => 'instagram',
						'icon' => 'icon-instagram',
						'class' => 'form-control',
						'value' => (set_value('instagram')) ? set_value('instagram') : get_setting('instagram'),
						'label' => translate('instagram'),
						'placeholder' => translate('instagram_placeholder'),
						'validation' => ['rules' => '']
					],
					'linkedin' => [
						'property' => 'text',
						'name' => 'linkedin',
						'icon' => 'icon-linkedin',
						'class' => 'form-control',
						'value' => (set_value('linkedin')) ? set_value('linkedin') : get_setting('linkedin'),
						'label' => translate('linkedin'),
						'placeholder' => translate('linkedin_placeholder'),
						'validation' => ['rules' => '']
					],
					'googleplus' => [
						'property' => 'text',
						'name' => 'googleplus',
						'icon' => 'icon-google-plus',
						'class' => 'form-control',
						'value' => (set_value('googleplus')) ? set_value('googleplus') : get_setting('googleplus'),
						'label' => translate('googleplus'),
						'placeholder' => translate('googleplus_placeholder'),
						'validation' => ['rules' => '']
					],
					'youtube' => [
						'property' => 'text',
						'name' => 'youtube',
						'icon' => 'icon-youtube',
						'class' => 'form-control',
						'value' => (set_value('youtube')) ? set_value('youtube') : get_setting('youtube'),
						'label' => translate('youtube'),
						'placeholder' => translate('youtube_placeholder'),
						'validation' => ['rules' => '']
					],
					'github' => [
						'property' => 'text',
						'name' => 'github',
						'icon' => 'icon-github',
						'class' => 'form-control',
						'value' => (set_value('github')) ? set_value('github') : get_setting('github'),
						'label' => translate('github'),
						'placeholder' => translate('github_placeholder'),
						'validation' => ['rules' => '']
					],
					'vimeo' => [
						'property' => 'text',
						'name' => 'vimeo',
						'icon' => 'icon-vimeo',
						'class' => 'form-control',
						'value' => (set_value('vimeo')) ? set_value('vimeo') : get_setting('vimeo'),
						'label' => translate('vimeo'),
						'placeholder' => translate('vimeo_placeholder'),
						'validation' => ['rules' => '']
					],
					'flickr' => [
						'property' => 'text',
						'name' => 'flickr',
						'icon' => 'icon-flickr',
						'class' => 'form-control',
						'value' => (set_value('flickr')) ? set_value('flickr') : get_setting('flickr'),
						'label' => translate('flickr'),
						'placeholder' => translate('flickr_placeholder'),
						'validation' => ['rules' => '']
					],
					'rss' => [
						'property' => 'text',
						'name' => 'rss',
						'icon' => 'icon-feed2',
						'class' => 'form-control',
						'value' => (set_value('rss')) ? set_value('rss') : get_setting('rss'),
						'label' => translate('rss'),
						'placeholder' => translate('rss_placeholder'),
						'validation' => ['rules' => '']
					],
					'wordpress' => [
						'property' => 'text',
						'name' => 'wordpress',
						'icon' => 'icon-wordpress',
						'class' => 'form-control',
						'value' => (set_value('wordpress')) ? set_value('wordpress') : get_setting('wordpress'),
						'label' => translate('wordpress'),
						'placeholder' => translate('wordpress_placeholder'),
						'validation' => ['rules' => '']
					],
					'dribbble' => [
						'property' => 'text',
						'name' => 'dribbble',
						'icon' => 'icon-dribbble',
						'class' => 'form-control',
						'value' => (set_value('dribbble')) ? set_value('dribbble') : get_setting('dribbble'),
						'label' => translate('dribbble'),
						'placeholder' => translate('dribbble_placeholder'),
						'validation' => ['rules' => '']
					],
					'blogger' => [
						'property' => 'text',
						'name' => 'blogger',
						'icon' => 'icon-blogger',
						'class' => 'form-control',
						'value' => (set_value('blogger')) ? set_value('blogger') : get_setting('blogger'),
						'label' => translate('blogger'),
						'placeholder' => translate('blogger_placeholder'),
						'validation' => ['rules' => '']
					],
					'tumblr' => [
						'property' => 'text',
						'name' => 'tumblr',
						'icon' => 'icon-tumblr',
						'class' => 'form-control',
						'value' => (set_value('tumblr')) ? set_value('tumblr') : get_setting('tumblr'),
						'label' => translate('tumblr'),
						'placeholder' => translate('tumblr_placeholder'),
						'validation' => ['rules' => '']
					],
					'skype' => [
						'property' => 'text',
						'name' => 'skype',
						'icon' => 'icon-skype',
						'class' => 'form-control',
						'value' => (set_value('skype')) ? set_value('skype') : get_setting('skype'),
						'label' => translate('skype'),
						'placeholder' => translate('skype_placeholder'),
						'validation' => ['rules' => '']
					]
				]
			],
			'contact' => [
				'icon' => 'icon-phone',
				'label' => translate('tab_contact'),
				'fields' => [
					'contact_email' => [
						'property' => 'text',
						'name' => 'email',
						'class' => 'form-control',
						'value' => (set_value('email')) ? set_value('email') : get_setting('email'),
						'label' => translate('contact_email'),
						'placeholder' => translate('contact_email_placeholder'),
						'validation' => ['rules' => ''],
						'icon' => 'icon-envelop'
					],
					'contact_latitude' => [
						'property' => 'text',
						'name' => 'latitude',
						'class' => 'form-control',
						'value' => (set_value('latitude')) ? set_value('latitude') : get_setting('latitude'),
						'label' => translate('contact_latitude'),
						'placeholder' => translate('contact_latitude_placeholder'),
						'validation' => ['rules' => ''],
						'icon' => 'icon-pin'
					],
					'contact_longitude' => [
						'property' => 'text',
						'name' => 'longitude',
						'class' => 'form-control',
						'value' => (set_value('longitude')) ? set_value('longitude') : get_setting('longitude'),
						'label' => translate('contact_longitude'),
						'placeholder' => translate('contact_longitude_placeholder'),
						'validation' => ['rules' => ''],
						'icon' => 'icon-pin'
					],
					'contact_address' => [
						'property' => 'text',
						'name' => 'contact_address',
						'class' => 'form-control',
						'value' => (set_value('contact_address')) ? set_value('contact_address') : get_setting('contact_address'),
						'label' => translate('contact_address'),
						'placeholder' => translate('contact_address_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'address_az' => [
						'property' => 'text',
						'name' => 'address_az',
						'class' => 'form-control',
						'value' => (set_value('address_az')) ? set_value('address_az') : get_setting('address_az'),
						'label' => 'Address for Azerbaijan',
						'placeholder' => 'Address for Azerbaijan',
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'phone_az' => [
						'property' => 'text',
						'name' => 'phone_az',
						'class' => 'form-control',
						'value' => (set_value('phone_az')) ? set_value('phone_az') : get_setting('phone_az'),
						'label' => 'Phones for Azerbaijan',
						'placeholder' => 'Phones for Azerbaijan',
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'contact_region' => [
						'property' => 'text',
						'name' => 'contact_region',
						'class' => 'form-control',
						'value' => (set_value('contact_region')) ? set_value('contact_region') : get_setting('contact_region'),
						'label' => translate('contact_region'),
						'placeholder' => translate('contact_region_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'contact_place' => [
						'property' => 'text',
						'name' => 'contact_place',
						'class' => 'form-control',
						'value' => (set_value('contact_place')) ? set_value('contact_place') : get_setting('contact_place'),
						'label' => translate('contact_place'),
						'placeholder' => translate('contact_place_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'contact_postal' => [
						'property' => 'text',
						'name' => 'contact_postal',
						'class' => 'form-control',
						'value' => (set_value('contact_postal')) ? set_value('contact_postal') : get_setting('contact_postal'),
						'label' => translate('contact_postal'),
						'placeholder' => translate('contact_postal_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'contact_phone' => [
						'property' => 'text',
						'name' => 'contact_phone',
						'class' => 'form-control',
						'value' => (set_value('contact_phone')) ? set_value('contact_phone') : get_setting('contact_phone'),
						'label' => translate('contact_phone'),
						'placeholder' => translate('contact_phone_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'contact_fax' => [
						'property' => 'text',
						'name' => 'contact_fax',
						'class' => 'form-control',
						'value' => (set_value('contact_fax')) ? set_value('contact_fax') : get_setting('contact_fax'),
						'label' => translate('contact_fax'),
						'placeholder' => translate('contact_fax_placeholder'),
						'validation' => ['rules' => ''],
						'translate' => true
					]
				]
			],
			'information' => [
				'icon' => 'icon-phone',
				'label' => translate('tab_information'),
				'fields' => [
					'vat_details' => [
						'property' => 'textarea',
						'name' => 'vat_details',
						'class' => 'form-control',
						'value' => (set_value('vat_details')) ? set_value('vat_details') : get_setting('vat_details'),
						'label' => translate('vat_details'),
						'placeholder' => translate('vat_details'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
					'shipping_details' => [
						'property' => 'textarea',
						'name' => 'shipping_details',
						'class' => 'form-control',
						'value' => (set_value('shipping_details')) ? set_value('shipping_details') : get_setting('shipping_details'),
						'label' => translate('shipping_details'),
						'placeholder' => translate('shipping_details'),
						'validation' => ['rules' => ''],
						'translate' => true
					],
				]
			],
			'mail' => [
				'icon' => 'icon-envelop',
				'label' => translate('tab_mail'),
				'fields' => [
					'mail_server' => [
						'property' => 'dropdown',
						'name' => 'mail_server',
						'id' => 'mail_server',
						'label' => translate('mail_server'),
						'class' => 'bootstrap-select',
						'data-style' => 'btn-default btn-xs',
						'data-width' => '100%',
						'options' => [
							'phpmailer' => translate('mail_server_phpmailer'),
							'smtp' => translate('mail_server_smtp')
						],
						'selected' => (set_value('mail_server')) ? set_value('mail_server') : get_setting('mail_server'),
						'validation' => ['rules' => '']
					],
					'mail_hostname' => [
						'property' => 'text',
						'name' => 'mail_hostname',
						'class' => 'form-control',
						'value' => (set_value('mail_hostname')) ? set_value('mail_hostname') : get_setting('mail_hostname'),
						'label' => translate('mail_hostname'),
						'placeholder' => translate('mail_hostname_placeholder'),
						'validation' => ['rules' => '']
					],
					'mail_username' => [
						'property' => 'text',
						'name' => 'mail_username',
						'class' => 'form-control',
						'value' => (set_value('mail_username')) ? set_value('mail_username') : get_setting('mail_username'),
						'label' => translate('mail_username'),
						'placeholder' => translate('mail_username_placeholder'),
						'validation' => ['rules' => '']
					],
					'mail_password' => [
						'property' => 'text',
						'name' => 'mail_password',
						'class' => 'form-control',
						'value' => (set_value('mail_password')) ? set_value('mail_password') : get_setting('mail_password'),
						'label' => translate('mail_password'),
						'placeholder' => translate('mail_password_placeholder'),
						'validation' => ['rules' => '']
					],
					'mail_port' => [
						'property' => 'text',
						'name' => 'mail_port',
						'class' => 'form-control',
						'value' => (set_value('mail_port')) ? set_value('mail_port') : get_setting('mail_port'),
						'label' => translate('mail_port'),
						'placeholder' => translate('mail_port_placeholder'),
						'validation' => ['rules' => '']
					],
					'mail_timeout' => [
						'property' => 'text',
						'name' => 'mail_timeout',
						'class' => 'form-control',
						'value' => (set_value('mail_timeout')) ? set_value('mail_timeout') : get_setting('mail_timeout'),
						'label' => translate('mail_timeout'),
						'placeholder' => translate('mail_timeout_placeholder'),
						'validation' => ['rules' => '']
					]
				]
			],
			'stock_status' => [
				'icon' => 'icon-coin-dollar',
				'label' => translate('tab_stock_status'),
				'fields' => [
					'stock_status_id' => [
						'property' => 'dropdown',
						'name' => 'stock_status_id',
						'class' => 'form-control',
						'options' => $stock_status_options,
						'selected' => (set_value('stock_status_id')) ? set_value('stock_status_id') : get_setting('stock_status_id'),
						'label' => translate('stock_status_id'),
						'validation' => ['rules' => '']
					]
				]
			]
		];


		if ($this->input->method() == 'post') {
			$setting_data = [];
			foreach ($this->data['tabs'] as $key => $value) {
				foreach ($value['fields'] as $field_key => $field) {
					if (isset($field['xss_filtering'])) {
						if (is_array($this->input->post($field['name']))) {
							$setting_data[$field['name']] = json_encode($this->input->post($field['name'], false));
						} else {
							$setting_data[$field['name']] = $this->input->post($field['name'], false);
						}
					} else {
						if (is_array($this->input->post($field['name']))) {
							$setting_data[$field['name']] = json_encode($this->input->post($field['name'], true));
						} else {
							$setting_data[$field['name']] = $this->input->post($field['name'], true);
						}
					}
				}
			}

			$this->{$this->model}->update_setting($setting_data);
			redirect(site_url_multi($this->directory . $this->controller));
		}

		$this->template->render();
	}
}
