{extends file=$layout}
{block name=content}
	<section class="container sign-in-cover">
		<h1 class="signin-title wow fadeInUp">{translate('form_label_sign_in')}</h1>
		<div class="text-center wow fadeInUp"><a href="{site_url_multi('account/create')}" class="link_underline create_account">{translate('form_label_create_account')}</a></div>
		{form_open(current_url())}
			<div class="sign__in">
				<div class="form-group form_group cover-validation  wow fadeInUp">
					<label for="" class="form-label">{translate('form_label_email')}</label>
					<input class="form-control main-form-element" id="email" type="email" name="email" value="{set_value('email')}" placeholder="" required>
					{if form_error('email')}
					<div class="error-message">
						<span>{form_error('email')}</span>
					</div>
					{/if}
				</div>
				<div class="form-group form_group cover-validation  wow fadeInUp">
					<label for="" class="form-label">{translate('form_label_password')}</label>
					<input class="form-control main-form-element" id="password" type="password" name="password" required placeholder="" min="6">
					{if form_error('password')}
					<div class="error-message">
						<span>{form_error('password')}</span>
					</div>
					{/if}
					{if isset($error_message)}
					<div class="error-message">
						<span>{$error_message}</span>
					</div>
					{/if}
					<input type="hidden" name="redirect" value="{$redirect}">

				</div>
				<div class="text-right forget_password wow fadeInUp"><a href="{site_url_multi('account/forget_password')}" class="link_underline">{translate('form_label_forgot_password')}</a></div>
				<div class="text-center form_btn_cover wow fadeInUp"><button type="submit" class="btn reviews-btn">{translate('form_label_sign_in')}</button></div>
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
	</section>
{/block}