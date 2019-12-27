{extends file=$layout}
{block name=content}
		<!-- starts faq-->
		<section class="container-fluid m-container faq-cover min_height">
			{include file="templates/mimelon/_partial/faq_sidebar.tpl"}
			{include file="templates/mimelon/_partial/faq_breadcramp.tpl"}
			<div class="col-md-6 margin-auto faq-content faq-list-cover">
				<form action="{site_url_multi('faq/category/')}{$current_faq_category->slug}" method="GET">
				<div class="faq-search wow fadeInUp">
					<input type="text" name="faq-query" value="{(isset($smarty.get.query)) ? $smarty.get.query : ''}" class="faq-search-input form-control" placeholder="{translate('what_are_you_looking_for')}">
					<img src="templates/mimelon/assets/img/faq icons/black-search.svg">
				</div>
				</form>
			   <h1 class="general-profile-title wow fadeInUp">{$current_faq_category->name}</h1>

				{if $faqs}
				{foreach from=$faqs item=faq}
				<div class="question-item">
					<h4 class="wow fadeInUp">{$faq->name}</h4>
					<div class="question-response wow fadeInUp">{$faq->description|truncate:185}<div>
					<div class="text-right wow fadeInUp"><a href="{site_url_multi('faq/view/')}{$faq->slug}" class="question-question">{translate('read_more',true)}</a></div>
				</div>
				{/foreach}
				{$pagination}
				{else} 
				<span class="no__result">{translate('no_result', true)}</span>
				{/if}
			</div>
		</section>
		<!-- ends faq-->
{/block}