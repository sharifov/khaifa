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
	{if isset($directories) && !empty($directories)}
			{include file="{$template_dir}/{$admin_url}/translation/dir_list_view.tpl"}
	{/if}
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover table-xxs">
			<thead>
				<tr>
					<th style="width: 1px;"><input type="checkbox" class="styled" onclick="$('input[name*=\'selected\']').prop('checked', this.checked); $.uniform.update();"></th>
					<th>Filename</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				{if isset($files) && !empty($files)}
					{foreach $files as $file} 					
					<tr>
						<td><input type="checkbox" name="selected[]" value="" class="styled"></td>
						<td><a href="{site_url($admin_url)}/translation/file/{$sel_dir}/{$file}">{$file}</td>
						<td>
							<ul class="icons-list">
								<li><a href="{site_url($admin_url)}/translation/file/{$sel_dir}/{$file}" data-popup="tooltip" title="{$text.common.common_edit}"><i class="icon-pencil7"></i></a></li>
								<li><a href="{site_url($admin_url)}/translation/delete_language_file/{$sel_dir}/{$file}" class="remove" data-popup="tooltip" title="{$text.common.common_remove}"><i class="icon-trash"></i></a></li>
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
	</div>
</div>
{/block}