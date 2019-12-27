{extends file=$layout}
{block name=content}
<style>
   .tst-cover{
    max-width:300px;
    margin:100px auto;
   }
   .ncarts_cover{
     max-height:100% !important;
     padding:0;
   }
   .carts_types{
    max-height:340px;
    overflow: auto;
   }
   .cart_cnt_top{
    background-color: #202020;
    padding-top:8px;
    padding-left:12px;
    padding-right:12px;
   }
   .nreviews-btn{
    background-color:#fff;
    color:#000;
    height: 36px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-width:116px;
    padding-top:8px !important;
    margin-left:6px !important;
    margin-right:6px !important;
   }
   .total-btns{
    padding-top:28px;
    padding-bottom:28px;
   }
   .nreviews-btn:hover{
    color:#000 !important;
   }
   .nreviews-btn.green_btn,.nreviews-btn.green_btn:hover{
    color:#fff !important;
   }
   .total-btns.flex{
    align-items: center;
    justify-content: center;
   }
   .cart-total{
    border-color:#4d4d4d;
   }
   .cart_total{
    padding-top:13px;
    padding-bottom:11px;
   }
   .cart_total .total-txt,.cart_total .product-caption-price-new{
    font-size:18px !important;
    color:#ebc733 !important;
   }
   .cart_total .product-caption-price .product-caption-price-new .fa-azn{
    margin-top:3px;
   }
   .cart-total{
    border-bottom:0;
   }
   .bb{
    border-bottom:1px solid #4d4d4d;
   }
   .cart_cnt_top .total-txt{
    padding-left:0;
   }
   .crt_total{
    padding-left: 8px;
   }
   .crt_item{
    margin-top:6px;
    margin-bottom:6px;
    color:#fff;
    font-size:14px;
    font-family: "FiraSans-Regular";
   }
   .crt_item .total-txt{
    color:#fff;
    font-family: "FiraSans-Regular";
   }

   .crt_item .product-caption-price-new{
    color:#fff !important;
    font-family: "FiraSans-Regular" !important;
    font-size:14px !important;
   }
   .carts_items{
   	padding-top:16px;
   }
   .carts_types .shopping-item{
   	border-bottom:0;
    padding:0 0 0 8px;
   }
   .crt-type1{
   	border-bottom:1px solid #d0d0d0;
   }
   .carts_items{
	margin-left:8px;
   	margin-right:8px;
   }
   .carts_type_name{
   		font-size:16px;
   		font-family: "FiraSans-SemiBold";
   		color:#000;
      margin-bottom:12px;
   }
   .plr10{
   	padding-left:10px;
   	padding-right:10px;
   }
   .carts_types .shopping-item .shopping-img{
    width:128px;
    height:128px;
    overflow: hidden;
    border:1px solid #e1e1e1;
    background-color:#fff;
   }
   .carts_types .shopping-item .shopping-img img{
    width:100%;
    height:100%;
    object-fit: contain;
   }
   .ncarts_cover .shopping-cart-details{
      padding-right:0;
      padding-left:5px;
   }
   .scd .cart-item-btn{
      position: static;
   }
   .scd .product-caption-price-new{
      font-size:18px !important;
      font-weight:bold;
      margin-top:8px !important;
   }
   .scd .product-caption-price{
     height: 26px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom:4px;
   }
   .scd .cart-txt-detail{
    padding-right:15px;
    margin-bottom:10px;
   }
   .carts_types::-webkit-scrollbar{
    width: 6px;
    border-radius: 50%;
  }
  .carts_types::-webkit-scrollbar-track{
      -webkit-box-shadow: none;
  }
  .carts_types::-webkit-scrollbar-thumb{
      background-color: darkgrey;
      outline: none;
  }
  .carts_types{
    padding-left:0;
    padding-right:0;
  }
  .shead{
    z-index:991;
  }
  .second-nav{
    z-index:999;
  }
  .crt_sum .total-txt,.crt_sum .product-caption-price-new{
    color:#ebc733 !important;
    font-family: "FiraSans-Medium" !important;
    font-size:18px !important;
  }
  .crt_sum .total-txt,.crt_sum .product-caption-price-new i.fa-azn{
    margin-top:2px;
  }
  @media screen and (max-width:992px){
    .ncarts_cover .shopping-cart-details{
      padding-left:0;
    }
  }
  @media screen and (max-width:460px){
    .carts_types{
      padding-left:0;
      padding-right:10px;
    }
    .scd .cart-txt-detail{
      padding-right:24px;
    }
    .scd .product-caption-price{
      margin-bottom:4px;
    }
    .carts_types .shopping-item{
      padding-bottom:10px;
    }
    .carts_types .qty ul{
      margin-bottom:0;
    }
    .scd .cart-txt-detail{
      margin-bottom:6px;
    }
  }
  @media screen and (max-width: 400px){
    .carts_types .shopping-item .shopping-img{
      width:112px;
      height:112px;
    }
    .scd .product-caption-price-new{
      font-size: 16px !important;
    }
    .cart_types .shopping-cart-img{
      padding-right:10px;
    }
  }
