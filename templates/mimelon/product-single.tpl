{extends file=$layout}
{block name=content}
<div id="VAT" class="modal fade delivery-modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			{get_setting('vat_details', $current_lang)}
			<button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt=""></button>
		</div>
	</div>
</div>
<div id="Shipping" class="modal fade delivery-modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			{get_setting('shipping_details', $current_lang)}
			<button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt=""></button>
		</div>
	</div>
</div>


<!-- starts modal change shipping -->
<div id="change-shipping" class="modal fade delivery-modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="cyloc-title h4">{translate('choose_location', true)}</div>
			<div class="cytxt-info">{translate('delivery_may_vary', true)}</div>
			<ul class="cycchoose-variants">
				{foreach $addresses as $address}
					<li {if $address['default']} class="active" {/if} data-id="{$address['id']}">
						<div class="shipdflex">
							<div>
								<p class="cyc-choose-name">{$address['firstname']} {$address['lastname']}</p>
								<p class="cyc-coose-adrs">{$address['city']}, {$address['country']}</p>
							</div>
							{if $address['default']}
							<span class="cyc-default-adrs">{translate('default_address', true)}</span>
							{/if}
						</div>
					</li>
				{/foreach}
			</ul>
			<div class="shpchange_txt">
				{if isset($customer->id)}
				<a href="{site_url_multi('account/address_book')}" class="mngloc">{translate('address_book', true)}</a>
				{*<div class="shp_change_txt">Delivery option and delivery speeds and lorem ipsum I am cola it is on the table</div>*}
				{else}
				<div class="text-center">
					<a href="{site_url_multi('account/address_book')}" class="signinlocation">{translate('sign_in_to_see_address', true)}</a>
				</div>
				{/if}
				<select name="country" class="form-control mngloc_slc">
					{foreach $countries as $country}
					<option value="{$country->id}" {if get_country_id() == $country->id} selected {/if}>{$country->name}</option>
					{/foreach}
				</select>
				<div class="text-right"><button type="button" id="change-country" class="sgnshp_btn">{translate('done', true)}</button></div>
			</div>
			<button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt=""></button>
		</div>
	</div>
</div>
<!-- ends modal change shipping -->




<header class="m-container page-header">
	<ol class="breadcrumb page-breadcrumb">
		<li class="active"><a href="#">{translate('home',true,true)}</a></li>
		{if isset($product->category)}
		<li class="active"><a href="{site_url_multi('/')}{$product->category_slug}/">{$product->category}</a></li>
		{/if}
		<li><a href="{current_url()}">{$product->name}</a></li>
	</ol>
</header>

