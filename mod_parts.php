<?php include('pages/page_header.php'); ?>
<link href="css/parts-mfile.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, '');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, '');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
	require_once('db/db_config.php');
	/**
		Check session id.
	**/
	$login = $_SESSION['LOGIN_ID'];
	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	$query = "SELECT * FROM tempSession WHERE emailAdd = '$login'";
	$result = mysql_query($query);
	if(!$result) die ("Table access failed: " . mysql_error());
	$data = mysql_fetch_assoc($result);
	$sid = $data['sid'];
	if($sid == $_SESSION['SID']) {
		if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
			if($_SESSION['GID'] < 3000) {
				$fname = $_SESSION['FNAME'];
				$uid = $_SESSION['UID'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				$partsId = mysql_escape_string($_GET['partsId']);
				/**
					Lifetime added 5min.
				**/
				if(isset($_SESSION['EXPIRETIME'])) {
					if($_SESSION['EXPIRETIME'] < time()) {
						unset($_SESSION['EXPIRETIME']);
						header('Location: logout.php?TIMEOUT');
						exit(0);
					} else {
						/**
							Session time out.
						**/
						$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
					};
				};
				/**
					Remove record.
				**/
				if($_GET['delPartsId']) {
					$delPartsId = $_GET['delPartsId'];
					deleteRecord($delPartsId);
				}
				/**
					Select parts details.
		   		**/
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * from partsMasterFile WHERE partsId = '$partsId' ORDER BY id DESC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				$data = mysql_fetch_assoc($result);
				$dateTimeFull = $data['dateTime'];
				if(preg_match('/(\d{4}-\d{2}-\d{2})/', $dateTimeFull, $match)) {
					$dateTime = $match[1];
				};
				$partsNumber = $data['partsNumber'];
				$partsDescription = $data['partsDescription'];
				$partsUom = $data['partsUom'];
				$partsBrand = $data['partsBrand'];
				$partsModel = $data['partsModel'];
				$partsWhereUsedI = $data['partsWhereUsedI'];
				$partsWhereUsedII = $data['partsWhereUsedII'];
				/**
					Get post data and submit to database.
				**/
				if(isset($_POST['serialNumber']) && isset($_POST['partsNumber'])) {
					$partsId = $_POST['serialNumber'];
					$partsNumber = strtoupper(mysql_escape_string($_POST['partsNumber']));
					$partsDescription = ucwords(mysql_escape_string($_POST['partsDescription']));
					$partsUom = ucwords(mysql_escape_string($_POST['partsUom']));
					$partsBrand = ucwords(mysql_escape_string($_POST['partsBrand']));
					$partsModel = ucwords(mysql_escape_string($_POST['partsModel']));
					$partsWhereUsedI = ucwords(mysql_escape_string($_POST['partsWhereUsedI']));
					$partsWhereUsedII = ucwords(mysql_escape_string($_POST['partsWhereUsedII']));
					/**
						Insert record.
					**/
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "UPDATE partsMasterFile
								SET
									partsNumber = '$partsNumber', partsDescription = '$partsDescription',
									partsUom = '$partsUom', partsBrand = '$partsBrand', partsModel = '$partsModel',
									partsWhereUsedI = '$partsWhereUsedI', partsWhereUsedII = '$partsWhereUsedII'
								WHERE partsId = '$partsId'";
					print($query);
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						/**
							Parts information created and redirected to previous page.
						**/
						$_SESSION['STATUS'] = 27;
						header("Location: status.php");
					};
				};
			} else {
				/**
					Redirect to dashboard if not Superuser or Manager
				**/
				$_SESSION['STATUS'] = 10;
				header('Location: status.php');
			}
		} else {
			unset($_SESSION['STATUS']);
			header('Location: status.php');
		};
	} else {
		unset($_SESSION['STATUS']);
		header('Location: status.php');
	}
	function deleteRecord($delPartsId) {
		$query = "UPDATE partsMasterFile SET status = 'Cancel' WHERE partsId = '$delPartsId'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($result) {
			header('Location: parts_mfile.php');
		}
	}
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Modify Parts <small>modify parts details</small></h1>
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
					<a href="javascript:;">Procurement</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Modify Parts
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Modify Parts</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="mod_parts.php" method="post" onsubmit="return validate();">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>S/N</label>
													<input type="text" class="form-control input-lg" id="partsId" name="serialNumber" style="text-align: center" value="<?php echo $partsId; ?>" readonly>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Date</label>
												<input type="text" class="form-control input-lg" name="dateTime" style="text-align: center" value="<?php echo $dateTime; ?>" disabled>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Parts Number</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-code"></i>
													<input type="text" class="form-control input-lg" id="partsNumber" name="partsNumber" placeholder="Parts Number" onkeyup="checkPartsNumber();" value="<?php echo $partsNumber; ?>" required>
												</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group">
												<label>Description</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-info"></i>
													<input type="text" class="form-control input-lg" name="partsDescription" placeholder="Description" value="<?php echo $partsDescription; ?>" required>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>UOM</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-balance-scale"></i>
													<input type="text" class="form-control input-lg" name="partsUom" placeholder="UOM" value="<?php echo $partsUom; ?>" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Brand</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-tags"></i>
													<input type="text" class="form-control input-lg" name="partsBrand" placeholder="Brand" value="<?php echo $partsBrand; ?>" required>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Model</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-tags"></i>
													<input type="text" class="form-control input-lg" name="partsModel" placeholder="Model" value="<?php echo $partsModel; ?>" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Where Used I</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-map-marker"></i>
													<input type="text" class="form-control input-lg" name="partsWhereUsedI" placeholder="Where Used I" value="<?php echo $partsWhereUsedI; ?>" required>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Where Used II <small class="addParts-optional"> - Optional</small></label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-map-marker"></i>
													<input type="text" class="form-control input-lg" name="partsWhereUsedII" placeholder="Where Used II - Optional" value="<?php echo $partsWhereUsedII; ?>">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="partsNumber_status"></span>
										</div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Update" class="btn blue">
									<a href="parts_mfile.php"><button type="button" class="btn default">Close</button></a>
									<label class="cancel-or-padding">or</label>
									<input type="button" id="partsDel" value="DELETE" class="btn red">
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-3"></div>
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
$(function() {
	var partsId = document.getElementById("partsId").value;
	$('#partsDel').click(function(){
		// bootbox.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
		alertify.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
			if(result) {
				window.location="mod_parts.php?delPartsId=" + partsId;
			}
		});
	})
})
function checkPartsNumber() {
	var partsNumber = document.getElementById("partsNumber").value;
	if(partsNumber) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				partsNumber: partsNumber,
			},
			success: function (response) {
				if(response == "true") {
					$('#partsNumber_status').html("");
					return true;
				} else {
					$('#partsNumber_status').html(response);
					return false;
               }
            }
		});
	} else {
		$('#partsNumber_status').html("");
		return false;
	}
}
function validate() {
	var partsNumber_status = document.getElementById("partsNumber_status").innerHTML;
	if(partsNumber_status == "") {
		return true;
	} else {
          return false;
	}
}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>