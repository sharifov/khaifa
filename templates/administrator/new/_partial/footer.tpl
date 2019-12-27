	<!-- Footer -->
	<div class="navbar navbar-default navbar-fixed-bottom">
		<ul class="nav navbar-nav no-border visible-xs-block">
			<li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second"><i class="icon-circle-up2"></i></a></li>
		</ul>

		<div class="navbar-collapse collapse" id="navbar-second">
			<div class="navbar-text">
				{$copyright}
			</div>

			<div class="navbar-right">
				<ul class="nav navbar-nav">
					<li><a>{translate('footer_memory_usage', true)} {$memory_usage}</a></li>
					<li><a>{translate('footer_elapsed_time', true)} {$elapsed_time}</a></li>
					<li><a id="back-to-top"><i class="icon-arrow-up16"></i><span class="visible-xs-inline-block position-right">{translate('footer_backtotop', true)}</span></a></li>
				</ul>
			</div>
			
		</div>
	</div>
	<!-- /footer -->
</body>
</html>

