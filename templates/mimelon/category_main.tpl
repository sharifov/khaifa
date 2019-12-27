{extends file=$layout}
{block name=content}
<div class="container-fluid product-main-categories m-container">

	<aside class="col-md-3 col-sm-3 product-categories-list product-aside-category">
		<div class="product-categories-aside">
			{if $sub_categories}
			<div class="category-list submenu-category-list">
				<h2 class="widget-title trend_title categories-title"><strong>{translate('category', true)}</strong></h2>
				<ul class="submenu-category-titles">
					{foreach from=$sub_categories item=sub_category}
					<li>
						<button type="button" class="accordion-submenu"> <i class="fa fa-chevron-left"></i> {$sub_category.name} </button>
						{if $sub_category.sub_categories}
						<div class="panel submenu-panel">
							<ul class="submenu-nav">
								{foreach from=$sub_category.sub_categories item=category}
								<li>
									<a href="{site_url_multi('/')}{$category.slug}/">
										<span class="submenu-name">{$category.name}</span> 
										<span class="submenu-count">{$category.quantity}</span>
									</a>
								</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</li>
					{/foreach}
				</ul>
			</div>
			{/if}

			{if $brands}
			<div class="category-list">
				<h2 class="widget-title trend_title categories-title wow fadeInUp"><strong class="uppercase">POPULAR</strong> Brands</h2>
				<ul class="categories-list categories-list-limit"> 
					{foreach from=$brands item=brand}
					<li class="wow fadeInUp"><a href="{site_url_multi('products/brand')}?id={$brand.id}">{$brand.name}</a></li>
					{/foreach}
				</ul>
			</div>
			{/if}
		</div>
	</aside>
	<section class="col-md-9 col-sm-9 product-categories-section">
		<div class="container product-category-container">
			<!-- starts banner -->
			<div class="banner-cover">
				<div class="m-banner category-banner wow fadeInUp">
					<!-- banner area 
						<h1 class="banner-here">BANNER</h1>
					-->
				</div>
			</div>
			<!-- ends banner -->
			<!-- starts trend section -->
			{if array_key_exists(0,$category_products) && $category_products[0].products}
				{assign var="category_product" value=$category_products[0]}
				<section class="col-md-12 pc-slider-item">
					{if $category->h1}
						<h1>{$category->h1}</h1>
					{/if}

					{$category->top_text}
				<h3 class="widget-title trend_title wow fadeInUp"><a href="{site_url_multi('/')}{$category_product.category_slug}/">{$category_product.category_name}</a></h3>
				{if $category_product.products}
					<div class="owl-carousel prev-next-carousel owl-loaded owl-nav-out">
					{foreach from=$category_product.products item=$product}
						{include file="templates/mimelon/_partial/product.tpl"}
					{/foreach}
					</div>
				{/if}
				<div class="col-md-12 text-center wow fadeInUp">
					<a href="{site_url_multi('/')}{$category_product.category_slug}/" class="main__btn black__btn">{translate('see_more',true)}</a>
				</div>
			</section>
			{/if}
			{if array_key_exists(1,$category_products) && $category_products[1].products}
				{assign var="category_product" value=$category_products[1]}
				<section class="col-md-12 pc-slider-item">
				<h3 class="widget-title trend_title wow fadeInUp"><a href="{site_url_multi('/')}{$category_product.category_slug}/">{$category_product.category_name}</a></h3>
				{if $category_product.products}
					<div class="owl-carousel prev-next-carousel owl-loaded owl-nav-out">
					{foreach from=$category_product.products item=$product}
						{include file="templates/mimelon/_partial/product.tpl"}
					{/foreach}
					</div>
				{/if}
				<div class="col-md-12 text-center wow fadeInDown">
					<a href="{site_url_multi('/')}{$category_product.category_slug}/" class="main__btn black__btn">{translate('see_more',true)}</a>
				</div>
			</section>
			{/if}
			
			{if $special_products}
			<section class="col-md-12 pc-slider-item">
				<div class="sale-discount-cover">
					<a href="#" class="sale-discount-link">
						<div class="sale-discount">
							<h4 class="discount-text">{translate('sale', true)}</h4>
							<p class="discount-percent">-{$special_products.percent}%</p>
						</div>
					</a>
					<div class="last-change">
						<h3 class="last-change-txt">{translate('last_chance', true)}</h3>
						<input type="hidden" id="countdown2" value="{$special_products.expired_date}"/>
						{if $special_products.expired_date}
						<div class='time-frame'>
							<div class="countdown">
								{*<b class="countdown2day"></b> <span>{translate('day', true)}</span>*}
								<b class="countdown2hrs"></b> <span>{translate('hrs', true)}</span>
								<b class="countdown2min"></b> <span>{translate('min', true)}</span>
								<b class="countdown2sec"></b> <span>{translate('sec', true)}</span>
							</div>
						</div>
						{/if}
					</div>
				</div>
				<!-- ends sale discount -->
				<div class="owl-carousel prev-next-carousel owl-loaded owl-nav-out">
					{foreach from=$special_products.products item=product}
					<div class="owl-item">
						{include file="templates/mimelon/_partial/product.tpl"}
					</div>
					{/foreach}
				</div>
				
				<div class="col-md-12 text-center wow fadeInUp">
					<a href="{site_url_multi('products/')}{$category->slug}" class="main__btn black__btn">{translate('see_more', true)}</a>
				</div>

			</section>
			{/if}

			{$category->bottom_text}

			<section class="col-md-12 product-types">
				{if isset($banners.footer.top_left) && $banners.footer.top_left}
					<div class="col-md-3 pro-type-item wow fadeInUp">
						<a href="{$banners['footer']['top_left']->link}">
							<img class="prop-banner" src="{base_url('uploads/')}{$banners['footer']['top_left']->image}" alt="{$banners['footer']['top_left']->name}">
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
							<img class="prop-banner" src="{base_url('uploads/')}{$banners['footer']['top_right']->image}" alt="{$banners['footer']['top_right']->name}">
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
							<img class="prop-banner" src="{base_url('uploads/')}{$banners['footer']['bottom_left']->image}" alt="{$banners['footer']['bottom_left']->name}">
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
							<img class="prop-banner" src="{base_url('uploads/')}{$banners['footer']['bottom_right']->image}" alt="{$banners['footer']['bottom_right']->name}">
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
	{*{$category->bottom_text}*}
</div>
{/block}