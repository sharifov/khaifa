{extends file=$layout}
{block name=content}
<div class="panel panel-white">
	<div class="panel-heading">
		<h5 class="panel-title">{$title}</h5>
		<div class="heading-elements">
		</div>
	</div>
	
	{if isset($message) && !empty($message)}
		<div class="panel-body">
			<div class="alert alert-success no-border">
				{$message}
			</div>
		</div>
	{/if}

	
	<div class="table-responsive">
	{form_open($admin_url|cat:'/translation/save', 'class="form-horizontal has-feedback", id="form-save"')}
	<input type="hidden" name="filename" value="{$file}" />
	<table class="table table-bordered table-striped table-hover table-xxs">
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
				<th>Pattern</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			{if isset($lang_array) && !empty($lang_array)}
				{foreach from=$lang_array key=key item=value}
				<tr>
					<td>{$key}</td>
					<td><input type="text" class="form-control" value="{$value}" name="key[{$key}]" size="159"/></td>
					<td>{$pattern.$key}</td>
					<td>
						<ul class="icons-list">
							<li><a href="#" class="delete_key" data-popup="tooltip" title="" data-original-title="Remove"><i class="icon-trash"></i></a></li>
						</ul>
					</td>
				</tr>
				{/foreach}
			{else}
			<tr>
				<td colspan="9"><div class="text-center">No languages</div></td>
			</tr>
			{/if}
		</tbody>
	</table>
	{form_close()}
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){

	$('.delete_key').on('click', function(e){
		e.preventDefault();
		$(this).parent().parent().parent().parent().remove().fadeOut();
	});

});
</script>
{/block}