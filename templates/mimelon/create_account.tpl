{extends file=$layout}
{block name=content}
<div class="new-account-cover container-fluid m-container">
	<div class="new-account">
		<h1 class="general-profile-title wow fadeInUp">{translate('form_label_new_account')}</h1>
		{form_open(base_url('account/create'))}
			<div class="form-group form_group account-element  wow fadeInUp">
				<label for="" class="form-label">{translate('form_label_firstname')}</label>
				<input class="form-control main-form-element" type="text" name="firstname" value="{set_value('firstname')}" placeholder="" id="firstname">
				{if form_error('firstname')}
				<div class="error-message">
					<span>{form_error('firstname')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="" class="form-label">{translate('form_label_lastname')}</label>
				<input class="form-control main-form-element" type="text" name="lastname" value="{set_value('lastname')}" placeholder="" id="lastname">
				{if form_error('lastname')}
				<div class="error-message">
					<span>{form_error('lastname')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="" class="form-label">{translate('form_label_password')}</label>
				<input class="form-control main-form-element" type="password" name="password" value="{set_value('password')}" placeholder="" id="password" min="6">
				{if form_error('password')}
				<div class="error-message">
					<span>{form_error('password')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="" class="form-label">{translate('form_label_repeat_password')}</label>
				<input class="form-control main-form-element" type="password" name="password_confirm" placeholder="" id="repeatpassword" min="6">
				{if form_error('password_confirm')}
				<div class="error-message">
					<span>{form_error('password_confirm')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="" class="form-label">{translate('form_label_email')}</label>
				<input class="form-control main-form-element" type="email" name="email" value="{set_value('email')}" placeholder="" id="email">
				{if form_error('email')}
				<div class="error-message">
					<span>{form_error('email')}</span>
				</div>
				{/if}
			</div>
			<div class="new-account-terms txt_main  wow fadeInUp">
				{translate('create_account_agree', true)}
				<a target="_blank" href="{site_url_multi('terms-and-conditions')}">{translate('term_and_conditions_privacy_policy', true)}</a>
			</div>
			<div class="text-center form_btn_cover save-change-btn  wow fadeInUp">
				<button type="submit" class="btn reviews-btn">{translate('form_label_create_account')}</button>
			</div>
		</form>
		<div class="or_cover">
			<span>{translate('or', true)}</span>
		</div>
		<div class="signin-socials">
			<p class="txt_main wow fadeInUp">{translate('sign_in_with', true)}</p>
			<ul class="social-links-sign-in padding_left wow fadeInUp">
				<li><a href="{get_facebook_login_url()}" class="btn reviews-btn btn_facebook"><i class="fa fa-facebook"></i> Facebook </a></li>
				<li><a href="{get_google_login_url()}" class="btn reviews-btn btn_google"><i class="fa fa-google-plus"></i> Google </a></li>
			</ul>
		</div>
	</div>
</div>
{/block}