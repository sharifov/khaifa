{extends file=$layout}
{block name=content}
<link rel="stylesheet" href="{$admin_theme}/global_assets/css/icons/fontawesome/styles.min.css">
<div class="panel panel-white">
	<div class="panel-heading">
		<h6 class="panel-title">{$title}<a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
		<div class="heading-elements">
			
    	</div>
	</div>

	<div class="panel-body">
		{form_open(current_url(), 'class="form-horizontal has-feedback", id="form-save"')}
			<div class="tabbable nav-tabs-vertical nav-tabs-left">
				<ul class="nav nav-tabs nav-tabs-highlight">
					{foreach from=$tabs key=tab item=tab_data}
						<li {if isset($tab_data.active)}class="active"{/if}><a href="#{$tab}" data-toggle="tab" aria-expanded="false"><i class="{$tab_data.icon} position-left"></i> {$tab_data.label}</a></li>
					{/foreach}					
				</ul>

				<div class="tab-content">
					{foreach from=$tabs key=tab item=tab_data}
					<div class="tab-pane has-padding {if isset($tab_data.active)}active{/if}" id="{$tab}">
						{foreach from=$tab_data.fields key=key item=value}
							{if isset($value.translate) and $value.translate}
								<fieldset class="mb-3">
								<legend class="text-uppercase font-size-sm font-weight-bold">{$tab_data.fields.$key.label}</legend>
								{if isset($languages)}
									{foreach $languages as $language}
										<div class="form-group">										
											<div class="col-sm-12"> <div class="input-group"><span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
												{if $value.property eq 'text'}
													<input type="text" name="{$key}[{{$language.code}}]" value="{(isset($value.value->{$language.code})) ? $value.value->{$language.code} : '' }" placeholder="{$value.placeholder}" class="form-control">
												{elseif $value.property eq 'textarea'}
													<textarea rows=7 name="{$key}[{{$language.code}}]" placeholder="{$value.placeholder}" class="form-control">{(isset($value.value->{$language.code})) ? $value.value->{$language.code} : '' }</textarea>
												{/if}
											</div>
											</div>
										</div>
									{/foreach}
								{/if}
								</legend>
								</fieldset>
														
							{else}
								<div class="form-group has-feedback has-feedback-right">
									{form_label($tab_data.fields.$key.label, $key, ['class' => 'control-label col-md-3'])}
									<div class="col-lg-9">
										{form_element($tab_data.fields.$key)}
										{if isset($tab_data.fields.$key.icon)}
										<div class="form-control-feedback">
											<i class="{$tab_data.fields.$key.icon}"></i>
										</div>
										{/if}
									</div>
								</div>
							{/if}
						{/foreach}
					</div>
					{/foreach}
				</div>
			</div>
		</form>
	</div>
</div>
{/block}