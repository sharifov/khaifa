<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$admin_url = 'administrator';

$route['default_controller']	= 'home';
$route['404_override']			= 'Errors/not_found';
$route['translate_uri_dashes']	= FALSE;


/* Custom Routes */
//Dashboard
$route[$admin_url] 														= $admin_url.'/dashboard';
$route['^(\w{2})/'.$admin_url]											= $admin_url.'/dashboard';
$route[$admin_url.'/dashboard'] 										= $admin_url.'/dashboard';
$route['^(\w{2})/'.$admin_url.'/dashboard']								= $admin_url.'/dashboard';

//Setting
$route[$admin_url.'/setting'] 											= $admin_url.'/setting';
$route['^(\w{2})/'.$admin_url.'/setting']								= $admin_url.'/setting';
$route[$admin_url.'/setting/(:any)'] 									= $admin_url.'/setting/$1';
$route['^(\w{2})/'.$admin_url.'/setting/(:any)']						= $admin_url.'/setting/$2';

//Filemanager
$route[$admin_url.'/filemanager'] 										= $admin_url.'/filemanager';
$route['^(\w{2})/'.$admin_url.'/filemanager']							= $admin_url.'/filemanager';
$route[$admin_url.'/filemanager/(:any)'] 								= $admin_url.'/filemanager/$1';
$route['^(\w{2})/'.$admin_url.'/filemanager/(:any)']					= $admin_url.'/filemanager/$2';
$route['^(\w{2})/'.$admin_url.'/filemanager/(:any)/(:any)']				= $admin_url.'/filemanager/$2/$3';

//Language
$route[$admin_url.'/language'] 											= $admin_url.'/language';
$route['^(\w{2})/'.$admin_url.'/language']								= $admin_url.'/language';
$route[$admin_url.'/language/(:any)'] 									= $admin_url.'/language/$1';
$route[$admin_url.'/language/(:any)/(:num)'] 							= $admin_url.'/language/$1/$2';
$route['^(\w{2})/'.$admin_url.'/language/(:any)']						= $admin_url.'/language/$2';
$route['^(\w{2})/'.$admin_url.'/language/(:any)/(:num)']				= $admin_url.'/language/$2/$3';

//Language
$route[$admin_url.'/translation/directory/(.*)'] 						= $admin_url.'/translation/directory/$1';
$route['^(\w{2})/'.$admin_url.'/translation/directory/(.*)']			= $admin_url.'/translation/directory/$2';
$route[$admin_url.'/translation/file/(.*)'] 						    = $admin_url.'/translation/file/$1';
$route['^(\w{2})/'.$admin_url.'/translation/file/(.*)']			        = $admin_url.'/translation/file/$2';
//Group
$route[$admin_url.'/group']												= $admin_url.'/group';
$route['^(\w{2})/'.$admin_url.'/group']									= $admin_url.'/group';
$route[$admin_url.'/group/(:any)']										= $admin_url.'/group/$1';
$route[$admin_url.'/group/(:any)/(:num)']								= $admin_url.'/group/$1/$2';
$route['^(\w{2})/'.$admin_url.'/group/(:any)']							= $admin_url.'/group/$2';
$route['^(\w{2})/'.$admin_url.'/group/(:any)/(:num)']					= $admin_url.'/group/$2/$3';

//User
$route[$admin_url.'/user'] 												= $admin_url.'/user';
$route['^(\w{2})/'.$admin_url.'/user']									= $admin_url.'/user';
$route[$admin_url.'/user/(:any)'] 										= $admin_url.'/user/$1';
$route[$admin_url.'/user/(:any)/(:num)'] 								= $admin_url.'/user/$1/$2';
$route['^(\w{2})/'.$admin_url.'/user/(:any)']							= $admin_url.'/user/$2';
$route['^(\w{2})/'.$admin_url.'/user/(:any)/(:num)']					= $admin_url.'/user/$2/$3';

//User field
$route[$admin_url.'/user_field'] 										= $admin_url.'/user_field';
$route['^(\w{2})/'.$admin_url.'/user_field']							= $admin_url.'/user_field';
$route[$admin_url.'/user_field/(:any)'] 								= $admin_url.'/user_field/$1';
$route[$admin_url.'/user_field/(:any)/(:num)'] 							= $admin_url.'/user_field/$1/$2';
$route['^(\w{2})/'.$admin_url.'/user_field/(:any)']						= $admin_url.'/user_field/$2';
$route['^(\w{2})/'.$admin_url.'/user_field/(:any)/(:num)']				= $admin_url.'/user_field/$2/$3';

