<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$title}</title>
<base href="{base_url()}">
<link rel="icon" type="image/png" href="{$admin_theme}/assets/images/favicon.png">
<link href="{$admin_theme}/global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
<link href="{$admin_theme}/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="{$admin_theme}/assets/css/core.css" rel="stylesheet" type="text/css">
<link href="{$admin_theme}/assets/css/components.css" rel="stylesheet" type="text/css">
<link href="{$admin_theme}/assets/css/colors.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/loaders/pace.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/core/libraries/jquery.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/core/libraries/bootstrap.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/loaders/blockui.min.js"></script>

<script src="{$admin_theme}/global_assets/js/plugins/forms/styling/switchery.min.js"></script>
<script src="{$admin_theme}/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script src="{$admin_theme}/global_assets/js/plugins/ui/moment/moment.min.js"></script>
<script src="{$admin_theme}/global_assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/ui/nicescroll.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/forms/tags/tagsinput.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/forms/styling/switch.min.js"></script>
<script type="text/javascript" src="{$admin_theme}/global_assets/js/plugins/forms/tags/tokenfield.min.js"></script>


<script type="text/javascript" src="{$admin_theme}/assets/js/app.js"></script>
<script type="text/javascript" src="{$admin_theme}/assets/js/common.js"></script>
</head>
<body class="login-container">
	<div class="page-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="content">
					{form_open()}
						<div class="panel panel-body login-form">
							<div class="text-center">
								<h5 class="content-group">
									{translate('login_head')}
									<small class="display-block">{translate('login_head_message')}</small>
								</h5>
							</div>
							{if $message}
								<div class="alert alert-danger">
									{$message}
								</div>
							{/if}						
							<div class="form-group has-feedback has-feedback-left">
								{form_input('login', '', 'class="form-control input-roundless" placeholder="Login" autocomplete="username" autofocus')}
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
							</div>
							<div class="form-group has-feedback has-feedback-left">
								{form_password('password', '', 'class="form-control input-roundless" placeholder="Password" autocomplete="current-password"')}
								<div class="form-control-feedback">
									<i class="icon-lock2 text-muted"></i>
								</div>
							</div>
							<div class="form-group login-options">
								<div class="row">
									<div class="col-sm-6">
										<label class="checkbox-inline">
											{form_checkbox('remember', '1', '', ["class" => "styled"])}
											{translate('remember_me')}
										</label>
									</div>
								</div>
							</div>
							<div class="form-group">
                                <button type="submit" class="btn btn-primary input-roundless color-microsoft btn-block"><i class="icon-lock"></i> <strong>{translate('sign_in')|upper}<strong></button>
							</div>							
						</div>
					{form_close()}
					<div class="footer text-muted text-center">{$copyright}</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>