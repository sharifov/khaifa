 		<!-- starts footer -->
		<footer class="footer">
			<div class="container footer-top">
				<div class="col-md-6 top_left">
					<div class="footer-item">
						<div class="h2">{translate('let_us_help_you', true)}</div>
						{get_menu_by_name('footer1')}
					</div>
				</div>
				<div class="col-md-6 top_right">
					<div class="footer-item">
						<div class="h2">{translate('contact_us', true)}</div>
						<ul class="footer-right">
							<li>
								<span>{translate('address', true)}</span>
								<address>
									{if $current_country_id == 15}
										{get_setting('address_az', $current_lang)}
									{else}
										{get_setting('contact_address', $current_lang)}
									{/if}

								</address>
							</li>
							<li>
								<span>{translate('email', true)}</span>
								<a href="mailto:{get_setting('email')}">{get_setting('email')}</a>
							</li>
							<li>
								<span>{translate('phone', true)}</span>
								<div class="footer-phones">
									{if $current_country_id == 15}
										{custom_parse_phone(get_setting('phone_az', $current_lang))}

									{else}
									<a href="tel: {get_setting('contact_mobile', $current_lang)}">{get_setting('contact_mobile', $current_lang)}</a>
									<a href="tel: {get_setting('contact_phone', $current_lang)}">{get_setting('contact_phone', $current_lang)}</a>
									{/if}
								</div>
							</li>
							<li>
								<span>{translate('follow_us', true)}</span>
								<ul class="m-socials">
									<li><a target="_blank" href="{get_setting('facebook')}"><i class="fa fa-facebook"></i></a></li>
									<li><a target="_blank" href="{get_setting('instagram')}"><i class="fa fa-instagram"></i></a></li>
									<li><a target="_blank" href="{get_setting('twitter')}"><i class="fa fa-twitter"></i></a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="footer-bottom">
				<p>{translate('copyright', true, date('Y'))}</p>
				<a target="_blank" href="http://www.webcoder.az/az">Developed by <span>Webcoder</span></a>
			</div>
		</footer>
		<!-- ends footer -->
	</div>
	{*<script src="{base_url('templates/mimelon/assets/js/jquery.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/bootstrap.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/icheck.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/ionrangeslider.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/jqzoom.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/card-payment.js')}"></script>
	<!-- owl carousel js -->
	<script src="{base_url('templates/mimelon/assets/js/owl.carousel.min.js')}"></script>


	<script src="{base_url('templates/mimelon/assets/js/magnific.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/bootstrap-datepicker/moment.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/bootstrap-datepicker/datepicker.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/rangeslidermin.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/jquery.validate.min.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/intlTelInput.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/star-rating.min.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/multiFileUpload.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/wow.min.js')}"></script>
	<script src="{base_url('templates/mimelon/assets/js/ion.rangeslider.min.js')}"></script>

	<!-- custom zoom plugin js -->
	<script src="{base_url('templates/mimelon/assets/js/zoom-image.js')}"></script>

	<script src="{base_url('templates/mimelon/assets/js/custom.js?v=9')}"></script>


	<script src="{base_url('templates/mimelon/assets/js/mjs.js?v=9')}"></script>*}

	<script src="{base_url('templates/mimelon/assets/js/app.min.js?v=4.2')}"></script>

	<!-- sticky sidebar -->
	<script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
