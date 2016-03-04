<?php include('pages/page_header.php'); ?>
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
			if($_SESSION['GID'] < 4000) {
				$fname = $_SESSION['FNAME'];
				$uid = $_SESSION['UID'];
				$position = $_SESSION['POSITION'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				// Lifetime added 5min.
				if(isset($_SESSION['EXPIRETIME'])) {
					if($_SESSION['EXPIRETIME'] < time()) {
						unset($_SESSION['EXPIRETIME']);
						header('Location: logout.php?TIMEOUT');
						exit(0);
					} else {
						// Session time out time 5min.
						$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
					};
				};
				// Select position lists.
				$query = "SELECT * from position WHERE status = 'Active' ORDER BY positionName ASC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				// Form submited!
				if(isset($_POST['position'])) {
					$position = mysql_escape_string($_POST['position']);
					$query = "UPDATE userAccounts SET position = '$position' WHERE id = '$uid'";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// Position update!
						$_SESSION['STATUS'] = 44;
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
				<h1>Update User Profile <small>update user profile</small></h1>
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
				<li class="active">
					Update Position
				</li>
			</ul>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="alert alert-info" role="alert"><b>Note:</b> Your were redirect here because of incomplete user profile. Please update your user profile now. Thanks You.</div>
				</div>
				<div class="col-md-3"></div>
			</div>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Update Position</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label>Position</label>
												<select name="position" class="form-control input-lg" required>
													<?php
														if($row < 1) {
															// No position were created.
															echo "<option value=''>No Position Found</option>";
														} else {
															// Found position lists.
															echo "<option value=''>Select Position</option>";
															for($i = 0; $i < $row; ++$i) {
																$positionName = mysql_result($result, $i, 'positionName');
																echo "<option value='$positionName'>$positionName</option>";
															}
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-4"></div>
									</div>
								</div>
								<div class="form-actions">
									<input type="submit" value="Submit" class="btn blue">
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
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>