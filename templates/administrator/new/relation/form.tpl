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
                        <legend>{translate('form_label_relation_values')}</legend>
                        <table id="relation-value" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="text-left required">{translate('form_label_relation_value_name')}</td>
                                    <td class="text-right">{translate('form_label_relation_value_sort_order')}</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-right"><button type="button" onclick="addRelationValue();" data-toggle="tooltip" title="Add Relation Value" class="btn btn-primary"><i class="icon-plus2"></i></button></td>
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
    <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script>
        var form_labels = {
            relation_value_name: "{translate('form_label_relation_value_name')}",
            value: "{translate('form_label_relation_value_value')}",
            sort_order: "{translate('form_label_relation_value_sort_order')}",
            relation_value_add: "{translate('form_label_relation_value_add')}",
        };
        var admin_theme = "{$admin_theme}";
        var slugGeneratorUrl = "{site_url_multi($admin_url)}/relation/slugGenerator";
        var moduleUrl = "{site_url_multi($admin_url)}/relation";
        var languages = $.parseJSON('{$languages|json_encode}');
        var item_id = "item_id";
        // Relation data
        var relation_value = $.parseJSON('{$relation_value|json_encode}');
        // Relation data end
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
                        $("input[name='translation["+lang_id+"]["+slug_for+"]']").on('keyup', function (e) {
                            let text = $(this).val();
                            if(text){
                                $.ajax({
                                    type: 'post',
                                    url: slugGeneratorUrl,
                                    dataType: 'json',
                                    data : {lang_id:lang_id,text:text,item_id:item_id},
                                    success: function (data) {
                                        if(data['success']){
                                            $("input[name='translation["+lang_id+"][slug]']").val(data['slug']);
                                        }
                                    }
                                });
                            }
                        });
                    });
                }
                else if(slug_type == 'general'){
                    $("input[name='"+slug_for+"']").on('keyup', function (e) {
                        let text = $(this).val();
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

                    });
                }

            }
        });

        // AJAX dropdown
        function selectItem(element) {
            let result_element = $(element).data("element");
            let id = $(element).data("value");
            let text = $(element).data("text");
            $("input#"+result_element).val(id);
            $('input[data-id="'+result_element+'"]').val(text);
            $("ul#"+result_element).css("display","none");
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


        var relation_value_row = 0;
        function addRelationValue() {
            html  = '<tr id="relation-value-row' + relation_value_row + '">';
            html += '  <td class="text-left">';
            html += '    <div class="input-group">';
            Object.keys(languages).forEach(function(key) {
                let language = languages[key];
                html += '    <span class="input-group-addon"><img src="'+admin_theme+'/global_assets/images/flags/'+language.code+'.png" title="'+language.name+'" /></span><input type="text" name="relation_value[' + relation_value_row + '][relation_value_description]['+language.id+'][name]" value="" placeholder="'+form_labels.relation_value_name+'" class="form-control" />';
            });
            html += '    </div>';
            html += '  </td>';
           html += '  <td class="text-right"><input type="text" name="relation_value[' + relation_value_row + '][sort_order]" value="" placeholder="'+form_labels.sort_order+'" class="form-control" /></td>';
            html += '  <td class="text-right"><button type="button" onclick="$(\'#relation-value-row' + relation_value_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus2"></i></button></td>';
            html += '</tr>';

            $('#relation-value tbody').append(html);

            relation_value_row++;
        }

        if(relation_value) {
            Object.keys(relation_value).forEach(function(value_key) {
                let element = relation_value[value_key];
                html  = '<tr id="relation-value-row' + relation_value_row + '">';
                html += '  <td class="text-left"><input type="hidden" name="relation_value[' + relation_value_row + '][relation_value_id]" value="" />';
                html += '    <input type="hidden" name="relation_value[' + relation_value_row + '][custom_value_id]" value="'+element.relation_value_description[1].id+'" />';
                html += '    <div class="input-group">';
                Object.keys(languages).forEach(function(key) {
                    let language = languages[key];
                    let language_index  = language.id;
                    html += '    <span class="input-group-addon"><img src="'+admin_theme+'/global_assets/images/flags/'+language.code+'.png" title="English" /></span><input type="text" name="relation_value[' + relation_value_row + '][relation_value_description]['+language_index+'][name]" value="'+element.relation_value_description[language_index].name+'" placeholder="Relation Value Name" class="form-control" />';
                });
                html += '    </div>';
                html += '  </td>';
                html += '  <td class="text-right"><input type="text" name="relation_value[' + relation_value_row + '][sort_order]" value="'+element.sort_order+'" placeholder="'+form_labels.sort_order+'" class="form-control" /></td>';
                html += '  <td class="text-right"><button type="button" onclick="$(\'#relation-value-row' + relation_value_row + '\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="icon-minus2"></i></button></td>';
                html += '</tr>';

                $('#relation-value tbody').append(html);

                relation_value_row++;
            });
        }

    {/literal}
    </script>
{/block}    