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
            {if isset($form_field['translation'])}<li><a href="#translation" data-toggle="tab"><i class="icon-menu7 position-left"></i> Translation</a></li>{/if}
            {if isset($form_field['affiliate'])}<li><a href="#affiliate" data-toggle="tab"><i class="icon-menu7 position-left"></i> {translate('tab_affiliate')}</a></li>{/if}
        </ul>
        <div class="tab-content">
            {if isset($form_field.general)}
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


                        <div class="col-md-6">

                            <table id="products">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <ul id="added-products">
                                {if isset($products)}
                                {foreach $products as $product}
                                    <li>{$product.model} <a class="product-remove" data-id="{$product.id}">x</a></li>
                                {/foreach}
                                {/if}
                            </ul>
                        </div>

                        <div id="inputs" style="display: none;">
                            {if isset($products)}
                            {foreach $products as $product}
                                <input name="products[{$product.id}]" value="{$product.id}">
                            {/foreach}
                            {/if}
                        </div>


                    </div>
                </div>
            {/if}

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
        </div>
        </div>
        {form_close()}
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{literal}

    <script>

            products = $('#products');

            datatable = products.DataTable();

            $('[name="categories"]').on('change', function () {

                category_id = $(this).val();
                category_name = $("[name='categories'] option:selected").text();

                $.get('/administrator/discounts/getCategoryProducts/' + category_id)
                    .done(function (response) {

                        if( response ) {

                            datatable.clear().draw();

                            $.each(response, function (key, value) {
                                datatable.row.add([
                                    value.id, value.model, '<button class="btn btn-primary heading-btn pull-right add-product" data-id="'+ value.id +'" data-name="'+ value.model +'" onclick=""><i class="icon-add"></i></button>'
                                ]).draw();
                            });

                            $('#products_paginate').append(' <button class="btn btn-primary heading-btn pull-right add-category" data-id="'+ category_id +'" data-name="'+category_name+'"><i class="icon-add"></i> Add all products</button>');
                        }

                    });
            });


            $(document).on('click', '.add-product', function (e) {

                e.preventDefault();

                id = $(this).data('id');

                if( ! $('[name="products['+ id +']"]').length ) {

                    $('#added-products').append('<li>'+ $(this).data('name') +' <a class="product-remove" data-id="'+ id +'">x</></li>');
                    $('#inputs').append('<input name="products['+ id +']" value="'+ id +'">');
                    $(this).attr('disabled', true);

                }


            });


            $(document).on('click', '.product-remove', function (e) {
                e.preventDefault();

                id = $(this).data('id');

                $('[name="products['+ id +']"]').remove();

                $('[data-id="'+ id +'"]').attr('disabled', false);

                $(this).parent().remove();

            });

            $(document).ready(function () {


            });



    </script>
{/literal}

{/block}    