<script>

	{if isset($dataLayer) && $dataLayer}
	var dataLayerElements = {json_encode($dataLayer)};
	dataLayer.push(dataLayerElements);
	{/if}


	$('.dataLayerProductClick').on('click', function () {

		var productID = $(this).data('id'),
			product	  = dataLayerElements.ecommerce.impressions[productID];

		dataLayer.push({
			"event": "productClick",
			"ecommerce": {
				"click": {
					"actionField": {
						"list": product.list
					},
					"products": [product]
				}
			}
		});

	});

	$('.add-to-card_btn').on('click', function () {
		var productID = $(this).data('id'),
			product	  = dataLayerElements.ecommerce.checkout?dataLayerElements.ecommerce.checkout.products[0]:dataLayerElements.ecommerce.detail.products[0];

		dataLayer.push({
			"event": "addToCart",
			"ecommerce": {
				"currencyCode": "USD",
				"add": {
					"products": [product]
				}
			}
		});
	});

	{literal}

	$(document).on('click','.top-shopping-cart',function(e){
		e.preventDefault();
	})
	$(document).on('click','.top-profile',function(e){
		e.preventDefault();
	})

	$("a.add_address_btn").on("click", function() {
		$('#bookEdit').modal('show');
	});

	$("button#btn_ajax_add_address").on("click", function() {

		$.ajax({
			type: 'post',
			url: '/account/ajax_add_address',
			dataType: 'json',
			data : $("form#address_form_data").serialize(),
			success: function (response)
			{
				if(response.status) {
					window.location.href = window.location.href+"?id"+response.address_id;
				} else {
					if(response.error) {
						$("form#address_form_data input").each(function(){
							$(this).next().remove();
							if($(this).attr("name") in response.error) {
								 input_name = $(this).attr("name");
								$(this).after('<div class="error-message"><span>'+response.error[input_name]+'</span></div>');
							}
						});

						$("form#address_form_data select").each(function(){
							$(this).next().remove();
							if($(this).attr("name") in response.error) {
								input_name = $(this).attr("name");
								$(this).after('<div class="error-message"><span>'+response.error[input_name]+'</span></div>');
							}
						});
					}
				}
			}
		});
	});

	if(document.querySelectorAll("#mobile-number").length> 0){
		$("#mobile-number").intlTelInput();
	}

	$('select.changeFilter').on("change", function() {
		$('form#formFilter').submit();
	});

	function loadCart() {
		$.get('/' + $('html').attr('lang') + '/cart/info', function(data, status){
			if(status == 'success') {
				$('sup.sup-shopping-cart').html(data['quantity'])
				$('.shopping-cart-element').html(data['html']);
			}
		});
	}

	function removeCart(cart_id, redirect) {

		if (redirect === undefined) {
			redirect = true;
		}

		if(cart_id > 0) {
			$.ajax({
				url: $('base').attr('href')+'cart/remove',
				type: 'post',
				data: {cart_id: cart_id},
				dataType: 'json',
				success: function(json) {
					if (json['success']) {

						dataLayer.push({
							"event": "removeFromCart",
							"ecommerce": {
								"currencyCode": "USD",
								"remove": {
									"products": [json['js']]
								}
							}
						});



						if(redirect) {
							console.log("true");
							location.reload();
						} else {
							console.log("false");
							loadCart();
						}
					}
				}
			});
		}
	}


	$('#button-cart-1').on('click', function () {
		addToCart()
	});
	$('#button-cart-2').on('click', function () {
		addToCart()
	});

	function addToCart(product_id) {

		if (product_id === undefined) {
			product_id = 0;
		}

		postdata = $('form#product input[type=\'text\'], form#product input[type=\'number\'], form#product input[type=\'hidden\'], form#product input[type=\'radio\']:checked, form#product input[type=\'checkbox\']:checked, form#product select, form#product textarea');
		if(product_id && parseInt(product_id) > 0 ) {
			product_id = parseInt(product_id);
			postdata = {product_id: product_id};
		}
		console.log(postdata);

		$.ajax({
			url: '/cart/add',
			type: 'post',
			data: postdata,
			dataType: 'json',
			beforeSend: function() {
				$('#button-cart').button('loading');
			},
			complete: function() {
				$('#button-cart').button('reset');
			},
			success: function(json) {
				$('form#product .text-danger').remove();
				$('form#product table td').removeClass('has-error');

				console.log(json);

				if (json['error']) {
					if (json['error']['option']) {
						for (i in json['error']['option']) {
							var element = $('#input-option' + i.replace('_', '-'));
							if (element.parent().hasClass('input-group')) {
								element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
							} else {
								element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
							}
						}
					}
					if (json['error']['shipping']) {
						$('#shipping_list').append('<div class="text-danger">' + json['error']['shipping'] + '</div>');
					}
					if (json['error']['recurring']) {
						$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
					}
					if(json['error']['quantity']) {
						alert(json['error']['quantity']);
					}
					// Highlight any found errors
					$('.text-danger').parent().addClass('has-error');
					
					$('html, body').animate({'scrollTop': ($('.has-error').parent().parent().position().top + 100)}, 800);
				}

				if(product_id > 0 && json['redirect']) {
					window.location.href = json['redirect'];
				}

				if (json['success']) {
					$('.header_line_height').after('<div class="alert alert-success alert-dismissible adsmss">' + json['success'] + '<button type="button" class="close cls" data-dismiss="alert" onclick = "$(this).parent().remove()">&times;</button></div>');
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
					$('html, body').animate({ scrollTop: 0 }, 'slow');
					loadCart();
				}
			}
		});
	}

	/* hide shopping notification */
	setInterval(function(){ $('.adsmss').hide(); }, 2500);

	$( document ).ready(function() {
		loadCart();
	});