//Permissions
$route[$admin_url.'/permission'] 										= $admin_url.'/permission';
$route['^(\w{2})/'.$admin_url.'/permission']							= $admin_url.'/permission';
$route[$admin_url.'/permission/(:any)']									= $admin_url.'/permission/$1';
$route[$admin_url.'/permission/(:any)/(:num)']							= $admin_url.'/permission/$1/$2';
$route['^(\w{2})/'.$admin_url.'/permission/(:any)']						= $admin_url.'/permission/$2';
$route['^(\w{2})/'.$admin_url.'/permission/(:any)/(:num)']				= $admin_url.'/permission/$2/$3';

//Transaction
$route[$admin_url.'/transaction'] 										= $admin_url.'/transaction';
$route['^(\w{2})/'.$admin_url.'/transaction']							= $admin_url.'/transaction';
$route[$admin_url.'/transaction/(:any)']								= $admin_url.'/transaction/$1';
$route[$admin_url.'/transaction/(:any)/(:num)']							= $admin_url.'/transaction/$1/$2';
$route['^(\w{2})/'.$admin_url.'/transaction/(:any)']					= $admin_url.'/transaction/$2';
$route['^(\w{2})/'.$admin_url.'/transaction/(:any)/(:num)']				= $admin_url.'/transaction/$2/$3';


//Order product
$route[$admin_url.'/order_product'] 										= $admin_url.'/order_product';
$route['^(\w{2})/'.$admin_url.'/order_product']							= $admin_url.'/order_product';
$route[$admin_url.'/order_product/(:any)']								= $admin_url.'/order_product/$1';
$route[$admin_url.'/order_product/(:any)/(:num)']							= $admin_url.'/order_product/$1/$2';
$route['^(\w{2})/'.$admin_url.'/order_product/(:any)']					= $admin_url.'/order_product/$2';
$route['^(\w{2})/'.$admin_url.'/order_product/(:any)/(:num)']				= $admin_url.'/order_product/$2/$3';


//Setting
$route[$admin_url.'/extension'] 										= $admin_url.'/extension';
$route['^(\w{2})/'.$admin_url.'/extension']								= $admin_url.'/extension';
$route[$admin_url.'/extension/(:any)']									= $admin_url.'/extension/$1';
$route['^(\w{2})/'.$admin_url.'/extension/(:any)']						= $admin_url.'/extension/$2';
$route[$admin_url.'/extension/(:any)/(:num)']							= $admin_url.'/extension/$1/$2';
$route['^(\w{2})/'.$admin_url.'/extension/(:any)/(:num)']				= $admin_url.'/extension/$2/$3';

//Option
$route[$admin_url.'/option'] 											= $admin_url.'/option';
$route['^(\w{2})/'.$admin_url.'/option']								= $admin_url.'/option';
$route[$admin_url.'/option/(:any)'] 									= $admin_url.'/option/$1';
$route[$admin_url.'/option/(:any)/(:num)'] 								= $admin_url.'/option/$1/$2';
$route['^(\w{2})/'.$admin_url.'/option/(:any)']							= $admin_url.'/option/$2';
$route['^(\w{2})/'.$admin_url.'/option/(:any)/(:num)']					= $admin_url.'/option/$2/$3';

//Attribute
$route[$admin_url.'/attribute'] 										= $admin_url.'/attribute';
$route['^(\w{2})/'.$admin_url.'/attribute']								= $admin_url.'/attribute';
$route[$admin_url.'/attribute/(:any)'] 									= $admin_url.'/attribute/$1';
$route[$admin_url.'/attribute/(:any)/(:num)'] 							= $admin_url.'/attribute/$1/$2';
$route['^(\w{2})/'.$admin_url.'/attribute/(:any)']						= $admin_url.'/attribute/$2';
$route['^(\w{2})/'.$admin_url.'/attribute/(:any)/(:num)']				= $admin_url.'/attribute/$2/$3';

