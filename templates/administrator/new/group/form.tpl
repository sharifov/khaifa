{extends file=$layout}
{block name=content}
<div class="panel panel-white">
	<div class="panel-heading">
		<h5 class="panel-title text-semibold">{$title} <a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
		<div class="heading-elements"></div>
	</div>

	{if validation_errors()}
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger no-border">
				<button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
				{$message}
		    </div>
		</div>
	</div>
	{/if}

	{form_open(current_url(), 'class="form-horizontal has-feedback", id="form-save"')}
	<ul class="nav nav-lg nav-tabs nav-tabs-bottom nav-tabs-toolbar no-margin">
		<li class="active"><a href="#general" data-toggle="tab"><i class="icon-menu7 position-left"></i> {translate("tab_general", true)}</a></li>
		<li><a href="#permissions" data-toggle="tab"><i class="icon-menu7 position-left"></i> {translate("tab_permission")}</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="general">
			<div class="panel-body">
				{foreach from=$form_field.general key=key item=value}
				<div class="form-group {if form_error($form_field.general[{$key}].name)}has-error{/if}">
					{form_label($form_field.general[{$key}].label, $key, ['class' => 'control-label col-md-2'])}
					<div class="col-md-10">
					{form_element($form_field.general[{$key}])}
					{form_error($form_field.general[{$key}].name)}
					</div>
				</div>
				{/foreach}
			</div>		
		</div>

		<div class="tab-pane" id="permissions">
			<div class="panel-body">
				<div class = "row">
				{foreach from=$permission_groups key=key item=permissions}
				<div class="col-md-3">
					{foreach from=$permissions item=row}
						<div class="panel panel-white">
							<div class="panel-heading">
								<div class="panel-title text-semibold">
									<div class="checkbox no-margin-top">
										<label>
											<input type="checkbox" data-controller="{$row.0.controller}" id="{$row.0.controller}" class="styled select_all" title="Select All">
											{$row.0.controller|capitalize}
										</label>
									</div>
 								</div>
							</div>
							<div class="panel-body">
								<div class="form-group">
									{foreach from=$row key=index item=permission}
									<div class="checkbox no-margin-top">
										<label>
											<input type="checkbox" class="styled {$row.0.controller}" name="permissions[]" value="{$permission.id}" {if in_array($permission.id, $selected_permissions)}checked="checked"{/if}>
											{$permission.name}
										</label>
									</div>
									{/foreach}
								</div>
							</div>
						</div>
					{/foreach}
				</div>
				{/foreach}
				</div>
			</div>		
		</div>
	</div>
	{form_close()}
</div>
<script>
{literal}
	$('input.select_all').on("change",function(){
		var input_select_all = $(this);
		let controller = $(input_select_all).data('controller');
		if(controller) {
			$("input."+controller).each(function( index ) {
				if($(input_select_all).is(":checked")) {	
					$('input.'+controller).prop('checked',true);
					$('input.'+controller).parent('span').addClass('checked');
				} else {
					$('input.'+controller).prop('checked',false);
					$('input.'+controller).parent('span').removeClass('checked');
				}
			});	
		}
	});
{/literal}

{if $permissions}
let make_checked = true;
{foreach from=$permissions item=permission}
	make_checked = true;
	$('input.{$permission}').each(function(index){
		if(!$(this).is(':checked')) {
			make_checked = false;
		}
	});
	if(make_checked) {
		$('input#{$permission}').prop('checked',true);
		$('input#{$permission}').parent('span').addClass('checked');
	}
	
{/foreach}
{/if}
</script>
{/block}