{/literal}


</script>

<script type="text/javascript">

	$_lang = $('html').attr('lang');
	_msgGuaranty = [];
	_msgGuaranty['az'] = 'Zəmanət növünü seçin!';
	_msgGuaranty['ru'] = 'Пожалуйста выберите тип гарантии!';
	_msgGuaranty['en'] = 'Please select warranty!';

	var searchRequest;

	const client = algoliasearch('EZAG2DUL38', '7ba62c7c497dbeda794c9338af82f60c');
	const index = client.initIndex('products');

	$('input[name="query"]').on('keyup', function(){
		 query = $(this).val();
		 category_id = $('.navbar-main-search-category-select option:selected').val();

		 clearTimeout(searchRequest);

		$('.search-loader').show();
		$('#mainSearchResultMobile').html('');
		$('#mainSearchResultMobileFront').html('');
		$('#mainSearchResult').html('');

		if(category_id > 0) {
			searchRequest = index.search(query, {
				facets: ["categories"],
				filters: 'categories:' + parseInt(category_id)
			});
		}
		else {
			searchRequest = index.search({
				query: query,
				hitsPerPage: 10
			})
		}

		searchRequest.then(res => {

				html = '';

				json = res.hits;

				if (json && json != '')
				{
					for (i = 0; i < json.length; i++)
					{
						if(json[i]['link'] == undefined) json[i]['link'] = '/product/'+json[i]['slug'];
						html += '<li><a href="' + json[i]['link'] + '"';
						html += '>' + '<img src="'+json[i]['image']+'">' + json[i]['name'] + '</a></li>';
					}


					$('#mainSearchResult').html(html);
					$('#mainSearchResult').show();

					$('#mainSearchResultMobile').html(html);
					$('#mainSearchResultMobile').show();

					$('#mainSearchResultMobileFront').html(html);
					$('#mainSearchResultMobileFront').show();

				}

				if(html.length == 0) {

					$('#mainSearchResult').removeClass('searchResultBlock');
					$('#mainSearchResultMobile').removeClass('searchResultBlock');
					$('#mainSearchResultMobileFront').removeClass('searchResultBlock');
				}

				$('.search-loader').hide();

			});



		/*searchRequest = setTimeout(function () {
			 $.ajax({
				 url: $('base').attr('href')+'/product/search?query='+query+'&category_id='+category_id,
				 type: 'GET',
				 dataType: 'json',
				 beforeSend: function() {
					 $('#button-cart').button('loading');

				 },
				 complete: function() {
					 $('#button-cart').button('reset');
				 },
				 success: function(json)
				 {
					 html = '';
					 if (json && json != '')
					 {
						 for (i = 0; i < json.length; i++)
						 {
							 html += '<li><a href="' + json[i]['link'] + '"';
							 html += '>' + '<img src="'+json[i]['image']+'">' + json[i]['name'] + '</a></li>';
						 }


					 $('#mainSearchResult').html(html);
					 $('#mainSearchResult').show();

					 $('#mainSearchResultMobile').html(html);
					 $('#mainSearchResultMobile').show();

					 }

					 if(html.length == 0) {

						 $('#mainSearchResult').removeClass('searchResultBlock');
						 $('#mainSearchResultMobile').removeClass('searchResultBlock');
					 }

					 $('.search-loader').hide();
				 }
			 });
		 }, 700);*/
	});

