<!DOCTYPE html>
<html dir="ltr" lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>Invoice</title>
		<base href="https://new.mimelon.com/matlab936admin/" />
		<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
		<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
	</head>
	<body>
		<div class="container">
			<div style="page-break-after: always;">
				<h1 style="display: inline">{translate('invoice')} #{$order->id}</h1>
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
							<td style="width: 50%;"><b>{translate('date_added')}</b> {$order->created_at}<br />
								<b>{translate('order_id')}</b> {$order->id}<br />
								<b>{translate('payment_method')}</b> {$order->payment_method}<br />
							
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
										{if is_object($address)}
											{$address->firstname} {$address->lastname} <br>
											{if $address->company != ''}{$address->company} <br> {/if}
											{$address->address_1} <br>
											{if $address->address_2 !=''}{$address->address_2} <br> {/if}
											{$address->city} <br>
											{$address->postcode} <br>
											{get_country_name($address->country_id)} <br>
											{get_region_name($address->zone_id)} <br>
											{$address->phone} <br>
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
							
							<td class="text-right"><b>{translate('quantity')}</b></td>
							<td class="text-right"><b>{translate('price')}</b></td>
							<td class="text-right">{translate('shipping')}</td>
							<td class="text-right"><b>{translate('total')}</b></td>	
						</tr>
					</thead>
					<tbody>
						{if isset($products) && !empty($products)}
							{foreach from=$products item=product}
								<tr>
									<td>{$product->name}
										<p>
											{if $product->product_seria}
												<br/>
												&nbsp - <small><b>Product Serial Number</b>  - {$product->product_seria}</small>
											{/if}
											<br />
											&nbsp;<small>{if $product->warranty!=''} - <b>Warranty</b>: {$product->warranty} {/if}</small>
										</p>
									</td>
									
									<td class="text-right">{$product->quantity}</td>
									<td class="text-right">{currency_formatter($product->price, currency_code($product->currency_original), $order->currency_code)}</td>
									<td class="text-right">{assign var=shipping value=json_decode($product->shipping)}{assign var=sh_pr value=currency_formatter($shipping[0]->price, $shipping[0]->currency, $order->currency_code)}  {if isset($shipping[0])} {$shipping[0]->name} ({$shipping[0]->code})  {currency_formatter($shipping[0]->price, $shipping[0]->currency, $order->currency_code)}{/if}</td>
									<td class="text-right">{assign var=pr_tot value=currency_formatter($product->total, currency_code($product->currency_original), $order->currency_code)}{preg_replace("/[^0-9.]/", "", $pr_tot)+preg_replace("/[^0-9.]/", "", $sh_pr)} {$order->currency_sign}</td>	
								</tr>
							{/foreach}
							<tr>
							<td colspan="4" class="text-right"><strong>{translate('total')}</strong></td>
							<td>{floor($order->total)} {$order->currency_sign}</td>
						</tr>
						{/if}

						{if isset($totals) && !empty($totals)}
							{foreach from=$totals item=total}
							<tr>
								<td class="text-right" colspan="4"><b>{$total->name}</b></td>
								<td class="text-right">{$total->amount}</td>
							</tr>
							{/foreach}
						{/if}
						
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>