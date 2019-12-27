<!DOCTYPE HTML>
{* dir="rtl" *}
{assign var="dir" value=$languages[$current_lang].dir}
<html dir="{$dir}" lang="{$current_lang}">
<head>

	{literal}
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','GTM-MF9J658');</script>
	<!-- End Google Tag Manager -->

	<!-- Facebook Pixel Code -->
		<script>
			!function(f,b,e,v,n,t,s)
			{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
					n.callMethod.apply(n,arguments):n.queue.push(arguments)};
				if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
				n.queue=[];t=b.createElement(e);t.async=!0;
				t.src=v;s=b.getElementsByTagName(e)[0];
				s.parentNode.insertBefore(t,s)}(window, document,'script',
					'https://connect.facebook.net/en_US/fbevents.js');
			fbq('init', '515669279176329');
			fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=515669279176329&ev=PageView&noscript=1"
			/></noscript>
		<!-- End Facebook Pixel Code -->
	{/literal}
{foreach $languages as $key=>$value}
	{if $key!=$current_lang}
		<link rel="alternate" href="https://mimelon.com/{$key}" hreflang="{$key}" />
	{/if}
{/foreach}


	{if isset($custom_title) && $custom_title}
		<title>{$custom_title}</title>
		{else}
		<title>{$title}</title>
	{/if}


	<base href="{base_url()}">
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	{*<meta name="robots" content="index,follow">*}
	<meta content="utf-8" http-equiv="encoding">
	{if isset($meta_keywords)}
	<meta name="keywords" content="{$meta_keywords}" />
	{else}
	<meta name="keywords" content="">
	{/if}
	{if isset($meta_description) && $meta_description}
	<meta name="description" content="{$meta_description}" />
	{else}
	<meta name="description" content="{$title}">
	{/if}

	<meta name="author" content="Webcoder">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

	<link rel="icon" href="{base_url('templates/mimelon/assets/img/icons/favicon.png')}" type="image/x-icon"/>

