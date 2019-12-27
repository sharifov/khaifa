{extends file=$layout}
{block name=content}
    <div id="VAT" class="modal fade delivery-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                {get_setting('vat_details', $current_lang)}
                <button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt=""></button>
            </div>
        </div>
    </div>
    <div id="Shipping" class="modal fade delivery-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                {get_setting('shipping_details', $current_lang)}
                <button type="button" class="btn btn-default delivery-modal-btn" data-dismiss="modal"><img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt=""></button>
            </div>
        </div>
    </div>


    <header class="m-container page-header">
        <ol class="breadcrumb page-breadcrumb">
            <li class="active"><a href="#">{translate('home',true,true)}</a></li>
            {if isset($product->category)}
                <li class="active"><a href="{site_url_multi('category/')}{$product->category_slug}">{$product->category}</a></li>
            {/if}
            <li><a href="{current_url()}">{$product->name}</a></li>
        </ol>
    </header>





    <div class="container product-single-cover">
        <div class="col-md-9 product-single-details">
            <div class="col-md-7 product-single-slider product__single_slider wow fadeInUp ">
                <div class="product-single__slider">
                    {if isset($product->images)}
                        <ul class="jqzoom-list pro-single-zoom-list js-zoom-img-nav"  id="pagerUl">
                            <li>
                                <a class="zoomThumbActive" href="#" data-rel="{literal}{gallery:'gal-1', smallimage: {/literal} '{$product->image}', largeimage: '{$product->image}'{literal}}{/literal}">
                                    <img src="{$product->image}" alt="{$product->name}" title="{$product->name}" />
                                </a>
                            </li>
                            {foreach from=$product->images item=image}
                                <li>
                                    <a class="zoomThumbActive" href="#" data-rel="{literal}{gallery:'gal-1', smallimage: {/literal} '{$image.url}', largeimage: '{$image.url}'{literal}}{/literal}">
                                        <img src="{$image.url}" alt="{$product->name}" title="{$product->name}" />
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                    <div class="product-page-product-wrap jqzoom-stage wow fadeInUp">
                        <div class="clearfix">
                            <a href="{$product->image}" id="jqzoom" data-rel="gal-1">
                                <img class="single-img222" src="{$product->image}" alt="{$product->name}" title="{$product->name}"/>
                            </a>
                        </div>
                        <img class="chevron_left" id="prevSlider" src="/templates/mimelon/assets/img/icons/chevron_left.svg" alt="">
                        <img class="chevron_right" id="nextSlider" src="/templates/mimelon/assets/img/icons/chevron_right.svg" alt="">
                    </div>
                </div>
                {if $copied_product_count > 0}
                    <div class="more__product33">
                        <div class="col-md-12 col-sm-12 col-xs-12 pro-des-cover">
                            <div class="des-info-txt">
                                <div class="des-info wow fadeInUp">
                                    <a href="{site_url_multi('product/search')}?copied_product_id={$product->id}">{translate('label_copied_product',true)} ({$copied_product_count}) <i class="fa fa-chevron-right"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                {*
                <div class="share-single-product">
                    <a href="#">
                        <img src="/templates/mimelon/assets/img/icons/share.svg" alt="" class="share-icon">
                        <p>Share</p>
                    </a>
                </div>
                *}
            </div>
            <div class="col-md-5 product-single-info">
                {if isset($shipping_list) && !empty($shipping_list)}
                    {if get_setting('stock_limit') != 0 && get_setting('stock_limit') > $product->quantity && $product->quantity > 0}
                        <div class="col-sm-12 col-xs-12 product-stock-limit pro-lr00" style="color:red; font-weight:bold;">{sprintf(translate('stock_limit'), $product->quantity)}</div>
                    {/if}
                {/if}
                <h2 class="product-single-title wow fadeInUp">{$product->name}
                    <!-- out of stock -->
                    {*<span class="outStocktxt">{$product->stock_status_name}</span>*}
                </h2>
                <div class="product-rating-top">
                    <ul class="product-caption-rating rating_star222 wow fadeInUp">
                        {for $rating=1 to 5}
                            {if $rating <= $product->rating}
                                <li class="rated"><i class="fa fa-star"></i></li>
                            {else}
                                <li><i class="fa fa-star-o"></i></li>
                            {/if}
                        {/for}
                    </ul>
                </div>

                <span class="product-caption-price-new last-chance-price wow fadeInUp">
				{$product->price}
                    {if $product->special}
                        <small class="product-old-price">{$product->special}</small>
                    {/if}
			</span>
                {if $product->special && $product->special_date_end}
                    <div class="single-discount-product wow fadeInUp">
                        {*
                        <p class="percent-off">30% Off</p>
                        *}
                        <input type="hidden" id="countdown3" value="{$product->special_date_end}" />
                        <div class="single-product-countdown">
                            <p class="single-count-txt">{translate('discount_ends_in', true)} : </p>
                            <div class="countdown">
                                <b class="countdown3day"></b> <span>{translate('day', true)}:</span>
                                <b class="countdown3hrs"></b> <span>{translate('hrs', true)}:</span>
                                <b class="countdown3min"></b> <span>{translate('min', true)}:</span>
                                <b class="countdown3sec"></b> <span>{translate('sec', true)}</span>
                            </div>
                        </div>
                    </div>
                {/if}
                <form id="product" method="post">
                    {if $product->quantity > 0}
                        <div class="col-sm-12 col-xs-12 pro-lr00">

                            {if $product_relations}
                                <div class="row">
                                    <ul class="unit-connections no-bullet product-unit-details wow fadeInUp">
                                        {foreach from=$product_relations item=product_relation}
                                            <li>
                                                <h3> {$product_relation.name} </h3>
                                                <ul class="menu connection-buttons wow fadeInUp">
                                                    {foreach from=$product_relation.product_relation_value item=relation_value}
                                                        <li><a href="{$relation_value.link}" class="btn btn-{if isset($relation_value.current) && $relation_value.current eq  1}danger{else}info{/if}" role="button">{$relation_value.name}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </li>
                                            <br>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            <table class="single-pro-color">
                                {if $product->manufacturer_id > 0}
                                    <tr class="wow fadeInUp">
                                        <td><b>{translate('label_manufacturer',true)}</b></td>
                                        <td><span>{$product->manufacturer_name}</span></td>
                                    </tr>
                                {/if}
                                {if $product->stock_status_id > 0}
                                    <tr class="wow fadeInUp">
                                        <td><b>{translate('label_availability',true)}</b></td>
                                        <td><span {if $product->stock_status_id eq get_setting('stock_status_id')} class="label label-primary" {/if}>{$product->stock_status_name}</span>
                                        </td>
                                    </tr>
                                {/if}
                                {if $product_options}
                                    {foreach from=$product_options item=product_option}
                                        {if $product_option.type eq 'select' || $product_option.type eq 'color'}
                                            <tr class="wow fadeInUp">
                                                <td><b>{$product_option.name}</b></td>
                                                <td>
                                                    <select name="option[{$product_option.product_option_id}]" id="input-option{$product_option.product_option_id}" class="form-control">
                                                        <option value="0">{translate('select', true)}</option>
                                                        {foreach from=$product_option.option_values item=option_value}
                                                            {assign var="product_label" value=$option_value.name}
                                                            {if $option_value.option_value_price}
                                                                {assign var="product_label" value=$product_label|cat:" ("|cat:$option_value.price_prefix|cat:$option_value.option_value_price|cat:")"}
                                                            {/if}
                                                            <option value="{$option_value.product_option_value_id}">{$product_label}</option>
                                                        {/foreach}
                                                    </select>
                                                </td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {/if}

                                <tr class="wow fadeInUp">
                                    <td><b>{translate('label_amount',true)}</b></td>
                                    <td>
                                        <div class="quantity">
                                            <input type="number" name="quantity" step="1" min="1" max="" value="1" title="Qty" class="input-text qty text" pattern="[0-9]*" inputmode="numeric">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        {foreach from=$product_options item=product_option}
                            {if $product_option.type eq 'radio'}
                                <div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22">
                                    <label for="" class="form-label shipping-quaranty-label">{$product_option.name}</label>
                                    {foreach from=$product_option.option_values item=option_value}
                                        <label class="control control--radio shipping-quaranty-cell">
                                            {assign var="product_label" value=$option_value.name}
                                            {if $option_value.option_value_price}
                                                {assign var="product_label" value=$product_label|cat:" ("|cat:$option_value.price_prefix|cat:$option_value.option_value_price|cat:")"}
                                            {/if}
                                            <input type="radio" id="input-option{$product_option.product_option_id}" name="option[{$product_option.product_option_id}]" value="{$option_value.product_option_value_id}">{$product_label}
                                            <div class="control__indicator"></div>
                                        </label>
                                    {/foreach}
                                </div>
                            {/if}
                        {/foreach}
                        {if isset($shipping_list) && !empty($shipping_list)}
                            <div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22" id="shipping_list">
                                <label for="" class="form-label shipping-quaranty-label">{translate('shipping')} ({get_country_name()})</label>

                                {foreach from=$shipping_list key=key item=shipping_item}
                                    <label class="control control--radio shipping-quaranty-cell">
                                        {$shipping_item.name}
                                        <input type="radio" id="" name="shipping" {if $key eq 0}checked="checked"{/if} value='{json_encode($shipping_item)}'> {$shipping_item.show_price}
                                        <div class="control__indicator"></div>
                                    </label>
                                {/foreach}

                            </div>
                        {else}
                            <div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22">
                                <div class="alert alert-warning">{translate('not_shipped_message', true)}</div>
                            </div>
                        {/if}
                        {if isset($shipping_list) && !empty($shipping_list)}
                            <div class="col-sm-12 col-xs-12 add-to-card_info desktop-add-card_info pro-lr00">
                                <button type="button" id="button-cart" onclick="addToCart()" class="green_btn add-to-card_btn">{translate('label_add_to_cart',true)}</button>
                                {if $customer}
                                    <button type="button" class="add__heart {if in_array($product->id,$favorite_ids)} active{/if}" data-id="{$product->id}"></button>
                                {/if}
                            </div>
                        {/if}
                    {/if}
                    <input type="hidden" name="product_id" value="{$product->id}"/>

                    <div class="clearfix"></div>
                    {if $product->quantity == 0}
                        <!-- out of stock -->
                        <div class="outStockCover">
                            <p class="notifyMe">Notify me when product is available</p>
                            <div class="outStockForm">
                                <input type="email" name="subs_email" placeholder="nur.ruslanova@mail.ru">
                                <button class="notifyMeBtn" type="button">Notify me <i class="fa fa-check"></i> </button>
                            </div>
                        </div>
                    {/if}

                </form>
            </div>
        </div>
        <div class="col-md-3 product-sold-by">
            <div class="sold-by-item wow fadeInUp">
                <ul class="sold-list">
                    {if !empty(trim($product->seller_note))}
                        <li><span>{translate('seller_note')} </span> {$product->seller_note}</li>
                    {/if}
                    <li><span>{translate('condition')} </span>{if $product->new eq '0'}{translate('new')}{elseif $product->new eq '1'}{translate('used')}{else} {translate('refurbished')}{/if}</li>
                    <li>
                        <span>{translate('seller')} </span>
                        <small class="soldbymimelon mtooltip">
                            <a class="deliver-change" href="{site_url_multi('products/seller')}?id={$product->created_by}">{get_seller($product->created_by)}</a>
                            {* <div class="mtooltipcontent">
                                <ul>
                                    <li>Stored,Packed and Shipped by mimelon</li>
                                    <li>Guaranteed Authentic</li>
                                    <li>Ships Quickly</li>
                                </ul>
                            </div> *}
                        </small>
                    </li>
                </ul>
            </div>
            <div class="sold-by-item sold-by-delivery wow fadeInUp">
                <ul class="sold-list">
                    <li>{translate('all_prices_include_vat',true)} <span class="deliver-change vat-popup vat-popup22" data-toggle="modal" data-target="#VAT">Details</span></li>
                    <li>{translate('shipping',true)} <span class="deliver-change shipping-popup shipping-popup22" data-toggle="modal" data-target="#Shipping">Details</span></li>
                    <!-- starts yeni elave -->
                    <div class="des-info-txt des-packing">
                        <div class="des-info-table des_info_table">
                            <ul>
                                <li>
                                    <b>{translate('packaging_details', true)}:</b>
                                    <br>

                                    <div>{translate('length', true)} {round($product->length, 2)} {$product->length_class_unit}</div>
                                    <div>{translate('width', true)} {round($product->width, 2)} {$product->length_class_unit}</div>
                                    <div>{translate('height', true)} {round($product->height, 2)} {$product->length_class_unit}</div>
                                    <div></div>
                                </li>
                                <li>
                                    <b>{translate('port', true)}:</b>
                                    <br>
                                    <div>{$product->country_name} {$product->region_name}</div>
                                </li>
                                <li>
                                    <b>{translate('lead_time', true)}: </b>
                                    <br>
                                    <div>{sprintf(translate('shipped_in',true), $product->day)}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- ends yeni elave -->
                    <li class="payment-method-cell wow fadeInUp">
                        <ul class="payment-methods">
                            <li>{translate('payment',true)} : </li>
                            <li><img src="{base_url('uploads/sprite_img.png')}" alt="Visa" class="sprite-visa"></li>
              							<li><img src="{base_url('uploads/sprite_img.png')}" alt="Mastercard" class="sprite-mastercard"></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- add to cart only for mobile -->
        <div class="col-md-12 col-sm-12 col-xs-12 add-to-card_info mobile-add-card_info">
            <button type="button" id="button-cart" onclick="addToCart()" class="green_btn add-to-card_btn">{translate('label_add_to_cart',true)}</button>
            {if $customer}
                <button type="button" class="add__heart {if in_array($product->id,$favorite_ids)} active{/if}" data-id="{$product->id}"></button>
            {/if}
        </div>
    </div>

    <div style="display: none;"  class="container product-single-cover">
        <div class="col-md-9 col-xs-12 product-single-details">
           <div class="col-md-7 product-single-slider product__single_slider custom-zoom-cover wow fadeInUp ">
    <!-- starts product single zoom animation -->
               <div class="zoom-cover">
                    <div class="small-img">
                    <div class="small-container">
                        <div id="small-img-roll">
                        <img src="https://placeimg.com/500/500/animals" class="show-small-img" alt="">
                        <img src="https://images.unsplash.com/photo-1558148706-ebaaea871026?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=633&q=80" class="show-small-img" alt="">
                        <img src="https://placeimg.com/500/500/nature" class="show-small-img" alt="">
                        <img src="https://placeimg.com/500/500/people" class="show-small-img" alt="">
                        <img src="https://placeimg.com/500/500/tech" class="show-small-img" alt="">
                        <img src="https://picsum.photos/500/500/?random" class="show-small-img" alt="">
                        </div>
                    </div>
                    </div>
                    <div class="show showen-img" href="https://placeimg.com/500/500/animals">
                        <img src="/templates/mimelon/assets/img/icons/chevron_left.svg" class="icon-left" alt="" id="prev-img">
                        <img src="https://placeimg.com/500/500/animals" id="show-img">
                        <img src="/templates/mimelon/assets/img/icons/chevron_right.svg" class="icon-right" alt="" id="next-img">
                    </div>
                </div>
               <!-- ends product single zoom animation -->
                <div class="clearfix"></div>
                {if $copied_product_count > 0}
                <div class="more__product33">
                    <div class="col-md-12 col-sm-12 col-xs-12 pro-des-cover">
                    <div class="des-info-txt">
                    <div class="des-info wow fadeInUp">
                    <a href="{site_url_multi('product/search')}?copied_product_id={$product->id}">{translate('label_copied_product',true)} ({$copied_product_count}) <i class="fa fa-chevron-right"></i> </a>
                    </div>
                    </div>
                    </div>
                </div>
                {/if}
                <div class="clearfix"></div>
                {*
                <div class="share-single-product">
                    <a href="#">
                    <img src="/templates/mimelon/assets/img/icons/share.svg" alt="" class="share-icon">
                    <p>Share</p>
                    </a>
                </div>
                *}
                </div>
            <div class="col-md-5 col-xs-12 product-single-info">
                {if isset($shipping_list) && !empty($shipping_list)}
                    {if get_setting('stock_limit') != 0 && get_setting('stock_limit') > $product->quantity}
                        <div class="col-sm-12 col-xs-12 product-stock-limit pro-lr00" style="color:red; font-weight:bold;">{sprintf(translate('stock_limit'), $product->quantity)}</div>
                    {/if}
                {/if}
                <h2 class="product-single-title wow fadeInUp">{$product->name}
                    <!-- out of stock -->
                    {*<span class="outStocktxt">{$product->stock_status_name}</span>*}
                </h2>
                <div class="product-rating-top">
                    <ul class="product-caption-rating rating_star222 wow fadeInUp">
                        {for $rating=1 to 5}
                            {if $rating <= $product->rating}
                                <li class="rated"><i class="fa fa-star"></i></li>
                            {else}
                                <li><i class="fa fa-star-o"></i></li>
                            {/if}
                        {/for}
                    </ul>
                </div>

                <span class="product-caption-price-new last-chance-price wow fadeInUp">
				{$product->price}
                    {if $product->special}
                        <small class="product-old-price">{$product->special}</small>
                    {/if}
			    </span>
                {if $product->special && $product->special_date_end}
                    <div class="single-discount-product wow fadeInUp">
                        {*
                        <p class="percent-off">30% Off</p>
                        *}
                        <input type="hidden" id="countdown3" value="{$product->special_date_end}" />
                        <div class="single-product-countdown">
                            <p class="single-count-txt">{translate('discount_ends_in', true)} : </p>
                            <div class="countdown">
                                <b class="countdown3day"></b> <span>{translate('day', true)}:</span>
                                <b class="countdown3hrs"></b> <span>{translate('hrs', true)}:</span>
                                <b class="countdown3min"></b> <span>{translate('min', true)}:</span>
                                <b class="countdown3sec"></b> <span>{translate('sec', true)}</span>
                            </div>
                        </div>
                    </div>
                {/if}
                <form id="product" method="post">
                    {if $product->quantity > 0}
                        <div class="col-sm-12 col-xs-12 pro-lr00">

                            {if $product_relations}
                                <div class="row">
                                    <ul class="unit-connections no-bullet product-unit-details wow fadeInUp">
                                        {foreach from=$product_relations item=product_relation}
                                            <li>
                                                <h3> {$product_relation.name} </h3>
                                                <ul class="menu connection-buttons wow fadeInUp">
                                                    {foreach from=$product_relation.product_relation_value item=relation_value}
                                                        <li><a href="{$relation_value.link}" class="btn btn-{if isset($relation_value.current) && $relation_value.current eq  1}danger{else}info{/if}" role="button">{$relation_value.name}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </li>
                                            <br>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            <table class="single-pro-color">
                                {if $product->manufacturer_id > 0}
                                    <tr class="wow fadeInUp">
                                        <td><b>{translate('label_manufacturer',true)}</b></td>
                                        <td><span>{$product->manufacturer_name}</span></td>
                                    </tr>
                                {/if}
                                {if $product->stock_status_id > 0}
                                    <tr class="wow fadeInUp">
                                        <td><b>{translate('label_availability',true)}</b></td>
                                        <td><span {if $product->stock_status_id eq get_setting('stock_status_id')} class="label label-primary" {/if}>{$product->stock_status_name}</span>
                                        </td>
                                    </tr>
                                {/if}
                                {if $product_options}
                                    {foreach from=$product_options item=product_option}
                                        {if $product_option.type eq 'select' || $product_option.type eq 'color'}
                                            <tr class="wow fadeInUp">
                                                <td><b>{$product_option.name}</b></td>
                                                <td>
                                                    <select name="option[{$product_option.product_option_id}]" id="input-option{$product_option.product_option_id}" class="form-control">
                                                        <option value="0">{translate('select', true)}</option>
                                                        {foreach from=$product_option.option_values item=option_value}
                                                            {assign var="product_label" value=$option_value.name}
                                                            {if $option_value.option_value_price}
                                                                {assign var="product_label" value=$product_label|cat:" ("|cat:$option_value.price_prefix|cat:$option_value.option_value_price|cat:")"}
                                                            {/if}
                                                            <option value="{$option_value.product_option_value_id}">{$product_label}</option>
                                                        {/foreach}
                                                    </select>
                                                </td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {/if}

                                <tr class="wow fadeInUp">
                                    <td><b>{translate('label_amount',true)}</b></td>
                                    <td>
                                        <div class="quantity">
                                            <input type="number" name="quantity" step="1" min="1" max="" value="1" title="Qty" class="input-text qty text" pattern="[0-9]*" inputmode="numeric">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        {foreach from=$product_options item=product_option}
                            {if $product_option.type eq 'radio'}
                                <div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22">
                                    <label for="" class="form-label shipping-quaranty-label">{$product_option.name}</label>
                                    {foreach from=$product_option.option_values item=option_value}
                                        <label class="control control--radio shipping-quaranty-cell">
                                            {assign var="product_label" value=$option_value.name}
                                            {if $option_value.option_value_price}
                                                {assign var="product_label" value=$product_label|cat:" ("|cat:$option_value.price_prefix|cat:$option_value.option_value_price|cat:")"}
                                            {/if}
                                            <input type="radio" id="input-option{$product_option.product_option_id}" name="option[{$product_option.product_option_id}]" value="{$option_value.product_option_value_id}">{$product_label}
                                            <div class="control__indicator"></div>
                                        </label>
                                    {/foreach}
                                </div>
                            {/if}
                        {/foreach}
                        {if isset($shipping_list) && !empty($shipping_list)}
                            <div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22" id="shipping_list">
                                <label for="" class="form-label shipping-quaranty-label">{translate('shipping')} ({get_country_name()})</label>

                                {foreach from=$shipping_list key=key item=shipping_item}
                                    <label class="control control--radio shipping-quaranty-cell">
                                        {$shipping_item.name}
                                        <input type="radio" id="" name="shipping" {if $key eq 0}checked="checked"{/if} value='{json_encode($shipping_item)}'> {$shipping_item.show_price}
                                        <div class="control__indicator"></div>
                                    </label>
                                {/foreach}

                            </div>
                        {else}
                            <div class="col-sm-12 col-xs-12 sold-by-item sold-by_item22">
                                <div class="alert alert-warning">{translate('not_shipped_message', true)}</div>
                            </div>
                        {/if}
                        {if isset($shipping_list) && !empty($shipping_list)}
                            <div class="col-sm-12 col-xs-12 add-to-card_info desktop-add-card_info pro-lr00">
                                <button type="button" id="button-cart" onclick="addToCart()" class="green_btn add-to-card_btn">{translate('label_add_to_cart',true)}</button>
                                {if $customer}
                                    <button type="button" class="add__heart {if in_array($product->id,$favorite_ids)} active{/if}" data-id="{$product->id}"></button>
                                {/if}
                            </div>
                        {/if}
                    {/if}
                    <input type="hidden" name="product_id" value="{$product->id}"/>

                    <div class="clearfix"></div>
                    {if $product->quantity == 0}
                        <!-- out of stock -->
                        <div class="outStockCover">
                            <p class="notifyMe">Notify me when product is available</p>
                            <div class="outStockForm">
                                <input type="email" name="subs_email" placeholder="nur.ruslanova@mail.ru">
                                <button class="notifyMeBtn" type="button">Notify me <i class="fa fa-check"></i> </button>
                            </div>
                        </div>
                    {/if}

                </form>
            </div>
        </div>
        <div class="col-md-3 product-sold-by">
            <div class="sold-by-item wow fadeInUp">
                <ul class="sold-list">
                    {if !empty(trim($product->seller_note))}
                        <li><span>{translate('seller_note')} </span> {$product->seller_note}</li>
                    {/if}
                    <li><span>{translate('condition')} </span>{if $product->new eq '0'}{translate('new')}{elseif $product->new eq '1'}{translate('used')}{else} {translate('refurbished')}{/if}</li>
                    <li>
                        <span>{translate('seller')} </span>
                        <small class="soldbymimelon mtooltip">
                            <a class="deliver-change" href="{site_url_multi('products/seller')}?id={$product->created_by}">{get_seller($product->created_by)}</a>
                            {* <div class="mtooltipcontent">
                                <ul>
                                    <li>Stored,Packed and Shipped by mimelon</li>
                                    <li>Guaranteed Authentic</li>
                                    <li>Ships Quickly</li>
                                </ul>
                            </div> *}
                        </small>
                    </li>
                </ul>
            </div>
            <div class="sold-by-item sold-by-delivery wow fadeInUp">
                <ul class="sold-list">
                    <li>{translate('all_prices_include_vat',true)} <span class="deliver-change vat-popup vat-popup22" data-toggle="modal" data-target="#VAT">Details</span></li>
                    <li>{translate('shipping',true)} <span class="deliver-change shipping-popup shipping-popup22" data-toggle="modal" data-target="#Shipping">Details</span></li>
                    <!-- starts yeni elave -->
                    <div class="des-info-txt des-packing">
                        <div class="des-info-table des_info_table">
                            <ul>
                                <li>
                                    <b>{translate('packaging_details', true)}:</b>
                                    <br>

                                    <div>{translate('length', true)} {round($product->length, 2)} {$product->length_class_unit}</div>
                                    <div>{translate('width', true)} {round($product->width, 2)} {$product->length_class_unit}</div>
                                    <div>{translate('height', true)} {round($product->height, 2)} {$product->length_class_unit}</div>
                                    <div></div>
                                </li>
                                <li>
                                    <b>{translate('port', true)}:</b>
                                    <br>
                                    <div>{$product->country_name} {$product->region_name}</div>
                                </li>
                                <li>
                                    <b>{translate('lead_time', true)}: </b>
                                    <br>
                                    <div>{sprintf(translate('shipped_in',true), $product->day)}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- ends yeni elave -->
                    <li class="payment-method-cell wow fadeInUp">
                        <ul class="payment-methods">
                            <li>{translate('payment',true)} : </li>
                            <li><img src="/templates/mimelon/assets/img/icons/visa.png" alt=""></li>
                            <li><img src="/templates/mimelon/assets/img/icons/mastercard.png" alt=""></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- add to cart only for mobile -->
        <div class="col-md-12 col-sm-12 col-xs-12 add-to-card_info mobile-add-card_info">
            <button type="button" id="button-cart" onclick="addToCart()" class="green_btn add-to-card_btn">{translate('label_add_to_cart',true)}</button>
            {if $customer}
                <button type="button" class="add__heart {if in_array($product->id,$favorite_ids)} active{/if}" data-id="{$product->id}"></button>
            {/if}
        </div>
    </div>
    <!-- starts product description-->
    <div class="gap"></div>
    <div class="m-container product-description-cover pro-des-cover22">
        <div class="col-md-12 col-sm-12 col-xs-12 pro-des-list pro-des-list22">
            <h2 class="pro-des-title wow fadeInUp">{translate('product_details',true)}</h2>
            <ul class="des-list wow fadeInUp des-list-js">
                {if $product->manufacturer_id}
                    <li>{translate('brand_name', true)} {$product->manufacturer_name}</li>
                {/if}
                <li>{translate('model')} {$product->model}</li>
                {if isset($attributes) && !empty($attributes)}
                    {foreach from=$attributes item=attribute}
                        <li>{$attribute->name}:  {$attribute->value}</li>
                    {/foreach}
                {/if}
            </ul>
            <a href="javascript:void(0);" class="des-see-more22">{translate('see_more', true)}</a>
            <a href="javascript:void(0);" class="des-see-less22" style="display:none;">{translate('see_less', true)}</a>
            <!-- when click see more btn then show all items -->
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 pro-des-cover">
            <div class="des-info-txt">
                <h2 class="pro-des-title pro_des_title wow fadeInUp">{translate('label_product_description',true)}</h2>
                <div class="des-info wow fadeInUp">
                    {$product->description}
                </div>
            </div>
        </div>
    </div>
    <div class="m-container product-review-cover">
        {if $customer}
            <div class="col-md-12 reviews-cover reviews-cover2">
                <p class="reviews-btn-txt wow fadeInUp">{translate('form_label_reviews',true)}</p>
                <div class="product-caption-rating2 wow fadeInUp">
                    <div class="rate2">{translate('rate', true)}:</div>
                    <span class="gl-star-rating" data-star-rating="">
			<select id="star-rating-3" name="rating">
				<option value="">{translate('select_rating', true)}</option>
				<option value="5">5</option>
				<option value="4">4</option>
				<option value="3">3</option>
				<option value="2">2</option>
				<option value="1">1</option>
			</select>
                </div>
            </div>
            <div class="col-md-12 reviews-form wow fadeInUp">
                <!-- error -->
                <div class="col-md-12 review_form_error error-alert alert-messages" style="display:none"></div>
                <!-- success -->
                <div class="col-md-12 review_form_success success-alert alert-messages" style="display:none"></div>

                <input class="form_element form-control" type="text" name="review_subject" placeholder="{translate('form_placeholder_subject',true)}">
                <textarea class="form_element form-control" name="review_text" id="" cols="30" rows="10" placeholder="{translate('form_placeholder_comment',true)}"></textarea>
                <div class="text-center"><button class="btn reviews-btn wow fadeInUp" onclick="sendReview()">{translate('form_label_send',true)}</button></div>
            </div>
        {/if}

        {if $reviews}
            {foreach from=$reviews item=review}
                <div class="col-md-12 reviews_cover wow fadeInUp">
                    <div class="reviews-star-info">
                        <a href="#"  class="reviewer-name">{$review->user_name}</a>
                        <ul class="product-caption-rating">
                            {for $rating=1 to 5}
                                {if $rating <= $review->rating}
                                    <li class="rated"><i class="fa fa-star"></i></li>
                                {else}
                                    <li><i class="fa fa-star-o"></i></li>
                                {/if}
                            {/for}
                        </ul>
                        <span class="reviewer-time">{$review->created_at}</span>
                    </div>
                    <div class="reviews-infos">
                        <h2 class="reviews-infos-title">{$review->subject}</h2>
                        <div class="txt_main">
                            {$review->text}
                        </div>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
    <!-- ends product description-->
    {if isset($customer_also_vieweds) && !empty($customer_also_vieweds)}
        <!-- starts trend section -->
        <div class="gap"></div>
        <section class="container">
            <h3 class="widget-title trend_title"><a href="#">{translate('customer_also_viewed', true)}</a></h3>
            <div class="owl-carousel pro-owl-carousel owl-loaded owl-nav-out">
                {foreach from=$customer_also_vieweds item=product}
                    <div class="owl-item">
                        {include file="templates/mimelon/_partial/product.tpl"}
                    </div>
                {/foreach}
            </div>
        </section>
        <!-- ends trend section -->
    {/if}
    <div class="gap"></div>
    <script>
        var product_id = "{$product->id}";
        {literal}
        function sendReview() {
            let subject = $('input[name="review_subject"]').val();
            let text = $('textarea[name="review_text"]').val();
            let rating = $('select[name="rating"]').val();

            $.ajax({
                type: 'post',
                url: $('base').attr('href')+"/product/review",
                dataType: 'json',
                data : {product: product_id, text: text, subject: subject, rating: rating},
                success: function (response) {
                    if(response['success']){
                        $('input[name="review_subject"]').val(null);
                        $('textarea[name="review_text"]').val(null);
                        $('div.review_form_error').hide();
                        $('div.review_form_success').show();
                        $('div.review_form_success').html(response['message']);
                    } else {
                        $('div.review_form_success').hide();
                        $('div.review_form_error').show();
                        $('div.review_form_error').html(response['message']);
                    }
                }
            });
        }
        {/literal}
    </script>
{/block}