<div class="container product-single-cover">
		<div class="col-md-9 col-xs-12 product-single-details">
			<div class="col-md-6 col-sm-6 col-xs-12 product-single-slider product__single_slider custom-zoom-cover">
				<!-- starts product single zoom animation -->
				<div class="zoom-cover">
					<h1 class="product-single-title">{$product->name}
						<!-- out of stock -->
						{*<span class="outStocktxt">{$product->stock_status_name}</span>*}
					</h1>
					<div class="product-rating-top">
						<ul class="product-caption-rating rating_star222">
							{for $rating=1 to 5}
								{if $rating <= $product->rating}
									<li class="rated"><i class="fa fa-star"></i></li>
								{else}
									<li><i class="fa fa-star-o"></i></li>
								{/if}
							{/for}
						</ul>
					</div>
					<div class="show showen-img" href="{$product->image}">
						<img src="{base_url('templates/mimelon/assets/img/icons/chevron_left.svg')}" class="icon-left" alt="{$product->alt_image}" id="prev-img">
						<img src="{$product->image}" alt="{$product->name}" alt="{$product->alt_image}" id="show-img">
						<img src="{base_url('templates/mimelon/assets/img/icons/chevron_right.svg')}" class="icon-right" alt="{$product->alt_image}" id="next-img">
					</div>
				</div>
				<div class="small-img">
						<div class="small-container">
							<div id="small-img-roll">

								<div class="owl-carousel owl-theme small-rolls">
									<div class="small-roll-item">
										<img src="{$product->image}" class="show-small-img" alt="{$product->alt_image}" title="{$product->name}" />
									</div>
									{if isset($product->images)}
										{foreach from=$product->images item=image}
											<div class="small-roll-item">
												<img src="{$image.url}" class="show-small-img" alt="{$image.alt_image}" title="{$product->name}">
											</div>
										{/foreach}
									{/if}
								</div>

								<div class="i-hide">
									<img src="{$product->image}" class="show-small-img" alt="{$product->name}" title="{$product->name}" />
									{if isset($product->images)}
										{foreach from=$product->images item=image}
											<img src="{$image.url}" class="show-small-img" alt="{$image.alt_image}" title="{$product->name}">
										{/foreach}
									{/if}
								</div>
							</div>
						</div>
					</div>
				<!-- ends product single zoom animation -->
				<div class="clearfix"></div>
				{if $copied_product_count > 0}
					<div class="more__product33">
						<div class="col-md-12 col-sm-12 col-xs-12 pro-des-cover">
							<div class="des-info-txt">
								<div class="des-info wow fadeInUp">
									<a href="{site_url_multi('product/search')}?copied_product_id={$product->id}">{translate('label_copied_product',true)} ({$copied_product_count}) <i class="fa fa-chevron-right"></i> </a>
								</div>
							</div>
						</div>
					</div>
				{/if}
				<div class="clearfix"></div>
				{*
                <div class="share-single-product">
                    <a href="#">
	                    <img src="/templates/mimelon/assets/img/icons/share.svg" alt="" class="share-icon">
	                    <p>Share</p>
                    </a>
                </div>
                *}
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12 product-single-info psi30">
				{if isset($shipping_list) && !empty($shipping_list)}
					{if $product->quantity > 0 && get_setting('stock_limit') > $product->quantity}
						<div class="col-sm-12 col-xs-12 product-stock-limit pro-lr00 palitra">{sprintf(translate('stock_limit'), $product->quantity)}</div>
					{/if}
				{/if}
				{if $product->status != 9 && $product->quantity != 0 }
					<span class="product-caption-price-new last-chance-price">
					{currency_symbol_converter($product->price)}
						{if $product->special}
							<small class="product-old-price">{currency_symbol_converter($product->special)}</small>
						{/if}
					</span>
				{else}
					<span class="product-caption-price-new last-chance-price">{translate('out_of_stock', true)}</span>
				{/if}

				<div class="price-border"></div>
				{if $product->special && $product->special_date_end}
					<div class="single-discount-product">
						{*
                        <p class="percent-off">30% Off</p>
                        *}
						<input type="hidden" id="countdown3" value="{$product->special_date_end}" />
						<div class="single-product-countdown">
							<p class="single-count-txt">{translate('discount_ends_in', true)} : </p>
							<div class="countdown">
								<b class="countdown3days"></b> <span>{translate('day', true)}:</span>
								<b class="countdown3hrs"></b> <span>{translate('hrs', true)}:</span>
								<b class="countdown3min"></b> <span>{translate('min', true)}:</span>
								<b class="countdown3sec"></b> <span>{translate('sec', true)}</span>
							</div>
						</div>
					</div>
				{/if}
				<form id="product" method="post">
					{if $product->quantity > 0}
						<div class="col-sm-12 col-xs-12 pro-lr00">

							{if $product_relations}
								<div class="row">
									<ul class="unit-connections no-bullet product-unit-details">
										{foreach from=$product_relations item=product_relation}
											<li>
												<h3> {$product_relation.name} </h3>
												<ul class="menu connection-buttons">
													{foreach from=$product_relation.product_relation_value item=relation_value}
														<li><a href="{$relation_value.link}" class="btn btn-{if isset($relation_value.current) && $relation_value.current eq  1}danger{else}info{/if}" role="button">{$relation_value.name}</a></li>
													{/foreach}
												</ul>
											</li>
										{/foreach}
									</ul>
								</div>
							{/if}

							<table class="single-pro-color">
								{if $product->manufacturer_id > 0}
									<tr>
										<td><b>{translate('label_manufacturer',true)}</b></td>
										<td><span>{$product->manufacturer_name}</span></td>
									</tr>
								{/if}
								{if $product->stock_status_id > 0}
									<tr>
										<td><b>{translate('label_availability',true)}</b></td>
										<td><span {if $product->stock_status_id eq get_setting('stock_status_id')} class="label label-primary" {/if}>{$product->stock_status_name}</span>
										</td>
									</tr>
								{/if}
								{if $product_options}
									{foreach from=$product_options item=product_option}
										{if $product_option.type eq 'select' || $product_option.type eq 'color'}
											<tr>
												<td><b>{$product_option.name}</b></td>
												<td>
													<select name="option[{$product_option.product_option_id}]" id="input-option{$product_option.product_option_id}" class="form-control">
														<option value="0">{translate('select', true)}</option>
														{foreach from=$product_option.option_values item=option_value}
															{assign var="product_label" value=$option_value.name}
															{if $option_value.option_value_price}
																{assign var="product_label" value=$product_label|cat:" ("|cat:$option_value.price_prefix|cat:$option_value.option_value_price|cat:")"}
															{/if}
															<option value="{$option_value.product_option_value_id}">{$product_label}</option>
														{/foreach}
													</select>
												</td>
											</tr>
										{/if}
									{/foreach}
								{/if}

								<tr>
									<td><b>{translate('label_amount',true)}</b></td>
									<td>
										<div class="quantity">
											<input type="number" name="quantity" step="1" min="1" max="" value="1" title="Qty" class="input-text qty text" pattern="[1-9]*" inputmode="numeric">
										</div>
									</td>
								</tr>
							</table>
						</div>
						{foreach from=$product_options item=product_option}
							{if $product_option.type eq 'radio'}
								<div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22">
									<label for="" class="form-label shipping-quaranty-label">{$product_option.name}</label>
									{foreach from=$product_option.option_values item=option_value}
										<div class="ship-qcell-cover">
											<label class="control control--radio has-quaranty shipping-quaranty-cell ship-qcell">
												{assign var="product_label" value=$option_value.name}
												{if $option_value.option_value_price}
												{assign var="product_label" value=$product_label|cat:" ("|cat:$option_value.price_prefix|cat:$option_value.option_value_price|cat:")"}
												{/if}
												<input type="radio" data-price="{$option_value.option_value_price|intval}" id="input-option{$product_option.product_option_id}" name="option[{$product_option.product_option_id}]" value="{$option_value.product_option_value_id}">{currency_symbol_converter($product_label)}
												<div class="control__indicator"></div>
											</label>
										</div>
									{/foreach}
								</div>
							{/if}
						{/foreach}
						{if isset($shipping_list) && !empty($shipping_list)}
							<div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22" id="shipping_list">
								<label for="" class="form-label shipping-quaranty-label">{translate('shipping')} <button type="button" data-toggle="modal" data-target="#change-shipping" class="sqlabel"> <i class="fa fa-map-marker"></i> {get_country_name()} </button></label>

								{foreach from=$shipping_list key=key item=shipping_item}
									<div class="shipping-label">
										<label class="control control--radio shipping-quaranty-cell ship-qcell">
											<input type="hidden" value="{$shipping_item.name}" class="shipping_show_name">{$shipping_item.name}

											<input type="radio" id="" name="shipping" class="shipping_data" {if $key eq 0}checked="checked"{/if} value='{json_encode($shipping_item)}'>
											<input type="hidden" value="{$shipping_item.show_price}" class="shipping_show_price" >
											{currency_symbol_converter($shipping_item.show_price)}
											<div class="control__indicator"></div>
										</label>
										<!-- starts details shipping quantity -->
										<button type="button" class="details-qship" data-id="QShipping-{$shipping_item.code}">{translate('details', true)} </button>
										<div id="QShipping-{$shipping_item.code}" class="modal fade details_qship_modal" role="dialog">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="qhip-title h4">{$shipping_item.name}</div>
													<div class="qship-text">
														{$trans="`$shipping_item.code`_detail_text"}
														{translate($trans, true)}
													</div>
													<button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt=""></button>
												</div>
											</div>
										</div>

									</div>
								{/foreach}

								{*<button type="button" class="details-qship" data-id="QShipping">details </button>
								<div id="QShipping" class="modal fade details_qship_modal" role="dialog">
									<div class="modal-dialog">
										<div class="modal-content">
											<h4 class="qhip-title">PREMIUM</h4>
											<div class="qship-text">
												There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour
											</div>
											<button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt=""></button>
										</div>
									</div>
								</div>*}

							</div>
						{else}
							<div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22">
								<label for="" class="form-label shipping-quaranty-label">{translate('shipping')} <button type="button" data-toggle="modal" data-target="#change-shipping" class="sqlabel">({get_country_name()})</button></label>
								<div class="alert alert-warning">{translate('not_shipped_message', true)}</div>
							</div>
						{/if}
						{if isset($shipping_list) && !empty($shipping_list)}
							<div class="col-sm-12 col-xs-12 add-to-card_info desktop-add-card_info pro-lr00">
								<button type="button" id="button-cart-1" {*onclick="addToCart()"*} class="green_btn add-to-card_btn">{translate('label_add_to_cart',true)}</button>
								{if $customer}
									<button type="button" class="add__heart add_heart22 {if in_array($product->id,$favorite_ids)} active{/if}" data-id="{$product->id}"></button>
								{/if}

								<!-- get credit button -->
								{if $product->price>=300 && $product->price<=10000}
									<button type="button" class="add-to-card_btn credit_btn" onClick="getClickValues()" data-target="#get_credit_modal">{translate('kredite_al',true)}</button>
								{/if}

							</div>
						{/if}
					{/if}
					<input type="hidden" name="product_id" value="{$product->id}"/>

					<div class="clearfix"></div>
					{if $product->quantity == 0 && $product->status != 9}
						<!-- out of stock -->
						<div class="outStockCover">
							<p class="notifyMe">{translate('available',true)}</p>
							<div class="outStockForm">
								<input type="email" name="subs_email" placeholder="user@domain.tld">
								<button class="notifyMeBtn" type="button">{translate('notify',true)} <i class="fa fa-check"></i> </button>
							</div>
						</div>
					{/if}

				</form>
			</div>
		</div>
	{if $product->status != 9}
		<div class="col-md-3 product-sold-by">
			<div class="sold-by-item">
				<ul class="sold-list">
					{if !empty(trim($product->seller_note))}
						<li><span class="seller_not_red">{translate('seller_note', true)} </span> {$product->seller_note}</li>
					{/if}
					<li><span class="seller_not_red">{translate('condition', true)} </span>
						{if $product->new eq '0'}{translate('new', true)}{elseif $product->new eq '1'}{translate('used', true)}{else} {translate('refurbished', true)}{/if}</li>
					<li>
						<span>{translate('seller', true)} </span>
						<small class="soldbymimelon mtooltip">
							<a class="deliver-change" href="{site_url_multi('products/seller')}?id={$product->created_by}">{get_seller($product->created_by)}</a>
							{* <div class="mtooltipcontent">
                                <ul>
                                    <li>Stored,Packed and Shipped by mimelon</li>
                                    <li>Guaranteed Authentic</li>
                                    <li>Ships Quickly</li>
                                </ul>
                            </div> *}
						</small>
					</li>
				</ul>
			</div>
			<div class="sold-by-item sold-by-delivery">
				<ul class="sold-list">
					<li>{translate('all_prices_include_vat',true)} <span class="deliver-change vat-popup vat-popup22" data-toggle="modal" data-target="#VAT">{translate('details', true)}</span></li>
					<li>{translate('shipping',true)} <span class="deliver-change shipping-popup shipping-popup22" data-toggle="modal" data-target="#Shipping">{translate('details', true)}</span></li>
					<!-- starts yeni elave -->
					<div class="des-info-txt des-packing">
						<div class="des-info-table des_info_table">
							<ul>
								<li>
									<b>{translate('packaging_details', true)}:</b>
									<br>

									<div>{translate('length', true)} {round($product->length, 2)} {$product->length_class_unit}</div>
									<div>{translate('width', true)} {round($product->width, 2)} {$product->length_class_unit}</div>
									<div>{translate('height', true)} {round($product->height, 2)} {$product->length_class_unit}</div>
									<div></div>
								</li>
								<li>
									<b>{translate('port', true)}:</b>
									<br>
									<div>{$product->country_name} {$product->region_name}</div>
								</li>
								<li>
									<b>{translate('lead_time', true)}: </b>
									<br>
									<div>{sprintf(translate('shipped_in',true), $product->day)}</div>
								</li>
							</ul>
						</div>
					</div>
					<!-- ends yeni elave -->
					<li class="payment-method-cell">
						<ul class="payment-methods">
							<li>{translate('payment',true)} : </li>
							<li><img src="{base_url('uploads/sprite_img.png')}" alt="Visa" class="sprite-visa"></li>
							<li><img src="{base_url('uploads/sprite_img.png')}" alt="Mastercard" class="sprite-mastercard"></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	{/if}
		<!-- add to cart only for mobile -->
	{if isset($shipping_list) && !empty($shipping_list) && !($product->quantity == 0 && $product->status != 9)}
		<div class="col-md-12 col-sm-12 col-xs-12 add-to-card_info mobile-add-card_info">
			<button type="button" id="button-cart-2" {*onclick="addToCart()"*} class="green_btn add-to-card_btn">{translate('label_add_to_cart',true)}</button>

			<!-- starts mobile get credit button -->
			{if $product->price>=300 && $product->price<=10000}
			<button type="button" class="add-to-card_btn credit_btn mobile_credit_btn" onClick="getClickValues()" data-toggle="modal" data-target="#get_credit_modal">{translate('kredite_al',true)}<button>
			{/if}
			<!-- ends mobile get credit button -->

			{if $customer}
				<button type="button" class="add__heart {if in_array($product->id,$favorite_ids)} active{/if}" data-id="{$product->id}"></button>
			{/if}

		</div>
	{/if}





	</div>