/*


	$('input[name="query"]').on('keyup', function(){
		 query = $(this).val();
		 category_id = $('.navbar-main-search-category-select option:selected').val();

			$.ajax({
			url: $('base').attr('href')+'/product/search?query='+query+'&category_id='+category_id,
			type: 'GET',
			dataType: 'json',
			beforeSend: function() {
				$('#button-cart').button('loading');
			},
			complete: function() {
				$('#button-cart').button('reset');
			},
			success: function(json)
			{
				 html = '';
				if (json && json != '')
				{
					for (i = 0; i < json.length; i++)
					{
						html += '<li><a href="' + json[i]['link'] + '"';
						html += '>' + '<img src="'+json[i]['image']+'">' + json[i]['name'] + '</a></li>';
					}
					console.log(json.length);
					$('#mainSearchResultMobile').html(html);
					$('#mainSearchResultMobile').show();
				}
			}
		});
	});

*/

	$('.checkbox').click(function(){
		var valBrand = $(this).val();
		var checked = $(this).is(':checked');
        var url = window.location.href;

        if(url.search('&brand') !== -1){
            if(checked === false){
                var url = window.location.href;
                var x = url.replace('&brand%5B%5D='+valBrand,'');
                window.location.replace(x);
            }
        }



    });



</script>
{literal}
<script type="text/javascript">
	$('.apply-btn').on('click', function(){
			 coupon = $('.apply-input').val();
			$.ajax({
				type: 'post',
				url: '/coupon/index',
				dataType: 'json',
				data : {coupon:coupon},
				success: function (response)
				{
					if(response.hasOwnProperty('redirect')){
						window.location.href = response.redirect;
					}
				}
			});
		});