{*	<script src="{base_url('templates/mimelon/assets/js/qazy.js?v=4')}"></script>*}
	<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@12.0.0/dist/lazyload.min.js"></script>

	{*	<link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/bootstrap.css')}">

        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/font-awesome.css')}">
        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/intlTelInput.css')}">

        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/animate.min.css')}">

        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/ion.rangeslider.css')}">
        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/bootstrap-datepicker/datepciker.css')}">
        <!-- owl carousel css -->

        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/owl.carousel.min.css')}">
        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/styles.css?v=10')}">

        <!--mstyle-->
        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/mstyle.css?v=10')}">
        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/responsive.css?v=10')}">
        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/app.css?v=17')}">
        <!-- rtl  css -->

        <link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/rtl.css')}">*}

		<link rel="stylesheet" href="{base_url('templates/mimelon/assets/css/app.min.css?v=61')}">

	<script src="https://cdn.checkout.com/js/frames.js"></script>
</head>
<body>

<script id="5d722f379abf1109624ab72d"> var script = document.createElement("script"); script.async = true; script.type = "text/javascript"; var target = "https://cdn.ppcprotect.com/tracking/va-monitor.js"; script.src = target; var elem = document.head; elem.appendChild(script); </script> <noscript><a href="https://monitor.ppcprotect.com/v1.0/pixel?accid=5d722f379abf1109624ab72d" rel="nofollow"><img src="https://monitor.ppcprotect.com/v1.0/pixel?accid=5d722f379abf1109624ab72d" alt="PpcProtect"/></a></noscript>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MF9J658"
				  height="0" width="0"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

	<div class="global-wrapper clearfix" id="global-wrapper">
		<!-- starts main nav-->
		<nav class="navbar navbar-default m-navbar navbar-pad-top navbar-first">
			<div class="m-container">
				<div class="navbar-header">
					<a class="navbar-brand" href="{site_url_multi('/')}">
						<img src="{base_url('templates/mimelon/assets/img/icons/logo-mimelon.svg')}"/>
					</a>
				</div>

				<div class="navbar-left m-navbar-left">
					<ul class="m-socials">
						<li><a target="_blank" href="{get_setting('facebook')}"><i class="fa fa-facebook"></i></a></li>
						<li><a target="_blank" href="https://api.whatsapp.com/send?phone={get_setting('whatsapp')}"><i class="fa fa-whatsapp"></i></a></li>
						<li><a target="_blank" href="{get_setting('instagram')}"><i class="fa fa-instagram"></i></a></li>
						<li><a target="_blank" href="{get_setting('twitter')}"><i class="fa fa-twitter"></i></a></li>
					</ul>
					<form class="navbar-form navbar-main-search navbar-main-search-category" role="search" action="{site_url_multi('product/search')}" method="GET">
						<div class="all-select">
							<select class="navbar-main-search-category-select" name="category_id">
								<option value="0">{translate('all_categories', true)}</option>
								{if isset($all_categories) && !empty($all_categories)}
									{foreach from=$all_categories item=all_category}
										<option value="{$all_category.id}" {if isset($smarty.get.category_id) && $smarty.get.category_id == $all_category.id} selected {/if}>{$all_category.name}</option>
									{/foreach}
								{/if}
							</select>
							<i class="fa fa-chevron-down"></i>
						</div>
						<div class="form-group relative" id="the-basics">
							<input id="all-search" value="{if isset($smarty.get.query)}{$smarty.get.query}{/if}" class="form-control all-search" name="query" type="text" placeholder="{translate('search_placeholder', true)}" autocomplete="off" />
							<img class="search-loader" src="/templates/mimelon/assets/img/icons/loading2.gif" alt="">
							<ul id="mainSearchResult">

							</ul>
						</div>
						<button type="submit" class="navbar-main-search-submit">
							<img src="{base_url('templates/mimelon/assets/img/icons/search.svg')}" alt="">
						</button>
					</form>
				</div>
				<ul class="nav navbar-nav navbar-right navbar-mob-item-left">

						{if $sell_enable}
							{if is_loggedin()}
								{if is_member('vendor')}
									<li><a href="{site_url_multi('administrator')}" class="sell-with-us">{translate('sell_with_us', true)}</a></li>
								{/if}
							{else}
								<li><a href="{site_url_multi('become_seller/login')}" class="sell-with-us">{translate('sell_with_us', true)}</a></li>
							{/if}
						{/if}
					<li>
						<ul class="lang-currency">
							{if isset($languages) && !empty($languages)}
							<li>
								<ul class="nav navbar-nav navbar-right">
									<li class="dropdown lang-dropdown">
										<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
											{$current_lang|upper}
											<i class="fa fa-chevron-down"></i>
										</a>
										<ul class="dropdown-menu dropdown__menu dropdown__lang">
											{foreach from=$languages key=language_key item=language}
											<li><a href="{$language.link}">{$language.code|upper}</a></li>
											{/foreach}
										</ul>
									</li>
								</ul>
							</li>
							{/if}
							{if isset($currencies) && !empty($currencies)}
							<li>
								<ul class="nav navbar-nav navbar-right">
									<li class="dropdown lang-dropdown">
										<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
											{$current_currency|upper}
											{if count($currencies) > 0}
												<i class="fa fa-chevron-down"></i>
											{/if}
										</a>
										{if count($currencies) > 0}
											<ul class="dropdown-menu dropdown__menu">
												{foreach from=$currencies item=currency}
													{if $currency->code != $current_currency}
														<li><a href="{site_url('home/set_currency?code=')}{$currency->code}">{$currency->code|upper}</a></li>
													{/if}
												{/foreach}
											</ul>
										{/if}
									</li>
								</ul>
							</li>
							{/if}
						</ul>
					</li>
					<li>
						<ul class="chat-profile">
							<li class="sign_in_drop_li">
								<a href="javascript:void(0)" class="top-profile"></a>
									<!--- starts before registration profile -->
								<!-- <div class="sign_in_dropdown">
									<div class="dropdown-head">
										<h4>Returning Customer</h4>
									</div>
									<div class="text-center form_btn_cover"><a href="signin.html" class="btn reviews-btn">Sign in</a></div>
									<a href="#" class="have_account">Dont have an account?</a>
									<div class="text-center"><a href="#" class="link_underline create_account">CREATE ACCOUNT</a></div>
								</div> -->
								<!-- ends before registration profile  -->

								<!-- starts after registration profile -->
								<div class="sign_in_dropdown">
									<div class="dropdown-head">
										{if $customer}
										<div class="h4">{translate('hi',true)} {$customer->firstname}</div>
										{else}
										<div class="h4">{translate('login_and_register',true)}</div>
										{/if}
									</div>
									<ul class="account-navigation">
										{if $customer}
										<li><a href="{site_url_multi('account')}">{translate('my_account',true)}</a></li>
										<li><a href="{site_url_multi('account/orders')}">{translate('my_orders',true)}</a></li>
										<li><a href="{site_url_multi('account/address_book')}">{translate('address_book',true)}</a></li>
										<li><a href="{site_url_multi('faq')}">{translate('faq',true)}</a></li>
										<li><a href="{site_url_multi('account/logout')}" class="account-sign-out">{translate('sign_out',true)}</a></li>
										{else}
										<li><a href="{site_url_multi('account/login')}">{translate('login',true)}</a></li>
										<li><a href="{site_url_multi('account/create')}">{translate('register',true)}</a></li>
										{/if}
									</ul>
								</div>
								<!-- ends after registration profile -->
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
		<!-- ends main nav-->

		<!-- starts second nav -->
		<nav class="container-fluid second-nav">
		   <div class="m-container mm-nav-container">
			<div class="col-md-9 second-menu">
				<ul class="second-nav-items">
					<li id="nav__categories" class="main-menu-li">
						<a href="{site_url_multi('/')}">{translate('home',true)}</a>
					</li>
					{if $categories}
						{foreach from=$categories item=category}
							<li class="main-menu-li">
								<a class="{$category|print_r}" href="{site_url_multi('/')}{$category.slug}/">{$category.name}</a>
								{if $category.sub_categories}
									<div class="menu-submenu-cover">
										<div class="menu-submenu-flex">
											{foreach from=$category.sub_categories item=sub_category}
												<div class="menu-submenu-cell">
													<h2 class="menu-submenu-title"><a href="{site_url_multi('/')}{$sub_category.slug}/">{$sub_category.name}</a></h2>
													{if $sub_category.sub_categories}
														<ul>
															{foreach from=$sub_category.sub_categories item=row}
																<li><a href="{site_url_multi('/')}{$row.slug}/">{$row.name} </a></li>
															{/foreach}
														</ul>
													{/if}
												</div>
											{/foreach}
										</div>
									</div>
								{/if}
							</li>
						{/foreach}
					{/if}
					<li>
						<a href="{site_url_multi('products/sale')}" class="nav-sale-btn">{translate('sale',true)}</a>
					</li>
				</ul>
			</div>
			<div class="col-md-3 second-right">
				<ul>
					{if $customer}
						<li>
							<a href="{site_url_multi('favorite')}" class="top-heart active">
								<sup class="heart-count" id="favorite_count">{$favorite_count}</sup>
							</a>
						</li>
					{/if}
					<li class="shopping-cart">
						<a href="javascript:void(0)" class="top-shopping-cart top_shop_cart active">
							<sup class="sup-shopping-cart"></sup>
						</a>
						<!-- shopping cart -->
						<div class="shopping-cart-element">
						</div>
					</li>
				</ul>
			</div>
		   </div>
		</nav>
		<!-- ends second nav-->


		<!-- starts mobile menu-->
		<nav class="container-fluid mobile-menu">
			<div class="nav-mobile-logo">
				<a href="{site_url_multi('/')}">
					<img src="{base_url('templates/mimelon/assets/img/icons/logo-mimelon.svg')}" alt="">
				</a>
			</div>
			<div class="mobile-head-details">
				<ul class="chat-profile mobile-chat-profile mobile_chat_profile">
					<li class="sign_in_drop_li">
						{* <a href="#" class="top-profile mobile-top-profile"></a> *}
						<!-- starts mobile special profile registration and after registration -->
						{* <div class="mobile-registration">
							<button class="mobile-reg-close">
								<img src="{base_url('templates/mimelon/assets/img/icons/close-icon-mobile.svg')}">
							</button>

							<!-- before registration mobile -->
							 <!-- <div class="sign_in_dropdown mobile-before-reg">
								<div class="dropdown-head mobile-head-reg">
									<div class="h4">Returning Customer</div>
								</div>
								<div class="text-center form_btn_cover"><a href="signin.html" class="btn reviews-btn">Sign in</a></div>
								<a href="#" class="have_account have_an_account">Dont have an account?</a>
								<div class="text-center"><a href="#" class="link_underline create_account mob-reg-create-account">CREATE ACCOUNT</a></div>
							</div> -->
							<!-- before registration mobile -->

							<!-- after registration mobile -->
							<div class="mobile-after-reg">
								<div class="dropdown-head mobile-head-reg">
									<h4>Hi Nuriyya!</h4>
								</div>
								<ul class="mob-profile-navpage">
									<li><a href="#">My account</a></li>
									<li><a href="#">My orders</a></li>
									<li><a href="#">Payment-methods</a></li>
									<li><a href="#">Address-book</a></li>
									<li><a href="#" class="faq_sub_mob">FAQ <i class="fa fa-chevron-right"></i></a>

										<!-- starts mobile faq sub category -->
										<div class="faq-sub-category">
											<div class="faq-sub-breadcramp">
												<ol>
													<li class="active"><a href="index.html">Home</a></li>
													<li class="active"><a href="faq.html">FAQ</a></li>
												</ol>
											</div>
											<div class="faq-sub-category-nav">
												<ul>
													<li><a href="#">Order issues</a></li>
													<li><a href="#">Delivery</a></li>
													<li><a href="#">Payments,Promos&Gift Vouchers</a></li>
													<li><a href="#">Returns & Refunds</a></li>
													<li><a href="#">Product & Stock</a></li>
													<li><a href="#">Technical</a></li>
												</ul>
											</div>
											<button class="faq-sub-close">
												<img src="{base_url('templates/mimelon/assets/img/icons/close-icon-mobile.svg')}">
											</button>
										</div>
										<!-- ends mobile faq sub category -->
									</li>
								</ul>

								<div class="mob-sign-out">Sign out</div>
							</div>
							<!-- after registration mobile -->

						</div> *}
						<!-- ends mobile special profile registration and after registration -->
					</li>
					{if $customer}
					<li>
						<a href="javascript:void(0)" class="top-heart active display0">
							<sup class="heart-count mobile">{$favorite_count}</sup>
						</a>
					</li>
					{/if}
					<li class="shopping-cart">
						<a href="javascript:void(0)" class="top-shopping-cart active">
							<sup class="sup-shopping-cart"></sup>
						</a>
						<!-- shopping cart -->
						<div class="shopping-cart-element">

						</div>
					</li>
					<!-- starts yeni elave -->
						<li class="mobile-search2">
						<button class="mobile-search2-icon">
							<img src="/templates/mimelon/assets/img/icons/search.svg" alt="">
						</button>
						<div class="mobile-search-input mobile-special-search">
								<form class="navbar-form navbar-main-search navbar-main-search-category" role="search" action="{site_url_multi('product/search')}" method="GET">
								 <div class="all-select">
									<select class="navbar-main-search-category-select">
										<option value="0">{translate('all_categories', true)}</option>
										{if isset($all_categories) && !empty($all_categories)}
											{foreach from=$all_categories item=all_category}
												<option value="{$all_category.id}">{$all_category.name}</option>
											{/foreach}
										{/if}
									</select>
									<i class="fa fa-chevron-down"></i>
								</div>
								<div class="form-group" id="the-basics" class="main-search-relative">
									<input id="mobile_all_search_main" class="form-control all-search mobile_all_search" name="query" type="text" placeholder="{translate('search_placeholder', true)}" autocomplete="off" />
									<!-- main search result -->
									<ul id="mainSearchResultMobile"></ul>
								</div>
								<button type="submit" class="navbar-main-search-submit">
									<img class="search2-mobile" src="/templates/mimelon/assets/img/icons/search2.svg" alt="">
								</button>
							</form>
						</div>
					</li>
					<!-- ends yeni elave -->
					<li>
						<div class="nav-mobile-icon">
							<img class="mm-mobile-menu-icon jew-sprite" src="/templates/mimelon/assets/img/icons/mm-mobile-menu-icon.svg" alt="">
						</div>
					</li>
				</ul>
			</div>
			<div class="mobile-menu-content">
				<div class="mobile-menu-left-top">
					<ul class="lang-currency">
						{if isset($currencies) && !empty($currencies)}
						<li>
							<ul class="nav navbar-nav navbar-right">
								<li class="dropdown lang-dropdown">
									<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
										{$current_currency}
										{if count($currencies) > 0}
											<i class="fa fa-chevron-down"></i>
										{/if}
									</a>
									{if count($currencies) > 0}
									<ul class="dropdown-menu dropdown__menu">
										{foreach from=$currencies  item=currency}
											{if $currency->code != $current_currency}
												<li><a href="{site_url('home/set_currency?code=')}{$currency->code}">{$currency->code|upper}</a></li>
											{/if}
										{/foreach}
									</ul>
									{/if}
								</li>
							</ul>
						</li>
						{/if}
						{if isset($languages) && !empty($languages)}
						<li>
							<ul class="nav navbar-nav navbar-right">
								<li class="dropdown lang-dropdown">
									<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
										{$current_lang|upper}
										<i class="fa fa-chevron-down"></i>
									</a>
									<ul class="dropdown-menu dropdown__menu dropdown__lang">
										{foreach from=$languages key=language_key item=language}
										<li><a href="{$language.link}">{$language.code|upper}</a></li>
										{/foreach}
									</ul>
								</li>
							</ul>
						</li>
						{/if}
					</ul>
				</div>
				<div class="mobile-menu-right-top">
					<button class="mobile-menu-close">
						<img src="{base_url('templates/mimelon/assets/img/icons/close-icon-mobile.svg')}">
					</button>
				</div>
				<div class="clearfix"></div>
				<div class="mobile-navigation">
					<a href="javascript:void(0)" class="mobile-nav-logo"><img src="{base_url('templates/mimelon/assets/img/icons/logo-mimelon.svg')}" alt=""></a>
					<!-- starts yeni elave -->
					<div class="mobile-inner-heart-bag">
						<div class="mobile-head-details">
							<ul class="chat-profile mobile-chat-profile">
								<li class="sign_in_drop_li">
									<a href="javascript:void(0)" class="top-profile mobile-top-profile"></a>
									<!-- starts mobile special profile registration and after registration -->
									<div class="mobile-registration">
										<button class="mobile-reg-close">
											<img src="/templates/mimelon/assets/img/icons/close-icon-mobile.svg">
										</button>

										{if $customer}
										<!-- after registration mobile -->
										<div class="mobile-after-reg">
											<div class="dropdown-head mobile-head-reg">
												<h4>{translate('hi',true)} {$customer->firstname}</h4>
											</div>
											<ul class="mob-profile-navpage">
											<li><a href="{site_url_multi('account')}">{translate('my_account',true)}</a></li>
											<li><a href="{site_url_multi('account/orders')}">{translate('my_orders',true)}</a></li>
											<li><a href="{site_url_multi('account/address_book')}">{translate('address_book',true)}</a></li>
											<li><a href="{site_url_multi('faq')}" class="faq_sub_mob">{translate('faq',true)} <i class="fa fa-chevron-right"></i></a>

													<!-- starts mobile faq sub category -->
													<div class="faq-sub-category">
														<div class="faq-sub-breadcramp">
															<ol>
																<li class="active"><a href="/">{translate('home',true)} </a></li>
																<li class="active"><a href="{site_url_multi('faq')}">{translate('faq',true)}</a></li>
															</ol>
														</div>
														<div class="faq-sub-category-nav">
															<ul>
																{if $faqs_for_user}
																	{foreach from=$faqs_for_user item=faq_for_user}
																		<li><a href="{site_url_multi('faq/category/')}{$faq_for_user->slug}">{$faq_for_user->name}</a></li>
																	{/foreach}
																{/if}
															</ul>
														</div>
														<button class="faq-sub-close">
															<img src="/templates/mimelon/assets/img/icons/close-icon-mobile.svg">
														</button>
													</div>
													<!-- ends mobile faq sub category -->
												</li>
											</ul>
											<div class="mob-sign-out"><a href="{site_url_multi('account/logout')}">{translate('sign_out', true)}</a></div>
										</div>

										<!-- after registration mobile -->
										{else}
										<!-- before registration mobile -->
										  <div class="sign_in_dropdown mobile-before-reg">
											<div class="dropdown-head mobile-head-reg">
												<h4>{translate('returning_customer', true)}</h4>
											</div>
											<div class="text-center form_btn_cover"><a href="{site_url_multi('account/login')}" class="btn reviews-btn">{translate('login',true)}</a></div>
											<a href="{site_url_multi('account/create')}" class="have_account have_an_account">{translate('dont_have_an_account', true)}</a>
											<div class="text-center"><a href="{site_url_multi('account/create')}" class="link_underline create_account mob-reg-create-account">{translate('register',true)}</a></div>
										</div>
										<!-- before registration mobile -->
										{/if}
									</div>
									<!-- ends mobile special profile registration and after registration -->
								</li>
								{if $customer}
								<li>
									<a href="{site_url_multi('favorite')}" class="top-heart active">
										<sup class="heart-count" id="favorite_count">{$favorite_count}</sup>
									</a>
								</li>
								{/if}
								<li class="shopping-cart">
									<a href="javascript:void(0)" class="top-shopping-cart active">
										<sup class="sup-shopping-cart"></sup>
									</a>
									<!-- shopping cart -->
									<div class="shopping-cart-element">

									</div>
								</li>
							</ul>
						</div>
					</div>
					<!-- ends yeni elave -->

					<form class="navbar-form navbar-main-search navbar-main-search-category" role="search" method="GET" action="{site_url_multi('product/search')}">
						<div class="form-group">
							<input class="form-control all-search" name="query" type="text" placeholder="{translate('search_placeholder', true)}" />
							<ul id="mainSearchResultMobileFront"></ul>
						</div>
						<button type="submit" class="navbar-main-search-submit">
							<img src="{base_url('templates/mimelon/assets/img/icons/search.svg')}">
						</button>
					</form>
					<ul class="m-socials mobile-menu-socials">
						<li><a target="_blank" href="{get_setting('facebook')}"><i class="fa fa-facebook"></i></a></li>
						<li><a target="_blank" href="https://api.whatsapp.com/send?phone={get_setting('whatsapp')}"><i class="fa fa-whatsapp"></i></a></li>
						<li><a target="_blank" href="{get_setting('instagram')}"><i class="fa fa-instagram"></i></a></li>
						<li><a target="_blank" href="{get_setting('twitter')}"><i class="fa fa-twitter"></i></a></li>
					</ul>
					{if $sell_enable}
						<div>
							{if is_loggedin()}
								{if is_member('vendor')}
									<a href="{site_url_multi('administrator')}" class="sell-with-us-mobile">{translate('sell_with_us', true)}</a>
								{/if}
							{else}
								<a href="{site_url_multi('become_seller/login')}" class="sell-with-us-mobile">{translate('sell_with_us', true)}</a>
							{/if}
						</div>
					{/if}
					<div>
						<a href="{site_url_multi('products/sale')}" class="sale-mobile">{translate('sale',true)} <i class="fa fa-chevron-right all-category-chevron"></i></a>
						<!-- starts mobile sub categories -->
						<ul class="mobile-menu-list">
							<li class="mobile-all-categories">
									{translate('all_categories', true)}
									<i class="fa fa-chevron-down"></i>
							</li>
							{if $categories}
							{foreach from=$categories item=category}
			  				<li>
							 {* <a href="{site_url_multi('category/')}{$category.slug}">{$category.name} {if $category.sub_categories}<i class="fa fa-chevron-right all-category-chevron"></i>{/if}</a> *}
							 <a href="{if $category.sub_categories}javascript:void(0);{else}{site_url_multi('/')}{$category.slug}{/if}">{$category.name} {if $category.sub_categories}<i class="fa fa-chevron-right all-category-chevron"></i>{/if}</a>



							 {if $category.sub_categories}
							 	<!-- starts mobile sub category -->
								<div class="mobile_sub_categories">
									<div class="faq-sub-breadcramp">
										<ol>
											<li class="active"><a data-href="{site_url_multi('/')}{$category.slug}" href="javascript:void(0);">{$category.name}</a></li>
										</ol>
									</div>
									{* all-categories-close close-icon-mobile-category *}
									<button class="mobile-menu-close mobile_menu_close">
										<img src="templates/mimelon/assets/img/icons/close-icon-mobile.svg" alt="">
									</button>
									<div class="all-sub-categories-list">
										<ul class="sub-sub-mobile">

											{foreach from=$category.sub_categories item=sub_category}
											<li>
												<a href="{if $sub_category.sub_categories}javascript:void(0);{else}{site_url_multi('/')}{$sub_category.slug}{/if}">{$sub_category.name} {if $sub_category.sub_categories}<i class="fa fa-chevron-right all-category-chevron"></i>{/if}</a>

												{if $sub_category.sub_categories}
													<div class="sub-sub-content">
														<div class="faq-sub-breadcramp">
															<ol>
																<li class="active"><a data-href="{site_url_multi('/')}{$category.slug}" href="javascript:void(0);">{$category.name}</a></li>
																<li><a data-href="{site_url_multi('/')}{$sub_category.slug}" href="javascript:void(0);">{$sub_category.name}</a></li>
															</ol>
														</div>
														<button class="mobile-menu-close mobile_menu_close">
															<img src="templates/mimelon/assets/img/icons/close-icon-mobile.svg" alt="">
														</button>
														<ul class="sub-sub-list">
															{foreach from=$sub_category.sub_categories item=row}
																<li><a href="{site_url_multi('/')}{$row.slug}">{$row.name} </a></li>
															{/foreach}
														</ul>
													</div>
												{/if}
											</li>
											{/foreach}
											<li>
												<a href="{site_url_multi('account/logout')}" class="account-sign-out">{translate('sign_out', true)}</a>
											</li>
										</ul>
									</div>
								</div>
								<!-- ends mobile sub category -->
							{/if}
							{* {site_url_multi('category/')}{$row.slug} *}

							 </li>
							{/foreach}
							{/if}
						</ul>
						<!-- ends mobile sub categories -->
					</div>
				</div>
			</div>
		</nav>
		<!-- ends mobile menu -->

		<!-- fixed top line -->
		<div class="line-height header_line_height"></div>