//Relation
$route[$admin_url.'/relation'] 											= $admin_url.'/relation';
$route['^(\w{2})/'.$admin_url.'/relation']								= $admin_url.'/relation';
$route[$admin_url.'/relation/(:any)'] 									= $admin_url.'/relation/$1';
$route[$admin_url.'/relation/(:any)/(:num)'] 							= $admin_url.'/relation/$1/$2';
$route['^(\w{2})/'.$admin_url.'/relation/(:any)']						= $admin_url.'/relation/$2';
$route['^(\w{2})/'.$admin_url.'/relation/(:any)/(:num)']				= $admin_url.'/relation/$2/$3';

//Geo Zone
$route[$admin_url.'/geo_zone'] 										    = $admin_url.'/geo_zone';
$route['^(\w{2})/'.$admin_url.'/geo_zone']								= $admin_url.'/geo_zone';
$route[$admin_url.'/geo_zone/(:any)'] 									= $admin_url.'/geo_zone/$1';
$route[$admin_url.'/geo_zone/(:any)/(:num)'] 				            = $admin_url.'/geo_zone/$1/$2';
$route['^(\w{2})/'.$admin_url.'/geo_zone/(:any)']			            = $admin_url.'/geo_zone/$2';
$route['^(\w{2})/'.$admin_url.'/geo_zone/(:any)/(:num)']	            = $admin_url.'/geo_zone/$2/$3';

//Tax
$route[$admin_url.'/tax_class'] 										= $admin_url.'/tax_class';
$route['^(\w{2})/'.$admin_url.'/tax_class']								= $admin_url.'/tax_class';
$route[$admin_url.'/tax_class/(:any)'] 									= $admin_url.'/tax_class/$1';
$route[$admin_url.'/tax_class/(:any)/(:num)'] 				            = $admin_url.'/tax_class/$1/$2';
$route['^(\w{2})/'.$admin_url.'/tax_class/(:any)']			            = $admin_url.'/tax_class/$2';
$route['^(\w{2})/'.$admin_url.'/tax_class/(:any)/(:num)']	            = $admin_url.'/tax_class/$2/$3';

//Tax
$route[$admin_url.'/tax_rate'] 										    = $admin_url.'/tax_rate';
$route['^(\w{2})/'.$admin_url.'/tax_rate']								= $admin_url.'/tax_rate';
$route[$admin_url.'/tax_rate/(:any)'] 									= $admin_url.'/tax_rate/$1';
$route[$admin_url.'/tax_rate/(:any)/(:num)'] 				            = $admin_url.'/tax_rate/$1/$2';
$route['^(\w{2})/'.$admin_url.'/tax_rate/(:any)']			            = $admin_url.'/tax_rate/$2';
$route['^(\w{2})/'.$admin_url.'/tax_rate/(:any)/(:num)']	            = $admin_url.'/tax_rate/$2/$3';

//Review
$route[$admin_url.'/review'] 										    = $admin_url.'/review';
$route['^(\w{2})/'.$admin_url.'/review']								= $admin_url.'/review';
$route[$admin_url.'/review/(:any)'] 									= $admin_url.'/review/$1';
$route[$admin_url.'/review/(:any)/(:num)'] 				                = $admin_url.'/review/$1/$2';
$route['^(\w{2})/'.$admin_url.'/review/(:any)']			                = $admin_url.'/review/$2';
$route['^(\w{2})/'.$admin_url.'/review/(:any)/(:num)']	                = $admin_url.'/review/$2/$3';

// Customers
$route[$admin_url.'/customers'] 										= $admin_url.'/customers';
$route['^(\w{2})/'.$admin_url.'/customers']								= $admin_url.'/customers';
$route[$admin_url.'/customers/(:any)'] 									= $admin_url.'/customers/$1';
$route[$admin_url.'/customers/(:any)/(:num)'] 				            = $admin_url.'/customers/$1/$2';
$route['^(\w{2})/'.$admin_url.'/customers/(:any)']			            = $admin_url.'/customers/$2';
$route['^(\w{2})/'.$admin_url.'/customers/(:any)/(:num)']	            = $admin_url.'/customers/$2/$3';

// Customer Approval
$route[$admin_url.'/customer_approval'] 								= $admin_url.'/customer_approval';
$route['^(\w{2})/'.$admin_url.'/customer_approval']				        = $admin_url.'/customer_approval';
$route[$admin_url.'/customer_approval/(:any)'] 					        = $admin_url.'/customer_approval/$1';
$route[$admin_url.'/customer_approval/(:any)/(:num)'] 				    = $admin_url.'/customer_approval/$1/$2';
$route['^(\w{2})/'.$admin_url.'/customer_approval/(:any)']			    = $admin_url.'/customer_approval/$2';
$route['^(\w{2})/'.$admin_url.'/customer_approval/(:any)/(:num)']	    = $admin_url.'/customer_approval/$2/$3';

