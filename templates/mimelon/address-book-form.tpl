{extends file=$layout}
{block name=content}
<section class="container-fluid m-container faq-cover min_height">
	{include file="templates/mimelon/_partial/account_sidebar.tpl"}
	<div class="col-md-6  margin-auto faq-content">
	<div class="payment-methods-detail">
		<div class="order-title">
			<h1 class="general-profile-title wow fadeInUp">{$title}</h1>
		</div>
		<div class="new-account new_account_delivery wow fadeInUp">
			<form action="{current_url()}" method="POST">
				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="" class="form-label">{translate('firstname')}</label>
					<input class="form-control main-form-element" type="text" name="firstname" placeholder="{translate('firstname')}" id="firstname" value="{(set_value('firstname')) ? set_value('firstname') : (isset($address->firstname)) ? $address->firstname : '' }" >
					{if form_error('firstname')}
						<div class="error-message">
							<span>{form_error('firstname')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="lastname" class="form-label">{translate('lastname')}</label>
					<input class="form-control main-form-element" type="text" name="lastname" placeholder="{translate('lastname')}" id="lastname" value="{(set_value('lastname')) ? set_value('lastname') : (isset($address->lastname)) ? $address->lastname : '' }" >
					{if form_error('lastname')}
						<div class="error-message">
							<span>{form_error('lastname')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="company" class="form-label">{translate('company')}</label>
					<input class="form-control main-form-element" type="text" name="company" placeholder="{translate('company')}" id="company" value="{(set_value('company')) ? set_value('company') : (isset($address->company)) ? $address->company : '' }" >
					{if form_error('company')}
						<div class="error-message">
							<span>{form_error('company')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element mobile_account_element wow fadeInUp">
					<label for="" class="form-label">{translate('phone')}</label>
					<input class="form-control main-form-element mobilenumber-flag  number-validation" type="tel" id="mobilenumber"  value="{(set_value('phone')) ? set_value('phone') : (isset($address->phone)) ? $address->phone : '' }" name="phone" placeholder="{translate('phone')}" >
					{if form_error('phone')}
						<div class="error-message">
							<span>{form_error('phone')}</span>
						</div>
					{/if}
					<i class="fa fa-chevron-down mobile-chevron"></i>
				</div>
				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="city" class="form-label">{translate('city')}</label>
					<input class="form-control main-form-element" type="text" name="city" placeholder="" id="city" value="{(set_value('city')) ? set_value('city') : (isset($address->city)) ? $address->city : '' }">
					{if form_error('city')}
						<div class="error-message">
							<span>{form_error('city')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="country_id" class="form-label">{translate('country')}</label>
					<select name="country_id" class="form-control main-form-element isnot-sell" id="country_id">
						<option value="0">{translate('please_select')}</option>
						{foreach from=$countries item=country}
							<option {if (set_value('country_id') && set_value('country_id') eq $country->id)}selected="selected"{/if} {if ($address->country_id && $address->country_id eq $country->id)}selected="selected"{/if} value="{$country->id}">{$country->name}</option>
						{/foreach}
					</select>
					{if form_error('country')}
						<div class="error-message">
							<span>{form_error('country')}</span>
						</div>
					{/if}
				</div>

				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="zone_id" class="form-label">{translate('region')}</label>
					<select name="zone_id" class="form-control main-form-element" id="zone_id"  {if set_value('zone_id')}data-selected="{set_value('zone_id')}"{/if}   {if $address->zone_id}data-selected="{$address->zone_id}"{/if}  >
						<option></option>
					</select>
					{if form_error('region')}
						<div class="error-message">
							<span>{form_error('region')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="" class="form-label">{translate('address')}</label>
					<input class="form-control main-form-element" type="text" name="address_1" placeholder="{translate('address_1')}" value="{(set_value('address_1')) ? set_value('address_1') : (isset($address->address_1)) ? $address->address_1 : '' }">
					<input class="form-control main-form-element address2-optional" type="text" name="address_2" value="{(set_value('address_2')) ? set_value('address_2') : (isset($address->address_2)) ? $address->address_2 : '' }" placeholder="{translate('address_2')}"> 
					{if form_error('address_1')}
						<span class="error-message">{form_error('address_1')}</span>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number wow fadeInUp">
					<label for="" class="form-label">{translate('postcode')}</label>
					<input class="form-control main-form-element" type="text" name="postcode" id="postcode" placeholder="" value="{(set_value('postcode')) ? set_value('postcode') : (isset($address->postcode)) ? $address->postcode : '' }">
					{if form_error('postcode')}
						<div class="error-message">
							<span>{form_error('postcode')}</span>
						</div>
					{/if}
				</div>
				<div class="text-center form_btn_cover save-change-btn wow fadeInUp">
					<button type="submit" class="btn reviews-btn payment-saved-btn">{translate('save')}</button>
				</div>
			</form>
		</div>
	</div>
	</div>
</section>

{/block}