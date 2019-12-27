{extends file=$layout}
{block name=content}
<section class="container terms-conditions-cover">
	<h1 class="terms-title">{$title}</h1>
	<div class="terms-content">
		{$description}
	</div>
	<form id="paypal" action="{$paypal.action}" method="post">
		<input type="hidden" name="cmd" value="_cart" />
		<input type="hidden" name="upload" value="1" />
		<input type="hidden" name="business" value="{$paypal.business}" />
		{assign var=i  value=1}
		{foreach from=$paypal.products item=product}
			<input type="hidden" name="item_name_{$i}" value="{$product.name}" />
			<input type="hidden" name="item_number_{$i}" value="{$product.model}" />
			<input type="hidden" name="amount_{$i}" value="{$product.price}" />
			<input type="hidden" name="quantity_{$i}" value="{$product.quantity}" />
			<input type="hidden" name="weight_{$i}" value="{$product.weight}" />
			{assign var=j value=0}
			{foreach from=$product.option item=option}
				<input type="hidden" name="on{$j}_{$i}" value="{$option.name}" />
				<input type="hidden" name="os{$j}_{$i}" value="{$option.value}" />
				{assign var=j value=$j+1}
			{/foreach}
			{assign var=i value=$i+1}
		{/foreach}
		{if $paypal.discount_amount_cart}
			<input type="hidden" name="discount_amount_cart" value="{$paypal.discount_amount_cart}" />
		{/if}
		<input type="hidden" name="currency_code" value="{$paypal.currency_code}" />
		<input type="hidden" name="first_name" value="{$paypal.first_name}" />
		<input type="hidden" name="last_name" value="{$paypal.last_name}" />
		<input type="hidden" name="address1" value="{$paypal.address1}" />
		<input type="hidden" name="address2" value="{$paypal.address2}" />
		<input type="hidden" name="city" value="{$paypal.city}" />
		<input type="hidden" name="zip" value="{$paypal.zip}" />
		<input type="hidden" name="country" value="{$paypal.country}" />
		<input type="hidden" name="address_override" value="0" />
		<input type="hidden" name="email" value="{$paypal.email}" />
		<input type="hidden" name="invoice" value="{$paypal.invoice}" />
		<input type="hidden" name="lc" value="{$paypal.lc}" />
		<input type="hidden" name="rm" value="2" />
		<input type="hidden" name="no_note" value="1" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="charset" value="utf-8" />
		<input type="hidden" name="return" value="{$paypal.return}" />
		<input type="hidden" name="notify_url" value="{$paypal.notify_url}" />
		<input type="hidden" name="cancel_return" value="{$paypal.cancel_return}" />
		<input type="hidden" name="paymentaction" value="{$paypal.paymentaction}" />
		<input type="hidden" name="custom" value="{$paypal.custom}" />
		<input type="hidden" name="bn" value="OpenCart_2.0_WPS" />
	</form>
</section>
{/block}