//Product
$route[$admin_url.'/product'] 											= $admin_url.'/product';
$route['^(\w{2})/'.$admin_url.'/product']								= $admin_url.'/product';
$route[$admin_url.'/product/(:any)'] 									= $admin_url.'/product/$1';
$route[$admin_url.'/product/(:any)/(:num)'] 							= $admin_url.'/product/$1/$2';
$route['^(\w{2})/'.$admin_url.'/product/(:any)']						= $admin_url.'/product/$2';
$route['^(\w{2})/'.$admin_url.'/product/(:any)/(:num)']					= $admin_url.'/product/$2/$3';

//Custom discounts
$route[$admin_url.'/discounts'] 										= $admin_url.'/discounts';
$route['^(\w{2})/'.$admin_url.'/discounts']								= $admin_url.'/discounts';
$route[$admin_url.'/discounts/(:any)'] 									= $admin_url.'/discounts/$1';
$route[$admin_url.'/discounts/(:any)/(:num)'] 							= $admin_url.'/discounts/$1/$2';
$route['^(\w{2})/'.$admin_url.'/discounts/(:any)']						= $admin_url.'/discounts/$2';
$route['^(\w{2})/'.$admin_url.'/discounts/(:any)/(:num)']				= $admin_url.'/discounts/$2/$3';

//Order
$route[$admin_url.'/order'] 											= $admin_url.'/order';
$route['^(\w{2})/'.$admin_url.'/order']									= $admin_url.'/order';
$route[$admin_url.'/order/ajax'] 										= $admin_url.'/order/ajax';
$route['^(\w{2})/'.$admin_url.'/order/ajax']							= $admin_url.'/order/ajax';
$route[$admin_url.'/order/delete'] 										= $admin_url.'/order/delete';
$route['^(\w{2})/'.$admin_url.'/order/delete']							= $admin_url.'/order/delete';
$route[$admin_url.'/order/(:any)'] 										= $admin_url.'/order/$1';
$route[$admin_url.'/order/(:any)/(:num)'] 								= $admin_url.'/order/$1/$2';
$route['^(\w{2})/'.$admin_url.'/order/(:any)']							= $admin_url.'/order/$2';
$route['^(\w{2})/'.$admin_url.'/order/(:any)/(:num)']					= $admin_url.'/order/$2/$3';

//Credit_request
$route[$admin_url.'/credit_request'] 											= $admin_url.'/credit_request';
$route['^(\w{2})/'.$admin_url.'/credit_request']									= $admin_url.'/credit_request';
$route[$admin_url.'/credit_request/(:any)'] 										= $admin_url.'/credit_request/$1';
$route[$admin_url.'/credit_request/(:any)/(:num)'] 								= $admin_url.'/credit_request/$1/$2';
$route['^(\w{2})/'.$admin_url.'/credit_request/(:any)']							= $admin_url.'/credit_request/$2';
$route['^(\w{2})/'.$admin_url.'/credit_request/(:any)/(:any)/(:num)']					= $admin_url.'/credit_request/$2/$3/$4';
$route['^(\w{2})/'.$admin_url.'/credit_request/(:any)/(:num)']					= $admin_url.'/credit_request/$2/$3';



$route[$admin_url.'/featured_product'] 								    = $admin_url.'/featured_product';
$route['^(\w{2})/'.$admin_url.'/featured_product']						= $admin_url.'/featured_product';
$route[$admin_url.'/featured_product/(:any)'] 							= $admin_url.'/featured_product/$1';
$route[$admin_url.'/featured_product/(:any)/(:num)'] 					= $admin_url.'/featured_product/$1/$2';
$route['^(\w{2})/'.$admin_url.'/featured_product/(:any)']				= $admin_url.'/featured_product/$2';
$route['^(\w{2})/'.$admin_url.'/featured_product/(:any)/(:num)']		= $admin_url.'/featured_product/$2/$3';

$route['^(\w{2})/'.$admin_url.'/authentication/(:any)']					= $admin_url.'/authentication/$2';
$route[$admin_url.'/authentication/(:any)']								= $admin_url.'/authentication/$1';




/* Seo Url Redirect  */

