{extends file=$layout}
{block name=content}
<div class="container-fluid product-main-categories m-container">
	<form action="{current_url()}" method="GET">
		<input type="hidden" name="query" value="{if isset($smarty.get.query)}{$smarty.get.query}{/if}"/>
		<input type="hidden" name="category_id" value="{if isset($smarty.get.category_id)}{$smarty.get.category_id}{/if}"/>
		{if isset($smarty.get.copied_product_id)}<input type="hidden" name="copied_product_id" value="{$smarty.get.copied_product_id}"/>{/if}
		<aside class="col-md-3 col-sm-3 product-categories-list mobile-fixed-aside">
			<button class="mobile-fixed-aside-btn">
				<img src="img/mimelon-imgs/icons/close-icon-mobile.svg" alt="">
			</button>
			<div class="product-categories-aside">
				{if $algolia_categories}
				<div class="category-list" style="display:none">
					<h2 class="widget-title trend_title categories-title"><strong>{translate('category', true)}</strong></h2>
					<ul class="customer-review-star algolia-cats">
						{foreach from=$algolia_categories key=$kc item=category}
							{if isset($category.children)}
								<li>
									<div class="checkbox_cover">
										<label class="container__checkbox">
											<a href="{$kc}" class="bold">{$category.name} ({$category.count})</a>
										</label>
									</div>
								</li>
							{/if}
						{/foreach}
					</ul>
				</div>
				{/if}
			
				{if $brands}
				<div class="category-list">
					<h2 class="widget-title trend_title categories-title"><strong>{translate('brand', true)}</strong></h2>
					<ul class="customer-review-stars">
						{foreach from=$brands item=brand}
						<li>
							<div class="checkbox_cover address-user-checkbox">
								<label class="container__checkbox category-filter-checkbox">
									<input name="brand[]" class="checkbox" {if isset($smarty.get.brand) && in_array($brand.id, $smarty.get.brand)} checked {/if} value="{$brand.id}" type="checkbox" data-toggle="toggle">
									<span class="checkmark"></span>
									{$brand.name}
								</label>
							</div>
						</li>
						{/foreach}
					</ul>
				</div>
				{/if}

				<div class="category-list">
					<h2 class="widget-title trend_title categories-title"><strong>{translate('price', true)} <small class="txt-main">({$current_currency})</small></strong></h2>
					{* <div class="range-slider range-slider-cover">
						<output class="range-output"><span class="text-bold output"></span></output>
						<input type="range" name="price" min="{if $price_range}{$price_range.min}{else}0{/if}" max="{if $price_range}{$price_range.max}{else}0{/if}" step="1" value="{if isset($smarty.get.price)}{$smarty.get.price}{else}{if $price_range}{$price_range.max}{else}0{/if}{/if}"/>
						<div class="range-values"><span class="range-min">{if $price_range}{$price_range.min}{else}0{/if}</span><span class="range-max">{if $price_range}{$price_range.max}{else}0{/if}</span></div>
					</div> *}

					<div class="price-range-slider22">
						<input id="price-range" type="text" class="js-range-slider" name="my_range" value=""  data-type="double"
						data-min="0"
						data-max="{if $price_range}{$price_range.max}{else}100{/if}"
						data-from="{if isset($smarty.get.price_from)}{$smarty.get.price_from}{/if}"
						data-to="{if isset($smarty.get.price_to)}{str_replace(' ','',$smarty.get.price_to)}{/if}"
						data-grid="true"/>
						<input type="text" id="data-from" name="price_from" value="{if isset($smarty.get.price_from)}{$smarty.get.price_from}{/if}" hidden>
						<input type="text" id="data-to" name="price_to" value="{if isset($smarty.get.price_to)}{str_replace(' ','',$smarty.get.price_to)}{/if}" hidden>
					</div>

				</div>
				{if $option_data}
				{foreach from=$option_data item=option}
					{if $option.type eq 'color'}
						<div class="category-list">
							<h2 class="widget-title trend_title categories-title"><strong>{translate('label_color',true)}</strong></h2>
							<ul class="customer-review-stars colour-review-list">
							{if $option.values}
								{foreach from=$option.values key=option_value_id item=option_value}
								<li>
									<div class="checkbox_cover address-user-checkbox">
										<label class="container__checkbox category-filter-checkbox">
											<input name="option[{$option.id}][]" {if isset($smarty.get.option) && array_key_exists($option.id, $smarty.get.option) && in_array($option_value_id,$smarty.get.option[$option.id])} checked {/if} value="{$option_value_id}" type="checkbox" data-toggle="toggle">
											<span class="checkmark" style="background:url('{base_url("uploads/")}{$option_value.image}')"></span>
											<small>{$option_value.name}</small>
										</label>
									</div>
								</li>
								{/foreach}
							{/if}
							</ul>
						</div>
					{else}
						<div class="category-list">
							<h2 class="widget-title trend_title categories-title"><strong>{$option.name}</strong></h2>
							<ul class="customer-review-stars">
								{if $option.values}
								{foreach from=$option.values key=option_value_id item=option_value}
								<li>
									<div class="checkbox_cover address-user-checkbox">
										<label class="container__checkbox category-filter-checkbox">
											<input name="option[{$option.id}][]" {if isset($smarty.get.option) && array_key_exists($option.id, $smarty.get.option) && in_array($option_value_id,$smarty.get.option[$option.id])} checked {/if} value="{$option_value_id}" value="{$option_value_id}" type="checkbox" data-toggle="toggle">
											<span class="checkmark"></span>
											{$option_value.name}
										</label>
									</div>
								</li>
								{/foreach}
								{/if}
							</ul>
						</div>
					{/if}
				{/foreach}
				{/if}

				{if $attribute_data}
				{foreach from=$attribute_data item=attribute}
				<div class="category-list">
					<h2 class="widget-title trend_title categories-title"><strong>{$attribute.name}</strong></h2>
					<ul class="customer-review-stars">
						{if $attribute.values}
						{foreach from=$attribute.values key=attribute_value_id item=attribute_value}
						<li>
							<div class="checkbox_cover address-user-checkbox">
								<label class="container__checkbox category-filter-checkbox">
									<input name="attribute[{$attribute.id}][]" {if isset($smarty.get.attribute) && array_key_exists($attribute.id, $smarty.get.attribute) && in_array($attribute_value_id,$smarty.get.attribute[$attribute.id])} checked {/if} value="{$attribute_value_id}" value="{$attribute_value_id}" type="checkbox" data-toggle="toggle">
									<span class="checkmark"></span>
									{$attribute_value.name}
								</label>
							</div>
						</li>
						{/foreach}
						{/if}
					</ul>
				</div>
				{/foreach}
				{/if}
				<div class="category-list">
					<h2 class="widget-title trend_title categories-title"><strong>Customer</strong> Review</h2>
					<ul class="customer-review-stars">
						<li>
							<div class="checkbox_cover address-user-checkbox">
								<label class="container__checkbox category-filter-checkbox">
									<input name="review[]" {if isset($smarty.get.review) && in_array(5,$smarty.get.review)} checked {/if}  value="5" type="checkbox" data-toggle="toggle">
									<span class="checkmark"></span>
									<ul class="product-caption-rating category-rating-filter">
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
									</ul>
								</label>
							</div>
						</li>
						<li>
							<div class="checkbox_cover address-user-checkbox">
								<label class="container__checkbox category-filter-checkbox">
									<input name="review[]" {if isset($smarty.get.review) && in_array(4,$smarty.get.review)} checked {/if} value="4" type="checkbox" data-toggle="toggle">
									<span class="checkmark"></span>
									<ul class="product-caption-rating category-rating-filter">
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
									</ul>
								</label>
							</div>
						</li>
						<li>
							<div class="checkbox_cover address-user-checkbox">
								<label class="container__checkbox category-filter-checkbox">
									<input name="review[]" {if isset($smarty.get.review) && in_array(3,$smarty.get.review)} checked {/if} value="3" type="checkbox" data-toggle="toggle">
									<span class="checkmark"></span>
									<ul class="product-caption-rating category-rating-filter">
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
									</ul>
								</label>
							</div>
						</li>
						<li>
							<div class="checkbox_cover address-user-checkbox">
								<label class="container__checkbox category-filter-checkbox">
									<input name="review[]" {if isset($smarty.get.review) && in_array(2,$smarty.get.review)} checked {/if} value="2" type="checkbox" data-toggle="toggle">
									<span class="checkmark"></span>
									<ul class="product-caption-rating category-rating-filter">
										<li class="rated"><i class="fa fa-star"></i></li>
										<li class="rated"><i class="fa fa-star"></i></li>
									</ul>
								</label>
							</div>
						</li>
						<li>
							<div class="checkbox_cover address-user-checkbox">
								<label class="container__checkbox category-filter-checkbox">
									<input name="review[]" {if isset($smarty.get.review) && in_array(1,$smarty.get.review)} checked {/if} value="1" type="checkbox" data-toggle="toggle">
									<span class="checkmark"></span>
									<ul class="product-caption-rating category-rating-filter">
										<li class="rated"><i class="fa fa-star"></i></li>
									</ul>
								</label>
							</div>
						</li>
					</ul>
				</div>
				<button type="submit" class="filter-submit-btn">{translate('filter', true)}</button>
			</div>
		</aside>
	
	<section class="col-md-9 col-sm-9 product-categories-section">
		<div class="container product-category-container category-container">    
			<div class="saved-items mobile-saved-items">
				<div class="row">
					{if $products}
						{foreach from=$products item=product}
						<div class="col-md-2 col-sm-3 col-xs-12 saved-item saved-item-js wow fadeInUp">
							{include file="templates/mimelon/_partial/product.tpl"}
						</div>
						{/foreach}
						<div class="col-md-12 col-sm-12 col-xs-12 text-center wow fadeInUp">
							{$pagination}
						</div>
					{else}
						<span class="alert alert-warning full-width">{translate('no_product', true)}</span>
					{/if}
				</div>
			</div>
		</div>
	</section>
	</form>
</div>
{/block}