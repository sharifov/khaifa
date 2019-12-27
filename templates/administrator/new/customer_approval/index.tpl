{extends file=$layout}
{block name=content}
<div class="panel panel-white">
	<div class="panel-heading">
		<h5 class="panel-title">{$title}</h5>
		<div class="heading-elements">
			<div class="btn-group">
				{if isset($language_list_holder) && is_array($language_list_holder)}
                    {foreach $language_list_holder as $language}		
                        <a href="{site_url_multi($admin_url)}/option?language_id={$language.id}" class="{$language.class}"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" alt="{$language.name}"> {$language.name|upper} <span class="label bg-slate-700">{$language.count}</span></a>
                    {/foreach}
				{/if}
			</div>

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
	<form id="form-list">
		{$table}
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
<script type="text/javascript">
	$('.table-toolbar-button').on('click', function(){
		$('.table-toolbar-area').slideToggle( "fast" );
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
	$('i.icon-checkmark4').on("click",function(){
		let href = $(this).parent('a').attr('href');
		var row = $(this).parent().parent().parent().parent().parent();
		
		$.ajax({
			type: 'get',
			url: href,
			dataType: 'json',
			success: function (response) {
				swal({
					title: "Customer successfully approved",
					type: "success",
					confirmButtonColor: "#4CAF50"
				});
				row.remove();
			}
		});
		return false;
		
	});
	$('i.icon-cross2').on("click",function(){
		let href = $(this).parent('a').attr('href');
		var row = $(this).parent().parent().parent().parent().parent();
		
		$.ajax({
			type: 'get',
			url: href,
			dataType: 'json',
			success: function (response) {
				swal({
					title: "Customer refused",
					type: "error",
					confirmButtonColor: "#F44336"
				});
				row.remove();
			}
		});
		return false;
		
	});
</script>
{/block}