<!-- starts product description-->
<div class="gap"></div>
<div class="m-container product-description-cover pro-des-cover22">
	<div class="col-md-12 col-sm-12 col-xs-12 pro-des-list pro-des-list22">
		<h2 class="pro-des-title">{translate('product_details',true)}</h2>
		<ul class="des-list des-list-js">
			{if $product->manufacturer_id}
			<li>{translate('brand_name', true)} {$product->manufacturer_name}</li>
			{/if}
			<li>{translate('model')} {$product->model}</li>
			{if isset($attributes) && !empty($attributes)}
			{foreach from=$attributes item=attribute}
			<li>{$attribute->name}:  {$attribute->value}</li>
			{/foreach}
			{/if}
		</ul>
		<a href="javascript:void(0);" class="des-see-more22">{translate('see_more', true)}</a>
		<a href="javascript:void(0);" class="des-see-less22 i-hide">{translate('see_less', true)}</a>
		<!-- when click see more btn then show all items -->
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12 pro-des-cover">
		<div class="des-info-txt">
			<h2 class="pro-des-title pro_des_title">{translate('label_product_description',true)}</h2>
			<div class="des-info">
				{if $product->status == 9}
					{html_entity_decode(html_entity_decode($product->description))}
				{else}
					{$product->description}
				{/if}
			</div>
		</div>
	</div>