$route[$admin_url.'/seo_url_redirect']                                   = $admin_url.'/seo_url_redirect';
$route['^(\w{2})/'.$admin_url.'/seo_url_redirect']                       = $admin_url.'/seo_url_redirect';
$route['^(\w{2})/'.$admin_url.'/seo_url_redirect/create']                = $admin_url.'/seo_url_redirect/create';
$route[$admin_url.'/seo_url_redirect/create']                            = $admin_url.'/seo_url_redirect/create';
$route[$admin_url.'/seo_url_redirect/edit/(:num)']                       = $admin_url.'/seo_url_redirect/edit/$1';
$route['^(\w{2})/'.$admin_url.'/seo_url_redirect/edit/(:num)']           = $admin_url.'/seo_url_redirect/edit/$1';
$route[$admin_url.'/seo_url_redirect/delete/(:num)']                     = $admin_url.'/seo_url_redirect/delete/$1';
$route['^(\w{2})/'.$admin_url.'/seo_url_redirect/delete/(:num)']         = $admin_url.'/seo_url_redirect/delete/$1';


/* End Seo Url Redirect */


/* End custom Routes */

/* Modules */
$route[$admin_url.'/(:any)'] 											= $admin_url.'/module/index';
$route['^(\w{2})/'.$admin_url.'/(:any)'] 								= $admin_url.'/module/index';
$route[$admin_url.'/(:any)/index']										= $admin_url.'/module/index';
$route['^(\w{2})/'.$admin_url.'/(:any)/index']							= $admin_url.'/module/index';
$route[$admin_url.'/(:any)/trash']										= $admin_url.'/module/trash';
$route['^(\w{2})/'.$admin_url.'/(:any)/trash'] 							= $admin_url.'/module/trash';
//Pagination route
$route[$admin_url.'/(:any)/index/(:num)']								= $admin_url.'/module/index/$1/$2';
$route['^(\w{2})/'.$admin_url.'/(:any)/index/(:num)']					= $admin_url.'/module/index/$1/$2';
//Create Route
$route[$admin_url.'/(:any)/create'] 									= $admin_url.'/module/create/$1';
$route['^(\w{2})/'.$admin_url.'/(:any)/create'] 						= $admin_url.'/module/create/$2';
//Edit route
$route[$admin_url.'/(:any)/edit/(:num)'] 								= $admin_url.'/module/edit/$2';
$route['^(\w{2})/'.$admin_url.'/(:any)/edit/(:num)'] 					= $admin_url.'/module/edit/$3';
//Delete Route
$route[$admin_url.'/(:any)/delete'] 							        = $admin_url.'/module/delete';
$route[$admin_url.'/(:any)/delete/(:num)'] 							    = $admin_url.'/module/delete/$2';
$route['^(\w{2})/'.$admin_url.'/(:any)/delete']  				        = $admin_url.'/module/delete';
$route['^(\w{2})/'.$admin_url.'/(:any)/delete/(:num)']  				= $admin_url.'/module/delete/$3';
//Remove Route
$route[$admin_url.'/(:any)/remove'] 							        = $admin_url.'/module/remove';
$route[$admin_url.'/(:any)/remove/(:num)'] 							    = $admin_url.'/module/remove/$2';
$route['^(\w{2})/'.$admin_url.'/(:any)/remove']  				        = $admin_url.'/module/remove';
$route['^(\w{2})/'.$admin_url.'/(:any)/remove/(:num)']  				= $admin_url.'/module/remove/$3';
//Restore Route
$route[$admin_url.'/(:any)/restore'] 							        = $admin_url.'/module/restore';
$route[$admin_url.'/(:any)/restore/(:num)'] 							= $admin_url.'/module/restore/$2';
$route['^(\w{2})/'.$admin_url.'/(:any)/restore']  				        = $admin_url.'/module/restore';
$route['^(\w{2})/'.$admin_url.'/(:any)/restore/(:num)']  				= $admin_url.'/module/restore/$3';
//Clean Route
$route[$admin_url.'/(:any)/clean']										= $admin_url.'/module/clean';
$route['^(\w{2})/'.$admin_url.'/(:any)/clean']							= $admin_url.'/module/clean';
//Show Route
$route[$admin_url.'/(:any)/show/(:num)']								= $admin_url.'/module/show/$2';
$route['^(\w{2})/'.$admin_url.'/(:any)/show/(:num)']	 				= $admin_url.'/module/show/$3';
//Change Status Route
$route[$admin_url.'/(:any)/changeStatus']								= $admin_url.'/module/changeStatus';
$route['^(\w{2})/'.$admin_url.'/(:any)/changeStatus']	 				= $admin_url.'/module/changeStatus';
//Create Slug Route
$route[$admin_url.'/(:any)/slugGenerator']								= $admin_url.'/module/slugGenerator';
$route['^(\w{2})/'.$admin_url.'/(:any)/slugGenerator']	 				= $admin_url.'/module/slugGenerator';
//Dropdown Ajax Route
$route[$admin_url.'/(:any)/ajaxDropdownSearch']							= $admin_url.'/module/ajaxDropdownSearch';
$route['^(\w{2})/'.$admin_url.'/(:any)/ajaxDropdownSearch']	 			= $admin_url.'/module/ajaxDropdownSearch';
/* End Modules */


