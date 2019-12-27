{extends file=$layout}
{block name=content}
	{if !isset($orders) && empty($orders)}
		<section class="container-fluid m-container faq-cover min_height">
			{include file="templates/mimelon/_partial/account_sidebar.tpl"}
			<div class="col-md-6  margin-auto faq-content">
			<div class="my-order-title">
					<div class="order-title">
						<h1 class="general-profile-title wow fadeInUp">{translate('my_orders')}</h1>
						<div class="txt_main text-center wow fadeInUp">{translate('no_orders')}</div>
						<div class="text-center form_btn_cover wow fadeInUp">
							<a href="{site_url_multi('/')}" class="btn reviews-btn con_shopping">{translate('continue_shopping')}</a>
						</div>
					</div>
			</div>
			</div>
		</section>
	{else}
		<section class="container-fluid m-container faq-cover min_height">
			{include file="templates/mimelon/_partial/account_sidebar.tpl"}
			<div class="col-md-6 margin-auto small-all-orders">
				<div class="all-orders-content">
					<h1 class="txt-bold26 text-center">{translate('my_orders')}</h1>
					{if isset($last_order) && !empty($last_order) }
					<div class="all-orders-section all-orders-medium">
						<div class="all-orders-head regular">
							<div class="uppercase font16">{translate('order_no')} : &nbsp;
								<strong>{$last_order->id}</strong> <small>({$last_order->status})</small>
							</div>
							<div class="orders-all-detail font14">
								<div>{translate('placed_on', true)} {date('Y-m-d', strtotime($last_order->created_at))}</div>
							</div>
							<ul class="bold">
								<li>{translate('item', true)} {$last_order->item}</li>
								<li>{translate('total', true)}
									<span class="product-caption-price-new">
										{currency_symbol_converter($last_order->total)}
									</span>
								</li>
							</ul>

						</div>
						<div class="container-fluid all-orders-body">
							<div class="col-md-12 order-body-element">
								{if isset($last_order->products) && !empty($last_order->products)}
								{foreach from=$last_order->products item=product}
								<div class="row shopping-item m-shopping-item order-body-item">
									<div class="col-md-6 col-sm-6 col-xs-12 orders-cart-img">
										<div class="all-orders-img">
											<a href="{get_product_link($product->product_id)}">
												<img src="{get_product_image($product->product_id)}" alt="{$product->name}">
											</a>
										</div>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12 shopping-cart-details">
										<div class="product-caption-price">
											<span class="product-caption-price-new">
												{currency_symbol_converter($product->price)}
											</span>
										</div>
										<div class="cart-txt-detail">
											{$product->name}
										</div>
										<div class="qty text-left">
											<ul>
												<li>
													<strong>{translate('qty', true)}</strong> {$product->quantity} </li>
												{if $product->options}
												{foreach from=$product->options item=option}
													<li><strong>{$option->name}</strong> {$option->value} </li>
												{/foreach}
												{/if}
											</ul>
										</div>
										{if $product->tracking_code}
											<a href="{site_url_multi('account/track')}/{$product->tracking_code}" class="location-track">
												<img src="{base_url('templates/mimelon/assets/img/icons/location.svg')}" alt="">{translate('track')}
											</a>
										{else}
											<a href="{site_url_multi('account/track')}/{$product->order_id}" class="location-track">
												<img src="{base_url('templates/mimelon/assets/img/icons/location.svg')}" alt="">{translate('track')}
											</a>
										{/if}
									</div>
								</div>
								{/foreach}
								{/if}
							</div>
						</div>
					</div>
					{/if}
				</div>
			</div>
			<div class="order-aside-right all_orders_aside small_orders_aside">
				{if isset($orders) && !empty($orders)}
				{foreach from=$orders item=order}
					<a href="{site_url_multi('account/orders')}?order_id={$order->id}">
						<div class="order-aside-item">
							<div class="order-aside-head white">
								<div class="uppercase regular">{translate('order_no')}
									<strong>{$order->id}</strong><small> ({$order->status})</small>
								</div>
								<div class="bold font14">{translate('total')}
									<strong class="product-caption-price-new">
										{currency_symbol_converter($order->total)}
									</strong>
								</div>
							</div>
							<div class="row order-aside-body">
								{if isset($order->products) && !empty($order->products)}
									{foreach from=$order->products item=product}
										<div class="col-md-6 order-aside-body-item">
											<div class="order-aside-body-img">
												<a href="{get_product_link($product->product_id)}">
													<img src="{get_product_image($product->product_id)}" alt="{$product->name}">
												</a>
											</div>
											<div class="order-aside-body-txt">
												{$product->name}
											</div>
										</div>
									{/foreach}
								{/if}
							</div>
						</div>
					</a>
				{/foreach}
				{/if}
			</div>
		</section>
	{/if}
{/block}