</div>
<div class="m-container product-review-cover">
	<div class="col-md-12 reviews-cover reviews-cover2">
		<p class="reviews-btn-txt">{translate('form_label_reviews',true)}</p>
		<div class="product-caption-rating2">
			<div class="rate2">{translate('rate', true)}:</div>
			<span class="gl-star-rating" data-star-rating="">
			<select id="star-rating-3" name="rating">
				<option value="">{translate('select_rating', true)}</option>
				<option value="5">5</option>
				<option value="4">4</option>
				<option value="3">3</option>
				<option value="2">2</option>
				<option value="1">1</option>
			</select>
		</div>
	</div>
	<div class="col-md-12 reviews-form">
		<!-- error -->
		<div class="col-md-12 review_form_error error-alert alert-messages i-hide"></div>
		<!-- success -->
		<div class="col-md-12 review_form_success success-alert alert-messages i-hide"></div>

		<input class="form_element form-control" type="text" name="review_subject" placeholder="{translate('form_placeholder_subject',true)}">
		<textarea class="form_element form-control" name="review_text" id="" cols="30" rows="10" placeholder="{translate('form_placeholder_comment',true)}"></textarea>
		<div class="text-center"><button class="btn reviews-btn" onclick="sendReview()">{translate('form_label_send',true)}</button></div>
	</div>


	{if $reviews}
	{foreach from=$reviews item=review}
		<div class="col-md-12 reviews_cover">
			<div class="reviews-star-info">
				<a href="#"  class="reviewer-name">{$review->user_name}</a>
				<ul class="product-caption-rating">
					{for $rating=1 to 5}
					{if $rating <= $review->rating}
					<li class="rated"><i class="fa fa-star"></i></li>
					{else}
					<li><i class="fa fa-star-o"></i></li>
					{/if}
					{/for}
				</ul>
				<span class="reviewer-time">{$review->created_at}</span>
			</div>
			<div class="reviews-infos">
				<h2 class="reviews-infos-title">{$review->subject}</h2>
				<div class="txt_main">
					{$review->text}
				</div>
			</div>
		</div>
	{/foreach}
	{/if}

