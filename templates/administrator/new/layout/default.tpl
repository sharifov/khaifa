{include file="templates/administrator/new/_partial/header.tpl"}
{include file="templates/administrator/new/_partial/navbar.tpl"}

	<!-- Page header -->
	<div class="page-header page-header-default">
		<div class="page-header-content">
			<div class="page-title">
				<h4>
					<a onclick="window.history.back();"><i class="icon-arrow-left52 position-left"></i></a>
					<span class="text-semibold">{$title}</span>
					<small class="display-block">{$subtitle}</small>
				</h4>
			</div>

			<div class="heading-elements visible-elements">
				{if isset($buttons) && is_array($buttons)}
					{foreach from=$buttons item=$button}
					<{$button.type} {if $button.type eq 'a'} href="{$button.href}" {/if} class="{$button.class}" id="{$button.id}" {if !empty($button.additional) && isset($button.additional)}{foreach from=$button.additional key=key item=value} {$key}="{$value}"{/foreach} {/if}>
						<b><i class="{$button.icon}"></i></b> {$button.text}</{$button.type}>
					{/foreach}
				{/if}
			</div>
		</div>
		<div class="breadcrumb-line"><a class="breadcrumb-elements-toggle"><i class="icon-menu-open"></i></a>
			{$breadcrumbs}
			{if isset($breadcrumb_links) && !empty($breadcrumb_links)}
				<ul class="breadcrumb-elements">
						{foreach from=$breadcrumb_links item=breadcrumb_link}
							<li><a href="{$breadcrumb_link.href}"><i class="{$breadcrumb_link.icon_class}"></i> {$breadcrumb_link.text} <span class="{$breadcrumb_link.label_class}">{$breadcrumb_link.label_value}</span></a></li>
						{/foreach}
				</ul>
			{/if}
		</div>
	</div>
	<!-- /page header -->

	

	<!-- Page container -->
	<div class="page-container">
		<!-- Page content -->
		<div class="page-content">
			<!-- Main content -->
			<div class="content-wrapper">
				{block name=content}{/block}
			</div>
			<!-- /main content -->
		</div>
		<!-- /page content -->
	</div>
	<!-- /page container -->
{include file="templates/administrator/new/_partial/footer.tpl"}