/* Site Routes */

$route['en?']								                = 'url_manager/redirect';
$route['home']								                = 'home';
$route['^(\w{2})/home']								        = 'home';


$route['product/search']								                = 'product/search';
$route['^(\w{2})/product/search']								        = 'product/search';


$route['become_seller/login']								        = 'become_seller/login';
$route['^(\w{2})/become_seller/login']								= 'become_seller/login';



$route['coupon']                           = 'coupon/index';
$route['coupon/index']                         = 'coupon/index';
$route['^(\w{2})/coupon']                          = 'coupon/index';
$route['^(\w{2})/coupon/index']                        = 'coupon/index';

$route['cart/find_category']                          = 'cart/find_category';

$route['product/stock_notifier']								                = 'product/stock_notifier';
$route['^(\w{2})/product/stock_notifier']								        = 'product/stock_notifier';

// TRASH API TEST
$route['product/ajaxExample'] = 'product/ajaxExample';
$route['^(\w{2})/product/ajaxExample'] = 'product/ajaxExample';

// Product routes
$route['product'] 											            = 'product';
$route['product/review']                                                = 'product/review';
$route['^(\w{2})/product/review']                                       = 'product/review';


$route['^(\w{2})/product/(:any)']								        = 'product/index/$2';
$route['^(\w{2})/product/clone/(:any)']								    = 'product/clone/$2';
$route['product/(:any)']								                = 'product/index/$1';

$route['products/(:any)']								                = 'products/index/$1';
$route['^(\w{2})/products/(:any)']								        = 'products/index/$2';
$route['products/(:any)/(:num)']								        = 'products/index/$1/$2';
$route['^(\w{2})/products/(:any)/(:num)']						        = 'products/index/$2/$3';
$route['products/(:any)/(:num)/(:num)']								    = 'products/index/$1/$2/$3';
$route['^(\w{2})/products/(:any)/(:num)/(:num)']						= 'products/index/$2/$3/$4';



// Category rotues
$route['category'] 											            = 'category';
$route['^(\w{2})/category/(:any)']								        = 'category/index/$2';
$route['category/(:any)']								                = 'category/index/$1';
$route['category/(:any)/(:num)']								        = 'category/index/$1/$2';
$route['^(\w{2})/category/(:any)/(:num)']						        = 'category/index/$2/$3';

$route['become_seller/forget_password'] 								= 'become_seller/forget_password';
$route['^(\w{2})/become_seller/forget_password']						= 'become_seller/forget_password';

$route['become_seller/reset_password/(:any)'] 							= 'become_seller/reset_password/$1';
$route['^(\w{2})/become_seller/reset_password/(:any)']					= 'become_seller/reset_password/$1';
$route['become_seller/reset_password'] 									= 'become_seller/reset_password';
$route['^(\w{2})/become_seller/reset_password'] 						= 'become_seller/reset_password';

$route['become_seller'] 											    = 'become_seller';
$route['^(\w{2})/become_seller']								        = 'become_seller/index';

$route['account/reset_password'] 										= 'account/reset_password';
$route['^(\w{2})/account/reset_password'] 										= 'account/reset_password';
$route['account/reset_password/(:any)'] 								= 'account/reset_password/$1';
$route['^(\w{2})/account/reset_password/(:any)']						= 'account/reset_password/$1/$2';

$route['favorite'] 											            = 'favorite/index';
$route['^(\w{2})/favorite']								            	= 'favorite/index/$1';

$route['checkout'] 											            = 'checkout/index';
$route['^(\w{2})/checkout/']								            = 'checkout/index';

