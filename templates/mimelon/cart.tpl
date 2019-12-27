{extends file=$layout}
{block name=content}
<header class="m-container page-header">
	<ol class="breadcrumb page-breadcrumb">
		<li class="active"><a href="#">{translate('home',true)}</a></li>
		<li><a href="#">{translate('cart',true)}</a></li>
	</ol>
</header>
{assign var="total_price" value=0}
<section class="container">
	{if $products}
	<div class="col-md-12 bag-all-cover">
		<div class="col-md-9 bag-all-item-info">
			{form_open(site_url_multi('checkout/cart'))}
			<table class="bag-item-table">
				<thead>
					<tr>
						<th>{translate('cart_label_item',true)}</th>
						<th>{translate('cart_lable_quantity',true)}</th>
						<th>{translate('cart_label_unit_price',true)}</th>
						<th>{translate('cart_label_shipping_price',true)}</th>
						{* <th>{translate('cart_label_total_price',true)}</th> *}
						<th>{translate('cart_label_total_price',true)}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>

				{foreach from=$products item=product}
					<tr id="cart{$product.cart_id}" class="wow fadeInUp">
						<td class="wow fadeInUp">
							<div class="bag-table-item-img pull-left">
								<a href="{site_url_multi('product/')}{$product.slug}">
									<img src="{base_url('uploads/')}{$product.image}" alt="{$product.name}">
								</a>
							</div>
							<div class="bag-table-item-txt text-left">
								<h2 class="font16 bold uppercase">{$product.name}</h2>
								{* <div class="txt_main">{if !empty($product.description)} {$product.description|truncate:110} {/if}</div> *}
								{if $product['option']}
									{foreach from=$product['option'] item=option}
										<li> <strong>{$option['name']}:</strong> {$option['value']}</li>
									{/foreach}
								{/if}
								{if $product['shipping'] }
									{foreach from=$product['shipping'] item=shipping}
										<li> <strong>{$shipping['name']}:</strong> {currency_formatter($shipping['price'], $shipping['currency'], $current_currency)}</li>
									{/foreach}
								{/if}
							</div>
						</td>
						<td class="flex-td wow fadeInUp">
							<div class="bag-mobile-item-name bag-mob-name">{translate('cart_lable_quantity',true)}</div>
							<div class="quantity">
								<input type="number" name="quantity[{$product.cart_id}]" onchange="updateQuantity({$product.cart_id},this)" step="1" min="0" max="" value="{$product.quantity}" class="input-text qty text" pattern="[0-9]*" inputmode="numeric">  
								<a class="inc qty-button"></a><a class="dec qty-button"></a>
							</div>
						</td>
						<td class="flex-td wow fadeInUp">
							<div class="bag-mobile-item-name bag-mob-name">{translate('cart_label_unit_price',true)}</div>
							<span class="product-caption-price-new font18">
								{currency_formatter($product.price, $product.currency, $current_currency)}
							</span> 
						</td>
						<td class="flex-td wow fadeInUp">
							<div class="bag-mobile-item-name bag-mob-name">{translate('cart_label_shipping_price',true)}</div>
							<span class="product-caption-price-new font18">
								{currency_formatter($product.shipping_price, $product.shipping_currency, $current_currency)}
							</span> 
						</td>
						{* <td class="flex-td wow fadeInUp">
							<div class="bag-mobile-item-name bag-mob-name">{translate('total_price')}</div>
							<span class="product-caption-price-new bold font18">
								{currency_formatter($product.total, $product.currency, $current_currency)}
							</span> 
						</td> *}
						<td class="flex-td wow fadeInUp">
							<div class="bag-mobile-item-name bag-mob-name">{translate('cart_label_total_price',true)}</div>
							<span class="product-caption-price-new bold font18">
								{currency_formatter($product.total_for_shipping+$product.total, $product.currency, $current_currency)}
							</span> 
						</td>
						<td class="flex-td wow fadeInUp">
							<button type="submit" class="bag-table-remove-btn">
								<i class="fa fa-refresh" aria-hidden="true"></i>
							</button>
							<button class="bag-table-remove-btn" onclick="removeCart({$product.cart_id})">
								<img src="{base_url('templates/mimelon/assets/img/icons/cart-close-icon.svg')}" alt="">  
							</button>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			{form_close()}
		</div>

		<div class="col-md-3 bag-all-total-cover wow fadeInUp">
			<div class="bag-all-item-total">
				<div class="bag-all-total">
					<h4 class="bag-all-title">{translate('total')}</h4>
					<div class="bag-sub-total">
						<div class="sub-total">
							{translate('sub_total')}
							<span class="product-caption-price-new">
								{$subtotal}
							</span> 
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="bag-sub-total bag_sub_total">
						<div class="sub-total">
							{translate('shipping_total', true)}
							<span class="product-caption-price-new">
								{$shipping_price}
							</span> 
						</div>
						<div class="clearfix"></div>
					</div>
					{if $coupon}
					<div class="bag-total-currency bold font14">
						{translate('coupon')} ({$coupon})
						<span class="product-caption-price-new">
							{$coupon_total}
						</span> 
					</div>
					{/if}
					<div class="bag-total-currency bold font14">
						{translate('total')} 
						<span class="product-caption-price-new">
							{$total}
						</span> 
					</div>
					<div class="deliver-apply">
						<input class="apply-input" type="text" placeholder="{translate('enter_coupon')}" value="{$coupon}">
						<button type="submit" class="apply-btn">{translate('apply')}</button>
					</div>
					
					<div class="checkbox-btn">
						<button onclick="window.location.href='{site_url_multi('checkout')}'"  class="green_btn add-to-card_btn">{translate('checkout', true)}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-xs-12 bag-shipment-info">
		<div class="shipment-info wow fadeInUp">
			<strong>{translate('text_1_title', true)}</strong>
			{translate('text_1_description', true)}
		</div>
		<div class="shipment-info wow fadeInUp">
			<strong>{translate('text_2_title', true)}</strong>
			{translate('text_2_description', true)}
		</div>
	</div>
	{else}
		{translate('empty',true)}
	{/if}
	<div class="col-xs-12 bag-continue-shop-btn text-center form_btn_cover">
		<a href="{base_url()}" class="btn reviews-btn con_shopping wow fadeInUp">{translate('continue_shopping')}</a>
	</div>
</section>

{if isset($customer_also_vieweds) && !empty($customer_also_vieweds)}
	<section class="container">
			<h3 class="widget-title trend_title"><a href="#">{translate('customer_also_viewed', true)}</h3>
			<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
				{foreach from=$customer_also_vieweds item=product}
				<div class="owl-item">
					{include file="templates/mimelon/_partial/product.tpl"}
				</div>
				{/foreach}
			</div>
	</section>
{/if}
<div class="gap"></div>
{/block}
