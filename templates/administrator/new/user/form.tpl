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

	{form_open(current_url(), 'class="form-horizontal has-feedback" id="form-save" autocomplete="false"')}
	<ul class="nav nav-lg nav-tabs nav-tabs-bottom nav-tabs-toolbar no-margin">
		<li class="active"><a href="#general" data-toggle="tab"><i class="icon-menu7 position-left"></i> {translate("tab_general", true)}</a></li>
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
				<div class="row">
					{foreach from=$files item=file}
					<div class="col-md-3">
						<div class="panel panel-body">
							<div class="media">
								<div class="media-left">
									<a href="{base_url('uploads/catalog/document')}/{$file}">
										<i class="icon-file-empty2" style="font-size:28px;"></i>
									</a>
								</div>

								<div class="media-body">
									<a href="{base_url('uploads')}/{$file}">
										<span class="text-regular">{$file}</span>
									</a>
								</div>
							</div>
						</div>
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
	var rows_count = 1;
	function addMultiElement(element,rows){
		if(rows_count < rows) {
			rows_count = parseInt(rows)+1;
		}
		let input_name = $(element).data('input-name');
		let input_type = $(element).data('input-type');
		let html = '<input  type="'+input_type+'" name="'+input_name+'['+rows_count+']" value=""  class="form-control" placeholder="Email" />';
		$(element).parent().append(html);
		rows_count++;
		console.log(html);
	}
{/literal}
</script>
{literal}
<script type="text/javascript">
	
	$('select[name="country"]').on('change', function(){
		let country_id = $(this).children("option:selected").val();
		let selected = $('select[name="city"]').data('selected');
		if (country_id) { 
			$.ajax({
				url: '/account/region?country_id='+ country_id,
				dataType: 'json',
				success: function(json) {
					html = '';
					if (json && json != '')
					{
						for (i = 0; i < json.length; i++)
						{
							html += '<option value="' + json[i]['id'] + '"';
							if (json[i]['id'] == selected)
							{
								html += ' selected="selected"';
							}
		
							html += '>' + json[i]['name'] + '</option>';
						}
					}
		
					$('select[name="city"]').html(html);
					$('select[name="city"]').selectpicker('refresh');

				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
	$('select[name="country"]').trigger('change');
	
</script>
{/literal}
{/block}