$route['checkout/cart'] 											    = 'checkout/cart';
$route['^(\w{2})/checkout/cart']								        = 'checkout/cart';

$route['latest_deals'] 											         = 'latest_deals/index';
$route['^(\w{2})/latest_deals/']								         = 'latest_deals/index';


$route['best_seller'] 											         = 'best_seller/index';
$route['^(\w{2})/best_seller/']								             = 'best_seller/index';

$route['account/create'] 											    = 'account/create';
$route['^(\w{2})/account/create']								        = 'account/create';
$route['account/region']								                = 'account/region';


$route['account/logout'] 											    = 'account/logout';
$route['^(\w{2})/account/logout']								        = 'account/logout';

$route['account/login'] 											    = 'account/login';
$route['^(\w{2})/account/login']								        = 'account/login';

$route['account/index'] 											    = 'account/index';
$route['^(\w{2})/account/index']								        = 'account/index';



$route['account/track/(:any)'] 											 = 'account/track/$1';
$route['^(\w{2})/account/track/(:any)']								     = 'account/track/$2';

$route['account/facebook_login'] 										= 'account/facebook_login';
$route['^(\w{2})/account/facebook_login']								= 'account/facebook_login';


$route['account/google_login'] 											= 'account/google_login';
$route['^(\w{2})/account/google_login']								    = 'account/google_login';

$route['account/forget_password'] 								        = 'account/forget_password';
$route['^(\w{2})/account/forget_password']								= 'account/forget_password';

$route['account/add_address_book']                                      = 'account/add_address_book';
$route['^(\w{2})/account/add_address_book']                             = 'account/add_address_book';

$route['account/ajax_add_address']                                      = 'account/ajax_add_address';
$route['^(\w{2})/account/ajax_add_address']                             = 'account/ajax_add_address';

$route['account/address_book'] 											 = 'account/address_book';
$route['^(\w{2})/account/address_book'] 				                 = 'account/address_book';

$route['account/edit_address_book/(:any)'] 								= 'account/edit_address_book/$1';
$route['^(\w{2})/account/edit_address_book/(:any)'] 				     = 'account/edit_address_book/$2';


$route['account/orders'] 											    = 'account/orders';
$route['^(\w{2})/account/orders'] 				                        = 'account/orders';


$route['account'] 											            = 'account';
$route['^(\w{2})/account'] 											    = 'account';

// Faq rotues
$route['faq'] 											                = 'faq';
$route['^(\w{2})/faq'] 											        = 'faq';

$route['faq/category/(:any)'] 											= 'faq/category/$1';
$route['^(\w{2})/faq/category/(:any)'] 									= 'faq/category/$2';

$route['faq/category/(:any)/(:num)'] 									= 'faq/category/$1/$2';
$route['^(\w{2})/faq/category/(:any)/(:num)'] 							= 'faq/category/$2/$3';

$route['faq/view/(:any)'] 											    = 'faq/view/$1';
$route['^(\w{2})/faq/view/(:any)'] 									    = 'faq/view/$2';

$route['^(\w{2})/cart/info'] 									        = 'cart/info';
$route['^(\w{2})/checkout'] 									        = 'checkout/index';
$route['^(\w{2})/checkout/(:any)'] 									    = 'checkout/$2';

// $route['^(\w{2})/faq/(:any)']								        = 'faq/index/$2';
// $route['faq/(:any)']								                    = 'faq/index/$1';
// $route['faq/(:any)/(:num)']								            = 'faq/index/$1/$2';
// $route['^(\w{2})/faq/(:any)/(:num)']						            = 'faq/index/$2/$3';


$route['cart/add']										                = 'cart/add';
$route['bag/test']                                                 = 'checkout/test';
$route['become_seller/upload']										    = 'become_seller/upload';
$route['cart/remove']										            = 'cart/remove';
$route['checkout/confirm']										        = 'checkout/confirm';
$route['home/set_currency']										        = 'home/set_currency';
$route['checkout/paypal']										        = 'checkout/paypal';
$route['checkout/error']										        = 'checkout/error';
$route['checkout/index']										        = 'checkout/index';
$route['checkout/success']										        = 'checkout/success';
$route['checkout/callback']										        = 'checkout/callback';
$route['checkout/cod']													= 'checkout/cod';
$route['checkout/payment_method']										= 'checkout/payment_method';
$route['^(\w{2})']													    = function($lang){
  $_langs = array_map(function($val){return substr(basename($val),0,2);}, glob(APPPATH.'language/*', GLOB_ONLYDIR));
  return in_array($lang, $_langs)?'home/index':'url_manager/index/'.$lang;
};
$route['product/search/(:any)']											= 'product/search/$1';
$route['^(\w{2})/product/search/(:any)']								= 'product/search/$2';
$route['favorite/add']													= 'favorite/add';
$route['favorite/remove']												= 'favorite/remove';
$route['account/change_address']										= 'account/change_address';
$route['home/change-country']										    = 'home/change_country';



