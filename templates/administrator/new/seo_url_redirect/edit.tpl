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
                    </div>
                </div>
            {/if}
        </div>
        {form_close()}
    </div>

{/block}

