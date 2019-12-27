<!DOCTYPE html>
<html dir="ltr" lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>Invoice Credit</title>
		<base href="https://new.mimelon.com/matlab936admin/" />
		<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
		<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
	</head>
	<body>
		<div class="container">
			<div style="page-break-after: always;">
				<h1 style="display: inline">{translate('invoice')} #{$request->id}</h1>
				<img width="150" src="{base_url('templates/administrator/new/assets/img/logo-mimelon-dark.svg')}" alt="" class="pull-right" style="display: inline">
				<table class="table table-bordered">
					<thead>
						<tr>
							<td colspan="2">{translate('details')}</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 50%;">
								<address>
									<strong>www.Mimelon.com</strong><br />
									P.O.BOX: 41416,SHOP # 10, AL ZAROUNI BLDG, DEIRA, DUBAI, U.A.E.
								</address>
								<b>E-Mail</b> <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="b8cbcdc8c8d7caccf8d5d1d5ddd4d7d696dbd7d5">{get_setting('email')}</a><br />
								<b>Web Site:</b> <a href="{site_url_multi('/')}">{site_url_multi('/')}</a> <br>
								<b>Telephone</b> {get_setting('contact_phone','en')}<br />
							</td>
							<td style="width: 50%;"><b>{translate('date_added')}</b> {$request->created_at}<br />
								<b>{translate('order_id')}</b> {$request->id}<br />
							</td>
						</tr>
					</tbody>
				</table>
				<table class="table table-bordered">
					<thead>
						<tr>
							<td style="width: 50%;"><b>{translate('address')}</b></td>
						</tr>
					</thead>
					<tbody>

							<tr>
								<td>
									<address>
										{if is_object($request)}
											{$request->username} <br>
											Personality No: {$request->personality_no} <br> 
											Personality Fin No: {$request->personality_fin_no} <br> 
											Birth: {$request->birth} <br> 
											Address: {$request->address} <br> 
											Phone: {$request->phone} <br> 
											Home Phone: {$request->home_phone} <br> 
										{/if}
									</address>
								</td>
							</tr>
							
						</tbody>
				</table>
				<table class="table table-bordered">
					<thead>
						<tr>
							<td><b>{translate('product')}</b></td>
							<td class="text-left">{translate('product')}</td>
							<td class="text-left">Product Salary</td>
							<td class="text-left">Loan Period</td>
							<td class="text-left">Delivery Name</td>
							<td class="text-right">Delivery Cost</td>
							<td class="text-right">Product Count</td>
							<td class="text-right">Mountly Payment</td>
							<td class="text-right">Quaranty</td>
							<td class="text-right">Total</td>
							
						</tr>
					</thead>
					<tbody>
						{if isset($request) && !empty($request)}
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
									<td class="text-right">{$request->monthly_payment*$request->loan_period}</td>
								</tr>
							<tr>
						</tr>
						{/if}

						
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>