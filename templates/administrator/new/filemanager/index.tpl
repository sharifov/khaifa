{extends file=$layout}
{block name=content}
<div class="panel panel-white">
	<div class="panel-heading">
		<h5 class="panel-title">{$title}</h5>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-5">
				<a href="{$parent}" data-toggle="tooltip" title="{translate('button_parent')}" id="button-parent" class="btn btn-default"><i class="icon-arrow-left8"></i></a>
				<a href="{$refresh}" data-toggle="tooltip" title="{translate('button_refresh')}" id="button-refresh" class="btn btn-default"><i class="icon-reset"></i></a>
				<button type="button" data-toggle="tooltip" title="{translate('button_upload')}" id="button-upload" class="btn btn-primary"><i class="icon-upload"></i></button>
				<button type="button" data-toggle="tooltip" title="{translate('button_folder')}" id="button-folder" class="btn btn-default"><i class="icon-folder"></i></button>
				<button type="button" data-toggle="tooltip" title="{translate('button_delete')}" id="button-delete" class="btn btn-danger"><i class="icon-trash"></i></button>
			</div>
			<div class="col-sm-7">
				<div class="input-group">
					<input type="text" name="search" value="{$filter_name}" placeholder="{translate('form_placeholder_search')}" class="form-control">
					<span class="input-group-btn">
					<button type="button" data-toggle="tooltip" title="{translate('button_search')}" id="button-search" class="btn btn-primary"><i class="icon-search4"></i></button>
					</span>
				</div>
			</div>
		</div>
		<hr />
		<div class="row">
			{if isset($folders)}
			{foreach from=$folders item=folder}
			<div class="col-md-3">
				<div class="panel panel-body">
					<div class="media">
						<div class="media-left">
							<a href="{$folder.href}">
								<i class="icon-folder5" style="font-size:28px;"></i>
							</a>
						</div>

						<div class="media-body">
							<span class="text-regular">{$folder.name}</span>
						</div>

						<div class="media-right media-middle">
							<input type="checkbox" class="styled" name="path[]" value="{$folder.path}" />
						</div>
					</div>
				</div>
			</div>
			{/foreach}
			{/if}
		</div>
		{foreach from=array_chunk($images, 6) item=image1}
		<div class="row">
			{foreach from=$image1 item=image}
			<div class="col-sm-2 col-xs-6">
				<div class="thumbnail">
					<div class="thumb">
						<img src="{$image.thumb}" alt="">
						<div class="caption-overflow">
							<span>
								<a href="{$image.href}" data-popup="lightbox" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-plus3"></i></a>
								<a href="{$image.href}" target="_blank" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5"><i class="icon-link2"></i></a>
							</span>
						</div>
					</div>

					<div class="caption">
						<input type="checkbox" class="styled" name="path[]" value="{$image.path}" /> <span class="text-regular">{$image.name}</span>
					</div>
				</div>
			</div>
			{/foreach}
		</div>
		<br />
		{/foreach}
	</div>
	<div class="panel-footer"></div>
</div>
<script type="text/javascript"><!--
	{if $target}
	$('a.thumbnail').on('click', function(e) {

		{if $thumb}
		$('#{$thumb}').find('img').attr('src', $(this).find('img').attr('src'));
		{/if}

		$('#{$target}').val($(this).parent().find('input').val());

	});
	{/if}




	$('input[name=\'search\']').on('keydown', function(e) {
		if (e.which == 13) {
			$('#button-search').trigger('click');
		}
	});

	$('#button-search').on('click', function(e) {
		var url = '{site_url_multi($admin_url)}/filemanager?directory={$directory}';

		var filter_name = $('input[name=\'search\']').val();

		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
		}

		{if $thumb}
		url += '&thumb=' + '{$thumb}';
		{/if}

		{if $target}
		url += '&target=' + '{$target}';
		{/if}

	});
	//-->
</script>
<script type="text/javascript"><!--
	$('#button-upload').on('click', function() {
		$('#form-upload').remove();

		$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file[]" value="" multiple="multiple" /></form>');

		$('#form-upload input[name=\'file[]\']').trigger('click');

		if (typeof timer != 'undefined') {
	    	clearInterval(timer);
		}

		timer = setInterval(function() {
			if ($('#form-upload input[name=\'file[]\']').val() != '') {
				clearInterval(timer);

				$.ajax({
					url: '{site_url_multi($admin_url)}/filemanager/upload?directory={$directory}',
					type: 'post',
					dataType: 'json',
					data: new FormData($('#form-upload')[0]),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function() {
						$('#button-upload i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
						$('#button-upload').prop('disabled', true);
					},
					complete: function() {
						$('#button-upload i').replaceWith('<i class="fa fa-upload"></i>');
						$('#button-upload').prop('disabled', false);
					},
					success: function(json) {
						$('#msg').html(json);
						if (json['error']) {
							//alert(json['error']);
						}

						if (json['success']) {
							alert(json['success']);
                            document.getElementById('button-refresh').click();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	});

	$('#button-folder').popover({
		html: true,
		placement: 'bottom',
		trigger: 'click',
		title: '{translate("form_placeholder_folder")}',
		content: function() {
			html  = '<div class="input-group">';
			html += '  <input type="text" name="folder" value="" placeholder="{translate("form_placeholder_folder")}" class="form-control">';
			html += '  <span class="input-group-btn"><button type="button" title="{translate("button_folder")}" id="button-create" class="btn btn-primary"><i class="icon-plus2"></i></button></span>';
			html += '</div>';

			return html;
		}
	});

	$('#button-folder').on('shown.bs.popover', function() {
		$('#button-create').on('click', function() {
			$.ajax({
				url: '{site_url_multi($admin_url)}/filemanager/folder?directory={$directory}',
				type: 'post',
				dataType: 'json',
				data: 'folder=' + encodeURIComponent($('input[name=\'folder\']').val()),
				beforeSend: function() {
					$('#button-create').prop('disabled', true);
				},
				complete: function() {
					$('#button-create').prop('disabled', false);
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
					}

					if (json['success'])
					{
						document.getElementById('button-refresh').click();
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
	});

	$('#button-delete').on('click', function(e) {
		if (confirm('{translate("confirm")}')) {
			$.ajax({
				url: '{site_url_multi($admin_url)}/filemanager/delete',
				type: 'post',
				dataType: 'json',
				data: $('input[name^=\'path\']:checked'),
				beforeSend: function() {
					$('#button-delete').prop('disabled', true);
				},
				complete: function() {
					$('#button-delete').prop('disabled', false);
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
					}
					if (json['success']) {
						alert(json['success']);
                        document.getElementById('button-refresh').click();
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
	//-->
</script>
{/block}