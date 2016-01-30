<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/setadmin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'add_department.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'add_department.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 3000) {
		$fname = $_SESSION['FNAME'];
		$uid = $_SESSION['UID'];
		$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
		$lastPage = $_SESSION['LAST_PAGE'];
		//$_SESSION['LAST_PAGE'] = 'add_department.php';
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
		 Select departments lists.
   		**/
   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		$query = "SELECT * from departments ORDER BY id DESC";
		$result = mysql_query($query);
		$row = mysql_num_rows($result);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($row == 0) {
			$deptId = 'D01';
		} else {
			if($row < 9) {
				$row++;
				$deptId = 'D0' . $row;
			} else {
				$row++;
				$deptId = 'D' . $row;
			}
		}

		if(isset($_POST['deptCode']) && isset($_POST['deptName'])) {
			$deptId = $_POST['deptId'];
			$deptCode = ucfirst(mysql_escape_string($_POST['deptCode']));
			$deptName = ucwords(mysql_escape_string($_POST['deptName']));
			/**
			 Get the gid from tables
			 **/
			// $dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
			$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$time = $row['dateTime'];
			$query = "INSERT INTO departments (dateTime, deptId, deptCode, deptName, createdBy) VALUES('$time', '$deptId', '$deptCode', '$deptName', '$uid')";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			if($result) {
				/**
				 Department created and redirected to registration.php.
				 **/
				$_SESSION['STATUS'] = 13;
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
	header('Location: status.php');
};
?>
<?php include('pages/page_menu.php'); ?>

<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Add Department <small>add new department</small></h1>
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
					Add Department
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-usereg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Department</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="add_department.php" method="post" onsubmit="return validate()">
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
														<div class="input-icon input-icon-lg"><i class="fa fa-code"></i>
															<input type="text" class="form-control input-lg" placeholder="Department Code" id="deptCode" name="deptCode" onkeyup="checkDeptCode();" autofocus="on" required>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building-o"></i>
															<input type="text" class="form-control input-lg" placeholder="Department Name" name="deptName" required>
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
											<input type="submit" value="Submit" class="btn blue">
											<!-- <button type="submit" class="btn blue">Submit</button> -->
											<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Cancel</button></a>
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