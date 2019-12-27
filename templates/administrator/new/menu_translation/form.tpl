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
            <li class="active"><a href="#admin_menu" data-toggle="tab"><i class="icon-earth position-left"></i> {translate('tab_admin_menu')}</a></li>
            <li><a href="#vendor_menu" data-toggle="tab"><i class="icon-earth position-left"></i> {translate('tab_vendor_menu')}</a></li>
        </ul> 
        <div class="tab-content">
            <div class="tab-pane active" id="admin_menu">
                <div class="panel-body">
                    {if $admin_menus}
                    {foreach from=$admin_menus item=$admin_menu key=menu_id}
                    <div class="col-md-6" style = "border: 1px solid">				
						<div class="tabbable nav-tabs-vertical nav-tabs-left">
							<div class="tab-content">
								<div class="tab-pane active" id="tab1">
                                    {if $admin_menu.name}
                                    {foreach from=$admin_menu.name item=menu key=language_code}
                                    <div class="form-group">	
                                        <div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language_code}.png" title="{$language_code}"></span>
                                            <input type="text" name="admin_menu[{$menu_id}][{$language_code}]" value="{$menu}" placeholder="{translate('form_placeholder_name')}" class="form-control">
                                        </div>
                                    </div>
                                    {/foreach}
                                    {/if}
								</div>
							</div>
						</div>                                       
					</div>
                    {/foreach}
                    {/if}
                </div>
            </div>
            
            <div class="tab-pane" id="vendor_menu">
                <div class="panel-body">
                    {if $vendor_menus}
                    {foreach from=$vendor_menus item=$vendor_menu key=menu_id}
                    <div class="col-md-6" style = "border: 1px solid">				
						<div class="tabbable nav-tabs-vertical nav-tabs-left" >
							<div class="tab-content">
								<div class="tab-pane active" id="tab1">
                                    {if $vendor_menu.name}
                                    {foreach from=$vendor_menu.name item=menu key=language_code}
                                    <div class="form-group">	
                                        <div class="input-group"> <span class="input-group-addon"><img src="{$admin_theme}/global_assets/images/flags/{$language_code}.png" title="{$language_code}"></span>
                                            <input type="text" name="vendor_menu[{$menu_id}][{$language_code}]" value="{$menu}" placeholder="{translate('form_placeholder_name')}" class="form-control">
                                        </div>
                                    </div>
                                    {/foreach}
                                    {/if}
								</div>
							</div>
						</div>                                       
					</div>
                    {/foreach}
                    {/if}
                </div>
            </div>

        </div> 
        {form_close()}
    </div>
{/block}    