</div>

<!-- starts credit modal  -->
	<div id="get_credit_modal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- starts count credit section -->
				<div class="count_credit_content credit_section1">
					<div class="modal-header">
						<h4 class="modal-title">{translate('krediti_hesabla',true)}</h4>
					</div>
					<div class="modal-body">
						<div class="clearfix"></div>
						<form>
							<div class="col-sm-12 col-xs-12">
								<div class="col-sm-6 col-xs-12 padding000 credit_calc">
									<div class="credit_range_slider">
										<div class="cr_prc">{translate('kredit_muddeti',true)}</div>
										<div class="cr_range_title"><span id="mon_range">10</span> {translate('month',true)}</div>
										<!-- starts range for month -->
										<input type="hidden" value="{$product->price_simple}" class="prod_price" >
										<div class="slidecontainer credit_range">
											<input type="range" min="3" max="36" value="3" class="slider1" onChange="getValues()" id="month_range">
										</div>
										<!-- ends range for month -->
									</div>
									<div class="credit_range_slider">
										<div class="cr_prc">{translate('ayliq_geliriniz',true)}</div>
										<div class="cr_range_title"><span id="slr_range">980</span> AZN</div>
										<!-- starts range for monthly salary -->
										<div class="slidecontainer credit_range">
											<input type="range" min="250" max="1500" value="250" class="slider2" onChange="getValues()" id="salary_range">
										</div>
										<!-- ends range for monthly salary -->
									</div>
									<div class="working_place">
										<label class="working_place cr_prc">{translate('resmi_is_yeri',true)}</label>
										<ul class="credit-work">
											<li>
												<input type="radio" value="1" name="working-place" class="working-place" id="working-yes" checked>
												<label for="working-yes" class="working_lbl">{translate('yes',true)}</label>
											</li>
											<li>
												<input type="radio" value="0" name="working-place" class="working-place" id="working-no">
												<label for="working-no" class="working_lbl">{translate('no',true)}</label>
											</li>
										</ul>
									</div>
									<div class="clearfix"></div>
									<div class="work_experience">
										<span class="cr_prc">{translate('is_staji',true)}</span>
										 <div class="wex_slc">
											<input type="number" min="0" name="staj_time" class="form-control staj_time" placeholder="-" aria-label="..." value="">
											<select class="form-control staj_sec" name="staj_sec">
												<option value="ay">{translate('month',true)}</option>
												<option value="il">{translate('year',true)}</option>
											</select>
										</div><!-- /input-group -->
									</div>
									<div class="clearfix"></div>
									<div class="cr_prc cr_count">{translate('kredit_meblegi_texmini',true)}</div>
								</div>
								<div class="col-sm-6 col-xs-12 padding000">
									<div class="credit_pro_details">
										<div class="credit_details123 credit_details1">
											<h4 class="credit_model">{$product->name}</h4>
											{* <div class="credit_model_name">Galaxy Note10 Dual SIM</div> *}
										</div>
										<div class="credit_details123 credit_details2">
											<div class="cr-dt">
												<div class="cr_prc">{translate('mehsul_qiymeti',true)}</div>
												<div class="cr_prc_cnt mhs_price">{currency_symbol_converter($product->price)}</div>
											</div>
											<div class="cr-dt">
												<div class="cr_prc">{translate('faiz_derecesi',true)}</div>
												<div class="cr_prc_cnt">%22</div>
											</div>
										</div>
										<input type="hidden" class="hide_product_name" value="{$product->name}">
										<div class="credit_details3">
											<div class="cr_prc">{translate('ayliq_odenis',true)}</div>
											<div class="cr_sum_price"><span class='ayliq_odenis'>300</span> AZN</div>
											<div class="no_salary_cover">
												<span class="no_salary i-hide">{translate('ayliq_gelir_kifayetdeyil',true)}</span>
												<span class="no_work i-hide">{translate('resmi_isyeri_yoxdur',true)}</span>
												<span class="no_staj i-hide">{translate('is_staji_catmir',true)}</span>
											</div>
										</div>
									</div>
									<button type="button" class="credit_btn1"> {translate('muraciet_et',true)} </button>
								</div>
							</div>
						</form>
						<div class="clearfix"></div>
					</div>
				</div>
				<!-- ends count credit section -->

				<!-- starts credit form section -->
				<div class="count_credit_content credit_form i-hide">
					<div class="modal-header">
						<h4 class="modal-title">{translate('kredit_muracieti',true)}</h4>
						<div class="cr_model_name">{$product->name}</div>
					</div>
					<div class="modal-body credit_form_body">
						<div class="clearfix"></div>
						<form>
							<div class="col-sm-12 col-xs-12">
								<div class="col-sm-6 col-xs-12 padding000 credit_calc">
									<div class="crdt-frm-element">
										<label for="crdt-username">{translate('ad_soyad',true)}</label>
										<input type="text" id="crdt-username" name="crdt-username">
									</div>
									<div class="crdt-frm-element">
										<label for="crdt-pincode">{translate('pin_kod',true)}</label>
										<input type="text" id="crdt-pincode" name="crdt-pincode">
									</div>
									<div class="crdt-frm-element">
										<label for="crdt-location">{translate('yasayis_yeri',true)}</label>
										<input type="text" id="crdt-location" name="crdt-location">
									</div>
									<div class="crdt-frm-element">
										<label for="crdt-phone">{translate('ev_telefonu',true)}</label>
										<input type="number" min="1" id="crdt-phone" name="crdt-phone">
									</div>
								</div>
								<div class="col-sm-6 col-xs-12 padding000">
									<div class="crdt-frm-element">
										<label for="crdt-ser">{translate('seriya_no',true)}</label>
										<input type="text" id="crdt-ser" name="crdt-ser">
									</div>
									<div class="crdt-frm-element">
										<label for="crdt-birthday">{translate('dogum_tarixi',true)}</label>
										<div class="birthday-group">
											<select name="birthday1" class="form-control" id="birthday1">
												<option value="0" class="disabled" selected>{translate('day',true)}</option>
												{for $x=1 to 31}
												<option value="{$x}">{$x}</option>
												{/for}
											</select>
											<select name="birthday2" class="form-control" id="birthday2">
												<option value="0" class="disabled" selected>{translate('month',true)}</option>
												{for $x=1 to count($months)-1}
												<option value="{$x}">{$months[$x]}</option>
												{/for}
											</select>
											<select name="birthday3" class="form-control" id="birthday3">
												<option value="0" class="disabled" selected>{translate('year',true)}</option>
												{for $x=1935 to 2001}
												<option value="{$x}">{$x}</option>
												{/for}
											</select>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="crdt-frm-element">
										<label for="crdt-phn">{translate('mobil_nomre',true)}</label>
										<input type="number" min="1" id="crdt-phn" name="crdt-phn">
									</div>
									<div class="crdt-frm-element">
										<label for="crdt-e_mail">{translate('e_mail_address',true)}</label>
										<input type="text" id="crdt-e_mail" name="crdt-e_mail">
									</div>
								</div>
								<div class="col-sm-12 col-xs-12 crd-frm-note padding000">
									<div class="crd-note-title"><small>*</small><span>{translate('qeyd',true)}</span></div>
									<div class="crd-note">{translate('raziliq_verirsiniz',true)}</div>
								</div>
								<div class="col-sm-12 col-xs-12 credit_prev_next padding000">
									<div class="col-sm-6 col-xs-6 padding000 crdt_lft00">
										<img src="templates/mimelon/assets/img/icons/arrow_left_crdt.svg">
										<button type="button" class="credit_frm_prev">Geri</button>
									</div>
									<div class="col-sm-6 col-xs-6 padding000">
										<button type="button" class="credit_frm_snd" onclick="sendCredit()">{translate('send',true)}</button>
									</div>
								</div>
							</div>
						</form>
						<div class="clearfix"></div>
					</div>

					<!-- starts form succces -->
					<div class="modal-body credit_success_body i-hide">
						<div class="text-center success_credit_img"><img src="templates/mimelon/assets/img/icons/credit_success.svg"></div>
						<div class="credit_success cr_model_name">{translate('success_credit_request',true)}</div>
						<div class="text-center">
							<button type="button" class="btn btn-default success_credit_btn" data-dismiss="modal">OK</button>
						</div>
					</div>
					<!-- ends form success -->
				</div>
				<!-- ends credit form section -->
			</div>
		</div>
	</div>
