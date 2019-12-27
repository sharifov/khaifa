<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$title}</title>

<base href="{base_url()}">
<!-- Favicons-->
<link rel="icon" type="image/png" href="{$admin_assets}/images/favicon.png">

<!-- Global stylesheets -->
<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
<link href="templates/administrator/assets_global/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
<link href="{$admin_assets}/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="{$admin_assets}/css/core.css" rel="stylesheet" type="text/css">
<link href="{$admin_assets}/css/components.css" rel="stylesheet" type="text/css">
<link href="{$admin_assets}/css/colors.css" rel="stylesheet" type="text/css">
<!-- /global stylesheets -->

<!-- Core JS files -->
<script src="templates/administrator/new/global_assets/js/plugins/loaders/pace.min.js"></script>
<script src="templates/administrator/new/global_assets/js/core/libraries/jquery.min.js"></script>
<script src="templates/administrator/new/global_assets/js/core/libraries/bootstrap.min.js"></script>
<script src="templates/administrator/new/global_assets/js/plugins/loaders/blockui.min.js"></script>
<script src="templates/administrator/new/global_assets/js/plugins/ui/drilldown.js"></script>
<!-- /core JS files -->

<!-- Theme JS files -->
<script src="templates/administrator/new/global_assets/js/plugins/forms/styling/switchery.min.js"></script>
<script src="templates/administrator/new/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script src="templates/administrator/new/global_assets/js/plugins/ui/moment/moment.min.js"></script>
<script src="templates/administrator/new/global_assets/js/plugins/pickers/daterangepicker.js"></script>
<script src="templates/administrator/new/global_assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/editors/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/ui/nicescroll.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/tags/tagsinput.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/styling/switch.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/tags/tokenfield.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/ui/prism.min.js"></script>
<script type="text/javascript" src="templates/administrator/new/global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>


<script src="{$admin_assets}/js/app.js"></script>
<!-- /theme JS files -->

<!-- Common JS files -->
<script type="text/javascript" src="{$admin_assets}/js/common.js"></script>
<!-- /common JS files -->

{$scripts}
</head>

<body class="navbar-top">
<!-- Page container -->
<div class="page-container">
	<div class="page-content">
		<div class="content-wrapper">
			<div class="content">
				{block name=content}{/block}
			</div>
		</div>
	</div>
</div>
</body>
</html>