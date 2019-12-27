{extends file=$layout}
{block name=content}
{if isset($addresses) && !empty($addresses)}
<section class="container-fluid m-container faq-cover min_height">
	{include file="templates/mimelon/_partial/account_sidebar.tpl"}
	<div class="col-md-6  margin-auto faq-content address-book-add-content">
		<div class="payment-methods-detail">
			<div class="order-title">
				<h1 class="general-profile-title">{$title}</h1>
				<div class="text-center form_btn_cover">
					<a href="{site_url_multi('account/add_address_book/')}" class="btn reviews-btn con_shopping">{translate('add_address')}</a>
				</div>
			</div>
			<div class="payment-methods-list">
				{if isset($addresses) && !empty($addresses)}
				{foreach from=$addresses item=address}
				<div class="payment-method-row">
					<div class="method-detail-cover">
						<ul class="txt_main address-user-details">
							<li>{$address.firstname} {$address.lastname}</li>
							<li>{$address.address1}</li>
							<li>{$address.address2}</li>
							<li>{$address.city}</li>
							<li>{$address.country}</li>
							<li>{$address.zone}</li>
							<li>{$address.postcode}</li>
							<li>{$address.phone}</li>
						</ul>
						{* <div class="txt_info">
							{translate('delivery_address')}
							<br>
							{translate('payment_address')}
						</div> *}
					</div>
					<div class="edit-remove">
						<button class="remove-icon flex" onclick="window.location.href='{site_url_multi('account/edit_address_book')}/{$address.id}'">
							<img src="img/mimelon-imgs/icons/pen.svg">
							<span>{translate('edit')}</span>
						</button>
						<button class="remove-icon flex" onclick="window.location.href='{site_url_multi('account/delete_address_book')}/{$address.id}'">
							<img src="img/mimelon-imgs/icons/cart-close-icon.svg">
							<span>{translate('remove')}</span>
						</button>
					</div>
				</div>
				{/foreach}
				{/if}
			</div>
		</div>
	</div>
</section>
{else}
<section class="container-fluid m-container faq-cover min_height">
	{include file="templates/mimelon/_partial/account_sidebar.tpl"}
	<div class="col-md-6  margin-auto faq-content">
		<div class="my-order-title">
			<div class="order-title">
				<h1 class="general-profile-title wow fadeInUp">{$title}</h1>
				<div class="txt_main text-center wow fadeInUp">{translate('no_address')}</div>
				<div class="text-center form_btn_cover wow fadeInUp">
					<a href="{site_url_multi('account/add_address_book/')}" class="btn reviews-btn con_shopping">{translate('add_address')}</a>
				</div>
			</div>
		</div>
	</div>
</section>
{/if}
{/block}