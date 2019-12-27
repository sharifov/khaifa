{extends file=$layout}
{block name=content}
		{if isset($banners.top) && $banners.top}
		<!-- starts banner -->
		<div class="row banner-cover">
			<div class="m-container m-banner banner-slider-cover wow fadeIn">
				<!-- banner area -->
				<div class="owl-carousel owl-theme banner-full-slider">
					{foreach from=$banners.top item=banner}
						<div class="item">
							<a href="{$banner->link}"><img class="img-responsive" alt="{$banner->name}" src="/uploads/{$banner->image}" width="1366" height="300"></a>
						</div>
					{/foreach}
          		</div>
				<!-- /// -->
			</div>
		</div>
		<!-- ends banner -->
		{/if}

	{if isset($success)}{$success}{/if}

		{if $top_products}
		<!-- starts top products section -->
		<section class="container">
			<h3 class="widget-title trend_title"><a href="{site_url_multi('products/top_products')}">{translate('home_top_products',true)}</a></h3>
			<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out wow slideInUp">
				{foreach from=$top_products item=product}
				<div class="owl-item">
				{include file="templates/mimelon/_partial/product.tpl"}
				</div>
				{/foreach}
			</div>
			<div class="col-md-12 text-center wow fadeInUp">
				<a href="{site_url_multi('products/top_products')}" class="main__btn black__btn">{translate('see_more',true)}</a>
			</div>
		</section>
		<!-- ends top products section -->
		{/if}

		{if $featured_products_1}
		<!-- starts change trend -->
		 <section class="container">
			<!-- starts sale discount-->
			<div class="sale-discount-cover">
				<a href="#" class="sale-discount-link wow slideInUp">
					<div class="sale-discount">
						<div class="discount-text h4">{translate('sale',true)}</div>
						<p class="discount-percent">-{$featured_products_1.percent}%</p>
					</div>
				</a>

				<div class="last-change">
					<h3 class="last-change-txt wow slideInLeft">Last chance</h3>
					<input class="wow slideInLeft" type="hidden" id="countdown1" value="{$featured_products_1.expired_date}"/>
					{if $featured_products_1.expired_date}
					<div class='time-frame'>
						<div class="countdown">
							<b class="countdown1hrs"></b> <span>hrs:</span>
							<b class="countdown1min"></b> <span>min:</span>
							<b class="countdown1sec"></b> <span>sec</span>
						</div>
					</div>
					{/if}
				</div>

			</div>
			<!-- ends sale discount -->
			<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
				{foreach from=$featured_products_1.products item=product}
				<div class="owl-item">
					{include file="templates/mimelon/_partial/product.tpl"}
				</div>
				{/foreach}
			</div>

			<div class="col-md-12 text-center wow fadeInUp">
				<a href="{site_url_multi('products/featured_1')}" class="main__btn black__btn">{translate('see_more',true)}</a>
			</div>
		</section>
		<!-- ends change tren-->
		{/if}

	{if isset($discounts[0]) && $discounts[0]}
		{assign var="discount" value=$discounts[0]}
		<!-- starts change trend -->
		<section class="container">
			<!-- starts sale discount-->
			<div class="sale-discount-cover">
				<a href="{site_url_multi('products/sale')}" class="sale-discount-link">
					<div class="sale-discount">
						<h4 class="discount-text">{$discount.title}</h4>
						<p class="discount-percent">-{$discount.discount}%</p>
					</div>
				</a>
				<div class="last-change">
					<h3 class="last-change-txt">{translate('last_chance',true)}</h3>
					<input class="wow slideInLeft" type="hidden" id="countdown{$discount.x}" value="{$discount.end_date}"/>

					<div class='time-frame'>
						<div class="countdown">
							<b class="countdown{$discount.x}days"></b> <span>{translate('day',true)}</span>
							<b class="countdown{$discount.x}hrs"></b> <span>{translate('hrs',true)}</span>
							<b class="countdown{$discount.x}min"></b> <span>{translate('min',true)}</span>
							<b class="countdown{$discount.x}sec"></b> <span>{translate('sec',true)}</span>
						</div>
					</div>
				</div>
			</div>
			<!-- ends sale discount -->
			<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
				{foreach from=$discount.products item=product}
					<div class="owl-item">
						{include file="templates/mimelon/_partial/product.tpl"}
					</div>
				{/foreach}
			</div>

			<div class="col-md-12 text-center">
				<a href="{site_url_multi('products/sale')}" class="main__btn black__btn">{translate('see_more', true)}</a>
			</div>

		</section>
		<!-- ends change tren-->

	{/if}

		{if isset($banners.middle) && $banners.middle}
		<!-- starts banner -->
		<div class="row banner-cover">
			<div class="m-container m-banner wow fadeInUp">
				<!-- banner area -->
				<a href="{$banners['middle']->link}">
					<img class="img-responsive" alt="{$banners['middle']->name}" src="/uploads/{$banners['middle']->image}" width="1366" height="300">
				</a>

			</div>
		</div>
		<!-- ends banner -->
		{/if}

		{if $new_products}
		<!-- starts trend section -->
		<section class="container">
			<h3 class="widget-title trend_title"><a href="{site_url_multi('products/new_products')}">{translate('home_new_products',true)}</a></h3>
			<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
				{foreach from=$new_products item=product}
				<div class="owl-item">
					{include file="templates/mimelon/_partial/product.tpl"}
				</div>
				{/foreach}
			</div>
			<div class="col-md-12 text-center wow fadeInUp">
				<a href="{site_url_multi('products/new_products')}" class="main__btn black__btn">{translate('see_more',true)}</a>
			</div>
		</section>
		<!-- ends trend section -->
		{/if}

		<div class="gap"></div>
		<!-- starts product type -->

		<div class="m-container">
			<section class="row product-types m-product-types">
				{if isset($banners.center.top_left) && $banners.center.top_left}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['center']['top_left']->link}">
						<img class="img-responsive" src="{base_url('uploads/')}{$banners['center']['top_left']->image}" alt="{$banners['center']['top_left']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['center']['top_left']->name}</h4>
								{if $banners['center']['top_left']->description}<small class="up_to">{$banners['center']['top_left']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
				{if isset($banners.center.top_right) && $banners.center.top_right}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['center']['top_right']->link}">
						<img class="img-responsive" src="{base_url('uploads/')}{$banners['center']['top_right']->image}" alt="{$banners['center']['top_right']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['center']['top_right']->name}</h4>
								{if $banners['center']['top_right']->description}<small class="up_to">{$banners['center']['top_right']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
				{if isset($banners.center.bottom_left) && $banners.center.bottom_left}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['center']['bottom_left']->link}">
						<img class="img-responsive" src="{base_url('uploads/')}{$banners['center']['bottom_left']->image}" alt="{$banners['center']['bottom_left']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['center']['bottom_left']->name}</h4>
								{if $banners['center']['bottom_left']->description}<small class="up_to">{$banners['center']['bottom_left']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
				{if isset($banners.center.bottom_right) && $banners.center.bottom_right}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['center']['bottom_right']->link}">
						<img class="img-responsive" src="{base_url('uploads/')}{$banners['center']['bottom_right']->image}" alt="{$banners['center']['bottom_right']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['center']['bottom_right']->name}</h4>
								{if $banners['center']['bottom_right']->description}<small class="up_to">{$banners['center']['bottom_right']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
			</section>
		</div>
		<!-- ends product type -->

		<!-- starts free shipping -->
		<section class="row free-shipping m-container">
			<div class="shipping-content">
				<div class="h2" class="wow fadeInUp">{translate('let_us_help_you',true)}</div>
				<p class="wow fadeInUp">{translate('free_shipping',true)}</p>
				{get_menu_by_name('homemenu')}
			</div>
		</section>
		<!-- ends free shipping -->

		<div class="gap"></div>

		{if $recently_viewed}
		<!-- starts trend section -->
		<section class="container">
				<h3 class="widget-title trend_title"><a href="">{translate('recently_viewed', true)}</a></h3>
				<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
					{foreach from=$recently_viewed item=product}
					<div class="owl-item">
						{include file="templates/mimelon/_partial/product.tpl"}
					</div>
					{/foreach}
				</div>
				<div class="col-md-12 text-center wow fadeInUp">
					<a href="{site_url_multi('products/recently_viewed')}" class="main__btn black__btn">{translate('see_more',true)}</a>
				</div>
		</section>
		<!-- ends trend section -->
		{/if}

		{if $featured_products_2}
		<!-- starts trend section -->
		<section class="container">
			<!-- starts sale discount-->
			<div class="sale-discount-cover">
				<a href="#" class="sale-discount-link wow slideInUp">
					<div class="sale-discount">
						<div class="discount-text h4">{translate('sale',true)}</div>
						<p class="discount-percent">-{$featured_products_2.percent}%</p>
					</div>
				</a>
				<div class="last-change">
					<h3 class="last-change-txt wow slideInLeft">{translate('last_chance',true)}</h3>
					<input class="wow slideInLeft" type="hidden" id="countdown2" value="{$featured_products_2.expired_date}"/>
					{if $featured_products_2.expired_date}
					<div class='time-frame'>
						<div class="countdown">
							<b class="countdown2hrs"></b> <span>hrs:</span>
							<b class="countdown2min"></b> <span>min:</span>
							<b class="countdown2sec"></b> <span>sec</span>
						</div>
					</div>
					{/if}
				</div>
			</div>
			<!-- ends sale discount -->
			<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
				{foreach from=$featured_products_2.products item=product}
				<div class="owl-item">
					{include file="templates/mimelon/_partial/product.tpl"}
				</div>
				{/foreach}
			</div>

			<div class="col-md-12 text-center wow fadeInUp">
				<a href="{site_url_multi('products/featured_2')}" class="main__btn black__btn">{translate('see_more',true)}</a>
			</div>
		</section>
		<!-- ends trend section -->
		{/if}

		{if isset($discounts[1]) && $discounts[1]}
			{assign var="discount" value=$discounts[1]}
			<!-- starts change trend -->
			<section class="container">
				<!-- starts sale discount-->
				<div class="sale-discount-cover">
					<a href="#" class="sale-discount-link">
						<div class="sale-discount">
							<h4 class="discount-text">{$discount.title}</h4>
							<p class="discount-percent">-{$discount.discount}%</p>
						</div>
					</a>
					<div class="last-change">
						<h3 class="last-change-txt">{translate('last_chance',true)}</h3>
						<input class="wow slideInLeft" type="hidden" id="countdown{$discount.x}" value="{$discount.end_date}"/>

						<div class='time-frame'>
							<div class="countdown">
								<b class="countdown{$discount.x}days"></b> <span>{translate('day',true)}</span>
								<b class="countdown{$discount.x}hrs"></b> <span>{translate('hrs',true)}</span>
								<b class="countdown{$discount.x}min"></b> <span>{translate('min',true)}</span>
								<b class="countdown{$discount.x}sec"></b> <span>{translate('sec',true)}</span>
							</div>
						</div>
					</div>
				</div>
				<!-- ends sale discount -->
				<div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
					{foreach from=$discount.products item=product}
						<div class="owl-item">
							{include file="templates/mimelon/_partial/product.tpl"}
						</div>
					{/foreach}
				</div>

				<div class="col-md-12 text-center">
					<a href="{site_url_multi('products/sale')}" class="main__btn black__btn">{translate('see_more', true)}</a>
				</div>

			</section>
			<!-- ends change tren-->

		{/if}

		<div class="m-container">
			<section class="row product-types m-product-types">
				{if isset($banners.footer.top_left) && $banners.footer.top_left}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['footer']['top_left']->link}">
						<img src="{base_url('uploads/')}{$banners['footer']['top_left']->image}" alt="{$banners['footer']['top_left']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['footer']['top_left']->name}</h4>
								{if $banners['footer']['top_left']->description}<small class="up_to">{$banners['footer']['top_left']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
				{if isset($banners.footer.top_right) && $banners.footer.top_right}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['footer']['top_right']->link}">
						<img src="{base_url('uploads/')}{$banners['footer']['top_right']->image}" alt="{$banners['footer']['top_right']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['footer']['top_right']->name}</h4>
								{if $banners['footer']['top_right']->description}<small class="up_to">{$banners['footer']['top_right']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
				{if isset($banners.footer.bottom_left) && $banners.footer.bottom_left}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['footer']['bottom_left']->link}">
						<img src="{base_url('uploads/')}{$banners['footer']['bottom_left']->image}" alt="{$banners['footer']['bottom_left']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['footer']['bottom_left']->name}</h4>
								{if $banners['footer']['bottom_left']->description}<small class="up_to">{$banners['footer']['bottom_left']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
				{if isset($banners.footer.bottom_right) && $banners.footer.bottom_right}
				<div class="col-md-3 pro-type-item wow fadeInUp">
					<a href="{$banners['footer']['bottom_right']->link}">
						<img src="{base_url('uploads/')}{$banners['footer']['bottom_right']->image}" alt="{$banners['footer']['bottom_right']->name}">
						<div class="type-name">
							<div class="name-content">
								<h4>{$banners['footer']['bottom_right']->name}</h4>
								{if $banners['footer']['bottom_right']->description}<small class="up_to">{$banners['footer']['bottom_right']->description}</small>{/if}
							</div>
						</div>
					</a>
				</div>
				{/if}
			</section>
		</div>
{/block}
