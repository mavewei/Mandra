<?php include('pages/page_header.php'); ?>
<link href="css/add-mod-status.css" rel="stylesheet" type="text/css" />
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
				$lastPage = $_SESSION['LAST_PAGE'];
				//$_SESSION['LAST_PAGE'] = 'add_department.php';
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
				// Select status lists.
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * FROM status ORDER BY id DESC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($row == 0) {
					$statusId = 'S01';
				} else {
					$row++;
					$statusId = 'S' . sprintf('%02d', $row);
				}
				if(isset($_POST['statusId']) && isset($_POST['statusName'])) {
					$statusId = $_POST['statusId'];
					$statusName = ucwords(mysql_escape_string($_POST['statusName']));
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "INSERT INTO status(dateTime, statusId, statusName, status)
								VALUES('$time', '$statusId', '$statusName', 'Active')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// status created and redirected to previous page.
						$_SESSION['STATUS'] = 29;
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
				<h1>Add Status <small>add new status details</small></h1>
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
					<a href="javascript:;">General</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Settings</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Add Status
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Status</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post" onsubmit="return validate()">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Status ID</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="statusId" value="<?php echo $statusId; ?>" readonly>
											</div>
										</div>
										<div class="col-md-8">
											<div class="form-group">
												<label>Name</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-code"></i>
													<input type="text" class="form-control input-lg" placeholder="Status Name" id="statusName" name="statusName" onkeyup="checkStatusName();" autofocus="on" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="statusName_status"></span>
										</div>
										<div class="col-md-6"></div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Submit" class="btn blue">
									<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Cancel</button></a>
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
	function checkStatusName() {
		var statusName = document.getElementById("statusName").value;
		if(statusName) {
			$.ajax({
				type: 'post',
				url: 'check_data.php',
				data: {
					statusName: statusName,
				},
				success: function (response) {
					if(response == "true") {
						$('#statusName_status').html("");
						return true;
					} else {
						$('#statusName_status').html(response);
						return false;
	               }
	            }
			});
		} else {
			$('#statusName_status').html("");
			return false;
		}
	}
	function validate() {
		var statusName_status = document.getElementById("statusName_status").innerHTML;
		if(statusName_status == "") {
			return true;
		} else {
	        return false;
		}
	}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>