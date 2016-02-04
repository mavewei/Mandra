<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, 'add_parts.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'add_parts.php');
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
				Select parts lists.
	   		**/
	   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
			$query = "SELECT * from partsMasterFile ORDER BY id DESC";
			$result = mysql_query($query);
			$row = mysql_num_rows($result);
			if(!$result) die ("Table access failed: " . mysql_error());
			if($row == 0) {
				$partsId = 'P000001';
			} else {
				$row++;
				$partsId = 'P' . sprintf('%06d', $row);
			}
			/**
				Get post data and submit to database.
			**/
			if(isset($_POST['serialNumber']) && isset($_POST['partsNumber'])) {
				$partsId = $_POST['serialNumber'];
				$partsNumber = strtoupper(mysql_escape_string($_POST['partsNumber']));
				$partsDescription = ucwords(mysql_escape_string($_POST['description']));
				$partsUom = ucwords(mysql_escape_string($_POST['uom']));
				$partsBrand = ucwords(mysql_escape_string($_POST['brand']));
				$partsModel = ucwords(mysql_escape_string($_POST['model']));
				$partsWhereUsedI = ucwords(mysql_escape_string($_POST['whereUsedI']));
				$partsWhereUsedII = ucwords(mysql_escape_string($_POST['whereUsedII']));
				/**
					Insert record.
				**/
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				$query = "INSERT INTO partsMasterFile
								(dateTime, partsId, partsNumber, partsDescription, partsUom, partsBrand, partsModel, partsWhereUsedI, partsWhereUsedII, status, createdBy)
							VALUES
								('$time', '$partsId', '$partsNumber', '$partsDescription', '$partsUom', '$partsBrand', '$partsModel', '$partsWhereUsedI', '$partsWhereUsedII', 'Active', '$uid')";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					/**
						Parts information created and redirected to previous page.
					**/
					$_SESSION['STATUS'] = 26;
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
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Add Parts <small>add parts to master file</small></h1>
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
					Add Parts
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-usereg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Parts</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="add_parts.php" method="post" onsubmit="return validate();">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>S/N</label>
															<input type="text" class="form-control input-lg" name="serialNumber" style="text-align: center" value="<?php echo $partsId; ?>" readonly>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Date</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-calendar"></i>
															<input type="text" class="form-control input-lg" name="dateTime" value="<?php echo date("d/m/Y"); ?>" disabled>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Parts Number</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-code"></i>
															<input type="text" class="form-control input-lg" name="partsNumber" placeholder="Parts Number" id="partsNumber" onkeyup="checkPartsNumber();" autofocus="on" required>
														</div>
													</div>
												</div>
												<div class="col-md-5">
													<div class="form-group">
														<label>Description</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-info"></i>
															<input type="text" class="form-control input-lg" name="description" placeholder="Description" required>
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>UOM</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-balance-scale"></i>
															<input type="text" class="form-control input-lg" name="uom" placeholder="UOM" required>
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
															<input type="text" class="form-control input-lg" name="brand" placeholder="Brand" required>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Model</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-tags"></i>
															<input type="text" class="form-control input-lg" name="model" placeholder="Model" required>
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
															<input type="text" class="form-control input-lg" name="whereUsedI" placeholder="Where Used I" required>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Where Used II <small class="addParts-optional"> - Optional</small></label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-map-marker"></i>
															<input type="text" class="form-control input-lg" name="whereUsedII" placeholder="Where Used II - Optional">
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
										<div class="form-actions">
											<input type="submit" value="Submit" class="btn blue">
											<!-- <button type="submit" class="btn blue">Submit</button> -->
											<a href="parts_mfile.php"><button type="button" class="btn default">Cancel</button></a>
										</div>
									</form>
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