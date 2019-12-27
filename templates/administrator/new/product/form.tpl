{extends file=$layout}
{block name=content}
	<div class="panel panel-white">
		<div class="panel-heading">
			<h5 class="panel-title text-semibold">{$title} <a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
			<div class="heading-elements"></div>
		</div>

		{if isset($message)}
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger no-border">
						<button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
						{$message}
					</div>
				</div>
			</div>
		{/if}

		{form_open(current_url(), 'class="form-horizontal has-feedback", id="form-save"')}
		<ul class="nav nav-lg nav-tabs nav-tabs-bottom nav-tabs-toolbar no-margin">
			{if isset($form_field.general)}<li class="active"><a href="#general" data-toggle="tab"><i class="icon-earth position-left"></i> General</a></li>{/if}
			{if isset($form_field['translation'])}<li><a href="#translation" data-toggle="tab"><i class="icon-menu7 position-left"></i> Translation</a></li>{/if}
			<li><a href="#links" data-toggle="tab"><i class="icon-menu7 position-left"></i> {translate('tab_title_link')}</a></li>
			{* <li><a href="#tab-attribute" data-toggle="tab"><i class="icon-list2 position-left"></i> {translate('tab_title_attribute')}</a></li> *}
			<li><a href="#tab-attribute1" data-toggle="tab"><i class="icon-list2 position-left"></i> {translate('tab_title_attribute')}</a></li>
			<li><a href="#tab-option" data-toggle="tab"><i class="icon-checkmark-circle position-left"></i> {translate('tab_title_option')}</a></li>
			{if is_admin()}<li><a href="#tab-relation" data-toggle="tab"><i class="icon-checkmark-circle position-left"></i> {translate('tab_title_relation')}</a></li>{/if}
			{if is_admin()}<li><a href="#special" data-toggle="tab"><i class="icon-percent position-left"></i> {translate('tab_title_special')}</a></li>{/if}
			<li><a href="#image" data-toggle="tab"><i class="icon-images2 position-left"></i> {translate('tab_title_image')}</a></li>
			{if is_admin()}<li><a href="#country_group" data-toggle="tab"><i class="icon-location3 position-left"></i> {translate('tab_title_country_group')}</a></li>{/if}
		</ul>
		<div class="tab-content">
			<div class="tab-pane" id="translation">
				<div class="panel-body">
					<div class="tabbable tab-content-bordered">
						<ul class="nav nav-tabs nav-tabs-highlight nav-justified" id="language">
							{if isset($languages)}
								{foreach $languages as $language}
									<li>
										<a href="#{$language.slug}" data-toggle="tab">
											{$language.name}
											<img src="{$admin_theme}/global_assets/images/flags/{$language.code}.png" alt="{$language.name}" class="pull-right">
										</a>
									</li>
								{/foreach}
							{/if}
						</ul>
						<div class="tab-content">
							{if isset($languages)}
								{foreach $languages as $language}
									<div class="tab-pane active" id="{$language.slug}">
										<div class="panel-body">
											<fieldset class="content-group">
												{foreach from=$form_field['translation'][{$language.id}] key=field_key item=field_value}
													<div class="form-group {if form_error($field_value.name)}has-error{/if}">
														{form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'],(isset($field_value.info)) ? $field_value.info : false)}
														<div class="col-md-10">
															{form_element($field_value)}
															{form_error($field_value.name)}
														</div>
													</div>
												{/foreach}
											</fieldset>
										</div>
									</div>
								{/foreach}
							{/if}
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane active" id="general">
				<div class="panel-body">
					<div class="form-group {if form_error('model')}has-error{/if}">
						<label for="model" class="control-label col-md-2">{translate('form_label_model')} <sup style="color:red">*</sup></label>
						<div class="col-md-10">
							<input type="text" name="model" data-id="model" value="{$model_value}" data-name="model" placeholder="{translate('form_label_model')}" class="form-control" autocomplete="off" />
							<ul class="dropdown-menu" id="model" style="top: 36px; left: 15px; display: none;"></ul>
							{form_error('model')}
						</div>
					</div>
					{foreach from=$form_field.general key=field_key item=field_value}
						<div class="form-group {if form_error($field_value.name)}has-error{/if}">
							{assign var=required value=(isset($field_value.validation['rules']) && in_array('required', explode('|', $field_value.validation['rules']))) ? true : false}
							{form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'],(isset($field_value.info)) ? $field_value.info : false, $required)}

							<div class="col-md-10">
								{form_element($field_value)}
								{form_error($field_value.name)}
							</div>
						</div>
					{/foreach}

				</div>
			</div>

			<div class="tab-pane" id="links">
				<div class="panel-body">

					<div class="form-group">
						<label class="control-label col-md-2">{translate('form_label_category')}</label>
						<div class="col-md-10">
							<select class="form-control" name="category_id[]"  {if isset($categories_data[0])}data-selected="{$categories_data[0]}"{/if}>
								<option value="0">{translate('select', true)}</option>
								{if $categories}
									{foreach from=$categories item=category}
										<option {if isset($categories_data[0]) && $categories_data[0] eq $category->id}selected="selected"{/if} data-attribute-group="{$category->attribute_group_id}" data-child="{$category->has_child}" value="{$category->id}">{$category->name}</option>
									{/foreach}
								{/if}
							</select>
						</div>
					</div>

					<div class="form-group" {if !isset($categories_data[1])}style="display:none"{/if}>
						<label class="control-label col-md-2">{translate('form_label_category')}</label>
						<div class="col-md-10">
							<select class="form-control" name="category_id[]"  {if isset($categories_data[1])}data-selected="{$categories_data[1]}"{/if}>

							</select>
						</div>
					</div>

					<div class="form-group" {if !isset($categories_data[2])}style="display:none"{/if}>
						<label class="control-label col-md-2">{translate('form_label_category')}</label>
						<div class="col-md-10">
							<select class="form-control" name="category_id[]"  {if isset($categories_data[2])}data-selected="{$categories_data[2]}"{/if}>

							</select>
						</div>
					</div>

					{foreach from=$form_field.links key=field_key item=field_value}
						<div class="form-group {if form_error($field_value.name)}has-error{/if}">
							{form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'],(isset($field_value.info)) ? $field_value.info : false)}
							<div class="col-md-10">
								{form_element($field_value)}
								{form_error($field_value.name)}
							</div>
						</div>
					{/foreach}
				</div>
			</div>

			<div class="tab-pane" id="country_group">
				<div class="panel-body">
					<table id="country-group" class="table table-bordered table-responsive">
						<thead>
						<tr>
							<th class="left">Country Group</th>
							<th class="left">Country Price (%)</th>
							<th></th>
						</tr>
						</thead>
						{assign var=country_group_row value=0}
						{if isset($product_country_groups) && !empty($product_country_groups)}
						{foreach from=$product_country_groups item=product_country_group}
						<tbody id="country-group-row{$country_group_row}">
						<tr>
							<td class="left">
							<select name="product_country_group[{$country_group_row}][country_group_id]" class="form-control">
								<option value="0" {if 0 == $product_country_group['country_group_id']}selected{/if}>Default</option>
								{foreach from=$country_groups item=country_group}
								<option value="{$country_group->id}" {if $country_group->id == $product_country_group['country_group_id']}selected{/if}>{$country_group->name}</option>
								{/foreach}
							</select>
							</td>
							<td class="left">
							<input type="text" name="product_country_group[{$country_group_row}][percent]" class="form-control" value="{$product_country_group['percent']}">
							</td>
							<td class="left"><a onclick="$('#country-group-row{$country_group_row}').remove();" class="btn btn-danger"><i class="icon-minus2"></i></a></td>
						</tr>
						</tbody>
						{assign var=country_group_row value=$country_group_row + 1}
						{/foreach}
						{/if}
						<tfoot>
						<tr>
							<td colspan="2"></td>
							<td class="left"><button type="button" onclick="addCountryGroup();" data-toggle="tooltip" title="Add Country Group" class="btn btn-primary"><i class="icon-plus2"></i></button></td>
						</tr>
						</tfoot>
					</table>
				</div>
			</div>

			<div class="tab-pane " id="tab-attribute1">
				<div class="panel-body tab-attribute1_area">
					<table class='table table-responsive table-striped table-hover table-xxs attribute_area'>
						<tbody>
							{assign var=attribute_row value=0}
							{if $product_attributes}
							{foreach from=$product_attributes item=product_attribute}
								<tr>
									<td width='30%'>
										<strong>{$product_attribute.name}</strong>
										<input type='hidden' name='product_attribute[{$attribute_row}][attribute_id]' value='{$product_attribute.attribute_id}'>
										<input type='hidden' name='product_attribute[{$attribute_row}][name]' value='{$product_attribute.name}'>
										<input type='hidden' name='product_attribute[{$attribute_row}][custom_enable]' value='{$product_attribute.custom_enable}'>
									</td>
									<td  width='30%'>
										<select class='form-control select-search attribute_value_select' name='product_attribute[{$attribute_row}][attribute_value_id]' required>
											<option {if $product_attribute.attribute_value_id eq -1} selected="selected" {/if} value='-1'>Please Select</option>";
											{if array_key_exists($product_attribute.attribute_id, $attribute_values)}
											{foreach from=$attribute_values[$product_attribute.attribute_id] item=$attr_value}
												<option {if $product_attribute.attribute_value_id eq $attr_value['attribute_value_id']} selected="selected" {/if}  value='{$attr_value['attribute_value_id']}'>{$attr_value['name']}</option>
											{/foreach}
											{/if}
											{if $product_attribute.custom_enable == 1}
												<option {if $product_attribute.attribute_value_id eq 0} selected="selected" {/if} value='0'>Custom value</option>";
											{/if}
										</select>
									</td>
									<td  width='40%'>
										<div class='custom' {if $product_attribute.attribute_value_id eq 0}style='display:block' {else} style='display:none'{/if}>
											{foreach from=$languages key=lang_key item=language}
												<div class='form-group'>
													<div class='input-group'>
														<span class='input-group-addon'><img src='/templates/administrator/new/global_assets/images/flags/{$lang_key}.png' title='{$language.name}'></span>
														<input type='text' name='product_attribute[{$attribute_row}][attribute_value][{$language.id}][name]' value="{if $product_attribute.attribute_value}{$product_attribute.attribute_value[$language.id].name}{/if}" placeholder='Attribute value name' class='form-control'>
													</div>
												</div>
											{/foreach}
										</div>
									</td>
								</tr>
							{assign var=attribute_row value=$attribute_row+1}
							{/foreach}
							{/if}

							{* Custom Attribute Value *}
							{assign var=custom_attribute_row value=0}
							{if $attributes}
							{foreach from=$attributes item=attribute}
							<tr id="customAttribute{$custom_attribute_row}">
								<td colspan='2'>
									{foreach from=$languages key=lang_key item=language}
										<div class='form-group'>
											<div class='input-group' style='width:100%'>
												<span class='input-group-addon'>
													<img src='/templates/administrator/new/global_assets/images/flags/{$lang_key}.png' title='{$language.name}'>
												</span>
												<input type='text' name='attribute[{$custom_attribute_row}][attribute_description][{$language.id}]name' value='{$attribute.attribute_description[$language.id]}' placeholder='Attribute name' class='form-control'>
											</div>
										</div>
									{/foreach}
								</td>
								<td>
									{foreach from=$languages key=lang_key item=language}
									<div class='form-group'>
										<div class='input-group'>
											<span class='input-group-addon'>
												<img src='/templates/administrator/new/global_assets/images/flags/{$lang_key}.png' title='{$language.name}'>
											</span>
											<input type='text' name='attribute[{$custom_attribute_row}][attribute_value_description][{$language.id}]name' value='{$attribute.attribute_value_description[$language.id]}' placeholder='Attribute value name' class='form-control'>
										</div>
									</div>
									{/foreach}
								</td>
								<td><button type='button' class='btn btn-danger btn-block remove_attr'><i class='icon-minus-circle2'></i></button></td>
							</tr
							{assign var=custom_attribute_row value=$custom_attribute_row+1}
							{/foreach}
							{/if}
						</tbody>
						<tfoot>
							<tr>
								<td class='text-right' colspan='3'>
									<button type='button' onclick='addAttribute()' class='btn btn-primary'><i class='icon-plus2'></i> Add attribute</button>
								</td>
							</tr>
						</tfoot>
						<input type="hidden" name="attribute_group_id" id="attribute_group_id" value="{$attribute_group_id}"/>
					</table>
				</div>
			</div>

			{* <div class="tab-pane " id="tab-attribute">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-2">
							<ul class="nav nav-pills nav-stacked" id="attribute">
								{assign var="attribute_row" value=0}
								{foreach from=$product_attributes item=product_attribute}
								{if isset($product_attribute.product_attribute_value)}
								<li><a href="#tab-attribute{$attribute_row}" data-toggle="tab"><i class="icon-minus-circle2" onclick="$('a[href=\'#tab-attribute{$attribute_row}\']').parent().remove(); $('#tab-attribute{$attribute_row}').remove(); $('#attribute a:first').tab('show');"></i> {$product_attribute.name}</a></li>
								{assign var="attribute_row" value=$attribute_row + 1}
								{/if}
								{/foreach}
								<li>
								<input type="text" name="attribute" value="" placeholder="attribute" id="input-attribute" class="form-control" />
								</li>
							</ul>
						</div>
						<div class="col-sm-10">
							<div class="tab-content">
							{assign var="attribute_row" value=0}
							{assign var="attribute_value_row" value=0}
							{foreach from=$product_attributes item=product_attribute}
							{if isset($product_attribute.product_attribute_value)}
							<div class="tab-pane" id="tab-attribute{$attribute_row}">
								<input type="hidden" name="product_attribute[{$attribute_row}][product_attribute_id]" value="{$product_attribute.product_attribute_id}" />
								<input type="hidden" name="product_attribute[{$attribute_row}][name]" value="{$product_attribute.name}" />
								<input type="hidden" name="product_attribute[{$attribute_row}][attribute_id]" value="{$product_attribute.attribute_id}" />
								<div class="table-responsive">
									<table id="attribute-value{$attribute_row}" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
										<td class="text-left">{translate('form_label_attribute_value')}</td>
										</tr>
									</thead>
									<tbody>
										{foreach from=$product_attribute.product_attribute_value item=product_attribute_value}
										<tr id="attribute-value-row{$attribute_value_row}">
											<td class="text-left">
												<select name="product_attribute[{$attribute_row}][product_attribute_value][{$attribute_value_row}][attribute_value_id]" class="form-control">
													{if $attribute_values[$product_attribute.attribute_id]}
													{foreach from=$attribute_values[$product_attribute.attribute_id] item=attribute_value}
													{if $attribute_value.attribute_value_id eq $product_attribute_value.attribute_value_id}
													<option value="{$attribute_value.attribute_value_id}" selected="selected">{$attribute_value.name}</option>
													{else}
													<option value="{$attribute_value.attribute_value_id}">{$attribute_value.name}</option>
													{/if}
													{/foreach}
													{/if}
												</select>
												<input type="hidden" name="product_attribute[{$attribute_row}][product_attribute_value][{$attribute_value_row}][product_attribute_value_id]" value="{$product_attribute_value.product_attribute_value_id}" />
											</td>

										</tr>
										{assign var="attribute_value_row" value=$attribute_value_row + 1}
										{/foreach}
									</tbody>

									</table>
								</div>
								<select id="attribute-values{$attribute_row}" style="display: none;">
									{if $attribute_values[$product_attribute.attribute_id]}
									{foreach from=$attribute_values[$product_attribute.attribute_id] item=attribute_value}
									<option value="{$attribute_value.attribute_value_id}">{$attribute_value.name}</option>
									{/foreach}
									{/if}
								</select>
							</div>
							{assign var="attribute_row" value=$attribute_row + 1}
							{/if}
							{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div> *}

			<div class="tab-pane " id="tab-option">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-2">
							<ul class="nav nav-pills nav-stacked" id="option">
								{assign var="option_row" value=0}
								{foreach from=$product_options item=product_option}
								{if isset($product_option.product_option_value)}
								<li><a href="#tab-option{$option_row}" data-toggle="tab"><i class="icon-minus-circle2" onclick="$('a[href=\'#tab-option{$option_row}\']').parent().remove(); $('#tab-option{$option_row}').remove(); $('#option a:first').tab('show');"></i> {$product_option.name}</a></li>
								{assign var="option_row" value=$option_row + 1}
								{/if}
								{/foreach}
								<li>
								<input type="text" name="option" value="" placeholder="{translate('form_label_option')}" id="input-option" class="form-control" />
								</li>
							</ul>
						</div>
						<div class="col-sm-10">
							<div class="tab-content">
							{assign var="option_row" value=0}
							{assign var="option_value_row" value=0}
							{foreach from=$product_options item=product_option}
							{if isset($product_option.product_option_value)}
							<div class="tab-pane" id="tab-option{$option_row}">
							<input type="hidden" name="product_option[{$option_row}][product_option_id]" value="{$product_option.product_option_id}" />
							<input type="hidden" name="product_option[{$option_row}][name]" value="{$product_option.name}" />
							<input type="hidden" name="product_option[{$option_row}][option_id]" value="{$product_option.option_id}" />
							<input type="hidden" name="product_option[{$option_row}][type]" value="{$product_option.type}" />
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-required{$option_row}">{translate('form_label_required')}</label>
								<div class="col-sm-10">
								<select name="product_option[{$option_row}][required]" id="input-required{$option_row}" class="form-control">
									{if $product_option.required}
									<option value="1" selected="selected">{translate('yes',true)}</option>
									<option value="0">{translate('no',true)}</option>
									{else}
									<option value="1">{translate('yes',true)}</option>
									<option value="0" selected="selected">{translate('no',true)}</option>
									{/if}
								</select>
								</div>
							</div>
							{if $product_option.type == 'select' or $product_option.type == 'radio' or $product_option.type == 'color' or $product_option.type == 'checkbox' or $product_option.type == 'image'}
							<div class="table-responsive">
								<table id="option-value{$option_row}" class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<td width="20%" class="text-left">{translate('form_label_option_value')}</td>
										{if is_admin()}<td width="20%" class="text-left">{translate('form_label_country_group_id')}</td>{/if}
										<td td width="13%" class="text-right">{translate('form_label_quantity')}</td>
										<td width="15%" class="text-left">{translate('form_label_subtract')}</td>
										<td class="text-right">{translate('form_label_price')}</td>
										<td class="text-right">{translate('form_label_weight')}</td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									{foreach from=$product_option.product_option_value item=product_option_value}
									<tr id="option-value-row{$option_value_row}">
										<td class="text-left">
											<select name="product_option[{$option_row}][product_option_value][{$option_value_row}][option_value_id]" class="form-control">
												{if $option_values[$product_option.option_id]}
												{foreach from=$option_values[$product_option.option_id] item=option_value}
												{if $option_value.option_value_id eq $product_option_value.option_value_id}
												<option value="{$option_value.option_value_id}" selected="selected">{$option_value.name}</option>
												{else}
												<option value="{$option_value.option_value_id}">{$option_value.name}</option>
												{/if}
												{/foreach}
												{/if}
											</select>
											<input type="hidden" name="product_option[{$option_row}][product_option_value][{$option_value_row}][product_option_value_id]" value="{$product_option_value.product_option_value_id}" />
										</td>
										{if is_admin()}
										<td class="text-left">
											<select name="product_option[{$option_row}][product_option_value][{$option_value_row}][country_group_id]" class="form-control">
												<option {if 0 eq $product_option_value.product_option_value_id}selected="selected"{/if} value="0">Default</option>
													{foreach from=$country_groups item=country_group}
														<option {if $country_group->id eq $product_option_value.country_group_id}selected="selected"{/if} value="{$country_group->id}">{$country_group->name}</option>
													{/foreach}
											</select>
										</td>
										{/if}
										<td class="text-right"><input type="text" name="product_option[{$option_row}][product_option_value][{$option_value_row}][quantity]" value="{$product_option_value.quantity}" placeholder="{translate('form_label_quantity')}" class="form-control" /></td>
										<td class="text-left">
											<select name="product_option[{$option_row}][product_option_value][{$option_value_row}][subtract]" class="form-control">
												{if $product_option_value.subtract}
												<option value="1" selected="selected">{translate('yes',true)}</option>
												<option value="0">{translate('no',true)}</option>
												{else}
												<option value="1">{translate('yes',true)}</option>
												<option value="0" selected="selected">{translate('no',true)}</option>
												{/if}
											</select>
										</td>
										<td class="text-right">
											<select name="product_option[{$option_row}][product_option_value][{$option_value_row}][price_prefix]" class="form-control">
												{if $product_option_value.price_prefix eq '+'}
												<option value="+" selected="selected">+</option>
												{else}
												<option value="+">+</option>
												{/if}
												{if $product_option_value.price_prefix eq '-'}
												<option value="-" selected="selected">-</option>
												{else}
												<option value="-">-</option>
												{/if}
											</select>
											<input type="text" name="product_option[{$option_row}][product_option_value][{$option_value_row}][price]" value="{$product_option_value.price}" placeholder="{translate('form_label_price')}" class="form-control" />
										</td>
										<td class="text-right">
											<select name="product_option[{$option_row}][product_option_value][{$option_value_row}][weight_prefix]" class="form-control">
												{if $product_option_value.weight_prefix eq '+'}
												<option value="+" selected="selected">+</option>
												{else}
												<option value="+">+</option>
												{/if}
												{if $product_option_value.weight_prefix eq '-'}
												<option value="-" selected="selected">-</option>
												{else}
												<option value="-">-</option>
												{/if}
											</select>
											<input type="text" name="product_option[{$option_row}][product_option_value][{$option_value_row}][weight]" value="{$product_option_value.weight}" placeholder="{translate('form_label_weight')}" class="form-control" />
										</td>
										<td class="text-right"><button type="button" onclick="/*$(this).tooltip('destroy');*/$('#option-value-row{$option_value_row}').remove();" data-toggle="tooltip" title="remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>
									</tr>
									{assign var="option_value_row" value=$option_value_row + 1}
									{/foreach}
								</tbody>
								<tfoot>
									<tr>
									<td colspan="{if is_admin()}6{else}5{/if}"></td>
									<td class="text-left"><button type="button" onclick="addOptionValue('{$option_row}');" data-toggle="tooltip" title="add" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>
									</tr>
								</tfoot>
								</table>
							</div>
							<select id="option-values{$option_row}" style="display: none;">
								{if $option_values[$product_option.option_id]}
								{foreach from=$option_values[$product_option.option_id] item=option_value}
								<option value="{$option_value.option_value_id}">{$option_value.name}</option>
								{/foreach}
								{/if}
							</select>
							{/if}
							</div>
							{assign var="option_row" value=$option_row + 1}
							{/if}
							{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane " id="tab-relation">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-2">
							<ul class="nav nav-pills nav-stacked" id="relation">
								{assign var="relation_row" value=0}
								{foreach from=$product_relations item=product_relation}
								{if isset($product_relation.product_relation_value)}
								<li><a href="#tab-relation{$relation_row}" data-toggle="tab"><i class="icon-minus-circle2" onclick="$('a[href=\'#tab-relation{$relation_row}\']').parent().remove(); $('#tab-relation{$relation_row}').remove(); $('#relation a:first').tab('show');"></i> {$product_relation.name}</a></li>
								{assign var="relation_row" value=$relation_row + 1}
								{/if}
								{/foreach}
								<li>
								<input type="text" name="relation" value="" placeholder="{translate('form_label_relation')}" id="input-relation" class="form-control" />
								</li>
							</ul>
						</div>
						<div class="col-sm-10">
							<div class="tab-content">
							{assign var="relation_row" value=0}
							{assign var="relation_value_row" value=0}
							{foreach from=$product_relations item=product_relation}
								{if isset($product_relation.product_relation_value)}
								<div class="tab-pane" id="tab-relation{$relation_row}">
								<input type="hidden" name="product_relation[{$relation_row}][product_relation_id]" value="{$product_relation.product_relation_id}" />
								<input type="hidden" name="product_relation[{$relation_row}][name]" value="{$product_relation.name}" />
								<input type="hidden" name="product_relation[{$relation_row}][relation_id]" value="{$product_relation.relation_id}" />
								<div class="table-responsive">
									<table id="relation-value{$relation_row}" class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<td class="text-left">{translate('form_label_relation_value')}</td>
												<td class="text-left">{translate('form_label_relation_current')}</td>
												<td class="text-left">{translate('form_label_relation_product')}</td>
												<td></td>
											</tr>
										</thead>
										<tbody>
											{foreach from=$product_relation.product_relation_value item=product_relation_value}
											<tr id="relation-value-row{$relation_value_row}">
												<td class="text-left">
													<select name="product_relation[{$relation_row}][product_relation_value][{$relation_value_row}][relation_value_id]" class="form-control">
														{if $relation_values[$product_relation.relation_id]}
														{foreach from=$relation_values[$product_relation.relation_id] item=relation_value}
														{if $relation_value.relation_value_id eq $product_relation_value.relation_value_id}
														<option value="{$relation_value.relation_value_id}" selected="selected">{$relation_value.name}</option>
														{else}
														<option value="{$relation_value.relation_value_id}">{$relation_value.name}</option>
														{/if}
														{/foreach}
														{/if}
													</select>
													<input type="hidden" name="product_relation[{$relation_row}][product_relation_value][{$relation_value_row}][product_relation_value_id]" value="{$product_relation_value.product_relation_value_id}" />
												</td>
												<td>
													<input type="radio" name="product_relation[{$relation_row}][current_product]" {if (isset($product_relation.current_product) && $product_relation.current_product eq $relation_value_row) || (isset($product_relation.custom_current_product) && $product_relation.custom_current_product eq $product_relation_value.relation_value_id)} checked {/if} value="{$relation_value_row}"/>
												</td>
												<td class="text-left">
													<input type="text" class="form-control filter_product_name" data-relation-row="{$relation_row}" data-relation-value-row="{$relation_value_row}"  name="product_relation[{$relation_row}][product_relation_value][{$relation_value_row}][product_name]" value="{$product_relation_value.product_name}" />
													<input type="hidden" name="product_relation[{$relation_row}][product_relation_value][{$relation_value_row}][product_id]" value="{$product_relation_value.product_id}" />
												</td>
												<td><button type="button" onclick="$('#relation-value-row{$relation_value_row}').remove();" data-toggle="tooltip" title="remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>
											</tr>
											{assign var="relation_value_row" value=$relation_value_row + 1}
											{/foreach}
										</tbody>
										<tfoot>
											<tr>
												<td colspan="3"></td>
												<td class="text-left"><button type="button" onclick="addRelationValue('{$relation_row}');" data-toggle="tooltip" title="add" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>
											</tr>
										</tfoot>
									</table>
								</div>
								<select id="relation-values{$relation_row}" style="display: none;">
									{if $relation_values[$product_relation.relation_id]}
										{foreach from=$relation_values[$product_relation.relation_id] item=relation_value}
											<option value="{$relation_value.relation_value_id}">{$relation_value.name}</option>
										{/foreach}
									{/if}
								</select>
								</div>
								{assign var="relation_row" value=$relation_row + 1}
								{/if}
							{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="discount" style="display:none;">
				<div class="panel-body">
					<div class="table-responsive">
						<table id="discount" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
							<td class="text-left">{translate('form_label_customer_group')}</td>
							<td class="text-right">{translate('form_label_quantity')}</td>
							<td class="text-right">{translate('form_label_priority')}</td>
							<td class="text-right">{translate('form_label_price')}</td>
							<td class="text-left">{translate('form_label_date_start')}</td>
							<td class="text-left">{translate('form_label_date_end')}</td>
							<td></td>
							</tr>
						</thead>
						<tbody>
						{assign var="discount_row" value=0}
						{if $product_discounts}
						{foreach from=$product_discounts item=product_discount}
						<tr id="discount-row{$discount_row}">
							<td class="text-left">
								<select name="product_discount[{$discount_row}][customer_group_id]" class="form-control">
								{foreach from=$customer_groups item=customer_group}
									<option value="{$customer_group.id}" {if $customer_group.id eq $product_discount.customer_group_id} selected="selected" {/if}>{$customer_group.name}</option>
								{/foreach}
								</select>
							</td>
							<td class="text-right"><input type="text" name="product_discount[{$discount_row}][quantity]" value="{$product_discount.quantity}" placeholder="{translate('form_label_quantity')}" class="form-control" /></td>
							<td class="text-right"><input type="text" name="product_discount[{$discount_row}][priority]" value="{$product_discount.priority}" placeholder="{translate('form_label_priority')}" class="form-control" /></td>
							<td class="text-right"><input type="text" name="product_discount[{$discount_row}][price]" value="{$product_discount.price}" placeholder="{translate('form_label_price')}" class="form-control" /></td>
							<td class="text-left" style="width: 20%;"><div class="input-group date">
								<input type="date" name="product_discount[{$discount_row}][date_start]" value="{$product_discount.date_start}" placeholder="{translate('form_label_date_start')}" class="form-control" /></div>
							</td>
							<td class="text-left" style="width: 20%;"><div class="input-group date">
								<input type="date" name="product_discount[{$discount_row}][date_end]" value="{$product_discount.date_end}" placeholder="{translate('form_label_date_end')}" class="form-control" /></div>
							</td>
							<td class="text-left"><button type="button" onclick="$('#discount-row{$discount_row}').remove();" data-toggle="tooltip" title="{translate('form_label__remove')}" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>
						</tr>
						{assign var="discount_row" value=$discount_row+1}
						{/foreach}
						{/if}
						</tbody>

						<tfoot>
							<tr>
							<td colspan="6"></td>
							<td class="text-left"><button type="button" onclick="addDiscount();" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>
							</tr>
						</tfoot>
						</table>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="special">
				<div class="panel-body">
					<div class="table-responsive">
						<table id="special" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
							<td class="text-left">{translate('form_label_customer_group')}</td>
							<td class="text-right">{translate('form_label_priority')}</td>
							<td class="text-right">{translate('form_label_price')}</td>
{*							<td class="text-right">Discount</td>*}
							<td class="text-left">{translate('form_label_date_start')}</td>
							<td class="text-left">{translate('form_label_date_end')}</td>
							<td></td>
							</tr>
						</thead>
						<tbody>
						{assign var="special_row" value=0}
						{if isset($product_specials) && $product_specials}
						{foreach from=$product_specials item=product_special}
						<tr id="special-row{$special_row}">
							<td class="text-left">
								<select name="product_special[{$special_row}][customer_group_id]" class="form-control">
									{foreach from=$customer_groups item=customer_group}
									{if $customer_group.id eq $product_special.customer_group_id}
									<option value="{$customer_group.id}" selected="selected">{$customer_group.name}</option>
									{else}
									<option value="{$customer_group.id}">{$customer_group.name}</option>
									{/if}
									{/foreach}
								</select>
							</td>
							<td class="text-right"><input type="text" name="product_special[{$special_row}][priority]" value="{$product_special.priority}" placeholder="{translate('form_label_priority')}" class="form-control" /></td>
							<td class="text-right"><input type="text" name="product_special[{$special_row}][price]" value="{$product_special.price}" placeholder="{translate('form_label_price')}" class="form-control" /></td>
							<td class="text-left" style="display: none;"><input type="text" name="product_special[{$special_row}][discount_id]" value="{$product_special.discount_id}" class="form-control"></td>
							<td class="text-left" style="width: 20%;"><div class="input-group date">
								<input type="date" name="product_special[{$special_row}][date_start]" value="{$product_special.date_start}" placeholder="{translate('form_label_date_start')}" class="form-control" /></div>
							</td>
							<td class="text-left" style="width: 20%;"><div class="input-group date">
								<input type="date" name="product_special[{$special_row}][date_end]" value="{$product_special.date_end}" placeholder="{translate('form_label_date_end')}" class="form-control" /></div>
							</td>
							<td class="text-left">
								{if !$product_special.discount_id}
									<button type="button" class="btn btn-danger disabled" disabled><i class="icon-minus-circle2"></i></button>
								{else}
									<button type="button" onclick="$('#special-row{$special_row}').remove();" data-toggle="tooltip" title="remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button>
								{/if}
							</td>
						</tr>
						{assign var="special_row" value=$special_row + 1}
						{/foreach}
						{/if}
						</tbody>
						<tfoot>
							<tr>
							<td colspan="5"></td>
							<td class="text-left"><button type="button" onclick="addSpecial();" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>
							</tr>
						</tfoot>
						</table>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="image">
				<div class="panel-body">
					<div class="row text-center">
						<button type="button" class="btn btn-success select_images"><i class="con-images2"></i> Select images</button>
						<input type="file" name="file[]" id="multi_images" multiple style="display:none">
					</div>
					<div id="image_area" class="row">
						{assign var=image_row value=0}


						{if isset($images) && !empty($images)}
							{foreach from=$images item=image key=key}
								<div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
									<div class="thumbnail">
										<div class="thumb">
											<img src="{$image->preview}" alt="{$image->name}">
											<div class="caption-overflow">
												<span>
													<a href="#" class="remove_image btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-cross2"></i></a>
												</span>
											</div>
										</div>
										<div class="caption" style="overflow: hidden;">
											<input type="hidden" name="images[{$image_row}][url]" value="{$image->path}">
											<span class="text-regular">{$image->name}</span>
										</div>
										<div class="row">
											<div class="col-xs-7"><input type="text" class="form-control" style="padding: 0 0 0 9px;"  name="images[{$image_row}][sort]" placeholder="Sort" value="{$image->sort}"></div>
											<div class="col-xs-7"><input type="text" class="form-control" style="padding: 0 0 0 9px;"  name="images[{$image_row}][alt][]" placeholder="Alt" value="{$image->alt_image}"></div>
											<div class="col-xs-5"><label><input type="radio" name="default_image"{if $image_row eq $default_image} checked="checked" {/if} value="{$image_row}" class="styled" style="margin-top: 10px;" title="Set as default">Default</label></div>
										</div>
									</div>
								</div>
								{assign var=image_row value=$image_row+1}
							{/foreach}
						{/if}
					</div>
				</div>
			</div>
		</div>
		{form_close()}
	</div>
	<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>

{* Variables *}
	<script>
		var admin_theme = "{$admin_theme}";
		var moduleUrl = "{site_url_multi($admin_url)}/product";
		var languages = $.parseJSON('{$languages|json_encode}');
		var form_labels = {
			option_value_name: "{translate('form_label_option_value_name')}",
			sort_order: "{translate('form_label_option_value_sort_order')}",
			option_value_add: "{translate('form_label_option_value_add')}",
			quantity: "{translate('form_label_quantity')}",
			price: "{translate('form_label_price')}",
			priority: "{translate('form_label_priority')}",
			button_remove: "{translate('remove',true)}",
			set_as_default: "{translate('form_label_set_as_default')}",
			sort: "{translate('form_label_sort')}",
			option_value: "{translate('form_label_option_value')}",
			quantity: "{translate('form_label_quantity')}",
			country_group_id: "{translate('form_label_country_group_id')}",
			subtract: "{translate('form_label_subtract')}",
			price: "{translate('form_label_price')}",
			weight: "{translate('form_label_weight')}",
			required: "{translate('form_label_required')}",
			relation_value: "{translate('form_label_relation_value')}",
			current_product: "{translate('form_label_relation_current')}",
			relation_product: "{translate('form_label_relation_product')}",
			attribute_value: "{translate('form_label_attribute_value')}",
			points: "{translate('form_label_points')}",
			yes: "{translate('yes',true)}",
			no: "{translate('no',true)}",
		};
	</script>
{* Variables END *}
{literal}
	<script>
		$('body').delegate('.remove_attr', 'click', function(){
			$(this).parent().parent().remove();
		});
	</script>
{/literal}

{* Add Attribute js *}
	<script>
	var custom_attribute_row = "{$custom_attribute_row}";
	function addAttribute() {
		let html = '';

		html += "<tr class='customAttribute"+custom_attribute_row+"'>";
		html += "<td colspan='2'>";
		{foreach from=$languages key=lang_key item=language}
			html += "<div class='form-group'><div class='input-group' style='width:100%'><span class='input-group-addon'><img src='/templates/administrator/new/global_assets/images/flags/{$lang_key}.png' title='{$language.name}'></span><input type='text' name='attribute["+custom_attribute_row+"][attribute_description][{$language.id}]name' value='' placeholder='Attribute name' class='form-control'></div></div>";
		{/foreach}
		html += "</td>";
		html += "<td>";
		{foreach from=$languages key=lang_key item=language}
			html += "<div class='form-group'><div class='input-group'><span class='input-group-addon'><img src='/templates/administrator/new/global_assets/images/flags/{$lang_key}.png' title='{$language.name}'></span><input type='text' name='attribute["+custom_attribute_row+"][attribute_value_description][{$language.id}]name' value='' placeholder='Attribute value name' class='form-control'></div></div>";
		{/foreach}
		html += "</td>";
		html += "<td><button type='button' class='btn btn-danger btn-block remove_attr'><i class='icon-minus-circle2'></i></button></td>";
		html += "</tr>";
		$('.attribute_area tbody').append(html);
		custom_attribute_row++;
	}
	</script>
{* Add attribute js end *}

{* Form element js *}
	<script type="text/javascript">
		$('body').delegate('.attribute_value_select', 'change', function(){
			let bu = $(this);
			let attribute_value_id = $(this).find('option:selected').val();

			if(attribute_value_id && attribute_value_id == 0)
			{
				bu.parent().parent().find('.custom').show();
			}
			else
			{
				bu.parent().parent().find('.custom').hide();
			}
		});

		var attribute_row = {$attribute_row};
		$('body').delegate('select[name="category_id[]"]', 'change', function(){
			let bu = $(this);

			let attribute_group_id = $(this).find('option:selected').data('attribute-group');
			if(attribute_group_id != 0)
			{
				if(attribute_row == 0)
				{
					$("input#attribute_group_id").val(attribute_group_id);
					get_attribute(attribute_group_id);
				}
			}
			let parent_id = $(this).children("option:selected").val();
			let selected = bu.parent().parent().next().find('.form-control').data('selected');
			if (parent_id) {
				$.ajax({
					url: '/administrator/product/category?parent_id='+ parent_id,
					dataType: 'json',
					success: function(json) {
						html = '';
						if (json && json != '')
						{
							html += '<option value="0">Select</option>';
							for (i = 0; i < json.length; i++)
							{
								html += '<option data-attribute-group="'+json[i]['attribute_group_id']+'" data-child="'+json[i]['has_child']+'" value="' + json[i]['id'] + '"';
								if (json[i]['id'] == selected)
								{
									html += ' selected="selected"';
								}

								html += '>' + json[i]['name'] + '</option>';
							}

							bu.parent().parent().next().find('.form-control').html(html);
							bu.parent().parent().next().find('.form-control').trigger('change');
							bu.parent().parent().next().show();
						}




					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		});

		$('select[name="category_id[]"]').trigger('change');

		function get_attribute(attribute_group)
		{
			if (attribute_group) {
				$.ajax({
					url: '/administrator/product/attribute?attribute_group='+ attribute_group,
					dataType: 'html',
					success: function(html) {
						$('.attribute_area tbody').html(html);
						$('.select-search').select2();
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}

	</script>
	<script>
		var item_id = "{$item_id}";
		var slugGeneratorUrl = "{site_url_multi($admin_url)}/product/slugGenerator";

		{literal}
		// Slug generator
		$(document).ready(function() {
			var slug_filed = $("input.slugField");
			if(slug_filed.length > 0){
				let slug_for = $("input.slugField:first").data('for');
				let slug_type = $("input.slugField:first").data('type');

				if(slug_type == 'translation'){
					$( "input.slugField" ).each(function(index) {
						let lang_id = $(this).data('lang-id');
						var timeout = null;
						$("input[name='translation["+lang_id+"]["+slug_for+"]']").on('keyup', function (e) {
							let text = $(this).val();
							clearTimeout(timeout);
							timeout = setTimeout(function () {
								if(text){
									$.ajax({
										type: 'post',
										url: slugGeneratorUrl,
										dataType: 'json',
										data : {lang_id:lang_id,text:text,item_id:item_id},
										//async:false,
										success: function (data) {
											if(data['success']){
												$("input[name='translation["+lang_id+"][slug]']").val(data['slug']);
											}
										}
									});
								}
							}, 300);

						});
						$("input[name='translation["+lang_id+"]["+slug_for+"]']").trigger('keyup');
					});
				}
				else if(slug_type == 'general'){
					var timeout = null;
					$("input[name='"+slug_for+"']").on('keyup', function (e) {
						let text = $(this).val();
						clearTimeout(timeout);
						timeout = setTimeout(function () {
							if(text){
								$.ajax({
									type: 'post',
									url: slugGeneratorUrl,
									dataType: 'json',
									data : {text:text,item_id:item_id},
									success: function (data) {
										if(data['success']){
											$("input[name='slug']").val(data['slug']);
										}
									}
								});
							}
						}, 300);

					});

					$("input[name='"+slug_for+"']").trigger("keyup");
				}

			}
		});

		// AJAX dropdown
		function selectItem(element) {
			let result_element = $(element).data("element");
			let id = $(element).data("value");
			if(id > 0) {
				let text = $(element).data("text");
				$("input#"+result_element).val(id);
				$('input[data-id="'+result_element+'"]').val(text);
				$("ul#"+result_element).css("display","none");
			}
			$("ul#"+result_element).css("display","none");
		}

		auto();
		function auto()
		{
			$('input[name=\'filter_name\']').autocomplete({
				'source': function(request, response) {
					var element = this.element;
					$.ajax({
						url:  moduleUrl+"/productAutocomplete?filter_name=" +  encodeURIComponent(request.term),
						dataType: 'json',
						success: function(json) {
							response($.map(json, function(item) {
								return {
									label: item.name,
									value: item.id
								}
							}));
						}
					});
				},
				'select': function(item) {
					console.log(item);
					$(this.element).val(item.label);

				}
			});
		}

		$('.dropdownSingleAjax').autocomplete({
			source: function(request, response){
				var input = this.element;
				var result_element = $(input).data('id');

				// Relation data
				let type  = $(input).data('type');
				let element  = $(input).data('element');

				if(result_element){
					$.ajax({
						type: 'post',
						url: moduleUrl+"/ajaxDropdownSearch",
						dataType: 'json',
						data : {element: element, type: type, keyword: $(input).val()},
						success: function (data) {
							let html = "";
							if(data['success']){
								console.log($("ul#"+result_element));
								$("ul#"+result_element).css("display","block");
								if(data['elements'].length > 0){
									data['elements'].forEach(function(element) {
										html += '<li onclick="selectItem(this)" data-element="'+result_element+'" data-value="'+element.id+'" data-text="'+element.value+'"><a>'+element.value+'</a></li>';
									});
								}
							}
							$("ul#"+result_element).html(html);
						}
					});

				}
			},
			minLength: 2,
			delay: 100
		});
		// AJAX dropdown end

		// AJAX multiselect
		function delSelectedItem(element) {
			result_element = $(element).data('element');
			id = $(element).data('id');

			let selected_items = $('input#'+result_element).val();
			//console.log(selected_items);
			if(selected_items != ""){
			let arr_selected_items = selected_items.split(",");
			let index = arr_selected_items.indexOf(""+id);
			if (index > -1) {
				arr_selected_items.splice(index, 1);
			}

			selected_items = arr_selected_items.join();

			}

			$('input#'+result_element).val(selected_items);
			$(element).parent().remove();
		}

		function selectMultiItem(element) {
			let result_element = $(element).data("element");
			let id = $(element).data("value");
			let text = $(element).data("text");
			let selected_items = $('input#'+result_element).val();
			let append_elements = false;
			if(id > 0) {
				if(!selected_items){
					selected_items = id;
					append_elements = true;
				} else {
					let arr_selected_items = selected_items.split(",");
					let index = arr_selected_items.indexOf(""+id);
					if(index == '-1') {
						selected_items += ","+id;
						append_elements = true;
					} else {
						$('input[data-id="'+result_element+'"]').val(null);
						$("ul#"+result_element).css("display","none");
					}
				}
				if(append_elements) {
					$('input#'+result_element).val(selected_items);
					$('input[data-id="'+result_element+'"]').val(null);
					html = '<div id="product-category'+id+'"><i class="icon-minus-circle2" data-element="'+result_element+'" data-id="'+id+'" onclick="delSelectedItem(this);"></i> '+text+'<input type="hidden" value="'+id+'"></div>';
					$("div#"+result_element).append(html);
					$("ul#"+result_element).css("display","none");
				}

			} else {
				$("ul#"+result_element).css("display","none");
			}


		}

		$('.dropdownMultiAjax').autocomplete({
			source: function(request, response){
				var input = this.element;
				var result_element = $(input).data('id');

				// Relation data
				let type  = $(input).data('type');
				let element  = $(input).data('element');

				if(result_element){
					$.ajax({
						type: 'post',
						url: moduleUrl+"/ajaxDropdownSearch",
						dataType: 'json',
						data : {element: element, type: type, keyword: $(input).val()},
						success: function (data) {
							let html = "";
							if(data['success']){
								$("ul#"+result_element).css("display","block");
								if(data['elements'].length > 0){
									data['elements'].forEach(function(element) {
										html += '<li onclick="selectMultiItem(this)" data-element="'+result_element+'" data-value="'+element.id+'" data-text="'+element.value+'"><a>'+element.value+'</a></li>';
									});
								}
							}
							$("ul#"+result_element).html(html);
						}
					});

				}
			},
			minLength: 2,
			delay: 100
		});
		// AJAX multiselect end

		{/literal}
	</script>
{* Form element js END*}

{* Add Country Group JS *}
	<script type="text/javascript">
		var country_group_row = {$country_group_row};
		function addCountryGroup() {
			html  = '<tbody id="country-group-row' + country_group_row + '">';
			html += '  <tr>';
			html += '    <td class="left"><select name="product_country_group[' + country_group_row + '][country_group_id]" class="form-control"><option value="0" >Default</option>';
			{foreach from=$country_groups item=country_group}
				html += '<option value="{$country_group->id}" >{$country_group->name}</option>';
			{/foreach}
			html += '</select></td>';
			html += '    <td class="left">';
			html += '<input type="text" class="form-control" name="product_country_group[' + country_group_row + '][percent]">';
			html += '    </td>';
			html += '    <td class="left"><button onclick="$(\'#country-group-row' + country_group_row + '\').remove();" title="Remove" class="btn btn-danger"><i class="icon-plus2"></i></button></td>';
			html += '  </tr>';
			html += '</tbody>';
			$('#country-group tfoot').before(html);
			country_group_row++;

		}
	</script>
{* Add Country Group JS END *}

{* Autocomplete *}
	<script>
		{literal}
			// Autocomplete */
			(function($) {
				$.fn.autocomplete = function(option) {
					return this.each(function() {
						var $this = $(this);
						var $dropdown = $('<ul class="dropdown-menu" />');

						this.timer = null;
						this.items = [];

						$.extend(this, option);

						$this.attr('autocomplete', 'off');

						// Focus
						$this.on('focus', function() {
							this.request();
						});

						// Blur
						$this.on('blur', function() {
							setTimeout(function(object) {
								object.hide();
							}, 200, this);
						});

						// Keydown
						$this.on('keydown', function(event) {
							switch(event.keyCode) {
								case 27: // escape
									this.hide();
									break;
								default:
									this.request();
									break;
							}
						});

						// Click
						this.click = function(event) {
							event.preventDefault();

							var value = $(event.target).parent().attr('data-value');

							if (value && this.items[value]) {
								this.select(this.items[value]);
							}
						}

						// Show
						this.show = function() {
							var pos = $this.position();

							$dropdown.css({
								position: "static",
								top: pos.top + $this.outerHeight(),
								left: pos.left
							});

							$dropdown.show();
						}

						// Hide
						this.hide = function() {
							$dropdown.hide();
						}

						// Request
						this.request = function() {
							clearTimeout(this.timer);

							this.timer = setTimeout(function(object) {
								object.source($(object).val(), $.proxy(object.response, object));
							}, 200, this);
						}

						// Response
						this.response = function(json) {
							var html = '';
							var category = {};
							var name;
							var i = 0, j = 0;

							if (json.length) {
								for (i = 0; i < json.length; i++) {
									// update element items
									this.items[json[i]['value']] = json[i];

									if (!json[i]['category']) {
										// ungrouped items
										html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
									} else {
										// grouped items
										name = json[i]['category'];
										if (!category[name]) {
											category[name] = [];
										}

										category[name].push(json[i]);
									}
								}

								for (name in category) {
									html += '<li class="dropdown-header">' + name + '</li>';

									for (j = 0; j < category[name].length; j++) {
										html += '<li data-value="' + category[name][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[name][j]['label'] + '</a></li>';
									}
								}
							}

							if (html) {
								this.show();
							} else {
								this.hide();
							}

							$dropdown.html(html);
						}

						$dropdown.on('click', '> li > a', $.proxy(this.click, this));
						$this.after($dropdown);
					});
				}
			})(window.jQuery);
		{/literal}
	</script>
{* Autocomplete END *}

{* Discount script *}
	<script>
		var discount_row = {$discount_row};
		var customer_groups = $.parseJSON('{$customer_groups|json_encode}');
		function addDiscount() {
			html  = '<tr id="discount-row' + discount_row + '">';
			html += '  <td class="text-left"><select name="product_discount[' + discount_row + '][customer_group_id]" class="form-control">';
			Object.keys(customer_groups).forEach(function(key) {
				let customer_group = customer_groups[key];
				html += '<option value="'+customer_group.id+'">'+customer_group.name+'</option>';
			});
			html += '  </select></td>';
			html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][quantity]" value="" placeholder="'+form_labels.quantity+'" class="form-control" /></td>';
			html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][priority]" value="" placeholder="'+form_labels.priority+'" class="form-control" /></td>';
			html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][price]" value="" placeholder="'+form_labels.price+'" class="form-control" /></td>';
			html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="date" name="product_discount[' + discount_row + '][date_start]" value="" class="form-control" /></div></td>';
			html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="date" name="product_discount[' + discount_row + '][date_end]" value="" class="form-control" /></div></td>';
			html += '  <td class="text-left"><button type="button" onclick="$(\'#discount-row' + discount_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>';
			html += '</tr>';

			$('#discount tbody').append(html);


			discount_row++;
		}

	</script>
{* Discount tab END*}

{* Special tab *}
	<script type="text/javascript">
	var special_row = {$special_row};
	function addSpecial(){
		html  = '<tr id="special-row' + special_row + '">';
		html += '  <td class="text-left"><select name="product_special[' + special_row + '][customer_group_id]" class="form-control">';
		{foreach from=$customer_groups item=customer_group}
			html += ' <option value="{$customer_group.id}">{$customer_group.name}</option>';
		{/foreach}
		html += '  </select></td>';
		html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][priority]" value="" placeholder="'+form_labels.priority+'" class="form-control" /></td>';
		html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][price]" value="" placeholder="'+form_labels.price+'" class="form-control" /></td>';
		html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="date" name="product_special[' + special_row + '][date_start]" value="" class="form-control" /></div></td>';
		html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="date" name="product_special[' + special_row + '][date_end]" value="" class="form-control" /></div></td>';
		html += '  <td class="text-left"><button type="button" onclick="$(\'#special-row' + special_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>';
		html += '</tr>';

		$('#special tbody').append(html);
		special_row++;
	}
	</script>
{* Speical tab END *}

{* Multi Image Upload *}


	<script>
		var image_row_number = {$image_row};
		{literal}
		let input_multi = $('#multi_images');
		$('.select_images').on('click', function() {
			$(input_multi).trigger('click');
		});

		input_multi.on('change', function () {
			for (let i = 0; i < $(this)[0].files.length; i++)
			{
				console.log('1');
				data = new FormData();
				data.append('file[]', input_multi[0].files[i]);
				$.ajax({
					url: 'administrator/filemanager/upload?directory=Product/',
					type: 'post',
					dataType: 'json',
					enctype: 'multipart/form-data',
					data: data,
					cache: false,
					contentType: false,
					processData: false,
					success: function(response) {
						if(response.success)
						{
							let image_block = '<div class="col-lg-2 col-md-4 col-sm-6 col-xs-12"><div class="thumbnail"><div class="thumb"><img src="'+response.image+'" alt="'+response.data.file_name+'"><div class="caption-overflow"><span><a href="#" class="remove_image btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-cross2"></i></a></span></div></div><div class="caption" style="overflow:hidden;"><input type="hidden" name="images['+image_row_number+'][url]" value="'+response.save+'"><span class="text-regular">'+response.data.file_name+'</span></div>';
							image_block += '<div class="row">';
							image_block += '	<div class="col-xs-7"><input type="text" class="form-control" style="padding: 0 0 0 9px;"  name="images['+image_row_number+'][sort]" placeholder="Sort" value="0"></div>';
							image_block += '	<div class="col-xs-7"><input type="text" class="form-control" style="padding: 0 0 0 9px;"  name="images['+image_row_number+'][alt][]" placeholder="Alt" value=""></div>';
							image_block += '	<div class="col-xs-5"><label><input type="radio" name="default_image" value="'+image_row_number+'" class="styled" style="margin-top: 10px;" title="Set as default">Default</label></div>';
							image_block += '</div></div></div>';
							$('#image_area').append(image_block);
							image_row_number += 1;
						}
						else
						{
							console.log(response.message);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
				}
		});

		$('body').delegate('.remove_image', 'click', function(e){
			e.preventDefault();
			$(this).parent().parent().parent().parent().parent().remove();
		});
		{/literal}
	</script>



	{literal}
	<script>
		let selector = $('.image_upload');
		let id = selector.data('id');
		let folder = selector.data('folder');
		let input = $('#input_single');
		let img = selector.find('img');
		let input_image = $('input[type="hidden"][data-id="'+id+'"]');
		selector.on('click', function() {
			input.trigger('click');
		});
		input.on('change', function () {
			data = new FormData();
			data.append('file[]', $(this)[0].files[0]);
			$.ajax({
				url: 'administrator/filemanager/upload?directory='+folder,
				type: 'post',
				dataType: 'json',
				enctype: 'multipart/form-data',
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(response) {
					if(response.success)
					{
						img.attr('src', response.image);
						input_image.val(response.save);
					}
					else
					{
						console.log(response.message);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});

		$('body').delegate('.remove_image', 'click', function(e){
			e.preventDefault();
			$(this).parent().parent().parent().parent().parent().remove();
		});
	</script>
	{/literal}

{* Multi image upload END *}


{* Option tab *}
	<script type="text/javascript">
		var option_row = {$option_row};
		{literal}
		$('input[name=\'option\']').autocomplete({
			'source': function(request, response) {
				var input_option = $('input[name=\'option\']');
				$.ajax({
					type: 'post',
					url: moduleUrl+'/optionAutocomplete',
					dataType: 'json',
					data : {filter_name: $(input_option).val()},
					success: function(json) {
						response($.map(json, function(item) {
							return {
								category: item.category,
								label: item.name,
								value: item.option_id,
								type: item.type,
								option_value: item.option_value
							}
						}));
					}
				});

			},
			'select': function(item) {
				$('input[name=\'option\']').val("");
				html  = '<div class="tab-pane" id="tab-option' + option_row + '">';
				html += '	<input type="hidden" name="product_option[' + option_row + '][product_option_id]" value="" />';
				html += '	<input type="hidden" name="product_option[' + option_row + '][name]" value="' + item['label'] + '" />';
				html += '	<input type="hidden" name="product_option[' + option_row + '][option_id]" value="' + item['value'] + '" />';
				html += '	<input type="hidden" name="product_option[' + option_row + '][type]" value="' + item['type'] + '" />';

				html += '	<div class="form-group">';
				html += '	  <label class="col-sm-2 control-label" for="input-required' + option_row + '">'+form_labels.required+'</label>';
				html += '	  <div class="col-sm-10"><select name="product_option[' + option_row + '][required]" id="input-required' + option_row + '" class="form-control">';
				html += '	      <option value="1">'+form_labels.yes+'</option>';
				html += '	      <option value="0">'+form_labels.no+'</option>';
				html += '	  </select></div>';
				html += '	</div>';

				if (item['type'] == 'select' || item['type'] == 'radio' || item['type'] == 'color' || item['type'] == 'checkbox' || item['type'] == 'image') {
					html += '<div class="table-responsive">';
					html += '  <table id="option-value' + option_row + '" class="table table-striped table-bordered table-hover">';
					html += '  	 <thead>';
					html += '      <tr>';
					html += '        <td width="20%" class="text-left">'+form_labels.option_value+'</td>';
					{/literal}
					{if is_admin()}html += '        <td width="20%" class="text-right">'+form_labels.country_group_id+'</td>';{/if}
					{literal}
					html += '        <td td width="13%" class="text-right">'+form_labels.quantity+'</td>';
					html += '        <td width="15%" class="text-left">'+form_labels.subtract+'</td>';
					html += '        <td class="text-right">'+form_labels.price+'</td>';
					html += '        <td class="text-right">'+form_labels.weight+'</td>';
					html += '        <td></td>';
					html += '      </tr>';
					html += '  	 </thead>';
					html += '  	 <tbody>';
					html += '    </tbody>';
					html += '    <tfoot>';
					html += '      <tr>';
					{/literal}
					html += '        <td colspan="{if is_admin()}6{else}5{/if}"></td>';
					{literal}
					html += '        <td class="text-left"><button type="button" onclick="addOptionValue(' + option_row + ');" data-toggle="tooltip" title="{button_option_value_add}" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>';
					html += '      </tr>';
					html += '    </tfoot>';
					html += '  </table>';
					html += '</div>';

					html += '  <select id="option-values' + option_row + '" style="display: none;">';

					for (i = 0; i < item['option_value'].length; i++) {
						html += '  <option value="' + item['option_value'][i]['option_value_id'] + '">' + item['option_value'][i]['name'] + '</option>';
					}

					html += '  </select>';
					html += '</div>';
				}

				$('#tab-option .tab-content').append(html);

				$('#option > li:last-child').before('<li><a href="#tab-option' + option_row + '" data-toggle="tab"><i class="icon-minus-circle2" onclick=" $(\'#option a:first\').tab(\'show\');$(\'a[href=\\\'#tab-option' + option_row + '\\\']\').parent().remove(); $(\'#tab-option' + option_row + '\').remove();"></i>' + item['label'] + '</li>');

				$('#option a[href=\'#tab-option' + option_row + '\']').tab('show');

				$('[data-toggle=\'tooltip\']').tooltip({
					container: 'body',
					html: true
				});

				$('.date').datepicker({
					language: '{datepicker}',
					pickTime: false
				});

				$('.time').datepicker({
					language: '{datepicker}',
					pickDate: false
				});

				$('.datetime').datepicker({
					language: '{datepicker}',
					pickDate: true,
					pickTime: true
				});

				option_row++;
			}
		});
		{/literal}

	</script>
	<script type="text/javascript">
		var option_value_row = {$option_value_row};

		function addOptionValue(option_row) {
			html  = '<tr id="option-value-row' + option_value_row + '">';
			html += '  <td class="text-left"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][option_value_id]" class="form-control">';
			html += $('#option-values' + option_row).html();
			html += '  </select><input type="hidden" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][product_option_value_id]" value="" /></td>';
			{if is_admin()}
			html += '<td class="text-right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][country_group_id]" class="form-control">';
			html += '<option value="0">Default</option>';
			{foreach from=$country_groups item=country_group}
				html += '<option value="{$country_group->id}">{$country_group->name}</option>';
			{/foreach}
			html += '</select></td>';
			{/if}
			{literal}
			html += '  <td class="text-right"><input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][quantity]" value="" placeholder="'+form_labels.quantity+'" class="form-control" /></td>';
			html += '  <td class="text-left"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][subtract]" class="form-control">';
			html += '    <option value="1">'+form_labels.yes+'</option>';
			html += '    <option value="0">'+form_labels.no+'</option>';
			html += '  </select></td>';
			html += '  <td class="text-right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][price_prefix]" class="form-control">';
			html += '    <option value="+">+</option>';
			html += '    <option value="-">-</option>';
			html += '  </select>';
			html += '  <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][price]" value="" placeholder="'+form_labels.price+'" class="form-control" /></td>';
			html += '  <td class="text-right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][weight_prefix]" class="form-control">';
			html += '    <option value="+">+</option>';
			html += '    <option value="-">-</option>';
			html += '  </select>';
			html += '  <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][weight]" value="" placeholder="'+form_labels.weight+'" class="form-control" /></td>';
			html += '  <td class="text-left"><button type="button" onclick="$(this).tooltip(\'destroy\');$(\'#option-value-row' + option_value_row + '\').remove();" data-toggle="tooltip" rel="tooltip" title="{button_remove}" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>';
			html += '</tr>';

			$('#option-value' + option_row + ' tbody').append(html);
			$('[rel=tooltip]').tooltip();

			option_value_row++;
		}
		{/literal}
	</script>
{* Option tab END *}

{* Attribute tab *}
	{* <script type="text/javascript">
		var attribute_row = {$attribute_row};
		{literal}
		$('input[name=\'attribute\']').autocomplete({
			'source': function(request, response) {
				var input_attribute = $('input[name=\'attribute\']');
				$.ajax({
					type: 'post',
					url: moduleUrl+'/attributeAutocomplete',
					dataType: 'json',
					data : {filter_name: $(input_attribute).val()},
					success: function(json) {
						if(json.length > 0) {
							response($.map(json, function(item) {
								return {
									category: "",
									label: item.name,
									value: item.id,
									attribute_value: item.attribute_value
								}
							}));
						}
					}
				});

			},
			'select': function(item) {
				console.log(item);
				html  = '<div class="tab-pane" id="tab-attribute' + attribute_row + '">';
				html += '	<input type="hidden" name="product_attribute[' + attribute_row + '][product_attribute_id]" value="" />';
				html += '	<input type="hidden" name="product_attribute[' + attribute_row + '][name]" value="' + item['label'] + '" />';
				html += '	<input type="hidden" name="product_attribute[' + attribute_row + '][attribute_id]" value="' + item['value'] + '" />';

				html += '<div class="table-responsive">';
				html += '  <table id="attribute-value' + attribute_row + '" class="table table-striped table-bordered table-hover">';
				html += '  	 <thead>';
				html += '      <tr>';
				html += '        <td class="text-left">'+form_labels.attribute_value+'</td>';
				//html += '        <td></td>';
				html += '      </tr>';
				html += '  	 </thead>';
				html += '  	 <tbody>';
				html += '    </tbody>';
				//html += '    <tfoot>';
				//html += '      <tr>';
				//html += '        <td colspan="1"></td>';
				//html += '        <td class="text-left"><button type="button" onclick="addAttributeValue(' + attribute_row + ');" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>';
				//html += '      </tr>';
				//html += '    </tfoot>';
				html += '  </table>';
				html += '</div>';

				html += '  <select id="attribute-values' + attribute_row + '" style="display: none;">';
				if(item['attribute_value'].length > 0) {
					for (i = 0; i < item['attribute_value'].length; i++) {
						html += '  <option value="' + item['attribute_value'][i]['attribute_value_id'] + '">' + item['attribute_value'][i]['name'] + '</option>';
					}
				}
				html += '  </select>';
				html += '</div>';

				$('#tab-attribute .tab-content').append(html);

				$('#attribute > li:last-child').before('<li><a href="#tab-attribute' + attribute_row + '" data-toggle="tab"><i class="icon-minus-circle2" onclick=" $(\'#attribute a:first\').tab(\'show\');$(\'a[href=\\\'#tab-attribute' + attribute_row + '\\\']\').parent().remove(); $(\'#tab-attribute' + attribute_row + '\').remove();"></i>' + item['label'] + '</li>');
				//$('#attribute > li:last-child').before('<li><a href="#tab-attribute' + attribute_row + '" data-toggle="tab"><i class="icon-minus-circle2" onclick=" $(\'#attribute a:first\').tab(\'show\');$(\'a[href=\\\'#tab-attribute' + attribute_row + '\\\']\').parent().remove(); $(\'#tab-attribute' + attribute_row + '\').remove();"></i>' + item['label'] + '</li>');

				$('#attribute a[href=\'#tab-attribute' + attribute_row + '\']').tab('show');

				$('[data-toggle=\'tooltip\']').tooltip({
					container: 'body',
					html: true
				});
				addAttributeValue(attribute_row);
				attribute_row++;
			}
		});
		{/literal}

	</script>
	<script type="text/javascript">
		var attribute_value_row = {$attribute_value_row};
		function addAttributeValue(attribute_row) {
			html  = '<tr id="attribute-value-row' + attribute_value_row + '">';
			html += '  <td class="text-left"><select name="product_attribute[' + attribute_row + '][product_attribute_value][' + attribute_value_row + '][attribute_value_id]" class="form-control">';
			html += $('#attribute-values' + attribute_row).html();
			html += '  </select><input type="hidden" name="product_attribute[' + attribute_row + '][product_attribute_value][' + attribute_value_row + '][product_attribute_value_id]" value="" /></td>';
			//html += '  <td class="text-right"><button type="button" onclick="$(this).tooltip(\'destroy\');$(\'#attribute-value-row' + attribute_value_row + '\').remove();" data-toggle="tooltip" rel="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>';
			html += '</tr>';

			$('#attribute-value' + attribute_row + ' tbody').append(html);
			$('[rel=tooltip]').tooltip();

			attribute_value_row++;
		}
	</script>  *}
{* Attribute tab END *}

{* Relation tab *}
	<script type="text/javascript">
		var relation_row = {$relation_row};
		{literal}
		$('input[name=\'relation\']').autocomplete({
			'source': function(request, response) {
				var input_relation = $('input[name=\'relation\']');
				$.ajax({
					type: 'post',
					url: moduleUrl+'/relationAutocomplete',
					dataType: 'json',
					data : {filter_name: $(input_relation).val()},
					success: function(json) {
						response($.map(json, function(item) {
							return {
								category: item.category,
								label: item.name,
								value: item.relation_id,
								type: item.type,
								relation_value: item.relation_value
							}
						}));
					}
				});

			},
			'select': function(item) {
				html  = '<div class="tab-pane" id="tab-relation' + relation_row + '">';
				html += '	<input type="hidden" name="product_relation[' + relation_row + '][product_relation_id]" value="" />';
				html += '	<input type="hidden" name="product_relation[' + relation_row + '][name]" value="' + item['label'] + '" />';
				html += '	<input type="hidden" name="product_relation[' + relation_row + '][relation_id]" value="' + item['value'] + '" />';
				html += '	<input type="hidden" name="product_relation[' + relation_row + '][type]" value="' + item['type'] + '" />';
				html += '	<div class="table-responsive">';
				html += '	  	<table id="relation-value' + relation_row + '" class="table table-striped table-bordered table-hover">';
				html += '	  	 <thead>';
				html += '	      <tr>';
				html += '	        <td class="text-left">'+form_labels.relation_value+'</td>';
				html += '	        <td class="text-left">'+form_labels.current_product+'</td>';
				html += '	        <td class="text-left">'+form_labels.relation_product+'</td>';
				html += '	        <td></td>';
				html += '	      </tr>';
				html += '	  	 </thead>';
				html += '	  	 <tbody>';
				html += '	    </tbody>';
				html += '	    <tfoot>';
				html += '	      <tr>';
				html += '	        <td colspan="3"></td>';
				html += '	        <td class="text-left"><button type="button" onclick="addRelationValue(' + relation_row + ');" data-toggle="tooltip" title="{button_relation_value_add}" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>';
				html += '	      </tr>';
				html += '	    </tfoot>';
				html += '	  </table>';
				html += '</div>';

				html += '  <select id="relation-values' + relation_row + '" style="display: none;">';

				for (i = 0; i < item['relation_value'].length; i++) {
					html += '  <option value="' + item['relation_value'][i]['relation_value_id'] + '">' + item['relation_value'][i]['name'] + '</option>';
				}

				html += '  </select>';
				html += '</div>';

				$('#tab-relation .tab-content').append(html);

				$('#relation > li:last-child').before('<li><a href="#tab-relation' + relation_row + '" data-toggle="tab"><i class="icon-minus-circle2" onclick=" $(\'#relation a:first\').tab(\'show\');$(\'a[href=\\\'#tab-relation' + relation_row + '\\\']\').parent().remove(); $(\'#tab-relation' + relation_row + '\').remove();"></i>' + item['label'] + '</li>');

				$('#relation a[href=\'#tab-relation' + relation_row + '\']').tab('show');

				$('[data-toggle=\'tooltip\']').tooltip({
					container: 'body',
					html: true
				});


				relation_row++;
			}
		});
		{/literal}
	</script>
	<script type="text/javascript">
		var relation_value_row = {$relation_value_row};
		{literal}
		function addRelationValue(relation_row) {
			html  = '<tr id="relation-value-row' + relation_value_row + '">';
			html += '  	<td class="text-left"><select name="product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][relation_value_id]" class="form-control">';
			html += 	$('#relation-values' + relation_row).html();
			html += '  	</select><input type="hidden" name="product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][product_relation_value_id]" value="" /></td>';
			html +=' 	<td><input type="radio" name="product_relation['+relation_row+'][current_product]" value="'+relation_value_row+'"/></td>';
			html += '	<td><input type="text" name="product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][product_name]" value="" placeholder="Product Name" class="form-control" /><input type="hidden" name="product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][product_id]" value="" /></td>';
			html += '  	<td class="text-left"><button type="button" onclick="$(this).tooltip(\'destroy\');$(\'#relation-value-row' + relation_value_row + '\').remove();" data-toggle="tooltip" rel="tooltip" title="{button_remove}" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>';
			html += '</tr>';

			$('#relation-value' + relation_row + ' tbody').append(html);
			$('#relation-value' + relation_row + ' tbody').find('[type="radio"]:first').click();
			$('[rel=tooltip]').tooltip();

			relationautocomplete(relation_row, relation_value_row);
			relation_row++;

			relation_value_row++;
		}


		function relationautocomplete(relation_row, relation_value_row) {
			$('input[name=\'product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][product_name]\']').autocomplete({
				'source': function(request, response) {
					var input = $('input[name=\'product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][product_name]\']');
					$.ajax({
						url:  moduleUrl+"/productAutocomplete?filter_name=" + encodeURIComponent($(input).val()),
						dataType: 'json',
						success: function(json) {
							if(json.length > 0) {
								response($.map(json, function(item) {
									return {
										label: item.name,
										value: item.id
									}
								}));
							}
						}
					});
				},
				'select': function(item) {
					$('input[name=\'product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][product_name]\']').val(item['label']);
					$('input[name=\'product_relation[' + relation_row + '][product_relation_value][' + relation_value_row + '][product_id]\']').val(item['value']);
				}
			});
		}

		$('.filter_product_name').each(function(index, element) {
			relationautocomplete($(element).data('relation-row'), $(element).data('relation-value-row'));
		});
		{/literal}
	</script>
{* Relation tab END *}

{* Vendor model autocomplete *}
	{if !is_admin() || true}
	<script>
	{literal}
	function copyProduct(id) {
		if(id > 0) {
			window.location.href = moduleUrl+"/copy/"+id;
		}
	}
	$('input[name="model"]').autocomplete({
		source: function(request, response){
			var input = this.element;

			if(request.trim()){
				$.ajax({
					type: 'post',
					url: moduleUrl+"/modelAutocomplete",
					dataType: 'json',
					data : {filter_name: request},
					success: function (data) {
						let html = "";
						if(data){
							$("ul#model").show();
							if(data.length > 0){
								data.forEach(function(element) {
									html += '<li onclick="copyProduct('+element.id+')"><a>'+element.model+'</a></li>';
								});
							} else {
								console.log('else');
								$("ul#model").hide();
							}
							$("ul#model").html(html);
						}

					}
				});

			}
		},
		minLength: 2,
		delay: 100
	});
	{/literal}
	</script>
	{/if}
{* Vendor model autocomplete END *}

{* Country Group JS *}
<script type="text/javascript">
	{literal}
	$('select[name="country_id"]').on('change', function(){
		let country_id = $(this).children("option:selected").val();
		let selected = $('select[name="region_id"]').data('selected_id');

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

					$('select[name="region_id"]').html(html);
					$('select[name="region_id"]').trigger('change');
					$('select[name="region_id"]').select2();

					$('select[name="region_id"]').parent().find('button').remove();


				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
	// $('select[name="country_id"]').trigger('change');



	{/literal}
</script>
{* Country Group JS END *}
{/block}
