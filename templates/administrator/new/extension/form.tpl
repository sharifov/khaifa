{extends file=$layout}
{block name=content}
<div class="panel panel-white">
	<div class="panel-heading">
		<h5 class="panel-title text-semibold">{$title} <a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
		<div class="heading-elements"></div>
	</div> 
	{if isset($message) && !empty($message)}
		<!-- Show form error -->
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-danger no-border">
					<button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
					{$message}
				</div>
			</div>
		</div>
		<!-- End show form error -->
	{/if} 
	{form_open(current_url(), 'class="form-horizontal has-feedback", id="form-save"')}
	<!-- Tabs -->	
	<ul class="nav nav-lg nav-tabs nav-tabs-bottom nav-tabs-toolbar no-margin">
		<li class="active"><a href="#text" data-toggle="tab"><i class="icon-paragraph-justify2 position-left"></i> {translate('tab_text')}</a></li>
		<li><a href="#fields" data-toggle="tab"><i class="icon-paragraph-justify2 position-left"></i> {translate('tab_fields')}</a></li>
	</ul>
	<!-- End tabs -->

	<!-- Tab content -->
	<div class="tab-content">
		

       

		<!-- Title tab -->
		<div class="tab-pane active" id="text">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<h6>{translate('text_index')}</h6>					
						<div class="tabbable nav-tabs-vertical nav-tabs-left">
							<ul class="nav nav-tabs nav-tabs-highlight" style="width:200px !important">
								<li class="active"><a href="#index_title" data-toggle="tab" aria-expanded="true"> {translate('text_tab_title')}</a></li>
								<li><a href="#index_subtitle" data-toggle="tab" aria-expanded="true">  {translate('text_tab_subtitle')}</a></li>
							</ul>

							<div class="tab-content">
								<div class="tab-pane active" id="index_title">
									{if isset($languages)}
										{foreach from=$languages key=language_code item=language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[index][title]['|cat:$language.code|cat:']'}
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[index][title][{$language.code}]" value="{(isset($text.index.title[$language.code])) ? $text.index.title[$language.code] : set_value($setvalue) }" placeholder="{translate('text_index_title')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

								<div class="tab-pane" id="index_subtitle">
									{if isset($languages)}
										{foreach from=$languages key=language_code item=language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[index][subtitle]['|cat:$language.code|cat:']'}
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[index][subtitle][{$language.code}]" value="{(isset($text.index.subtitle[$language.code])) ? $text.index.subtitle[$language.code] : set_value($setvalue) }" placeholder="{translate('text_index_subtitle')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

							</div>
						</div>                                       
					</div>
					<div class="col-md-6">
						<h6>{translate('text_trash')}</h6>					
						<div class="tabbable nav-tabs-vertical nav-tabs-left">
							<ul class="nav nav-tabs nav-tabs-highlight" style="width:200px !important">
								<li class="active"><a href="#trash_title" data-toggle="tab" aria-expanded="true"> {translate('text_tab_title')}</a></li>
								<li><a href="#trash_subtitle" data-toggle="tab" aria-expanded="true">  {translate('text_tab_subtitle')}</a></li>
							</ul>

							<div class="tab-content">
								<div class="tab-pane active" id="trash_title">
									{if isset($languages)}
										{foreach from=$languages item=language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[trash][title]['|cat:$language.code|cat:']'}
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[trash][title][{$language.code}]" value="{(isset($text.trash.title[$language.code])) ? $text.trash.title[$language.code] : set_value($setvalue) }" placeholder="{translate('text_trash_title')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

								<div class="tab-pane" id="trash_subtitle">
									{if isset($languages)}
										{foreach from=$languages item=language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[trash][subtitle]['|cat:$language.code|cat:']'}	
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[trash][subtitle][{$language.code}]" value="{(isset($text.trash.subtitle[$language.code])) ? $text.trash.subtitle[$language.code] : set_value($setvalue) }" placeholder="{translate('text_trash_subtitle')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

							</div>
						</div>                                       
					</div>
					<div class="col-md-6">
						<h6>{translate('text_show')}</h6>					
						<div class="tabbable nav-tabs-vertical nav-tabs-left">
							<ul class="nav nav-tabs nav-tabs-highlight" style="width:200px !important">
								<li class="active"><a href="#show_title" data-toggle="tab" aria-expanded="true"> {translate('text_tab_title')}</a></li>
								<li><a href="#show_subtitle" data-toggle="tab" aria-expanded="true">  {translate('text_tab_subtitle')}</a></li>
							</ul>

							<div class="tab-content">
								<div class="tab-pane active" id="show_title">
									{if isset($languages)}
										{foreach from=$languages item=language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[show][title]['|cat:$language.code|cat:']'}
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[show][title][{$language.code}]" value="{(isset($text.show.title[$language.code])) ? $text.show.title[$language.code] : set_value($setvalue) }" placeholder="{translate('text_show_title')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

								<div class="tab-pane" id="show_subtitle">
									{if isset($languages)}
										{foreach from=$languages item=language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[show][subtitle]['|cat:$language.code|cat:']'}	
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[show][subtitle][{$language.code}]" value="{(isset($text.show.subtitle[$language.code])) ? $text.show.subtitle[$language.code] : set_value($setvalue) }" placeholder="{translate('text_show_subtitle')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

							</div>
						</div>                                       
					</div>
					<div class="col-md-6">
						<h6>{translate('text_create')}</h6>					
						<div class="tabbable nav-tabs-vertical nav-tabs-left">
							<ul class="nav nav-tabs nav-tabs-highlight" style="width:200px !important">
								<li class="active"><a href="#create_title" data-toggle="tab" aria-expanded="true"> {translate('text_tab_title')}</a></li>
								<li><a href="#create_subtitle" data-toggle="tab" aria-expanded="true">  {translate('text_tab_subtitle')}</a></li>
							</ul>

							<div class="tab-content">
								<div class="tab-pane active" id="create_title">
									{if isset($languages)}
										{foreach $languages as $language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[create][title]['|cat:$language.code|cat:']'}
												<div class="form-group">
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[create][title][{$language.code}]" value="{(isset($text.create.title[$language.code])) ? $text.create.title[$language.code] : set_value($setvalue) }" placeholder="{translate('text_index_title')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

								<div class="tab-pane" id="create_subtitle">
									{if isset($languages)}
										{foreach $languages as $language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[create][subtitle]['|cat:$language.code|cat:']'}
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[create][subtitle][{$language.code}]" value="{(isset($text.create.subtitle[$language.code])) ? $text.create.subtitle[$language.code] : set_value($setvalue) }" placeholder="{translate('text_create_subtitle')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

							</div>
						</div>                                       
					</div>
					<div class="col-md-6">
						<h6>{translate('text_edit')}</h6>					
						<div class="tabbable nav-tabs-vertical nav-tabs-left">
							<ul class="nav nav-tabs nav-tabs-highlight" style="width:200px !important">
								<li class="active"><a href="#edit_title" data-toggle="tab" aria-expanded="true"> {translate('text_tab_title')}</a></li>
								<li><a href="#edit_subtitle" data-toggle="tab" aria-expanded="true"> {translate('text_tab_subtitle')}</a></li>
							</ul>

							<div class="tab-content">
								<div class="tab-pane active" id="edit_title">
									{if isset($languages)}
										{foreach $languages as $language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[edit][title]['|cat:$language.code|cat:']'}
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[edit][title][{$language.code}]" value="{(isset($text.edit.title[$language.code])) ? $text.edit.title[$language.code] : set_value($setvalue) }" placeholder="{translate('text_edit_title')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

								<div class="tab-pane" id="edit_subtitle">
									{if isset($languages)}
										{foreach $languages as $language}
											{if $language.admin == 1}
												{assign var="setvalue" value='text[edit][subtitle]['|cat:$language.code|cat:']'}
												<div class="form-group">	
													<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
														<input type="text" name="text[edit][subtitle][{$language.code}]" value="{(isset($text.edit.subtitle[$language.code])) ? $text.edit.subtitle[$language.code] : set_value($setvalue) }" placeholder="{translate('text_edit_subtitle')}" class="form-control">
													</div>
												</div>
											{/if}
										{/foreach}
									{/if}
								</div>

							</div>
						</div>                                       
					</div>
				</div>
			</div>
		</div>
		<!-- End title tab -->

		<!-- Fields tab -->
		<div class="tab-pane" id="fields">
			<ul class="nav nav-lg nav-tabs nav-tabs-bottom nav-tabs-toolbar no-margin nav-justified">
				<li class="general active"><a href="#general_field" data-toggle="tab"><i class="icon-menu7 position-left"></i> {translate('tab_fields_general')}</a></li>
				<li class="translation"><a href="#translation_field" data-toggle="tab"><i class="icon-earth position-left"></i> {translate('tab_fields_translatable')}</a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="general_field">
					<table class="table table-bordered table-striped table-responsive extension">
						<thead>
							<th class="text-center" width="15%">{translate('fields_general_column')}</th>
							<th class="text-center" width="25%">{translate('fields_general_text')}</th>
						</thead>
						<tbody class="flowers">
							{assign var=i value=0}
							{if isset($db_fields_general)}
								{foreach from=$db_fields_general item=$db_field}
									{assign var=i value=$i+1}
										<tr class="row_{$i}">
											<td><input type="text" name="general[{$i}][column]" value="{$db_field->name}" class="form-control" readonly/></td>
											<td>
												<ul class="nav nav-tabs bg-indigo-800 nav-tabs-component nav-justified">
													<li class="active table_{$i}"><a href="#table_{$i}" data-toggle="tab">{translate('fields_general_text_table')}</a></li>
													<li class="label_{$i}"><a href="#label_{$i}" data-toggle="tab">{translate('fields_general_text_label')}</a></li>
													<li class="placeholder_{$i}"><a href="#placeholder_{$i}" data-toggle="tab">{translate('fields_general_text_placeholder')}</a></li>
												</ul>
												<div class="tab-content">
													<div class="tab-pane active" id="table_{$i}">
														{if isset($languages)}
															{foreach from=$languages item=language}
																{if $language.admin == 1}
																	<div class="form-group">
																		<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
																			<input type="text" name="general[{$db_field->name}][table][{$language.code}]" value="{(isset($fields.general[$db_field->name].table[$language.code])) ? $fields.general[$db_field->name].table[$language.code] : '' }" placeholder="{translate('fields_general_text_table')}" class="form-control">
																		</div>
																	</div>
																{/if}
															{/foreach}
														{/if}
													</div>
													<div class="tab-pane" id="label_{$i}">
														{if isset($languages)}
															{foreach from=$languages item=language}
																{if $language.admin == 1}
																	<div class="form-group">	
																		<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
																			<input type="text" name="general[{$db_field->name}][label][{$language.code}]" value="{(isset($fields.general[$db_field->name].label[$language.code])) ? $fields.general[$db_field->name].label[$language.code] : ''}" placeholder="{translate('fields_general_text_label')}" class="form-control">
																		</div>
																	</div>
																{/if}
															{/foreach}
														{/if}
													</div>
													<div class="tab-pane" id="placeholder_{$i}">
														{if isset($languages)}
															{foreach from=$languages item=language}
																{if $language.admin == 1}
																	<div class="form-group">	
																		<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
																			<input type="text" name="general[{$db_field->name}][placeholder][{$language.code}]" value="{(isset($fields.general[$db_field->name].placeholder[$language.code])) ? $fields.general[$db_field->name].placeholder[$language.code] : ''}" placeholder="{translate('fields_general_text_placeholder')}" class="form-control">
																		</div>
																	</div>
																{/if}
															{/foreach}
														{/if}
													</div>
												</div>
											</td>
										</tr>
								{/foreach}
							{/if} 
						<tbody>
					</table>
				</div> 
				
				<div class="tab-pane" id="translation_field">
					<table class="table table-bordered table-striped table-responsive extension">
						<thead>
							<th class="text-center" width="15%">{translate('fields_general_column')}</th>
							<th class="text-center" width="25%">{translate('fields_general_text')}</th>
						</thead>
						<tbody class="translation_flow">
							{if isset($db_fields_translation)}
								{foreach from=$db_fields_translation item=$db_field}
									{assign var=i value=$i+1}
										<tr class="row_{$i}">
											<td><input type="text" name="translation[{$i}][column]" value="{$db_field->name}" class="form-control" readonly/></td>
											<td>
												<ul class="nav nav-tabs bg-indigo-800 nav-tabs-component nav-justified">
													<li class="active table_{$i}"><a href="#table_{$i}" data-toggle="tab">{translate('fields_general_text_table')}</a></li>
													<li class="label_{$i}"><a href="#label_{$i}" data-toggle="tab">{translate('fields_general_text_label')}</a></li>
													<li class="placeholder_{$i}"><a href="#placeholder_{$i}" data-toggle="tab">{translate('fields_general_text_placeholder')}</a></li>
												</ul>
												<div class="tab-content">
													<div class="tab-pane active" id="table_{$i}">
														{if isset($languages)}
															{foreach $languages as $language}
																{if $language.admin == 1}
																	<div class="form-group">
																		<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
																			<input type="text" name="translation[{$db_field->name}][table][{$language.code}]" value="{(isset($fields.translation[$db_field->name].table[$language.code])) ? $fields.translation[$db_field->name].table[$language.code] : ''}" placeholder="{translate('fields_general_text_table')}" class="form-control">
																		</div>
																	</div>
																{/if}
															{/foreach}
														{/if}
													</div>
													<div class="tab-pane" id="label_{$i}">
														{if isset($languages)}
															{foreach $languages as $language}
																{if $language.admin == 1}
																	<div class="form-group">	
																		<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
																			<input type="text" name="translation[{$db_field->name}][label][{$language.code}]" value="{(isset($fields.translation[$db_field->name].label[$language.code])) ? $fields.translation[$db_field->name].label[$language.code] : ''}" placeholder="{translate('fields_general_text_label')}" class="form-control">
																		</div>
																	</div>
																{/if}
															{/foreach}
														{/if}
													</div>
													<div class="tab-pane" id="placeholder_{$i}">
														{if isset($languages)}
															{foreach $languages as $language}
																{if $language.admin == 1}
																	<div class="form-group">	
																		<div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" title="{$language.name}"></span>
																			<input type="text" name="translation[{$db_field->name}][placeholder][{$language.code}]" value="{(isset($fields.translation[$db_field->name].placeholder[$language.code])) ? $fields.translation[$db_field->name].placeholder[$language.code] : ''}" placeholder="{translate('fields_general_text_placeholder')}" class="form-control">
																		</div>
																	</div>
																{/if}
															{/foreach}
														{/if}
													</div>
												</div>
											</td>
											
										</tr>
								{/foreach}
							{/if} 
						<tbody>
					</table> 
				</div>
			</div>

		</div>
		<!-- End field tab -->

	</div>
	<!-- End tab content -->
	{form_close()}
</div>
<style>
	.extension > tbody > tr > td {
		vertical-align: top !important;
	}
	.extension > tbody > tr > td:first-child {
		vertical-align: middle !important;
	}
</style>
{/block}