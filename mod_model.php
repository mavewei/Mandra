<?php include('pages/page_header.php'); ?>
<link href="css/pmf-settings.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
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
			if($_SESSION['GID'] < 3000) {
				$fname = $_SESSION['FNAME'];
				$uid = $_SESSION['UID'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				$partsModelId = $_GET['partsModelId'];
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
				// Remove record.
				if($_GET['delModelId']) {
					$delId = $_GET['delModelId'];
					deleteRecord($delId, 'partsModel', 'partsModelId');
				}
				// Select parts uom.
				mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * FROM partsModel WHERE partsModelId = '$partsModelId'";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				$data = mysql_fetch_array($result);
				$partsModelName = $data['partsModelName'];
				if(isset($_POST['partsModelId']) && isset($_POST['partsModelName'])) {
					$partsModelId = $_POST['partsModelId'];
					$partsModelName = mysql_escape_string($_POST['partsModelName']);
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "UPDATE partsModel SET partsModelName = '$partsModelName' WHERE partsModelId = '$partsModelId'";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// Category details updated and redirected to previous page.
						$_SESSION['STATUS'] = 40;
						header("Location: status.php");
					};
				};
			} else {
				// Redirect to dashboard if not Superuser or Manager
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
	function deleteRecord($idx, $tables, $tableId) {
		$query = "UPDATE $tables SET status = 'Cancel' WHERE $tableId = '$idx'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($result) {
			header('Location: pmf_settings.php');
		}
	}
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Modify Model <small>modify model details</small></h1>
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
				<li>
					<a href="javascript:;">Parts Master File</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Modify Model
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Modify Model</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post" onsubmit="return validate()">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Model ID</label>
												<input type="text" id="partsModelId" class="form-control input-lg" style="text-align: center" name="partsModelId" value="<?php echo $partsModelId; ?>" readonly>
											</div>
										</div>
										<div class="col-md-8">
											<div class="form-group">
												<label>Name</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-code"></i>
													<input type="text" class="form-control input-lg" id="partsModelName" placeholder="Model Name" name="partsModelName" value="<?php echo $partsModelName; ?>" onkeyup="checkModelName();" autofocus="on" onfocus="this.value = this.value;" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="partsModelName_status"></span>
										</div>
										<div class="col-md-6"></div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Update" class="btn blue">
									<a href="pmf_settings.php"><button type="button" class="btn default">Close</button></a>
									<label class="cancel-or-padding">or</label>
									<!-- <input type="button" value="DELETE" class="btn red" onclick="return deleteData();"> -->
									<input type="button" id="partsModelDel" value="DELETE" class="btn red">
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
	// Alertify confirm logout.
	$(function() {
		$('.logoutAlert').click(function() {
			alertify.confirm("[ALERT]  Are you sure you want to LOGOUT?", function(result) {
				if(result) {
					window.location = "logout.php";
				}
			})
		})
	})
	// Bootbox alert customize.
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
		var partsModelId = document.getElementById("partsModelId").value;
		$('#partsModelDel').click(function(){
			alertify.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
				if(result) {
					window.location="mod_model.php?delModelId=" + partsModelId;
				}
			});
		})
	})
	function checkModelName() {
		var partsModelName = document.getElementById("partsModelName").value;
		if(partsModelName) {
			$.ajax({
				type: 'post',
				url: 'check_data.php',
				data: {
					partsModelName: partsModelName,
				},
				success: function (response) {
					if(response == "true") {
						$('#partsModelName_status').html("");
						return true;
					} else {
						$('#partsModelName_status').html(response);
						return false;
	               }
	            }
			});
		} else {
			$('#partsModelName_status').html("");
			return false;
		}
	}
	function validate() {
		var partsModelName_status = document.getElementById("partsModelName_status").innerHTML;
		if(partsModelName_status =="") {
			return true;
		} else {
	          return false;
		}
	}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>