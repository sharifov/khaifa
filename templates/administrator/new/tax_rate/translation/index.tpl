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
		<table class="table table-bordered table-striped table-hover table-xxs">
			<thead>
				<tr>
					<th>Filename</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				{if isset($directories) && !empty($directories)}
					{foreach $directories as $directory}
					<tr>
						<td><i class="icon-folder2"></i> <a href="{site_url_multi($admin_url)}/translation/directory/{$directory.path}">{$directory.name} </a></td>
						<td></td>
					</tr>
					{/foreach}
				{/if}
				{if isset($files) && !empty($files)}
					{foreach from=$files item=file}
					<tr>
						<td><i class="icon-file-css2"></i> <a href="{$file.href}">{$file.name}</td>
						<td>
							<ul class="icons-list">
								<li><a href="{$file.href}" data-popup="tooltip" title="{translate('edit', true)}"><i class="icon-pencil7"></i></a></li>
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