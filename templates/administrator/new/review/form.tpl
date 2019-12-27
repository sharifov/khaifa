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
            {if isset($form_field.general)}<li class="active"><a href="#general" data-toggle="tab"><i class="icon-earth position-left"></i> {translate('tab_general',true)}</a></li>{/if}
        </ul> 
        <div class="tab-content">
            <div class="tab-pane active" id="general">
                <div class="panel-body">
                    {foreach from=$form_field.general key=field_key item=field_value}
                    <div class="form-group {if form_error($field_value.name)}has-error{/if}">
                        {form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'],(isset($field_value.info)) ? $field_value.info : false)}
                        <div class="col-md-10">
                            {form_element($field_value)}
                            {form_error($field_value.name)}
                        </div>
                    </div>
                    {/foreach}
                    <div class="form-group ">
                        <label for="rating" class="control-label col-md-2">{translate('form_label_rating')}</label>
                        <div class="col-md-10">
                            <input type="radio" name="rating" value="1" {if $selected_rating eq 1} checked="checked" {/if} />
                            <input type="radio" name="rating" value="2" {if $selected_rating eq 2} checked="checked" {/if} />
                            <input type="radio" name="rating" value="3" {if $selected_rating eq 3} checked="checked" {/if} />
                            <input type="radio" name="rating" value="4" {if $selected_rating eq 4} checked="checked" {/if} />
                            <input type="radio" name="rating" value="5" {if $selected_rating eq 5} checked="checked" {/if} />
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        {form_close()}
    </div>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>

    {* Variables *}
    <script>
        var admin_theme = "{$admin_theme}";
        var moduleUrl = "{site_url_multi($admin_url)}/review";
    </script>
    {* Variables END *}

    {* Form element js *}
    <script>
        {literal}
    
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
        {/literal}
    </script>
    {* Form element js END*}

{/block}    