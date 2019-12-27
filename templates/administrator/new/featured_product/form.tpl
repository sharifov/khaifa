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
                    <div class="form-group ">
                        <label for="date" class="control-label col-md-2">{translate('form_label_start_date')}</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-icon" id="ButtonCreationDemoButton1"><i class="icon-calendar3"></i></button>
                                </span>
                                <input type="text" name="start_date" value="{$start_date}" id="ButtonCreationDemoInput1" class="form-control" placeholder="{translate('form_label_start_date')}"  />
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label for="date" class="control-label col-md-2">{translate('form_label_expired_date')}</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-icon" id="ButtonCreationDemoButton"><i class="icon-calendar3"></i></button>
                                </span>
                                <input type="text" name="expired_date" value="{$expired_date}" id="ButtonCreationDemoInput" class="form-control" placeholder="{translate('form_label_expired_date')}"  />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            {/if} 
        </div> 
        {form_close()}
    </div>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

    <script>
        var admin_theme = "{$admin_theme}";
        var moduleUrl = "{site_url_multi($admin_url)}/featured_product";
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
        };
    </script>
    <script>
        {literal}
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
                    html = '<div id="customer_group_id'+id+'"><i class="icon-minus-circle2" data-element="'+result_element+'" data-id="'+id+'" onclick="delSelectedItem(this);"></i> '+text+'<input type="hidden" value="'+id+'"></div>';
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
{/block}    