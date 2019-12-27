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
		<li class="active"><a href="#general" data-toggle="tab"><i class="icon-menu7 position-left"></i> {translate('tab_general',true)}</a></li>
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
	</div>
	{form_close()}
</div>
{literal}
<script>
	var Select2Selects = function () { 
		var _componentSelect2 = function () {
			if (!$().select2) { console.warn('Warning - select2.min.js is not loaded.'); return; }
			$('.select').select2({ minimumResultsForSearch: Infinity });
			$('.select-search').select2(); 
			// Format icon
			function iconFormat(icon) {
				var originalOption = icon.element;
				if (!icon.id) {return icon.text;}
				var $icon = '<i class="' + $(icon.element).val() + '"></i>' + icon.text;
				return $icon;
			}
			// Initialize with options
			$('.select-icons').select2({ templateResult: iconFormat, minimumResultsForSearch: Infinity, templateSelection: iconFormat, escapeMarkup: function (m) { return m; } });
		}; 
		// Return objects assigned to module 
		return { init: function () { _componentSelect2(); } }
	}(); 
	// Initialize module
	document.addEventListener('DOMContentLoaded', function () {
		Select2Selects.init();
	});
</script>
{/literal}
{/block}