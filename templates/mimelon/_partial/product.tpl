
                    <div class="product  owl-item-slide">
                        <div class="product-img-wrap trend_img">
{*                            <img class="product-img" src="{$product.image}" alt="{$product.alt_image}" title="" data-qazy="true" />*}
                            {if isset($product.alt_image)}
                                <img class="product-img lazy" src="{base_url('uploads/mimelon5.jpg')}" alt="{$product.alt_image}" data-src="{$product.image}">
                                {else}
                                <img class="product-img lazy" src="{base_url('uploads/mimelon5.jpg')}" data-src="{$product.image}">
                            {/if}

                            <a href="{site_url_multi('/')}{$product.slug}" class="trend_overlay dataLayerProductClick" data-id="{$product.id}"><span>More</span></a>
                            {if $customer}<button class="add__heart {if in_array($product.id,$favorite_ids)} active {/if}" data-id="{$product.id}"></button>{/if}
                        </div>
                        <a href="{site_url_multi('product/')}{$product.slug}" class="dataLayerProductClick" data-id="{$product.id}">
                            <div class="product-caption">
                                <ul class="product-caption-rating wow bounceIn" data-wow-delay=".3s">
                                {for $rating=1 to 5}
                                {if $rating <= $product.rating}
                                    <li class="rated"><i class="fa fa-star"></i></li>
                                    {else}
                                    <li><i class="fa fa-star-o"></i></li>
                                {/if}
                                {/for}
                                </ul>
                                <h5 class="product-caption-title wow slideInUp">{$product.name}</h5>
                                <div class="product-caption-price">
                                    {if $product.special_price}
                                    <span class="product-caption-price-new last-chance-price">
                                        {currency_symbol_converter($product.price)}
                                        <small class="product-old-price wow zoomIn">
                                            {currency_symbol_converter($product.special_price)}
                                        </small>
                                    </span>
                                    {else}
                                    <span class="product-caption-price-new  wow slideInUp">
                                        {currency_symbol_converter($product.price)}
                                    </span>
                                    {/if}
                                </div>
                            </div>
                        </a>
                    </div>
                 {* <span class="trend_overlay"><span>More info</span></span> *}
