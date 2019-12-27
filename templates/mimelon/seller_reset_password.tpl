{extends file=$layout}
{block name=content}
<section class="container sign-in-cover">
	<h1 class="signin-title">{translate('form_label_forgot_password')}</h1>
	{form_open(site_url_multi('become_seller/reset_password'))}
		<input type="hidden" name="key" value="{$key}"/>
		<div class="sign__in">
			{if isset($message) && !empty($message)}
				<div class="alert alert-info">{$message}</div>
			{/if}
			<div class="form-group form_group cover-validation">
				<label for="" class="form-label">{translate('password')}</label>
				<input class="form-control main-form-element" id="password" type="password" name="password" value="{set_value('password')}" placeholder="" required>
				{if form_error('password')}
				<div class="error-message">
					<span>{form_error('password')}</span>
				</div>
				{/if}
			</div>
			
			<div class="text-center form_btn_cover"><button type="submit" class="btn reviews-btn forget-btn22">{translate('form_label_create_password')}</button></div>
			
		</div>
	</form>
</section>
{/block}