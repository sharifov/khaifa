{extends file=$layout}
{block name=content}
<link rel="stylesheet" href="{$admin_theme}/global_assets/css/icons/fontawesome/styles.min.css">
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
	$(function(){
		$('.datepicker').datepicker();
	});
</script>
<style type="text/css">
	#list_frm table{
		width:100%;
	}
	#list_frm{
		overflow: auto;
	}
</style>

<div class="panel panel-white">
	<div class="panel-heading">
		<h5 class="panel-title">{$title}</h5>
		<div class="heading-elements">
		{form_open(current_url(), 'class="heading-form pull-right" method="get"')}
		
				<div class="form-group">
					<i class="fa fa-calendar absolute center"></i>
					<input type="text" autocomplete="off" name="date_from" class="form-control datepicker" placeholder="From" value="{$date_from}" />
				</div>
				<div class="form-group">
					<i class="fa fa-calendar absolute center"></i>
					<input type="text" autocomplete="off" name="date_to" class="form-control datepicker" placeholder="To" value="{$date_to}" />
				</div>
				
				<div class="form-group">
					<button class="btn btn-primary">Date Filter</button>
				</div>

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
	
	<form id="form-list">
		<div id="list_frm">
			{$table}
		</div>
	</form>

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
{/block}