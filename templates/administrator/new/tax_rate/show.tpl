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
                                            <table class="table table-bordered table-framed">
                                                <tbody>
                                                    {foreach from=$form_field['translation'][{$language.id}] key=field_key item=field_value}
                                                    <tr>
                                                        <td>{$field_value.label}</td>
                                                        <td>{$field_value.value}</td>
                                                    </tr>
                                                    {/foreach}
                                                </tbody>
                                            </table>
                                            {* <fieldset class="content-group">
                                                {foreach from=$form_field['translation'][{$language.id}] key=field_key item=field_value}
                                                    <div class="form-group {if form_error($field_value.name)}has-error{/if}">
                                                        {form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'])}
                                                        <div class="col-md-10">
                                                            {form_element($field_value)}
                                                            {form_error($field_value.name)}
                                                        </div>
                                                    </div>
                                                {/foreach}																		
                                            </fieldset> *}
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
                    <table class="table table-bordered table-framed">
                        <tbody>
                            {foreach from=$form_field.general key=field_key item=field_value}
                            <tr>
                                <td>{$field_value.label}</td>
                                <td>{$field_value.value}</td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    {* {foreach from=$form_field.general key=field_key item=field_value}
                        <div class="form-group {if form_error($field_value.name)}has-error{/if}">
                            {form_label($field_value.label, $field_key, ['class' => 'control-label col-md-2'])}
                            <div class="col-md-10">
                                {form_element($field_value)}
                                {form_error($field_value.name)}
                            </div>
                        </div>
                    {/foreach} *}
                </div>
            </div>
            {/if} 
        </div> 
        {form_close()}
    </div>
{/block}