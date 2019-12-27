{extends file=$layout}
{block name=content}
    <style>
        td{
            padding: 10px !important;
        }
    </style>

    <div class="container-fluid">
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
                            <td>{$order->created_at}</td>
                        </tr>
                        <tr>
                            <td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs"><i class="icon-credit-card2"></i></button></td>
                            <td>{$order->payment_method}</td>
                        </tr>
                        {if $order->payment_code == 'credit'}
                            <tr>
                                <td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs"><i class="icon-credit-card2"></i></button></td>
                                <td><form action="{site_url_multi('administrator/order/refund')}/{$order->id}" id="refund-to-user" method="POST">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="number" name="amount" value="{$order_refund_amount}" class="form-control" {if $order->order_status_id == 8} disabled {/if}/>
                                            </div>
                                            <div class="col-md-6">
                                                {if $order->order_status_id == 8}
                                                    <button type="button"  class="btn btn-danger btn-block" disabled>Refunded</button>
                                                {else}
                                                    <button type="button"  class="btn btn-primary btn-block  submit-refund-to-user">Refund</button>
                                                {/if}
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        {/if}
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
                            <td>{$customer->firstname} {$customer->lastname}</td>
                        </tr>
                        <tr>
                            <td><button data-toggle="tooltip" title="" class="btn btn-info btn-xs"><i class="icon-mention"></i></button></td>
                            <td><a href="mailto:{$customer->email}}">{$customer->email}</a></td>
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
                    {if isset($address) && !empty($address)}
                        <table class="table">
                            <tbody>
                                <tr><td> {$address->firstname} {$address->lastname}</td></tr>
                                <tr><td> {$address->company}</td></tr>
                                <tr><td> {$address->address_1}</td></tr>
                                <tr><td> {$address->address_2}</td></tr>
                                <tr><td> {$address->city}</td></tr>
                                <tr><td> {$address->postcode}</td></tr>
                                <tr><td> {get_country_name($address->country_id)}</td></tr>
                                <tr><td> {get_region_name($address->zone_id)}</td></tr>
                                <tr><td> {$address->phone}</td></tr>
                            </tbody>
                        </table>
                    {/if}
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle"></i> {translate('index_title')} (#{$order->id})</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td class="text-left">{translate('product_id')}</td>
                        <td class="text-left">{translate('product')}</td>
                        <td class="text-left">{translate('seller')}</td>
                        {*							<td class="text-left">{translate('model')}</td>*}
                        <td class="text-left">Warranty</td>
                        <td class="text-right">{translate('quantity')}</td>
                        <td class="text-right">{translate('price')}</td>
                        <td class="text-right">{translate('total')}</td>
                        <td class="text-right">Percent</td>
                        <td class="text-right">{translate('shipping')}</td>
                        <td class="text-right">{translate('status')}</td>
                        <td class="text-right">{translate('booking')}</td>
                        <td class="text-right">{translate('add_payment')}</td>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$products item=product}
                        <tr>
                            <td class="text-left">{$product->id}</td>
                            <td class="text-left">{$product->name}</td>
                            <td class="text-left">
                                <a href="{if $product->seller_id}{site_url_multi('administrator/user/show')}/{$product->seller_id}{else}javascript:void(){/if}">{$product->seller}</a>
                            </td>
                            {*							<td class="text-left">{$product->model}</td>*}
                            <td class="text-left">{$product->warranty}</td>
                            <td class="text-right">{$product->quantity}</td>
                            <td class="text-right">{currency_formatter($product->price, currency_code($product->currency_original), $order->currency_code)}</td>
                            <td class="text-right">{currency_formatter($product->total, currency_code($product->currency_original), $order->currency_code)}</td>
                            <td class="text-right">{$product->vendor_percent} {if $product->vendor_percent != ''}%{/if}</td>
                            <td class="text-right">{assign var=shipping value=json_decode($product->shipping)}{assign var=sh_pr value=currency_formatter($shipping[0]->price, $shipping[0]->currency, $order->currency_code)}  {if isset($shipping[0])} {$shipping[0]->name} ({$shipping[0]->code})  {currency_formatter($shipping[0]->price, $shipping[0]->currency, $order->currency_code)}{/if}</td>
                            <td class="text-right">
                                {if ! $product->seller_id}
                                    <span class="label label-danger">Deleted</span>
                                {else}
                                    {vendor_product_status($product->status)}
                                {/if}
                            </td>
                            <td class="text-right">
                                {if $product->tracking_code}{$product->tracking_code}{else}{if isset($shipping[0])}
                                <a style="font-size: 12px;width: 110px;white-space: pre-line;padding: 2px;" data-type="{$product->shipping_code}" data-id="{$product->id}" class="booking_ems btn btn-primary btn-block {if ! $product->seller_id} disabled {/if}"> {translate('booking_with')}
                                    {if isset($shipping[0])} {translate($shipping[0]->code, true)} {/if} </a>{/if}
                                {/if}
                            </td>
                            <td>

                                {if $product->verify eq 0}
                                    {if $product->seller_id}
                                        <form action="{site_url_multi('administrator/order/add_payment')}/{$product->id}" method="post">
                                            <input type="number" name="percent" class="form-control" title="Percent">
                                            <br>
                                            <input type="submit" class="btn btn-success btn-block" value="{translate('transfer')}">
                                        </form>
                                        {*									<a href="{site_url_multi('administrator/order/add_payment')}/{$product->id}" class="btn btn-success btn-block">{translate('transfer')}</a>*}
                                    {/if}
                                {elseif $product->verify eq 1}
                                    <span class="label label-success">{translate('paid')}</span>
                                    <a data-id="{$product->id}" class="label label-danger refund-product">Refund</a>
                                {else}
                                    <span class="label label-danger">Refunded</span>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                    <tr>
                        <td colspan="12" class="text-right"><strong>Shipping total</strong></td>
                        <td>{$combined_shipping_price} USD </td>
                    </tr>
                    <tr>
                        <td colspan="12" class="text-right"><strong>{translate('total')}</strong></td>
                        <td>{floor($order->total)} {$order->currency_code}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                {if $ems_express}
                    <form action="{site_url_multi('administrator/order/booking')}" method="POST">
                        <div class="form-group">
                            <input type="hidden" value="" name="ems_express_product">
                            <input type="hidden" value="Express" name="type">
                            <input type="hidden" value="{$order->id}" name="order_id">
                            <input type="text" name="tracking_code" value="" placeholder="tracking code" class="form-control" />
                            <button type="submit" disabled="disabled" class="ems_express_product btn btn-primary btn-block">{translate('booking_with_ems_express')}</button>
                        </div>
                    </form>
                {/if}

                {if $ems_standard}
                    <form action="{site_url_multi('administrator/order/booking')}" method="POST">
                        <div class="form-group">
                            <input type="hidden" value="" name="ems_standard_product">
                            <input type="hidden" value="Standard" name="type">
                            <input type="hidden" value="{$order->id}" name="order_id">
                            <input type="text" name="tracking_code" value="" placeholder="tracking code" class="form-control" />
                            <button type="submit" disabled="disabled" class="ems_standard_product btn btn-primary btn-block">{translate('booking_with_ems_standard')}</button>
                        </div>
                    </form>
                {/if}

                {if $ems_economy}
                    <form action="{site_url_multi('administrator/order/booking')}" method="POST">
                        <div class="form-group">
                            <input type="hidden" value="" name="ems_economy_product">
                            <input type="hidden" value="Economy" name="type">
                            <input type="hidden" value="{$order->id}" name="order_id">
                            <input type="text" name="tracking_code" value="" placeholder="tracking code" class="form-control" />
                            <button type="submit" disabled="disabled" class="ems_economy_product btn btn-primary btn-block">{translate('booking_with_ems_economy')}</button>
                        </div>
                    </form>
                {/if}

                {if $ems_premium}
                    <form action="{site_url_multi('administrator/order/booking')}" method="POST">
                        <div class="form-group">
                            <input type="hidden" value="" name="ems_premium_product">
                            <input type="hidden" value="Premium" name="type">
                            <input type="hidden" value="{$order->id}" name="order_id">
                            <input type="text" name="tracking_code" value="" placeholder="tracking code" class="form-control" />
                            <button type="submit" disabled="disabled" class="ems_premium_product btn btn-primary btn-block">{translate('booking_with_ems_premium')}</button>
                        </div>
                    </form>
                {/if}
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-comment-o"></i> {translate('order_history')}</h3>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class=""><a href="#tab-history" data-toggle="tab" aria-expanded="false">{translate('history')}</a></li>
                    <li class="active"><a href="#tab-additional" data-toggle="tab" aria-expanded="true">{translate('additional')}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab-history">
                        <div id="history">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <td class="text-left">{translate('date_added')}</td>
                                        <td class="text-left">{translate('comment')}</td>
                                        <td class="text-left">{translate('order_Status')}</td>
                                        <td class="text-left">{translate('notify_customer')}</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {if $order_status_history}
                                        {foreach from=$order_status_history item=order_status_histor}
                                            <tr>
                                                <td class="text-left">{$order_status_histor->created_at}</td>
                                                <td class="text-left">{$order_status_histor->comment}</td>
                                                <td class="text-left">{get_order_status_name($order_status_histor->order_status_id)}</td>
                                                <td class="text-left">{if $order_status_histor->notify == 1}{translate('yes')}{else}{translate('no')}{/if}</td>
                                            </tr>
                                        {/foreach}
                                    {/if}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <fieldset>
                            <legend>{translate('add_history')}</legend>
                            <form class="form-horizontal" method="POST" action="{current_url()}">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-order-status">{translate('order_Status')}</label>
                                    <div class="col-sm-10">
                                        <select name="order_status_id" id="input-order-status" class="form-control">
                                            {foreach from=$order_statuses item=order_status}
                                                <option value="{$order_status->id}">{$order_status->name}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-override"><span data-toggle="tooltip" title="">{translate('override')}</span></label>
                                    <div class="col-sm-10">
                                        <div class="checkbox">
                                            <input type="checkbox" name="override" class="styled" value="1" id="input-override">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-notify">{translate('notify_customer')}</label>
                                    <div class="col-sm-10">
                                        <div class="checkbox">
                                            <input type="checkbox" name="notify" value="1" class="styled" id="input-notify">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-comment">{translate('comment')}</label>
                                    <div class="col-sm-10">
                                        <textarea name="comment" rows="8" id="input-comment" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> {translate('add_history')}</button>
                                </div>
                            </form>
                        </fieldset>

                    </div>
                    <div class="tab-pane active" id="tab-additional">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td colspan="2">{translate('browser')}</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{translate('ip')}</td>
                                    <td>{$order->ip}</td>
                                </tr>
                                <tr>
                                    <td>{translate('user_agent')}</td>
                                    <td>{$order->user_agent}</td>
                                </tr>
                                <tr>
                                    <td>{translate('accept_language')}</td>
                                    <td>{$order->accept_language}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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