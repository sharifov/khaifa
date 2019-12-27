{extends file=$layout}
{block name=content}
<!-- //added new starts book edit modal -->
<div id="bookEdit" class="modal fade bookEdit delivery-modal" role="dialog">
	<div class="modal-content">
		<div class="bookEdit-title">
			<div class="tleft"><span>{translate('edit_address')}</span></div>
			<div class="tright">
				<div class="checkbox_cover">
					<label class="container__checkbox disabled-input">
					<input name="is_copied" type="checkbox" data-toggle="toggle" checked>
					<span class="checkmark"></span>
						Set us default address
					</label>
				</div>
			</div>
		</div>
		<div class="bookEdit-content">
			<form action="" id="address_form_data">
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('firstname')}</label>
					<input class="form-control main-form-element" type="text" name="firstname" required placeholder="{translate('firstname')}" id="firstname" value="" >
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="lastname" class="form-label">{translate('lastname')}</label>
					<input class="form-control main-form-element" type="text" name="lastname" required placeholder="{translate('lastname')}" id="lastname" value="" >
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="company" class="form-label">{translate('company')}</label>
					<input class="form-control main-form-element" type="text" name="company" placeholder="{translate('company')}" id="company" value="" >
				</div>
				<div class="form-group form_group account-element mobile_account_element">
					<label for="" class="form-label">{translate('phone')}</label>
					<input class="form-control main-form-element mobilenumber-flag  number-validation" type="tel" id="mobilenumber"  value="" name="phone" placeholder="{translate('phone')}" >
					<i class="fa fa-chevron-down mobile-chevron"></i>
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="city" class="form-label">{translate('city')}</label>
					<input class="form-control main-form-element" type="text" name="city" placeholder="" id="city" value="">
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="country_id" class="form-label">{translate('country')}</label>
					<select name="country_id" class="form-control main-form-element isnot-sell" id="country_id">
						<option value="">{translate('please_select')}</option>
						{if $countries}
						{foreach from=$countries item=country}
							<option value="{$country->id}">{$country->name}</option>
						{/foreach}
						{/if}
					</select>
				</div>

				<div class="form-group form_group account-element payment-card-number">
					<label for="zone_id" class="form-label">{translate('region')}</label>
					<select name="zone_id" class="form-control main-form-element" id="zone_id">
						<option></option>
					</select>
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('address')}</label>
					<input class="form-control main-form-element" type="text" name="address_1" placeholder="{translate('address_1')}" value="">
					<input class="form-control main-form-element address2-optional" type="text" name="address_2" value="" placeholder="{translate('address_2')}">
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('postcode')}</label>
					<input class="form-control main-form-element" type="text" name="postcode" id="postcode" placeholder="" value="">
				</div>
				<div class="text-center form_btn_cover save-change-btn">
					<button type="button" id="btn_ajax_add_address" class="btn reviews-btn payment-saved-btn">{translate('save')}</button>
				</div>
			</form>
		</div>
		<button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="{base_url('templates/mimelon/assets/img/icons/cart-close-icon.svg')}" alt=""></button>
	</div>
