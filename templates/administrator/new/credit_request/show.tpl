{extends file=$layout}

{block name=content}

	<style>

		td{

			padding: 10px !important;

		}

	</style>



	<div class="container-fluid" id="HTMLtoPDF">

		<div class="row">

			<div class="col-md-6">

			

				<div class="panel panel-default">

					<div class="panel-heading">

						<h3 class="panel-title"><i class="fa fa-shopping-cart"></i> {translate('details')}</h3>

					</div>

					

					<table class="table">

						<tbody>

							<tr>

								<td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs"><i class="icon-calendar"></i></button></td>

								<td>{$request->created_at}</td>

							</tr>



						</tbody>

					</table>

				</div>

				<div class="panel panel-default">

					<div class="panel-heading">

						<h3 class="panel-title"><i class="fa fa-user"></i> {translate('customer_details')}</h3>

					</div>

					<table class="table">

						<tbody>

							<tr>

								<td style="width: 1%;"><button data-toggle="tooltip" title="" class="btn btn-info btn-xs"><i class="icon-user"></i></button></td>

								<td> {$request->username}</td>

							</tr>

							<tr>

								<td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs"><i class="icon-mention"></i></button></td>

								<td><a href="mailto:{$request->e_mail}}">{$request->e_mail}</a></td>

							</tr>

						</tbody>

					</table>

				</div>

			</div>

			<div class="col-md-6">

				<div class="panel panel-default">

					<div class="panel-heading">

						<h3 class="panel-title"><i class="fa fa-user"></i> {translate('address')}</h3>

					</div>

					<table class="table">

						<tbody>

							<tr><td> {$request->username} </td></tr>

							<tr><td>Personality no: {$request->personality_no}</td></tr>

							<tr><td>Personality fin no: {$request->personality_fin_no}</td></tr>

							<tr><td>Birth: {$request->birth}</td></tr>

							<tr><td>Address: {$request->address}</td></tr>

							<tr><td>Phone: {$request->phone}</td></tr>

							<tr><td>Home phone: {$request->home_phone}</td></tr>

						</tbody>

					</table>

				</div>

			</div>

		</div>

		<div class="panel panel-default">

			<div class="panel-heading">

				<h3 class="panel-title"><i class="fa fa-info-circle"></i> {translate('index_title')} (#{$request->id})</h3>

			</div>

			<div class="panel-body">

				<table class="table table-bordered">

					<thead>

						<tr>

							<td class="text-left">{translate('product_id')}</td>

							<td class="text-left">{translate('product')}</td>

							<td class="text-left">Product Salary</td>

							<td class="text-left">Loan Period</td>

{*							<td class="text-left">{translate('model')}</td>*}

							<td class="text-left">Delivery Name</td>

							<td class="text-right">Delivery Cost</td>

							<td class="text-right">Product Count</td>

							<td class="text-right">Mountly Payment</td>

							<td class="text-right">Quaranty</td>

						</tr>

					</thead>

					<tbody>



								<tr>

									<td class="text-left">{$request->id}</td>

									<td class="text-left">{$request->product_name}</td>

									<td class="text-left">{$request->product_salary}</td>

									<td class="text-left">{$request->loan_period}</td>

									<td class="text-right">{$request->delivery_type}</td>

									<td class="text-right">{$request->delivery_cost}</td>

									<td class="text-right">{$request->count}</td>

									<td class="text-right">{$request->monthly_payment}</td>

									<td class="text-right">{$request->quaranty_price}</td>

								

								</tr>



						<tr>

							<td colspan="12" class="text-right"><strong>Shipping total</strong></td>

							<td>{$combined_shipping_price} AZN </td>

						</tr>

						<tr>

							<td colspan="12" class="text-right"><strong>{translate('total')}</strong></td>

							<td> {$request->monthly_payment*$request->loan_period}</td>

						</tr>

					</tbody>

				</table>

				<br><br>

				{if $request->status=="0"}

				<div class="pull-right">

				<a href="{site_url_multi('administrator/credit_request/change_request_status/accept/')}{$request->id}" class="btn btn-success">Accept</a>

				<a href="#"  data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo" class="btn btn-danger">X</a>

				</div>

				{/if}
				
				{if $request->status=="1"}
						<h3 style="color:green;">Kredit müraciəti qəbul edilib.</h3>
				{/if}
				{if $request->status=="2"}
						<h3 style="color:red;">{$request->cancelled_reason}</h3>
				{/if}
			</div>

		</div>



	</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ləğv etmə səbəbi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form method="post" action="{site_url_multi('administrator/credit_request/change_request_status/cancel/')}{$request->id}">
      <div class="modal-body">
          <div class="form-group">
            <label for="message-text" class="col-form-label">Səbəb:</label>
            <textarea class="form-control" id="message-text" name="cancelled_reason"></textarea>
          </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Bağla</button>
        <button type="submit" class="btn btn-primary">Ləğv et</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<script type="text/javascript">

	var refundUrl = "{site_url_multi('administrator/order/refund_product')}";

{literal}

	$('.booking_ems').on('click', function(){

		if($(this).hasClass('btn-primary'))

		{

			$(this).removeClass('btn-primary');

			$(this).addClass('btn-warning');

			if($(this).data('type') == 'ems_express')

			{

				let ems_express_ids  = $('input[name="ems_express_product"]').val();

				if(ems_express_ids) {

					ems_express_arr = ems_express_ids.split(',');

					ems_express_arr.push($(this).data('id'));

					ems_express_ids = ems_express_arr.join();

				} else {

					ems_express_ids = $(this).data('id');

				}

				$('input[name="ems_express_product"]').val(ems_express_ids)

				$('.ems_express_product').removeAttr('disabled');

			}



			if($(this).data('type') == 'ems_standard')

			{

				let ems_standard_ids  = $('input[name="ems_standard_product"]').val();

				if(ems_standard_ids) {

					ems_standard_arr = ems_standard_ids.split(',');

					ems_standard_arr.push($(this).data('id'));

					ems_standard_ids = ems_standard_arr.join();

				} else {

					ems_standard_ids = $(this).data('id');

				}

				$('input[name="ems_standard_product"]').val(ems_standard_ids)

				$('.ems_standard_product').removeAttr('disabled');

			}



			if($(this).data('type') == 'ems_premium')

			{

				let ems_premium_ids  = $('input[name="ems_premium_product"]').val();

				if(ems_premium_ids) {

					ems_premium_arr = ems_premium_ids.split(',');

					ems_premium_arr.push($(this).data('id'));

					ems_premium_ids = ems_premium_arr.join();

				} else {

					ems_premium_ids = $(this).data('id');

				}

				$('input[name="ems_premium_product"]').val(ems_premium_ids)

				$('.ems_premium_product').removeAttr('disabled');

			}



			if($(this).data('type') == 'ems_economy')

			{

				let ems_economy_ids  = $('input[name="ems_economy_product"]').val();

				if(ems_economy_ids) {

					ems_economy_arr = ems_economy_ids.split(',');

					ems_economy_arr.push($(this).data('id'));

					ems_economy_ids = ems_economy_arr.join();

				} else {

					ems_economy_ids = $(this).data('id');

				}

				$('input[name="ems_economy_product"]').val(ems_economy_ids)

				$('.ems_economy_product').removeAttr('disabled');

			}

			

		}

		else if($(this).hasClass('btn-warning'))

		{

			if($(this).data('type') == 'ems_express')

			{

				let ems_express_ids  = $('input[name="ems_express_product"]').val();

				if(ems_express_ids) {

					ems_express_arr = ems_express_ids.split(',');

					var index = ems_express_arr.indexOf($(this).data('id').toString());

					if (index > -1) {

						ems_express_arr.splice(index, 1);

						

					}

					ems_express_ids = ems_express_arr.join();

				}

				$('input[name="ems_express_product"]').val(ems_express_ids)

				$('.ems_express_product').removeAttr('disabled');

			}



			if($(this).data('type') == 'ems_standard')

			{

				let ems_standard_ids  = $('input[name="ems_standard_product"]').val();

				if(ems_standard_ids) {

					ems_standard_arr = ems_standard_ids.split(',');

					var index = ems_standard_arr.indexOf($(this).data('id').toString());

					if (index > -1) {

						ems_standard_arr.splice(index, 1);

						

					}

					ems_standard_ids = ems_standard_arr.join();

				}

				$('input[name="ems_standard_product"]').val(ems_standard_ids)

				$('.ems_standard_product').removeAttr('disabled');

			}



			if($(this).data('type') == 'ems_premium')

			{

				let ems_premium_ids  = $('input[name="ems_premium_product"]').val();

				if(ems_premium_ids) {

					ems_premium_arr = ems_premium_ids.split(',');

					var index = ems_premium_arr.indexOf($(this).data('id').toString());

					if (index > -1) {

						ems_premium_arr.splice(index, 1);

						

					}

					ems_premium_ids = ems_premium_arr.join();

				}

				$('input[name="ems_premium_product"]').val(ems_premium_ids)

				$('.ems_premium_product').removeAttr('disabled');

			}



			if($(this).data('type') == 'ems_economy')

			{

				let ems_economy_ids  = $('input[name="ems_economy_product"]').val();

				if(ems_economy_ids) {

					ems_economy_arr = ems_economy_ids.split(',');

					var index = ems_economy_arr.indexOf($(this).data('id').toString());

					if (index > -1) {

						ems_economy_arr.splice(index, 1);

						

					}

					ems_economy_ids = ems_economy_arr.join();

				}

				$('input[name="ems_economy_product"]').val(ems_economy_ids)

				$('.ems_economy_product').removeAttr('disabled');

			}



			$(this).removeClass('btn-warning');

			$(this).addClass('btn-primary');

		}

	});



	$(document).on('click', '.refund-product', function () {



		if(confirm('Do you want to refund this product?')) {

			var product_id = $(this).data('id');



			$.post(refundUrl, {product_id: product_id})

					.done(function () {

						window.location.reload();

					});

		}



	});

	$(document).on('click', '.submit-refund-to-user', function () {



		if(confirm('Do you want to refund this product?')) {



			$('#refund-to-user').submit();



		}



	});

</script>

{/literal}

{/block}

