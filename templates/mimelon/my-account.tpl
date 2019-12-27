{extends file=$layout}
{block name=content}
<section class="container-fluid m-container faq-cover min_height">
	{include file="templates/mimelon/_partial/account_sidebar.tpl"}
	<div class="col-md-6  margin-auto faq-content">
		{if isset($message) && !empty($message)}
			<div class="panel-body">
				<div class="alert alert-success no-border">
					{$message}
				</div>
			</div>
		{/if}
		<h1 class="general-profile-title  wow fadeInUp">{translate('label_my_account')}</h1>
		{form_open(site_url_multi('account/index'))}
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="email" class="form-label">{translate('form_label_email')}</label>
				<input class="form-control main-form-element changeble-input" type="email" name="email" value="{if set_value('email')} {set_value('email')} {else} {$customer->email} {/if} " disabled required  id="email"  placeholder="" >
				{if form_error('email')}
				<div class="error-message">
					<span>{form_error('email')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="firstname" class="form-label">{translate('form_label_firstname')}</label>
				<input class="form-control main-form-element changeble-input" type="text" name="firstname" value="{if set_value('firstname')} {set_value('firstname')} {else} {$customer->firstname} {/if} " disabled  id="firstname" placeholder="" required>
				{if form_error('firstname')}
				<div class="error-message">
					<span>{form_error('firstname')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="" class="form-label">{translate('form_label_lastname')}</label>
				<input class="form-control main-form-element changeble-input" type="text" name="lastname" value="{if set_value('lastname')} {set_value('lastname')} {else} {$customer->lastname} {/if} " disabled  id="lastname"  placeholder="" >
				{if form_error('lastname')}
				<div class="error-message">
					<span>{form_error('lastname')}</span>
				</div>
				{/if}
			</div>
			<div class="form-group form_group account-element wow fadeInUp">
				<label for="" class="form-label">{translate('form_label_password')}</label>
				<input class="form-control main-form-element changeble-input" disabled type="password" id="password" name="password" placeholder="" min="6">
				{if form_error('password')}
					<div class="error-message">
						<span>{form_error('password')}</span>
					</div>
				{/if}
				<div class="text-right form_btn_cover account-btn wow fadeInUp">
					<a class="btn reviews-btn change-account-btn">{translate('label_change')}</a>
				</div>
			</div>
			<div class="text-center form_btn_cover save-change-btn wow fadeInUp">
				<button type="submit" class="btn reviews-btn">{translate('form_label_save_changes')}</button>
			</div>
		{form_close()}
	</div>
</section>
{/block}