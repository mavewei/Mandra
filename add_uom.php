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
				// Select parts uom lists.
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * FROM partsUom ORDER BY id DESC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($row == 0) {
					$partsUomId = 'PU001';
				} else {
					$row++;
					$partsUomId = 'PU' . sprintf('%03d', $row);
				}
				if(isset($_POST['partsUomId']) && isset($_POST['partsUomName'])) {
					$partsUomId = mysql_escape_string($_POST['partsUomId']);
					$partsUomName = ucwords(strtolower(mysql_escape_string($_POST['partsUomName'])));
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "INSERT INTO partsUom(dateTime, partsUomId, partsUomName, status)
								VALUES('$time', '$partsUomId', '$partsUomName', 'Active')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// parts uom created and redirected to previous page.
						$_SESSION['STATUS'] = 32;
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
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Add UOM <small>add new uom details</small></h1>
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
					Add UOM
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add UOM</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post" onsubmit="return validate()">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>UOM ID</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="partsUomId" value="<?php echo $partsUomId; ?>" readonly>
											</div>
										</div>
										<div class="col-md-8">
											<div class="form-group">
												<label>Name</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-code"></i>
													<input type="text" class="form-control input-lg" placeholder="UOM Name" id="partsUomName" name="partsUomName" onkeyup="checkUomName();" autofocus="on" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="partsUomName_status"></span>
										</div>
										<div class="col-md-6"></div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Submit" class="btn blue">
									<a href="pmf_settings.php"><button type="button" class="btn default">Cancel</button></a>
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
	function checkUomName() {
		var partsUomName = document.getElementById("partsUomName").value;
		if(partsUomName) {
			$.ajax({
				type: 'post',
				url: 'check_data.php',
				data: {
					partsUomName: partsUomName,
				},
				success: function (response) {
					if(response == "true") {
						$('#partsUomName_status').html("");
						return true;
					} else {
						$('#partsUomName_status').html(response);
						return false;
	               }
	            }
			});
		} else {
			$('#partsUomName_status').html("");
			return false;
		}
	}
	function validate() {
		var partsUomName_status = document.getElementById("partsUomName_status").innerHTML;
		if(partsUomName_status == "") {
			return true;
		} else {
	        return false;
		}
	}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>