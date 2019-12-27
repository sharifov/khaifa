{extends file=$layout}
{block name=content}
<div class="panel panel-white">
	<div class="panel-heading">
		<h5 class="panel-title">{$title}</h5>
		<div class="heading-elements">
			<a class="btn btn-default heading-btn pull-right table-toolbar-button"><i class="icon-gear"></i></a>
			{form_open(current_url(), 'class="heading-form pull-right" method="get"')}
				<div class="form-group has-feedback">
					{form_element($search_field.name)}
					<div class="form-control-feedback">
						<i class="icon-search4 text-size-base text-muted"></i>
					</div>
				</div>
			{form_close()}
		</div>
	</div>
	{if isset($message) && !empty($message)}
	<div class="panel-body">
		<div class="alert alert-success no-border">
			{$message}
		</div>
	</div>
	{/if}
    {if isset($columns) && !empty($columns)}
	<div class="table-toolbar-area" style="display: none; border-bottom: 1px solid #dfdfdf; background: #f5f5f5; padding: 10px;">
		<div class="row">
			{form_open(current_url(), 'method="GET"')}
			<div class="col-md-11" style="padding-top: 5px">
				{foreach from=$columns key=column item=column_data} 
				<label class="checkbox-inline"><input name="fields[]" type="checkbox" class="styled table-column-checkbox" {if in_array($column, $fields)}checked="checked"{/if} value="{$column}">{$column_data.table.{$current_lang}}</label></a>
				{/foreach}
			</div>
			<div class="col-md-1">				
				<button class="btn btn-xs btn-primary btn-labeled btn-block"><b><i class="icon-floppy-disk"></i></b> {translate('form_button_save', TRUE)}</button>
			</div>
			{form_close()}
		</div>
	</div>
	{/if}
	{form_open_multipart(current_url(), 'class="form-horizontal" id="form-list"')}
        {$table}
	{form_close()}
	<div class="panel-footer">
		<a class="heading-elements-toggle"><i class="icon-more"></i></a>
		<div class="heading-elements">
			<span class="heading-left-element">
			{form_dropdown('per_page', $per_page_lists, $per_page, ["class" => "bootstrap-select", "data-style" => "btn-default btn-xs"])}
			</span>
			{$pagination}
		</div>
	</div>
</div>
<script type="text/javascript">
	$('.table-toolbar-button').on('click', function(){
		$('.table-toolbar-area').toggle();
	});
	$('.table-column-checkbox').change(function(){
		var column = $(this).val();
		if($(this).prop('checked')){
			$('.column_'+column).removeClass('hide');
		}
		else{
			$('.column_'+column).addClass('hide');
		}
	});
</script>
{/block}
