{extends file=$layout}
{block name=content}
<section class="container-fluid m-container faq-cover min_height">
	{include file="templates/mimelon/_partial/faq_sidebar.tpl"}
	{include file="templates/mimelon/_partial/faq_breadcramp.tpl"}
	<div class="col-md-6  margin-auto faq-content faq-list-cover">
		<h1 class="general-profile-title">{$faq->name}</h1>
		<div class="question-item">
			{$faq->description}
		</div>
	</div>
</section>
{/block}