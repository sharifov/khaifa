{extends file=$layout}
{block name=content}
<section class="container sign-in-cover">
	<h1 class="signin-title">{translate('form_label_forgot_password')}</h1>
	{form_open(site_url_multi('become_seller/forget_password'))}
		<div class="sign__in">
		{if !$reset }
			{if isset($message) && !empty($message)}
				<div class="alert alert-info">{$message}</div>
			{/if}
			
			<div class="form-group form_group cover-validation">
				<label for="" class="form-label">{translate('email')}</label>
				<input class="form-control main-form-element" id="email" type="email" name="email" value="{set_value('email')}" placeholder="" required>
				{if form_error('email')}
				<div class="error-message">
					<span>{form_error('email')}</span>
				</div>
				{/if}
			</div>
			
			<div class="text-center form_btn_cover"><button type="submit" class="btn reviews-btn forget-btn22">{translate('form_label_forgot_password')}</button></div>
		{else}
			<div class="alert alert-success no-fixed">{$message}</div>
		{/if}
		</div>
	</form>
</section>
{/block}