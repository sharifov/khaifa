{extends file=$layout}
{block name=content}
<section class="container terms-conditions-cover">
	<h1 class="terms-title">{$title}</h1>
	<div class="terms-content text-center">
		{$description}
	</div>
</section>
	<script>
		{if isset($json)}
		dataLayer.push({$json});
		{/if}
	</script>

{/block}