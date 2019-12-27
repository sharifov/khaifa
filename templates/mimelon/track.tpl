{extends file=$layout}
{block name=content}
    <section class="container-fluid m-container faq-cover min_height">
        {include file="templates/mimelon/_partial/account_sidebar.tpl"}
        <div class="col-md-7 col-sm-12 col-xs-12 margin-auto small-all-orders all__tracks">
            <div class="all-orders-content traking_all_orders">
                <h1 class="txt-bold26 text-center trc_title">{translate('tracking')}</h1>
                <div class="tracking_table_cover">
                   <div class="trc-cell">
                   		<ul class="trc_details_list">
                   			<li>Order No :  <span>OR043LGL53</span></li>
                   			<li>Вес :  <span>2 кг</span></li>
                   			<li>Тип отправления : <span>UPU</span></li>
                   			<li>В пути : <span>24 days</span></li>
                   		</ul>
                   		<div class="trc_details_info">Your order will be delivered by <span>31.08.19</span></div>
                   </div>
                   <div class="trc-tbl-section">
                   		<h2 class="trc-tbl-title">Status & Location</h2>
                   		<div class="trc-tbl-items">
                   			<div class="trc-tbl-item active">
                   				<div class="trc_items">
                   					<div class="trc_item trc-box"></div>
                   				</div>
                   				<div class="trc-tbl"></div>
                   				<p class="trcit">Preparing to Ship</p>
                   			</div>
                   			<div class="trc-tbl-item">
                   				<div class="trc_items">
                   					<div class="trc_item trc-car"></div>
                   				</div>
                   				<div class="trc-tbl"></div>
                   				<p class="trcit">Shipped</p>
                   			</div>
                   			<div class="trc-tbl-item">
                   				<div class="trc_items">
                   					<div class="trc_item trc-check"></div>
                   				</div>
                   				<div class="trc-tbl"></div>
                   				<p class="trcit">Delivered</p>
                   			</div>
                   		</div>
                   		<div class="tbl_cvr">
                   			<table class="trc_tbl">
	                   			<tr>
	                   				<td>23.08.18</td>
	                   				<td>19:19</td>
	                   				<td>Waiting for pickup <br> <span class="tdg">Berlin</span> </td>
	                   				<td class="tdg">Aliexpress - Cainiao</td>
	                   			</tr>
	                   			<tr>
	                   				<td>24.08.18</td>
	                   				<td>10:30</td>
	                   				<td>Shipment confirmation <br> <span class="tdg">Berlin</span> </td>
	                   				<td class="tdg">Aliexpress - Cainiao</td>
	                   			</tr>
	                   			<tr>
	                   				<td>23.08.18</td>
	                   				<td>19:19</td>
	                   				<td>Waiting for pickup <br> <span class="tdg">Berlin</span> </td>
	                   				<td class="tdg">Aliexpress - Cainiao</td>
	                   			</tr>
	                   			<tr>
	                   				<td>24.08.18</td>
	                   				<td>10:30</td>
	                   				<td>Shipment confirmation <br> <span class="tdg">Berlin</span> </td>
	                   				<td class="tdg">Aliexpress - Cainiao</td>
	                   			</tr>
	                   			<tr>
	                   				<td>23.08.18</td>
	                   				<td>19:19</td>
	                   				<td>Waiting for pickup <br> <span class="tdg">Berlin</span> </td>
	                   				<td class="tdg">Aliexpress - Cainiao</td>
	                   			</tr>
                   			</table>
                   		</div>
                   </div>
                </div>
            </div>
        </div>
    </section>
{/block}