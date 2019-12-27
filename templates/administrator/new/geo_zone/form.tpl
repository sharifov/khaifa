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
        </ul> 
        <div class="tab-content">
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
                        <legend>{translate('form_label_geo_zone')}</legend>
                        <table id="zone-to-geo-zone" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                            <td class="text-left">{translate('form_label_country')}</td>
                            <td class="text-left">{translate('form_label_zone')}</td>
                            <td></td>
                            </tr>
                        </thead>
                        <tbody>
                        
                        {assign var="zone_to_geo_zone_row" value=0}
                        {if $zone_to_geo_zones}
                        {foreach from=$zone_to_geo_zones item=zone_to_geo_zone}
                        <tr id="zone-to-geo-zone-row{$zone_to_geo_zone_row}">
                            <td class="text-left">
                                <select name="zone_to_geo_zone[{$zone_to_geo_zone_row}][country_id]" class="form-control" data-index="{$zone_to_geo_zone_row}" data-zone-id="{$zone_to_geo_zone.zone_id}" disabled="disabled">
                                {foreach from=$countries item=country}
                                {if $country.country_id == $zone_to_geo_zone.country_id}
                                <option value="{$country.country_id}" selected="selected">{$country.name}</option>
                                {else}
                                <option value="{$country.country_id}">{$country.name}</option>
                                {/if}
                                {/foreach}
                                </select>
                            </td>
                            <td class="text-left"><select name="zone_to_geo_zone[{$zone_to_geo_zone_row}][zone_id]" class="form-control" disabled="disabled">
                            </select></td>
                            <td class="text-left"><button type="button" onclick="$('#zone-to-geo-zone-row{$zone_to_geo_zone_row}').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>
                        </tr>
                        {assign var="zone_to_geo_zone_row" value=$zone_to_geo_zone_row + 1}
                        {/foreach}
                        {/if}
                        </tbody>
                        
                        <tfoot>
                            <tr>
                            <td colspan="2"></td>
                            <td class="text-left"><button type="button" id="button-geo-zone" data-toggle="tooltip" title="Add" class="btn btn-primary"><i class="icon-plus-circle2"></i></button></td>
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
        var form_labels = {
            all_zones: "{translate('form_label_all_zones')}"
        };
        var admin_theme = "{$admin_theme}";
        var moduleUrl = "{site_url_multi($admin_url)}/geo_zone";
        var languages = $.parseJSON('{$languages|json_encode}');


        var zone_to_geo_zone_row = {$zone_to_geo_zone_row};

        $('#button-geo-zone').on('click', function() {
            html  = '<tr id="zone-to-geo-zone-row' + zone_to_geo_zone_row + '">';
            html += '  <td class="text-left"><select name="zone_to_geo_zone[' + zone_to_geo_zone_row + '][country_id]" class="form-control" data-index="' + zone_to_geo_zone_row + '">';
            {foreach from=$countries item=country}
                html += " <option value='{$country.country_id}'>{$country.name}</option>";
            {/foreach}
            html += '</select></td>';
            html += '  <td class="text-left"><select name="zone_to_geo_zone[' + zone_to_geo_zone_row + '][zone_id]" class="form-control"><option value="0">'+form_labels.all_zones+'</option></select></td>';
            html += '  <td class="text-left"><button type="button" onclick="$(\'#zone-to-geo-zone-row' + zone_to_geo_zone_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus-circle2"></i></button></td>';
            html += '</tr>';
            
            $('#zone-to-geo-zone tbody').append(html);
            
            $('zone_to_geo_zone[' + zone_to_geo_zone_row + '][country_id]').trigger();
                    
            zone_to_geo_zone_row++;
        });

        $('#zone-to-geo-zone').on('change', 'select[name$=\'[country_id]\']', function() {
            var element = this;
            
            if (element.value) { 
                $.ajax({
                    url: moduleUrl+'/ajax_get_zones?country_id=' + element.value,
                    dataType: 'json',
                    beforeSend: function() {
                        $(element).prop('disabled', true);
                        $('button[form=\'form-geo-zone\']').prop('disabled', true);
                    },
                    complete: function() {
                        $(element).prop('disabled', false);
                        $('button[form=\'form-geo-zone\']').prop('disabled', false);
                    },
                    success: function(json) {
                        html = '<option value="0">'+form_labels.all_zones+'</option>';
                        
                        if (json['zone'] && json['zone'] != '') {	
                            for (i = 0; i < json['zone'].length; i++) {
                                html += '<option value="' + json['zone'][i]['zone_id'] + '"';
            
                                if (json['zone'][i]['zone_id'] == $(element).attr('data-zone-id')) {
                                    html += ' selected="selected"';
                                }
            
                                html += '>' + json['zone'][i]['name'] + '</option>';
                            }
                        }
            
                        $('select[name=\'zone_to_geo_zone[' + $(element).attr('data-index') + '][zone_id]\']').html(html);
                        
                        $('select[name=\'zone_to_geo_zone[' + $(element).attr('data-index') + '][zone_id]\']').prop('disabled', false);
                        
                        $('select[name$=\'[country_id]\']:disabled:first').trigger('change');
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        });

        $('select[name$=\'[country_id]\']:disabled:first').trigger('change');
    </script>
{/block}    