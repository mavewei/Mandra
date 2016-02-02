<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/setadmin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'mod_department.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'mod_department.php');
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
$query = "SELECT *
			FROM
				tempSession
			WHERE
				emailAdd = '$login'";
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
			$deptId = $_GET['deptId'];
			$lastPage = $_SESSION['LAST_PAGE'];
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
						Session time out time 5min.
					**/
					//$_SESSION['EXPIRETIME'] = time() + 300;
					$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
				};
			};
			/**
				Select department lists.
	   		**/
	   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	   		$query = "SELECT * FROM departments WHERE deptId = '$deptId'";
	   		$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$data = mysql_fetch_array($result);
			$deptCode = $data['deptCode'];
			$deptName = $data['deptName'];
			if(isset($_POST['deptCode']) && isset($_POST['deptName'])) {
				$deptId = $_POST['deptId'];
				$deptCode = ucwords(mysql_escape_string($_POST['deptCode']));
				$deptName = ucwords(mysql_escape_string($_POST['deptName']));
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				$query = "UPDATE departments
							SET
								deptCode = '$deptCode', deptName = '$deptName'
							WHERE
								deptId = '$deptId'";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					/**
						Department information updated and redirected to previous page.
					**/
					$_SESSION['STATUS'] = 20;
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
				<h1>Modify Department <small>modify department information</small></h1>
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
					Modify Department
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-usereg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Modify Department</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="" method="post" onsubmit="return validate()">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Department ID</label>
														<input type="text" class="form-control input-lg" style="text-align: center" name="deptId" value="<?php echo $deptId; ?>" readonly>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Created By</label>
														<input type="text" class="form-control input-lg" name="uid" style="text-align: center" value="<?php echo $fname; ?>" readonly>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Code</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-code"></i>
															<input type="text" class="form-control input-lg" id="deptCode" placeholder="Department Code" name="deptCode" value="<?php echo $deptCode; ?>" onkeyup="checkDeptCode();" autofocus="on" required>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building-o"></i>
															<input type="text" class="form-control input-lg" placeholder="Department Name" name="deptName" value="<?php echo $deptName; ?>" required>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<span class="error-status" id="deptCode_status"></span>
												</div>
												<div class="col-md-6"></div>
											</div>
										</div>
										<div class="form-actions">
											<input type="submit" value="Update" class="btn blue">
											<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Close</button></a>
											<!--
											<label>or</label>
											<button type="button" class="btn red">Delete</button> -->
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
function checkDeptCode() {
	var deptCode = document.getElementById("deptCode").value;
	if(deptCode) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				deptCode: deptCode,
			},
			success: function (response) {
				if(response == "true") {
					$('#deptCode_status').html("");
					return true;
				} else {
					$('#deptCode_status').html(response);
					return false;
               }
            }
		});
	} else {
		$('#deptCode_status').html("");
		return false;
	}
}
function validate() {
	var deptCode_status=document.getElementById("deptCode_status").innerHTML;
	if(deptCode_status =="") {
		return true;
	} else {
          return false;
	}
}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>