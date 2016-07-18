<div id="footer">
	<div>
		<ul>
			<li id="facebook">
				<a href="javascript:void(0);">facebook</a>
			</li>
			<li id="twitter">
				<a href="javascript:void(0);">twitter</a>
			</li>
			<li id="googleplus">
				<a href="javascript:void(0);">googleplus</a>
			</li>
		</ul>
		<p>
			@ Copyright 2016 Max Sherlock. All rights reserved.
		</p>
	</div>
</div>

<div id="alertMsg" title="">
</div>

<?PHP 
	if(isset($_SESSION['alert_message'])){
		echo "<script>alertMsg('提示訊息', '".$_SESSION['alert_message']."');</script>";
		unset($_SESSION['alert_message']);
	}
?>