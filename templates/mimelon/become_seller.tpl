{extends file=$layout}
{block name=content}
<div class="new-account-cover coontainer-fluid m-container">
	<div class="new-account">
		{if isset($success)}
		<h1 class="general-profile-title become-seller-title">{$success}</h1>
{*			<a href="{site_url_multi('home')}" class="filter-submit-btn">{translate('home', true)}</a>*}
			{else}
			<h1 class="general-profile-title become-seller-title">{$title}</h1>

			<form action="{current_url()}" method="POST">
{*				{if isset($success)}<div class="alert alert-success">{$success}</div>{/if}*}
				<div class="form-group form_group account-element">
					<label for="" class="form-label">{translate('firstname')}</label>
					<input class="form-control main-form-element" type="text" name="firstname" placeholder="{translate('firstname')}" id="firstname" value="{set_value('firstname')}">
					{if form_error('firstname')}
						<div class="error-message">
							<span>{form_error('firstname')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element">
					<label for="" class="form-label">{translate('lastname')}</label>
					<input class="form-control main-form-element" type="text" name="lastname" placeholder="{translate('lastname')}" id="lastname" value="{set_value('lastname')}">
					{if form_error('lastname')}
						<div class="error-message">
							<span>{form_error('lastname')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element">
					<label for="" class="form-label">{translate('password')}</label>
					<input class="form-control main-form-element" type="password" name="password" placeholder="{translate('password')}" id="password" min="6" value="{set_value('password')}">
					{if form_error('password')}
						<div class="error-message">
							<span>{form_error('password')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element">
					<label for="" class="form-label">{translate('password_confirm')}</label>
					<input class="form-control main-form-element" type="password" name="password_confirm" id="password_confirm" placeholder="{translate('password_confirm')}" min="6" value="{set_value('password_confirm')}">
					{if form_error('password_confirm')}
						<div class="error-message">
							<span>{form_error('password_confirm')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element">
					<label for="" class="form-label">{translate('email')}</label>
					<input class="form-control main-form-element" type="email" name="email"  id="email" placeholder="{translate('email')}" value="{set_value('email')}">
					{if form_error('email')}
						<div class="error-message">
							<span>{form_error('email')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element mobile_account_element">
					<label for="" class="form-label">{translate('mobile')}</label>
					<input class="form-control main-form-element mobilenumber-flag  number-validation" type="tel" id="mobilenumber" name="mobile" placeholder="" value="{set_value('mobile')}">
					{if form_error('mobile')}
						<div class="error-message">
							<span>{form_error('mobile')}</span>
						</div>
					{/if}
					<i class="fa fa-chevron-down mobile-chevron"></i>
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('country')}</label>
					<select name="country_id" class="form-control main-form-element" id="country_id">
						<option value="0">{translate('please_select')}</option>
						{foreach from=$countries item=country}
							<option {if (set_value('country_id') && set_value('country_id') eq $country->id)}selected="selected"{/if} value="{$country->id}">{$country->name}</option>
						{/foreach}
					</select>
					{if form_error('country')}
						<div class="error-message">
							<span>{form_error('country')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('city')}</label>
					<select name="zone_id" class="form-control main-form-element" id="zone_id"  {if set_value('country_id')}data-selected="{set_value('zone_id')}"{/if}>
						<option></option>
					</select>
					{if form_error('city')}
						<div class="error-message">
							<span>{form_error('city')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('address')}</label>
					<input class="form-control main-form-element" type="text" name="address" value="{set_value('address')}" placeholder="{translate('address')}">
					<input class="form-control main-form-element address2-optional" type="text" value="{set_value('address2')}" name="address2" placeholder="{translate('address2')}">
					{if form_error('address')}
						<div class="error-message">
							<span>{form_error('address')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('postcode')}</label>
					<input class="form-control main-form-element" type="text" name="postcode" placeholder="{translate('postcode')}" value="{set_value('postcode')}" id="postcode">
					{if form_error('postcode')}
						<div class="error-message">
							<span>{form_error('postcode')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('type')}</label>
					<select name="type" class="form-control main-form-element seller-type-select" id="type">
						<option value="0">{translate('please_select')}</option>
						<option {if (set_value('type') && set_value('type') eq 1)}selected="selected"{/if} value="1">{translate('personal')}</option>
						<option {if (set_value('type') && set_value('type') eq 2)}selected="selected"{/if} value="2">{translate('business')}</option>
					</select>
					{if form_error('type')}
						<div class="error-message">
							<span>{form_error('type')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number">
					{* <label for="" class="form-label text-center">{translate('passport_image')}</label> *}
					<!-- starts seller file upload -->
					<div class="seller-file-upload-cover file-upload-none">
						<label for="" class="form-label text-left file-input-label22 label_personal i-hide">{translate('personal_label')} </label>
						<label for="" class="form-label text-left file-input-label22 label_business i-hide">{translate('business_label')} </label>
						<div class="seller-file-upload">
							<div class="contact-add-file add-file-step">
								<div class="add-file-btn">
									<input type="file"  class="multi multi1" name="file" maxlength="10"/>
									<div class="add-file-icon">
										<span>{translate('add_files')}</span>
									</div>
								</div>
							</div>
							<div class="add-file-results add-stepfile-results" id="multi1"></div>
						</div>
					</div>
					<!-- ends seller file upload -->

					{* <div class="passport-images">
						<div class="passport-front-side">
							<input type='file' name="file" class="imgInp1"  />
							<input class="file-upload-img11" type="hidden" name="front_side" value="{set_value('front_side')}">
							<img class="file-upload-img1" src="{set_value('front_side')}" />
							<div class="add-passport-img">{translate('add_front_side')}</div>
						</div>
						<div class="passport-front-side">
							<input type='file' class="imgInp2" />
							<img class="file-upload-img2" src="{set_value('back_side')}" />
							<input class="file-upload-img21" type="hidden" name="back_side"  value="{set_value('back_side')}">
							<div class="add-passport-img">{translate('add_back_side')}</div>
						</div>
					</div> *}
					{if form_error('front_side')}
						<div class="error-message">
							<span>{form_error('front_side')}</span>
						</div>
					{/if}
					{if form_error('back_side')}
						<div class="error-message">
							<span>{form_error('back_side')}</span>
						</div>
					{/if}
				</div>
				<div class="form-group form_group account-element payment-card-number">
					<label for="" class="form-label">{translate('brand')}</label>
					<input class="form-control main-form-element" type="text" name="brand" placeholder="{translate('brand')}" value=" {set_value('brand')}" />
					{if form_error('brand')}
						<div class="error-message">
							<span>{form_error('brand')}</span>
						</div>
					{/if}
				</div>
				<div class="new-account-terms txt_main">
					<h5 class="new_account_terms text-left">
						<button type="button" class="new_account_terms_more">
							{translate('become_seller_agreement', true)}
							<i class="fa fa-chevron-down"></i>
						</button>
					</h5>
					<div class="new_account_terms_info main_txt text-left i-hide">
						{translate('become_seller_agreement_text', true)}
					</div>
				</div>
				<div class="text-center form_btn_cover save-change-btn">
					<button type="submit" class="btn reviews-btn">{translate('create_account')}</button>
				</div>
			</form>
		{/if}

	</div>
</div>
{/block}