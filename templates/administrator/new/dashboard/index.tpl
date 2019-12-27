{extends file=$layout}
{block name=content}
	<div class="content">
		<div class="row">
			{if $modules}
				{foreach from=$modules item=module}
					<div class="col-lg-2 col-md-4 col-sm-6">
						<div class="panel panel-body panel-body-accent" style="min-height:114px;">
							<div class="media no-margin">
								<div class="media-left media-middle">
									<a href="{$module->link}"><i class="{$module->icon} icon-3x text-info-800"></i></a>
								</div>

								<div class="media-body text-right">
									<h3 class="no-margin text-semibold"><span class="text-success">{$module->active_count}</span> / <span class="text-danger">{$module->deactive_count}</span></h3>
									<span class="text-uppercase text-size-mini text-muted">{$module->name}</span>
								</div>
							</div>
						</div>
					</div>
					
				{/foreach}
			{/if}
		</div>
	</div>
{/block}