</script>
<script type="text/javascript">
	$('select[name="country_id"]').on('change', function(){
		 country_id = $(this).children("option:selected").val();
		 selected = $('select[name="zone_id"]').data('selected');
		var sell_str = 'sell=1&';
		if($(this).hasClass('isnot-sell')) sell_str='';
		if (country_id) {
			$.ajax({
				url: '/account/region?'+sell_str+'country_id='+ country_id,
				dataType: 'json',
				success: function(json) {
					html = '';
					if (json && json != '')
					{
						for (i = 0; i < json.length; i++)
						{
							html += '<option value="' + json[i]['id'] + '"';
							if (json[i]['id'] == selected)
							{
								html += ' selected="selected"';
							}

							html += '>' + json[i]['name'] + '</option>';
						}
					}

					$('select[name="zone_id"]').html(html);
					$('select[name="zone_id"]').trigger('change');


				}
			});
		}
	});
	$('select[name="country_id"]').trigger('change');
	$( "#paypal" ).submit();
	</script>

	<script>
		let muddet=$(".slider1").val();
		let	gelir=$(".slider2").val();
		var odenis="";
		var resmi_is=$(".working-place").val();
		var data=[];
		var pmt = "";
		var can_credit = "";
		var staj_time = $(".staj_time").val();
		var staj_sec = $(".staj_sec").val();
		var product_salary = "";
		var product_name = "";
		var kredit_verilme = "";
		var qiymet = $(".prod_price").val();
		var shipping_index = $(".shipping_data:checked").parents('.shipping-label').index()-1;
		var shipping_name = $(".shipping_show_name").eq(shipping_index).val();
		var shipping_price = $(".shipping_show_price").eq(shipping_index).val();
		var x = 1;
		var quaranty_name = "";
		var quaranty_price = 0;
		var product_count = 1;

		if (window.matchMedia('(max-width: 1024px)').matches){
			$('*').removeClass('wow');
		}

		$('.credit_btn1').click(function(){
			staj_time = $(".staj_time").val();
			staj_sec = $(".staj_sec").val();
			product_name = $(".hide_product_name").val();
			$('.credit_section1').hide();
			$('.credit_form').show();
			$('.credit_form_body').show();
		})
		$('.crdt_lft00').click(function(){
			$('.credit_section1').show();
			$('.credit_form').hide();
		})


		$('.credit_btn').click(function(){
			if($('.has-quaranty').length){
				$('.has-quaranty:first').removeClass('has-error').find('.text-danger').remove();
				if(!($('.has-quaranty :checked').length)){
					//alert(_msgGuaranty[$_lang]);
					$('.has-quaranty:first').addClass('has-error').find('input').after('<div class="text-danger">'+_msgGuaranty[$_lang]+'</div>');
					$('html, body').animate({'scrollTop': ($('.has-error').parent().parent().position().top + 100)}, 800);
				}else{
					$('#get_credit_modal').modal('toggle');
				}
			}else
				$('#get_credit_modal').modal('toggle');
		});


		if($("label").hasClass("has-quaranty")){
			$(".has-quaranty input").click(function(){
				quaranty_price = parseInt($(this).data('price'));
			})
		}

		$(".qty").change(function(){
			product_count = $(this).val();
			if(product_count<1){
				product_count = 1;
			}
		})
		$(".qty-button").click(function(){
			product_count = $(this).val();
			if(product_count<1){
				product_count = 1;
			}
		})

		function getValues(){
			muddet=$(".slider1").val();
			gelir=$(".slider2").val();

			product_count = $(".qty").val();
			if(product_count<1){
				product_count = 1;
			}

			shipping_index = $(".shipping_data:checked").parents('.shipping-label').index()-1;
			shipping_name = $(".shipping_show_name").eq(shipping_index).val();
			shipping_price = $(".shipping_show_price").eq(shipping_index).val();
			var qiymeti=(parseInt(qiymet)*parseInt(product_count))+parseInt(shipping_price)+(quaranty_price*parseInt(product_count));
			$(".mhs_price").html(qiymeti);

			data["muddet"]=muddet;
			data["gelir"]=gelir;
			pmt = PMT(22,muddet,qiymeti).toFixed(2);
			data["odenis"]=pmt;
			var can_credit = PV(22,muddet,gelir).toFixed(2);
			product_salary = parseInt(qiymet);
			kredit_verilme = (gelir*45)/100;
			if(kredit_verilme<=pmt){
				$(".credit_btn1").addClass("display_credit_btn");
				$(".credit_btn1").attr("disabled",true);
				$(".credit_btn1").removeClass("credit_btn_click");
				$(".no_salary").show();
				$(".no_work").hide();
			}
			else if(resmi_is==1 && kredit_verilme>pmt && staj_time<6 && $(".staj_sec").val()=="il"){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").attr("disabled",false);
				$(".credit_btn1").addClass("credit_btn_click");
				$(".no_salary").hide();
				$(".no_work").hide();
				$(".no_staj").show();
			}
			else if(kredit_verilme>pmt && staj_time>=6 && resmi_is==1){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").attr("disabled",false);
				$(".credit_btn1").addClass("credit_btn_click");
				$(".no_salary").hide();
				$(".no_staj").hide();
				$(".no_work").hide();
			}
			if(kredit_verilme<pmt && resmi_is=="0" || staj_time<6){
				$(".no_salary").hide();
				$(".no_work").hide();
			}
			if(can_credit>qiymet){
				$(".ayliq_odenis").html(pmt);
			}
			else{
				$(".ayliq_odenis").html(pmt);
			}
		}
		function getClickValues(){
			muddet=$(".slider1").val();
			gelir=$(".slider2").val();

			shipping_index = $(".shipping_data:checked").parents('.shipping-label').index()-1;
			shipping_name = $(".shipping_show_name").eq(shipping_index).val();
			shipping_price = $(".shipping_show_price").eq(shipping_index).val();
			var qiymeti=(parseInt(qiymet)*parseInt(product_count))+parseInt(shipping_price)+(quaranty_price*parseInt(product_count));
			$(".mhs_price").html(qiymeti);
			data["muddet"]=muddet;
			data["gelir"]=gelir;
			pmt = PMT(22,muddet,qiymeti).toFixed(2);
			data["odenis"]=pmt;
			var can_credit = PV(22,muddet,gelir).toFixed(2);
			product_salary = parseInt(qiymet);
			kredit_verilme = (gelir*45)/100;
			if(kredit_verilme<=pmt){
				$(".credit_btn1").addClass("display_credit_btn");
				$(".credit_btn1").removeClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",true);
			}
			else if(kredit_verilme>pmt && staj_time>=6 && resmi_is==1){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").addClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",false);
				$(".no_salary").hide();
			}
			if(can_credit>qiymet){
				$(".ayliq_odenis").html(pmt);
			}
			else{
				$(".ayliq_odenis").html(pmt);
			}
		}
		function sendCredit(){

		    var send_request = 0;

			let crdt_username=$("input[name='crdt-username']").val();
			let crdt_pincode=$("input[name='crdt-pincode']").val();
			let crdt_location=$("input[name='crdt-location']").val();
			let crdt_phone=$("input[name='crdt-phone']").val();
			let crdt_ser=$("input[name='crdt-ser']").val();
			let birthday=$("#birthday1").val()+"-"+$("#birthday2").val()+"-"+$("#birthday3").val();

			let crdt_phn=$("input[name='crdt-phn']").val();
			let crdt_e_mail=$("input[name='crdt-e_mail']").val();

			if(crdt_username==""){
                $("input[name='crdt-username']").addClass("err_input");
                send_request = 1;
            }else{
                $("input[name='crdt-username']").removeClass("err_input");
            }
            if(crdt_pincode==""){
                $("input[name='crdt-pincode']").addClass("err_input");
                send_request = 1;
            }else{
                $("input[name='crdt-pincode']").removeClass("err_input");
            }
            if(crdt_location==""){
                $("input[name='crdt-location']").addClass("err_input");
                send_request = 1;
            }else{
                $("input[name='crdt-location']").removeClass("err_input");
            }
            if($("#birthday1").val()=="0" || $("#birthday2").val()=="0" || $("#birthday3").val()=="0"){
                $("#birthday1").addClass("err_input");
                $("#birthday2").addClass("err_input");
                $("#birthday3").addClass("err_input");
                send_request = 1;
            }else{
                $("#birthday1").removeClass("err_input");
                $("#birthday2").removeClass("err_input");
                $("#birthday3").removeClass("err_input");
            }
            if(crdt_phone==""){
                $("input[name='crdt-phone']").addClass("err_input");
                send_request = 1;
            }else{
                $("input[name='crdt-phone']").removeClass("err_input");
            }
            if(crdt_ser==""){
                $("input[name='crdt-ser']").addClass("err_input");
                send_request = 1;
            }else{
                $("input[name='crdt-ser']").removeClass("err_input");
            }
            if(crdt_phn==""){
                $("input[name='crdt-phn']").addClass("err_input");
                send_request = 1;
            }else{
                $("input[name='crdt-phn']").removeClass("err_input");
            }
            if(crdt_e_mail==""){
                $("input[name='crdt-e_mail']").addClass("err_input");
                send_request = 1;
            }else{
                $("input[name='crdt-e_mail']").removeClass("err_input");
            }
			if(send_request == 0){
			$.ajax({
				type:"POST",
				url:"/add_credit_request",
				data:{product_name:product_name,muddet:muddet,gelir:gelir,resmi_is:resmi_is,staj_time:staj_time,
					staj_sec:staj_sec,pmt:pmt,product_salary:product_salary,shipping_name:shipping_name,shipping_price:parseInt(shipping_price),
					crdt_username:crdt_username,crdt_pincode:crdt_pincode,
					crdt_location:crdt_location,crdt_phone:crdt_phone,crdt_ser:crdt_ser,birthday:birthday,
					crdt_phn:crdt_phn,crdt_e_mail:crdt_e_mail,product_count:product_count,quaranty_price:quaranty_price},
				success:function(result){
                    $('.credit_form_body').hide();
                    $('.credit_success_body').show();
				}
			})
            }
		}

		$(".staj_sec").change(function(){
			staj_time = $(".staj_time").val();
			if(staj_time<6 && $(".staj_sec").val()=="ay"){
				$(".credit_btn1").addClass("display_credit_btn");
				$(".credit_btn1").removeClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",true);
				$(".no_salary").hide();
				$(".no_work").hide();
				$(".no_staj").show();
			}
			else if(resmi_is==1 && kredit_verilme>pmt && staj_time<6 && $(".staj_sec").val()=="il"){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").addClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",false);
				$(".no_salary").hide();
				$(".no_work").hide();
				$(".no_staj").hide();
			}
			else if(resmi_is==1 && kredit_verilme>pmt && staj_time>=6){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").addClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",false);
				$(".no_salary").hide();
				$(".no_staj").hide();
				$(".no_work").hide();
			}
			if(staj_time<6 && $(".staj_sec").val()=="il"){
				$(".no_staj").hide();
			}
		})
		$(".staj_time").keyup(function(){
			staj_time = $(".staj_time").val();
			if(staj_time==""){
				$(".credit_btn1").addClass("display_credit_btn");
				$(".credit_btn1").removeClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",true);
				$(".no_salary").hide();
				$(".no_work").hide();
				$(".no_staj").show();
			}
			if(staj_time<6 && staj_sec=="ay"){
				$(".credit_btn1").addClass("display_credit_btn");
				$(".credit_btn1").removeClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",true);
				$(".no_salary").hide();
				$(".no_work").hide();
				$(".no_staj").show();
			}
			else if(resmi_is==1 && kredit_verilme>pmt && staj_time<6 && $(".staj_sec").val()=="il"){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").addClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",false);
				$(".no_salary").hide();
				$(".no_staj").hide();
				$(".no_work").hide();
			}
			else if(resmi_is==1 && kredit_verilme>pmt && staj_time>=6){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").addClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",false);
				$(".no_salary").hide();
				$(".no_staj").hide();
				$(".no_work").hide();
			}
			if(staj_time>=6 && kredit_verilme<pmt){
				$(".no_salary").show();
				$(".no_staj").hide();
				$(".no_work").hide();
			}
		})

		$(".working-place").click(function(){
			resmi_is = $(this).val();
			if(resmi_is==0){
				$(".credit_btn1").addClass("display_credit_btn");
				$(".credit_btn1").removeClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",true);
				$(".no_work").show();
				$(".no_salary").hide();
				$(".no_staj").hide();
			}
			else if(resmi_is==1 && kredit_verilme>pmt && staj_time<6 && $(".staj_sec").val()=="il"){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").addClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",false);
				$(".no_work").hide();
				$(".no_salary").hide();
				$(".no_staj").show();
			}
			else if(resmi_is==1 && kredit_verilme>pmt && staj_time>=6){
				$(".credit_btn1").removeClass("display_credit_btn");
				$(".credit_btn1").addClass("credit_btn_click");
				$(".credit_btn1").attr("disabled",false);
				$(".no_work").hide();
				$(".no_salary").hide();
				$(".no_staj").hide();
			}
			if(resmi_is==1 && kredit_verilme<pmt){
				$(".no_salary").show();
				$(".no_work").hide();
				$(".no_staj").hide();
			}
		})

		function PMT(yearlyInterestRate, totalNumberOfMonths, loanAmount){
			var rate = yearlyInterestRate / 100 / 12;

            var denominator = Math.pow((1 + rate), totalNumberOfMonths) - 1;

            var pmt = (rate + (rate / denominator)) * loanAmount;

            return pmt;
		}
		function PV(yearlyInterestRate, totalNumberOfMonths, pmt)
        {

            var rate = yearlyInterestRate / 100 / 12;

            var denominator = Math.pow((1 + rate), totalNumberOfMonths) - 1;

            var loanAmount = pmt / (rate + (rate / denominator));

            return loanAmount/2;

        }
		function error_input(classname){
		    $("input[name='"+classname+"']").addClass("err_input");
        }
        function clear_input(){
        }
	</script>

{/literal}


{get_setting('gl_analytic_code')}
{get_setting('custom_js')}

</body>

</html>