</style>
<!-- shopping-cart-element -->
<div class="tst-cover">
     <div class="container-fluid shopping-cart-cover">
        <div class="dropdown-head shopping-head shead"><h4>My <span>bag: 1 Items</span></h4></div>
        <div class="col-sm-12 col-xs-12 carts-cover ncarts_cover">

           	<div class="col-xs-12 col-sm-12 carts_types">

	           <div class="row carts_items crt-type1">
	           		{*<div class="div-xs-12 col-sm-12 plr10">
	           			<h4 class="carts_type_name">Seller : Name Surname1</h4>
	           		</div>*}
		           	<div class="col-xs-12 col-sm-12 shopping-item">
		                <div class="col-md-6 shopping-cart-img">
		                    <div class="shopping-img">
		                        <a href="https://mimelon.com/product/viva-madrid-glazo-flex-back-case-for-iphone-xs-max-champagne-gold">
		                            <img src="https://mimelon.com/uploads/catalog/Product//xs23.jpg" alt=""> 
		                        </a>
		                    </div>
		                </div>
		                <div class="col-md-6 shopping-cart-details scd">
		                    <div class="product-caption-price">
		                        <span class="product-caption-price-new">115 <i title="AZN" class="fa fa-azn">m</i></span>
                            <button class="cart-item-btn" onclick="removeCart(1444, false)">
                              <img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">
                            </button>
		                    </div>
		                    <div class="cart-txt-detail">Viva Madrid Glazo Flex Back Case for iPhone Xs Max - Champagne Gold
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Qty:</strong> 1</li>
		                        </ul>
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Express:</strong> 32 <i title="AZN" class="fa fa-azn">m</i></li>
		                        </ul>
		                    </div>
		                </div>
		            </div>
		            <div class="col-xs-12 col-sm-12 shopping-item">
		                <div class="col-md-6 shopping-cart-img">
		                    <div class="shopping-img">
		                        <a href="https://mimelon.com/product/viva-madrid-glazo-flex-back-case-for-iphone-xs-max-champagne-gold">
		                            <img src="https://mimelon.com/uploads/catalog/Product//xs23.jpg" alt=""> 
		                        </a>
		                    </div>
		                </div>
		                <div class="col-md-6 shopping-cart-details scd">
		                    <div class="product-caption-price">
		                        <span class="product-caption-price-new">115 <i title="AZN" class="fa fa-azn">m</i></span>
                            <button class="cart-item-btn" onclick="removeCart(1444, false)">
                              <img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">
                            </button>
		                    </div>
		                    <div class="cart-txt-detail">Viva Madrid Glazo Flex Back Case for iPhone Xs Max - Champagne Gold
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Qty:</strong> 1</li>
		                        </ul>
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Express:</strong> 32 <i title="AZN" class="fa fa-azn">m</i></li>
		                        </ul>
		                    </div>
		                </div>
		            </div>
	           </div>

	           <div class="row carts_items crt-type1">
	           		{*<div class="div-xs-12 col-sm-12 plr10">
	           			<h4 class="carts_type_name">Seller : Name Surname2</h4>
	           		</div>*}
		           	<div class="col-xs-12 col-sm-12 shopping-item">
		                <div class="col-md-6 shopping-cart-img">
		                    <div class="shopping-img">
		                        <a href="https://mimelon.com/product/viva-madrid-glazo-flex-back-case-for-iphone-xs-max-champagne-gold">
		                            <img src="https://mimelon.com/uploads/catalog/Product//xs23.jpg" alt=""> 
		                        </a>
		                    </div>
		                </div>
		                <div class="col-md-6 shopping-cart-details scd">
		                    <div class="product-caption-price">
		                        <span class="product-caption-price-new">115 <i title="AZN" class="fa fa-azn">m</i></span>
                            <button class="cart-item-btn" onclick="removeCart(1444, false)">
                              <img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">
                            </button>
		                    </div>
		                    <div class="cart-txt-detail">Viva Madrid Glazo Flex Back Case for iPhone Xs Max - Champagne Gold
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Qty:</strong> 1</li>
		                        </ul>
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Express:</strong> 32 <i title="AZN" class="fa fa-azn">m</i></li>
		                        </ul>
		                    </div>
		                </div>
		            </div>
		            <div class="col-xs-12 col-sm-12 shopping-item">
		                <div class="col-md-6 shopping-cart-img">
		                    <div class="shopping-img">
		                        <a href="https://mimelon.com/product/viva-madrid-glazo-flex-back-case-for-iphone-xs-max-champagne-gold">
		                            <img src="https://mimelon.com/uploads/catalog/Product//xs23.jpg" alt=""> 
		                        </a>
		                    </div>
		                </div>
		                <div class="col-md-6 shopping-cart-details scd">
		                    <div class="product-caption-price">
		                        <span class="product-caption-price-new">115 <i title="AZN" class="fa fa-azn">m</i></span>
                            <button class="cart-item-btn" onclick="removeCart(1444, false)">
                              <img src="/templates/mimelon/assets/img/icons/cart-close-icon.svg" alt="">
                            </button>
		                    </div>
		                    <div class="cart-txt-detail">Viva Madrid Glazo Flex Back Case for iPhone Xs Max - Champagne Gold
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Qty:</strong> 1</li>
		                        </ul>
		                    </div>
		                    <div class="qty text-left">
		                        <ul>
		                            <li><strong>Express:</strong> 32 <i title="AZN" class="fa fa-azn">m</i></li>
		                        </ul>
		                    </div>
		                </div>
		            </div>
	           </div>

           	</div>
            
            <!-- starts cart content -->
           <div class="col-xs-12 col-sm-12 cart_cnt_top">
           	{*<div class="bb crt_total">
                    <div class="cart-total">
                        <div class="ncart-txt crt_item">Seller : Name Surname1</div>
                        <div class="cart-total-content crt_item">
                            <p class="total-txt">Delivery</p>
                            <p class="product-caption-price">
                                <span class="product-caption-price-new">Standart</span>
                            </p>
                        </div>
                        <div class="cart-total-content crt_item">
                            <p class="total-txt">Price:</p>
                            <p class="product-caption-price">
                                <span class="product-caption-price-new"> 30 <i title="AZN" class="fa fa-azn">m</i>
                                </span>
                            </p>
                        </div>
                    </div>
            </div>*}
            {*<div class="bb crt_total">
                <div class="cart-total">
                    <div class="ncart-txt crt_item">Seller : Name Surname2</div>
                    <div class="cart-total-content crt_item">
                        <p class="total-txt">Delivery</p>
                        <p class="product-caption-price">
                            <span class="product-caption-price-new">Standart</span>
                        </p>
                    </div>
                    <div class="cart-total-content crt_item">
                        <p class="total-txt">Price:</p>
                        <p class="product-caption-price">
                            <span class="product-caption-price-new"> 30 <i title="AZN" class="fa fa-azn">m</i>
                            </span>
                        </p>
                    </div>
                </div>
            </div>*}
            <div class="bb crt_total">
                <div class="cart-total">
                    <div class="cart-total-content crt_item crt_sum">
                        <p class="total-txt">Total :</p>
                        <p class="product-caption-price">
                            <span class="product-caption-price-new"> 468 <i title="AZN" class="fa fa-azn">m</i>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="total-btns flex">
                <a href="https://mimelon.com/checkout/cart" class="btn reviews-btn nreviews-btn">View bag</a>
                <a href="https://mimelon.com/checkout" class="btn reviews-btn nreviews-btn green_btn">Checkout</a>
            </div>
           </div> 
           <!-- ends cart content -->  
        </div>
    </div>
</div>
{/block}