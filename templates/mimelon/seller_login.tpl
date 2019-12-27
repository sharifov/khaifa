{extends file=$layout}
{block name=content}
<section class="container sign-in-cover">
	<h1 class="signin-title">{translate('login')}</h1>
	<form action="{current_url()}" method="POST">
		<div class="sign__in">
			{if isset($message)}
			<div class="error-message">
				<span>{$message}</span>
			</div>
			{/if}
			<div class="form-group form_group cover-validation">
				<label for="" class="form-label">{translate('email')}</label>
				<input class="form-control main-form-element" id="email" type="email" name="login" placeholder="{translate('email')}" value="{set_value('email')}" required>
				{if form_error('email')}
				<div class="error-message">
					<span>{form_error('email')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group cover-validation">
				<label for="" class="form-label">{translate('password')}</label>
				<input class="form-control main-form-element" id="password" type="password" name="password" placeholder="{translate('password')}" required placeholder="" min="6">
				{if form_error('password')}
				<div class="error-message">
					<span>{form_error('password')}</span>
				</div>
				{/if}
			</div>
			<div class="text-right forget_password wow fadeInUp"><a href="{site_url_multi('become_seller/forget_password')}" class="link_underline">{translate('form_label_forgot_password')}</a></div>
			<div class="text-center form_btn_cover"><button type="submit" class="btn reviews-btn">{translate('signin')}</button></div>
		</div>
	</form>
	<div class="or_cover create_or">
		<span>{translate('new_seller')}</span>
	</div>
	<div class="text-center"><a href="{site_url_multi('become_seller')}" class="link_underline create_account">{translate('become_seller')}</a></div>
</section>
{/block}
