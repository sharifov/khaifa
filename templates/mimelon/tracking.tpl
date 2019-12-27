{extends file=$layout}
{block name=content}
    <section class="container-fluid m-container faq-cover min_height">
			{include file="templates/mimelon/_partial/account_sidebar.tpl"}
			<div class="col-md-7 col-sm-12 col-xs-12 margin-auto small-all-orders all__tracks">


				<div class="all-orders-content traking_all_orders">
					<h1 class="txt-bold26 text-center">{translate('tracking')}</h1>

					<div class="trc-cell">
						<ul class="trc_details_list">
							<li>Track No: <span># {$code} </span></li>
							<li>{translate('order_no')} :  <span># {$track->order}</span></li>
							<li>{translate('weight')} :  <span> {$track->weight / 1000} {translate('kg')}</span></li>

							{if isset($transit) && $transit == true}

							<li>{translate('on_the_way')} : <span>{$time} {translate('days')}</span></li>
						</ul>
						<div class="trc_details_info">{translate('delivered')} : <span>{$start_date} - {$end_date}</span></div>

						{/if}
					</div>


					<div class="trc-tbl-section">
						<h2 class="trc-tbl-title">Status & Location</h2>
						<div class="trc-tbl-items">

                            {if $status['order_status_id'] == 1}
								<div class="trc-tbl-item active">
									<div class="trc_items trc_item_box">
										<div class="trc_item trc-box"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Preparing to Ship</p>
								</div>

								<div class="trc-tbl-item">
									<div class="trc_items trc_item_car">
										<div class="trc_item trc-car"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Out For Delivery</p>
								</div>

								<div class="trc-tbl-item">
									<div class="trc_items trc_item_check">
										<div class="trc_item trc-check"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Delivered</p>
								</div>
                            {elseif $status['order_status_id'] == 2}
								<div class="trc-tbl-item active">
									<div class="trc_items trc_item_box">
										<div class="trc_item trc-box"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Preparing to Ship</p>
								</div>
								<div class="trc-tbl-item active">
									<div class="trc_items trc_item_car">
										<div class="trc_item trc-car"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Out For Delivery</p>
								</div>
								<div class="trc-tbl-item">
									<div class="trc_items trc_item_check">
										<div class="trc_item trc-check"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Delivered</p>
								</div>
                            {elseif  $status['order_status_id'] == 4 or $status['order_status_id'] == 13}
								<div class="trc-tbl-item active">
									<div class="trc_items trc_item_box">
										<div class="trc_item trc-box"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Preparing to Ship</p>
								</div>
								<div class="trc-tbl-item active">
									<div class="trc_items trc_item_car">
										<div class="trc_item trc-car"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Out For Delivery</p>
								</div>
								<div class="trc-tbl-item active">
									<div class="trc_items trc_item_check">
										<div class="trc_item trc-check"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Delivered</p>
								</div>
                            {else}
								<div class="trc-tbl-item">
									<div class="trc_items trc_item_box">
										<div class="trc_item trc-box"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Preparing to Ship</p>
								</div>
								<div class="trc-tbl-item">
									<div class="trc_items trc_item_car">
										<div class="trc_item trc-car"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Out For Delivery</p>
								</div>
								<div class="trc-tbl-item ">
									<div class="trc_items trc_item_check">
										<div class="trc_item trc-check"></div>
									</div>
									<div class="trc-tbl"></div>
									<p class="trcit">Delivered</p>
								</div>

                            {/if}

                            {*<div class="trc-tbl-item">*}
                            {*<div class="trc_items trc_item_car">*}
                            {*<div class="trc_item trc-car"></div>*}
                            {*</div>*}
                            {*<div class="trc-tbl"></div>*}
                            {*<p class="trcit">Shipped</p>*}
                            {*</div>*}

                            {*<div class="trc-tbl-item">*}
                            {*<div class="trc_items trc_item_check">*}
                            {*<div class="trc_item trc-check"></div>*}
                            {*</div>*}
                            {*<div class="trc-tbl"></div>*}
                            {*<p class="trcit">Delivered</p>*}
                            {*</div>*}

						</div>

					</div>
				</div>

					<div class="tracking_table_cover">
						<table class="tracking_table">
							<thead>
								<tr>
									<td>{translate('TransactionType')}</td>
									<td>{translate('TransactionDescription')}</td>
									<td>{translate('TranscationDate')}</td>
									<td>{translate('Origin')}</td>
									<td>{translate('Destination')}</td>
									<td>{translate('Status')}</td>
									<td>{translate('remarks')}</td>
								</tr>
							</thead>
							<tbody>
							{foreach from=$results item=result}
								<tr>
										<td>{$result.TransactionType}</td>
										<td>{$result.TransactionDescription}</td>
										<td>{$result.TranscationDate}</td>
										<td>{$result.Origin}</td>
										<td>{$result.Destination}</td>
										<td>{$result.Status}</td>
										<td>{$result.remarks}</td>
									</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>

		</section>
{/block}