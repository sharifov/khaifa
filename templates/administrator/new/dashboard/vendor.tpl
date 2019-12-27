{extends file=$layout}
{block name=content}
	<div class="content">
		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-6">
				<div class="panel panel-body panel-body-accent" style="min-height:114px;">
					<div class="media no-margin">
						<div class="media-left media-middle">
							<a href="{site_url_multi('administrator/product?status=1')}"><i class="icon-database-check icon-3x text-info-800"></i></a>
						</div>
						<div class="media-body text-right">
							<h3 class="no-margin text-semibold"><span class="text-success">{$product_count}</span></h3>
							<span class="text-uppercase text-size-mini text-muted">Active product</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-6">
				<div class="panel panel-body panel-body-accent" style="min-height:114px;">
					<div class="media no-margin">
						<div class="media-left media-middle">
							<a href="{site_url_multi('administrator/product?status=0')}"><i class="icon-database-remove icon-3x text-info-800"></i></a>
						</div>
						<div class="media-body text-right">
							<h3 class="no-margin text-semibold"><span class="text-danger">{$product_count_deactive}</span></h3>
							<span class="text-uppercase text-size-mini text-muted">Deactive product</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-6">
				<div class="panel panel-body panel-body-accent" style="min-height:114px;">
					<div class="media no-margin">
						<div class="media-left media-middle">
							<a href="{site_url_multi('administrator/product?status=2')}"><i class="icon-database-refresh icon-3x text-info-800"></i></a>
						</div>
						<div class="media-body text-right">
							<h3 class="no-margin text-semibold"><span class="text-warning">{$product_count_waiting}</span></h3>
							<span class="text-uppercase text-size-mini text-muted">Pending product</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-6">
				<div class="panel panel-body panel-body-accent" style="min-height:114px;">
					<div class="media no-margin">
						<div class="media-left media-middle">
							<a href="{site_url_multi('administrator/order_product')}"><i class="icon-basket icon-3x text-info-800"></i></a>
						</div>
						<div class="media-body text-right">
							<h3 class="no-margin text-semibold"><span class="text-warning">{$total_order}</span></h3>
							<span class="text-uppercase text-size-mini text-muted">Total order</span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-lg-3 col-md-4 col-sm-6">
				<div class="panel panel-body panel-body-accent" style="min-height:114px;">
					<div class="media no-margin">
						<div class="media-left media-middle">
							<a href="{site_url_multi('administrator/transaction/create')}" class="btn btn-success">Withdraw</a>
						</div>
						<div class="media-body text-right">
							<h3 class="no-margin text-semibold"><span class="text-warning">{$balance} $</span></h3>
							<span class="text-uppercase text-size-mini text-muted">Balance</span>
							
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
{/block}