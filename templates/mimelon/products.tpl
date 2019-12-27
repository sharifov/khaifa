{extends file=$layout}
{block name=content}
<div class="container-fluid product-main-categories m-container">
	
	<section class="col-md-12 col-sm-12 product-categories-section">
		<div class="container product-category-container category-container">    
			<div class="saved-items mobile-saved-items">
				{if isset($title)}<h1 class="bold_txt26 text-center wow fadeInUp">{$title}</h1>{/if}
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
</div>
{/block}