<!-- get credit button -->

<!-- ends credit modal -->


	<div class="gap"></div>
	{if $similar_products}
		<!-- starts trend section -->
		<section class="container">
			<div class="widget-title trend_title h3"><a href="">{translate('similar_products', true)}</a></div>
			<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
				{foreach from=$similar_products item=product}
					<div class="owl-item">
						{include file="templates/mimelon/_partial/product.tpl"}
					</div>
				{/foreach}
			</div>
		</section>
		<!-- ends trend section -->
	{/if}

	<section class="container">
		<div class="col-md-12">
			{if isset($seo_links) && $seo_links}
				<div class="clearfix"></div>
				<ul class="seo_tags owl-carousel">
					{foreach $seo_links as $seo_link}
						<li>
							<a href="{site_url_multi($seo_link['slug'])}/">{$seo_link['name']}</a>
						</li>
					{/foreach}
				</ul>
			{/if}
		</div>
	</section>


<!-- ends product description-->
{if isset($customer_also_vieweds) && !empty($customer_also_vieweds)}
<!-- starts trend section -->
<div class="gap"></div>
<section class="container">
	<div class="h3 widget-title trend_title"><a href="#">{translate('customer_also_viewed', true)}</a></div>
	<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
		{foreach from=$customer_also_vieweds item=product}
		<div class="owl-item">
			{include file="templates/mimelon/_partial/product.tpl"}
		</div>
		{/foreach}
	</div>
</section>
<!-- ends trend section -->
{/if}

<div class="gap"></div>
<script>
	var product_id = "{$product->id}";
	{literal}
	function sendReview() {
		 subject = $('input[name="review_subject"]').val();
		 text = $('textarea[name="review_text"]').val();
		 rating = $('select[name="rating"]').val();

		$.ajax({
			type: 'post',
			url: $('base').attr('href')+"/product/review",
			dataType: 'json',
			data : {product: product_id, text: text, subject: subject, rating: rating},
			success: function (response) {
				if(response['success']){
					$('input[name="review_subject"]').val(null);
					$('textarea[name="review_text"]').val(null);
					$('div.review_form_error').hide();
					$('div.review_form_success').show();
					$('div.review_form_success').html(response['message']);
				} else {
					$('div.review_form_success').hide();
					$('div.review_form_error').show();
					$('div.review_form_error').html(response['message']);
				}
			}
		});
	}
	{/literal}
</script>
{/block}
