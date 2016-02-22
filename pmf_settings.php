<?php include('pages/page_header.php'); ?>
<link href="css/pmf-settings.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/center.css" rel="stylesheet" type="text/css" /> -->
<!-- <link href="css/signin.css" rel="stylesheet" type="text/css" /> -->
<!-- <link href="css/plugin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, '');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, '');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
	require_once('db/db_config.php');
	// Check session id.
	$login = $_SESSION['LOGIN_ID'];
	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	$query = "SELECT * FROM tempSession WHERE emailAdd = '$login'";
	$result = mysql_query($query);
	if(!$result) die ("Table access failed: " . mysql_error());
	$data = mysql_fetch_assoc($result);
	$sid = $data['sid'];
	if($sid == $_SESSION['SID']) {
		if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
			if($_SESSION['GID'] < 4000) {
				$fname = $_SESSION['FNAME'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				$_SESSION['LAST_PAGE'] = "general_settings.php";
				// Lifetime added 5min.
				if(isset($_SESSION['EXPIRETIME'])) {
					if($_SESSION['EXPIRETIME'] < time()) {
						unset($_SESSION['EXPIRETIME']);
						header('Location: logout.php?TIMEOUT');
						exit(0);
					} else {
						// Session time out time 5min.
						//$_SESSION['EXPIRETIME'] = time() + 300;
						$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
					};
				};
				$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				if($dbSelected) {
					// Select from partsUom.
					$queryUom = "SELECT * FROM partsUom WHERE status = 'Active' ORDER BY id DESC";
					$resultUom = mysql_query($queryUom);
					if(!$resultUom) die ("Table access failed: " . mysql_error());
					$rowsUom = mysql_num_rows($resultUom);
					// Select from partsCategory.
					$queryCategory = "SELECT * FROM partsCategory WHERE status = 'Active' ORDER BY id DESC";
					$resultCategory = mysql_query($queryCategory);
					if(!$resultCategory) die ("Table access failed: " . mysql_error());
					$rowsCategory = mysql_num_rows($resultCategory);
					// Select from partsBrand.
					$queryBrand = "SELECT * FROM partsBrand WHERE status = 'Active' ORDER BY id DESC";
					$resultBrand = mysql_query($queryBrand);
					if(!$resultBrand) die ("Table access failed: " . mysql_error());
					$rowsBrand = mysql_num_rows($resultBrand);
					// Select from partsModel.
					$queryModel = "SELECT * FROM partsModel WHERE status = 'Active' ORDER BY id DESC";
					$resultModel = mysql_query($queryModel);
					if(!$resultModel) die ("Table access failed: " . mysql_error());
					$rowsModel = mysql_num_rows($resultModel);
					// Select from partsEquipType.
					$queryEquipType = "SELECT * FROM partsEquipType WHERE status = 'Active' ORDER BY id DESC";
					$resultEquipType = mysql_query($queryEquipType);
					if(!$resultEquipType) die ("Table access failed: " . mysql_error());
					$rowsEquipType = mysql_num_rows($resultEquipType);
				};
			} else {
				// Redirect to dashboard if not Superuser or Manager.
				$_SESSION['STATUS'] = 10;
				header('Location: status.php');
			};
		} else {
			unset($_SESSION['STATUS']);
			header('Location: status.php');
		};
	} else {
		unset($_SESSION['STATUS']);
		header('Location: status.php');
	}
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Parts Master File Settings <small> setup initial information and data.</small></h1>
			</div>
			<div class="page-toolbar">
				<div class="btn-group btn-theme-panel"><a href="javascript:;" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="glyphicon glyphicon-cog"></i></a></div>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<ul class="breadcrumb">
				<li>
					<a href="index.php">Home</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Extras</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Setup</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Parts Master File
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">UOM</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_parts_uom.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div id="slimScrollUom">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
											if($rowsUom > 0) {
												echo "<thead><tr class='uppercase'>";
												echo "<th class='center' style='width: 25%'>Id</th>";
												echo "<th class='left' style='width: 40%'>Name</th>";
												echo "<th class='center' style='width: 35%'>Date Created</th>";
												echo "</tr></thead><tbody>";
												for($j = 0; $j < $rowsUom; ++$j) {
													$partsUomId = mysql_result($resultUom, $j, 'partsUomId');
													$partsUomName = mysql_result($resultUom, $j, 'partsUomName');
													$string = mysql_result($resultUom, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													echo "<tr><td align='center'>";
													echo "<a href='mod_parts_uom.php?partsUomId=$partsUomId' class='primary-link'>$partsUomId</a></td>";
													echo "<td align='left'>$partsUomName</td>";
													echo "<td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
													No uom details.
												**/
												echo "<h3 class='no-infor'>No uom details found!</h3>";
											}
										?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Category</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_parts_category.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div id="slimScrollCategory">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
											if($rowsCategory > 0) {
												echo "<thead><tr class='uppercase'>";
												echo "<th class='center' style='width: 25%'>Id</th>";
												echo "<th class='left' style='width: 40%'>Name</th>";
												echo "<th class='center' style='width: 35%'>Date Created</th>";
												echo "</tr></thead><tbody>";
												for($j = 0; $j < $rowsCategory; ++$j) {
													$partsCategoryId = mysql_result($resultCategory, $j, 'partsCategoryId');
													$partsCategoryName = mysql_result($resultCategory, $j, 'partsCategoryName');
													$string = mysql_result($resultCategory, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													echo "<tr><td align='center'>";
													echo "<a href='mod_parts_category.php?partsCategoryId=$partsCategoryId' class='primary-link'>$partsCategoryId</a></td>";
													echo "<td align='left'>$partsCategoryName</td>";
													echo "<td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
													No category details
												**/
												echo "<h3 class='no-infor'>No category details found!</h3>";
											}
										?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Brand</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_parts_brand.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div id="slimScrollBrand">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
											if($rowsBrand > 0) {
												echo "<thead><tr class='uppercase'>";
												echo "<th class='center' style='width: 25%'>Id</th>";
												echo "<th class='left' style='width: 40%'>Name</th>";
												echo "<th class='center' style='width: 35%'>Date Created</th>";
												echo "</tr></thead><tbody>";
												for($j = 0; $j < $rowsBrand; ++$j) {
													$partsBrandId = mysql_result($resultBrand, $j, 'partsBrandId');
													$partsBrandName = mysql_result($resultBrand, $j, 'partsBrandName');
													$string = mysql_result($resultBrand, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													echo "<tr><td align='center'>";
													echo "<a href='mod_parts_brand.php?partsBrandId=$partsBrandId' class='primary-link'>$partsBrandId</a></td>";
													echo "<td align='left'>$partsBrandName</td>";
													echo "<td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
													No brand details
												**/
												echo "<h3 class='no-infor'>No brand details found!</h3>";
											}
										?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Model</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_parts_model.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div id="slimScrollModel">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
											if($rowsModel > 0) {
												echo "<thead><tr class='uppercase'>";
												echo "<th class='center' style='width: 25%'>Id</th>";
												echo "<th class='left' style='width: 40%'>Name</th>";
												echo "<th class='center' style='width: 35%'>Date Created</th>";
												echo "</tr></thead><tbody>";
												for($j = 0; $j < $rowsModel; ++$j) {
													$partsModelId = mysql_result($resultModel, $j, 'partsModelId');
													$partsModelName = mysql_result($resultModel, $j, 'partsModelName');
													$string = mysql_result($resultModel, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													echo "<tr><td align='center'>";
													echo "<a href='mod_parts_model.php?partsModelId=$partsModelId' class='primary-link'>$partsModelId</a></td>";
													echo "<td align='left'>$partsModelName</td>";
													echo "<td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
													No model details.
												**/
												echo "<h3 class='no-infor'>No model details found!</h3>";
											}
										?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Equipment Type</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_parts_equipType.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div id="slimScrollEquipType">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
											if($rowsEquipType > 0) {
												echo "<thead><tr class='uppercase'>";
												echo "<th class='center' style='width: 25%'>Id</th>";
												echo "<th class='left' style='width: 40%'>Name</th>";
												echo "<th class='center' style='width: 35%'>Date Created</th>";
												echo "</tr></thead><tbody>";
												for($j = 0; $j < $rowsEquipType; ++$j) {
													$partsEquipTypeId = mysql_result($resultEquipType, $j, 'partsEquipTypeId');
													$partsEquipTypeName = mysql_result($resultEquipType, $j, 'partsEquipTypeName');
													$string = mysql_result($resultEquipType, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													echo "<tr><td align='center'>";
													echo "<a href='mod_parts_equipType.php?partsEquipTypeId=$partsEquipTypeId' class='primary-link'>$partsEquipTypeId</a></td>";
													echo "<td align='left'>$partsEquipTypeName</td>";
													echo "<td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
													No equipment type details.
												**/
												echo "<h3 class='no-infor'>No equipment type details found!</h3>";
											}
										?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('pages/page_jquery.php'); ?>
<script>
/**
   Alertify confirm logout.
**/
$(function() {
	$('.logoutAlert').click(function() {
		alertify.confirm("[ALERT]  Are you sure you want to LOGOUT?", function(result) {
			if(result) {
				window.location = "logout.php";
			}
		})
	})
})
/**
   Bootbox alert customize.
**/
/*
$(function() {
	$('.logoutAlert').click(function(){
		bootbox.confirm("Are you sure you want to LOGOUT?", function(result) {
			if(result) {
				window.location = "logout.php";
			}
		});
	})
})
*/
$(function(){
	var row = <?php echo $rowsUom; ?>;
	if(row < 6) {	} else {
		$('#slimScrollUom').slimScroll({
	    		height: '230px'
    		});
	}
});
$(function(){
	var row = <?php echo $rowsCategory; ?>;
	if(row < 6) {	} else {
		$('#slimScrollCategory').slimScroll({
	    		height: '230px'
    		});
	}
});
$(function(){
	var row = <?php echo $rowsBrand; ?>;
	if(row < 6) {	} else {
		$('#slimScrollBrand').slimScroll({
	    		height: '230px'
    		});
	}
});
$(function(){
	var row = <?php echo $rowsModel; ?>;
	if(row < 6) {	} else {
		$('#slimScrollModel').slimScroll({
	    		height: '230px'
    		});
	}
});
$(function(){
	var row = <?php echo $rowsEquipType; ?>;
	if(row < 6) {	} else {
		$('#slimScrollEquipType').slimScroll({
	    		height: '230px'
    		});
	}
});
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>