</div>
<!-- //added new ends book edit modal -->
<section class="checkout-section">
	<div class="container checkout-cover">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<h1 class="txt-bold26 uppercase checkout-title">{$title}</h1>
		</div>
		<div class="checkout-section-cover">
			<form id="payment-form" {if isset($payment_methods) && !empty($payment_methods)}action="{site_url_multi('checkout/confirm')}" method="POST"{/if}>

			{if isset($error)}
			<!-- starts checkout error message -->
			<div class="col-md-8 col-sm-12 col-xs-12">
				<div class="ck-error-msg">
					{$error}
				</div>
			</div>
			<!-- ends chekcout error message -->
			{/if}

			<div class="clearfix"></div>
			<div class="col-md-8 col-sm-12 col-xs-12 checkout-details-item ">
				<div class="checkbox-details">
						<div class="checkbox-detail-item">
							<h3 class="check-inner-title fontBold20 uppercase">{translate('email')}</h3>
							<p class="txt-checkbox">{$customer->email}</p>
						</div>
						<div class="checkbox-detail-item">
							<div class="txt-checkbox">{translate('promo_code_title')}</div>
							<div class="deliver-apply">
								<input class="apply-input" type="text" placeholder="{translate('enter_coupon_code')}" value="{$coupon}">
								<button type="button" class="apply-btn">{translate('apply')}</button>
							</div>
						</div>
						<div class="checkbox-detail-item">
							<div class="checkbox-detail-title">
								<h3 class="check-inner-title fontBold20 uppercase">{translate('delivery_address')}<span class="text-danger">*</span></h3>
							</div>
							{if !isset($addresses) && empty($addresses)}
								{if !isset($addresses) && empty($addresses)}
									<span class="text-danger">{translate('you_must_enter_address', true)}</span>
								{/if}
							{/if}
							<div><a href="javascript:void(0)" class="btn reviews-btn add_address_btn" data-toggle="modal" data-target="#bookEdit">{translate('add_new_address')}</a></div>
							<div class="checkbox-deliver-address">
								<div class="checkbox_deliver_address">
									{if isset($addresses) && !empty($addresses)}
									{foreach from=$addresses item=$address}
									<div class="checkbox-deliver-add txt-checkbox">
										<div class="checkbox-free-paid payment-type-choose">
											<div class="free_paid">
												<label class="control control--radio">
													<input type="radio" name="address_id" value="{$address.id}" {if !$country && $customer->address_id == $address.id}checked="checked"{/if}>
													<ul>
														<li>{$address.firstname} {$address.lastname}</li>
														<li>{$address.address1}</li>
														<li>{$address.address2}</li>
														<li>{$address.city}</li>
														<li>{$address.country}</li>
														<li>{$address.zone}</li>
														<li>{$address.postcode}</li>
														<li>{$address.phone}</li>
													</ul>
													<div class="control__indicator"></div>
												</label>
											</div>
										</div>
										<span onclick="window.location.href='{site_url_multi('account/edit_address_book')}/{$address.id}'" class="btn reviews-btn">{translate('edit_address')}</span>
									</div>
									{/foreach}
									{/if}
								</div>
							</div>
						</div>
						<div class="checkbox-detail-item">
							<h3 class="check-inner-title fontBold20 uppercase">{translate('payment_type')}</h3>
							<div class="txt-bold14 payment_type_cover">
								{if isset($payment_methods) && !empty($payment_methods)}
									{foreach from=$payment_methods item=payment_method}
										<div class="checkbox-free-paid payment-type-choose">
											<div class="free_paid">
												<label class="control control--radio txt-bold14">
													<input type="radio" name="payment_method" value="{$payment_method->code}" {if ($payment_method_selected == $payment_method->code)}checked="checked"{/if}/>{$payment_method->name}
													<div class="control__indicator"></div>
												</label>
											</div>
										</div>
									{/foreach}
								{else}
								<div class="checkbox-free-paid payment-type-choose">
									<div class="free_paid">
										<label class="control control--radio txt-bold14">
											No payment method defined for your country
										</label>
									</div>
								</div>
								{/if}

								{if isset($payment_methods) && !empty($payment_methods)}
								  <!-- add frames script -->
								<div class="frames-container i-hide">
								<!-- form will be added here -->
								</div>
									<!-- add submit button -->
								<div class="all-terms-total">
									<a target="_blank" href="{site_url_multi(__('terms-and-conditions'))}" class="read-terms-conditions">{translate('read_term', true)}</a>
								</div>
								<div class="checkbox_cover">
									<label class="container__checkbox">
										<input name="agreement" type="checkbox" data-toggle="toggle">
										<span class="checkmark"></span>
										{translate('agree', true)}
									</label>
								</div>
								<ul class="we-accept-list">
									<li><span>{translate('we_checkout')}</span></li>
									<li><img src="{base_url('uploads/sprite_img.png')}" alt="Visa" class="sprite-visa"></li>
									<li><img src="{base_url('uploads/sprite_img.png')}" alt="Mastercard" class="sprite-mastercard"></li>
								</ul>
								{/if}

							</div>
						</div>

						{if isset($payment_methods) && !empty($payment_methods)}
						<div class="checkbox-btn">
							<button type="submit" id="pay-now-button" class="green_btn add-to-card_btn">{translate('checkout')}</button>
						</div>
						{/if}
				</div>
			</div>
			</form>
			{if true}
			<div class="col-md-4 col-sm-12 col-xs-12 shopping-cart-cover scc">
		        <div class="dropdown-head shopping-head shead s_head_before"><h4>My <span>bag: {count($cart)} Items</span></h4></div>
		        <div class="col-sm-12 col-xs-12 carts-cover ncarts_cover">

		           	<div class="col-xs-12 col-sm-12 carts_types carts_types_checkout">

						{foreach from=$vendors key=vendor item=products}
						<div class="row carts_items crt-type1">
								<div class="div-xs-12 col-sm-12 plr10">
									<h4 class="carts_type_name">{translate('seller', true)} : {$vendor}</h4>
								</div>
							{foreach $products as $product}
								<div class="col-xs-12 col-sm-12 shopping-item">
									<div class="col-md-6 shopping-cart-img">
										<div class="shopping-img">
											<a href="{site_url_multi($product['slug'])}">
												<img src="{base_url('uploads/')}{$product['image']}" alt="{$product['name']}">
											</a>
										</div>
									</div>
									<div class="col-md-6 shopping-cart-details scd">
										<div class="product-caption-price">
											<span class="product-caption-price-new">{currency_formatter($product['price'], $product['currency'], $current_currency)}</span>
											<button class="cart-item-btn" onclick="removeCart({$product['cart_id']},false); window.location.reload()">
												<img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">
											</button>
										</div>
										<div class="cart-txt-detail">{$product['name']}
										</div>
										<div class="qty text-left">
											<ul>
												<li><strong>Qty:</strong> {$product['quantity']}</li>
											</ul>
										</div>
										{*<div class="qty text-left">
											<ul>
												<li><strong>Express:</strong> 32 <i title="AZN" class="fa fa-azn">m</i></li>
											</ul>
										</div>*}
									</div>
								</div>
							{/foreach}
						</div>
						{/foreach}

		           	</div>

		            <!-- starts cart content -->
		           <div class="col-xs-12 col-sm-12 cart_cnt_top">
				   {foreach $shipments as $shipment}
					   <div class="bb crt_total">
						   <div class="cart-total">
							   <div class="ncart-txt crt_item">{translate('seller', true)} : {$shipment['seller_name']}</div>
							   <div class="cart-total-content crt_item">
								   <p class="total-txt">{translate('delivery', true)}</p>
								   <p class="product-caption-price">
									   <span class="product-caption-price-new">{$shipment['shipment_name']}</span>
								   </p>
							   </div>
							   <div class="cart-total-content crt_item">
								   <p class="total-txt">{translate('price', true)}:</p>
								   <p class="product-caption-price">
									<span class="product-caption-price-new"> {$shipment['price']}
									</span>
								   </p>
							   </div>
						   </div>
					   </div>
				   {/foreach}

				   {if $coupon}
					   <div class="bb crt_total">
						   <div class="cart-total">
							   <div class="cart-total-content crt_item crt_sum">
								   <p class="total-txt">{translate('coupon', true)} :</p>
								   <p class="product-caption-price">
		                            <span class="product-caption-price-new"> {$coupon_total}
		                            </span>
								   </p>
							   </div>
						   </div>
					   </div>
				   {/if}

				   {if (int)$payment_total_price > 0}
					   <div class="bb crt_total">
						   <div class="cart-total">
							   <div class="cart-total-content crt_item crt_sum">
								   <p class="total-txt">{translate('payment', true)} :</p>
								   <p class="product-caption-price">
		                            <span class="product-caption-price-new"> {$payment_total}
		                            </span>
								   </p>
							   </div>
						   </div>
					   </div>
				   {/if}

		            <div class="bb crt_total">
		                <div class="cart-total">
		                    <div class="cart-total-content crt_item crt_sum">
		                        <p class="total-txt">{translate('subtotal', true)} :</p>
		                        <p class="product-caption-price">
		                            <span class="product-caption-price-new"> {$subtotal}
		                            </span>
		                        </p>
		                    </div>
		                </div>
		            </div>


		            <div class="bb crt_total">
		                <div class="cart-total">
		                    <div class="cart-total-content crt_item crt_sum">
		                        <p class="total-txt">{translate('total')} :</p>
		                        <p class="product-caption-price">
		                            <span class="product-caption-price-new"> {$total}
		                            </span>
		                        </p>
		                    </div>
		                </div>
		            </div>
		            {*<div class="total-btns flex">
		                <a href="https://mimelon.com/checkout/cart" class="btn reviews-btn nreviews-btn">View bag</a>
		                <a href="https://mimelon.com/checkout" class="btn reviews-btn nreviews-btn green_btn">Checkout</a>
		            </div>*}
		           </div>
		           <!-- ends cart content -->
		        </div>
    		</div>
		    {else}
		    <div class="col-md-4 col-sm-12 col-xs-12 checkout-bag-item">
						<div class="row checkout-bag">
							<div class="dropdown-head shopping-head">
								<h4>My <span>Bag: {count($cart)} items</span></h4>
							</div>

							<div class="col-md-12 checkout-carts-cover">
								{foreach from=$cart item=product}
								<div class="row shopping-item">
									<div class="col-md-6 shopping-cart-img">
										<div class="shopping-img">
											<a href="{site_url_multi('product/')}{$product.slug}">
												<img src="{base_url('uploads/')}{$product['image']}" alt="{$product['name']}">
											</a>
										</div>
									</div>
									<div class="col-md-6 shopping-cart-details">
										<div class="product-caption-price">
											<span class="product-caption-price-new">
												{currency_formatter($product['price'], $product['currency'], $current_currency)}
											</span>
										</div>
										<div class="cart-txt-detail">{$product['name']}</div>
										<div class="qty text-left">
											<ul>
												<li> <strong>{translate('qty')} </strong> {$product['quantity']} </li>
												{if $product['option']}
												{foreach from=$product['option'] item=option}
													<li> <strong>{$option['name']}:</strong> {$option['value']} </li>
												{/foreach}
												{/if}
											</ul>
										</div>
										<div class="qty text-left">
											<ul>
											{if $product['shipping'] }
												{foreach from=$product['shipping'] item=shipping}
												<li> <strong>{$shipping['name']}:</strong> {currency_formatter($shipping['price'], $shipping['currency'], $current_currency)} </li>
													<button class="cart-item-btn" onclick="removeCart({$product['cart_id']},false); window.location.reload()">
														<img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">
													</button>
												{/foreach}
											{/if}
											</ul>
										</div>
									</div>
								</div>
								{/foreach}
							</div>
						</div>
						<div class="col-md-12 col-xs-12 cart_total_cover">
							<div class="row cart-total">
								<div class="cart-total-content">
									<p class="total-txt bold16">{translate('subtotal', true)}</p>
									<p class="product-caption-price">
										<span class="product-caption-price-new">{$subtotal}</span>
									</p>
								</div>
							</div>
							<div class="row cart-total">
								<div class="cart-total-content">
									<p class="total-txt bold16">{translate('shipping', true)}</p>
									<p class="product-caption-price">
										<span class="product-caption-price-new">{$shipping_total}</span>
									</p>
								</div>
							</div>
							{if (int)$payment_total_price > 0}
							<div class="row cart-total">
								<div class="cart-total-content">
									<p class="total-txt bold16">{translate('payment', true)}</p>
									<p class="product-caption-price">
										<span class="product-caption-price-new">{$payment_total}</span>
									</p>
								</div>
							</div>
							{/if}
							{if $coupon}
							<div class="row cart-total">
								<div class="cart-total-content">
									<p class="total-txt bold16">{translate('coupon', true)}</p>
									<p class="product-caption-price">
										<span class="product-caption-price-new">{$coupon_total}</span>
									</p>
								</div>
							</div>
							{/if}
							<div class="row cart-total checkout-cart-total">
								<div class="cart-total-content">
									<p class="total-txt bold20">{translate('total')}</p>
									<p class="product-caption-price">
										<span class="product-caption-price-new bold26">
											{$total}
										</span>
									</p>
								</div>
							</div>
						</div>
			</div>
			{/if}

		</div>
	</div>
</section>
<!-- ends faq-->
{/block}