$route['home/testshipping']													= 'home/testshipping';
$route['home/cron_algolia']													= 'home/cron_algolia';
$route['checkout/test2']													= 'checkout/test2';

$route['add_credit_request']                     = 'url_manager/add_credit_request';

$route['^(\w{2})/(:any)/(:any)']										= 'url_manager/index/$2/$3';
$route['^(\w{2})/(:any)']										        = 'url_manager/index/$2';
$route['(:any)/(:any)']													= 'url_manager/index/$1/$2';
$route['(:any)']													    = 'url_manager/index/$1';


$expired_links = ['electronica/phones', 'electronica/Laptop', 'electronica/accessories-engls', 'electronica/mobile-accessories', 'electronica/phones/phones-xiaomi', 'electronica/tablets', 'phones/phones-oppo', 'electronica/phones/honor', 'electronica/Laptop/all-in-ones-desktops', 'phones/phones-sony', 'electronica/notebooks', 'phones/phones-asus', 'phones/phones-apple', 'Laptop/Macbooks', 'phones/phones-huawei', 'electronica/Tablets', 'phones/phones-samsung', 'electronica/computers-periphera', 'electronica/photos-video', 'electronica/Laptop/Macbooks', 'electronica/phones/phones-huawei', 'Laptop/gaming-laptops', 'accessories-engls/smart-watches', 'electronica/phones/phones-asus', 'electronica/pc-components', 'Laptop/all-in-ones-desktops', 'electronica/phones/phones-samsung', 'computers-periphera/notebooks', 'electronica/phones/phones-nokia', 'phones/apple', 'electronica/accessories-engls/smart-watches', 'electronica/phones/phones-apple', 'electronica/phones/phones-oneplus', 'electronica/phones/phones-sony', 'electronica/Tablets/tablets-apple', 'electronica/phones/phones-oppo', 'accessories-engls/cases-covers', 'catalog/view/theme/so-maxshop/template/social', 'phones/phones-xiaomi', 'phones/phones-htc', 'tablets/tablets-apple', 'phones/phones-nokia', 'phones/phones-oneplus', 'electronica/tv-audio', 'tv-audio/musical-instruments', 'accessories-engls/Headsets', 'phones/phones-blackberry', 'electronica/video-games', 'electronica/Tablets/tablets-samsung', 'electronica/Laptop/gaming-laptops', 'electronica/computers-periphera/notebooks', 'image/cache/catalog', 'phones', 'electronica', 'accessories-engls', 'mobile-tablet', 'toys-gifts', 'Macbooks', 'Maserati', 'apple', 'video-games', 'cables-chargers', 'phones-apple', 'mobile-accessories', 'photos-video/dslrs', 'Headsets', 'all-in-ones-desktops', 'sony', 'tablets', 'tablets-apple', 'phones-samsung', 'phones-asus', 'laptop-notebook', 'phones-xiaomi', 'htc', 'phones-sony', 'Tablets', 'phones/honor', 'Marshall', 'Fitbit', 'tv-audio', 'pc-components', 'Laptop', 'phones-htc', 'photos-video', 'phones-oneplus', 'phones-oppo', 'accessories-engls/cables-chargers', 'phones-google', 'electronica/accessories-engls/cables-chargers', 'camcorders', 'electronica/accessories-engls/Headsets', 'cases-covers', 'gaming-laptops', 'computers-periphera/tablets', 'Tablets/tablets-apple', 'phones-nokia', 'games-gadgets-accessories', 'Belkin', 'Monster'];

foreach ($expired_links as $link) {

    $route[$link . '/(:any)'] = 'url_manager/redirect/$1';

}

//$route['^(\w{2})/(:any)']												= 'page/index/$2';
$route['^(\w{2})$']														= $route['default_controller'];
//$route['(:any)']														= 'page/index/$1';
