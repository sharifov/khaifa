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
        </ul> 
        <div class="tab-content">	
            {if isset($form_field['translation'])} 
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
                                                        {form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'])}
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
            {/if} 
            {if isset($form_field.general)} 
            <div class="tab-pane active" id="general">
                <div class="panel-body">
                    {foreach from=$form_field.general key=field_key item=field_value}
                        <div class="form-group {if form_error($field_value.name)}has-error{/if}">
                            {form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'])}
                            <div class="col-md-10">
                                {form_element($field_value)}
                                {form_error($field_value.name)}
                            </div>
                        </div>
                    {/foreach}
                    <fieldset>
                        <legend>{translate('form_label_tax_rate')}</legend>
                        <table id="tax-rule" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                            <td class="text-left">{translate('form_label_rate')}</td>
                            <td class="text-left">{translate('form_label_based')}</td>
                            <td class="text-left">{translate('form_label_priority')}</td>
                            <td></td>
                            </tr>
                        </thead>
                        <tbody>
                        {assign var="tax_rule_row" value=0}
                        {if $tax_rules}
                        {foreach from=$tax_rules item=tax_rule}
                        <tr id="tax-rule-row{$tax_rule_row}">
                            <td class="text-left">
                                <select name="tax_rule[{$tax_rule_row}][tax_rate_id]" class="form-control">
                                    {foreach from=$tax_rates item=tax_rate}
                                    <option value="{$tax_rate.tax_rate_id}" {if $tax_rate.tax_rate_id eq $tax_rule.tax_rate_id} selected="selected" {/if}>{$tax_rate.name}</option>
                                    {{/foreach}}
                                </select>
                            </td>
                            <td class="text-left">
                                <select name="tax_rule[{$tax_rule_row}][based]" class="form-control">
                                    <option value="shipping" {if $tax_rule.based eq 'shipping'} selected="selected" {/if}>{translate('form_label_shipping')}</option>
                                    <option value="payment" {if $tax_rule.based eq 'payment'}selected="selected"{/if}>{translate('form_label_payment')}</option>
                                    <option value="store" {if $tax_rule.based eq 'store'}selected="selected"{/if}>{translate('form_label_store')}</option>
                                </select>
                            </td>
                            <td class="text-left"><input type="text" name="tax_rule[{$tax_rule_row}][priority]" value="{$tax_rule.priority}" placeholder="{translate('form_label_priority')}" class="form-control"/></td>
                            <td class="text-left"><button type="button" onclick="$('#tax-rule-row{$tax_rule_row}').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>
                        </tr>
                        {assign var="tax_rule_row" value=$tax_rule_row + 1}
                        {/foreach}
                        {/if}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-left"><button type="button" onclick="addRule();" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>
                            </tr>
                        </tfoot>
                        </table>
                    </fieldset>
                </div>
            </div>
            {/if} 
        </div> 
        {form_close()}
    </div>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script>
        var form_labels = {};
        var admin_theme = "{$admin_theme}";
        var moduleUrl = "{site_url_multi($admin_url)}/tax_class";
        var languages = $.parseJSON('{$languages|json_encode}');
        var item_id = "item_id";
        var tax_rule_row = {$tax_rule_row};
        
        function addRule() {
            html  = '<tr id="tax-rule-row' + tax_rule_row + '">';
            html += '  <td class="text-left"><select name="tax_rule[' + tax_rule_row + '][tax_rate_id]" class="form-control">';
            {foreach from=$tax_rates item=tax_rate}
                html += '    <option value="{$tax_rate.tax_rate_id}">{$tax_rate.name}</option>';
            {/foreach}
            html += '  </select></td>';
            html += '  <td class="text-left"><select name="tax_rule[' + tax_rule_row + '][based]" class="form-control">';
            html += '    <option value="shipping">{translate("form_label_shipping")}</option>';
            html += '    <option value="payment">{translate("form_label_payment")}</option>';
            html += '    <option value="store">{translate("form_label_store")}</option>';
            html += '  </select></td>';
            html += '  <td class="text-left"><input type="text" name="tax_rule[' + tax_rule_row + '][priority]" value="" placeholder="{translate("form_label_priority")}" class="form-control" /></td>';
            html += '  <td class="text-left"><button type="button" onclick="$(\'#tax-rule-row' + tax_rule_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>';
            html += '</tr>';
            
            $('#tax-rule tbody').append(html);
            
            tax_rule_row++;
        }
    </script>
{/block}    