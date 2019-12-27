{extends file=$layout}
{block name=content}
<section class="container-fluid m-container faq-cover min_height">
	{include file="templates/mimelon/_partial/account_sidebar.tpl"}
	<div class="col-md-6  margin-auto faq-content">
		<h1 class="general-profile-title wow fadeInUp">{$title}</h1>
		{if $faq_categories}
			{foreach from=$faq_categories item=faq_category}
				<div class="col-md-4 col-sm-4 col-xs-6 faq-item wow zoomIn">
					<a href="{site_url_multi('faq/category/')}{$faq_category->slug}">
						<img src="{base_url('uploads/')}{$faq_category->image}" alt="{$faq_category->name}">
						<p class="faq-item-txt">{$faq_category->name}</p>
					</a>
				</div>
			{/foreach}
		{/if}
	</div>
</section>
{/block}