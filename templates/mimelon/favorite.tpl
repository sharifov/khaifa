{extends file=$layout}
{block name=content}
<section>
	<div class="container saved-items">
		<h1 class="bold_txt26 text-center wow fadeInUp">{$title}</h1>
		<div class="text-center txt_main saved-item-count wow fadeInUp">{$favorite_count} {translate('items', true)}</div>
		<div class="row">
			{if isset($products) && !empty($products)}
			{foreach from=$products item=product}
			<div class="col-md-2 col-sm-3 col-xs-6 saved-item saved-item-js">
				<div class="product wow fadeInUp">
					<div class="product-img-wrap trend_img">
						<img class="product-img" src="{$product->image}" alt="{$product->name}" title="{$product->name}" />
						<a href="{$product->link}" class="trend_overlay"><span>{translate('more_info', true)}</span></a>
						<button class="saved-item-close saved-close-js" data-id="{$product->id}"></button>
					</div>
					<a href="{$product->link}">
						<div class="product-caption">
							<ul class="product-caption-rating wow fadeInUp">
								{for $rating=1 to 5}
									{if $rating <= $product->rating}
										<li class="rated"><i class="fa fa-star"></i></li>
										{else}
										<li><i class="fa fa-star-o"></i></li>
									{/if}
								{/for}
							</ul>
							<h5 class="product-caption-title wow fadeInUp">{$product->name}</h5>
							<div class="product-caption-price wow fadeInUp">
								<span class="product-caption-price-new wow fadeInUp">
									{$product->price}
								</span>
							</div>
						</div>
					</a>
				</div>
			</div>
			{/foreach}
			{$pagination}
			{/if}
		</div>
	</div>
</section>
{/block}