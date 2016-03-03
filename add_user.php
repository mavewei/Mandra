<?php include('pages/page_header.php'); ?>
<link href="css/users.css" rel="stylesheet" type="text/css" />
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
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				$_SESSION['LAST_PAGE'] = 'add_user.php';
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
				// Select departments lists.
				//mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$queryDept = "SELECT * FROM departments WHERE status = 'Active' ORDER BY deptName ASC";
				$resultDept = mysql_query($queryDept);
				$rowDept = mysql_num_rows($resultDept);
				if(!$resultDept) die ("Table access failed: " . mysql_error());
				// Select position.
				$queryPosition = "SELECT * FROM position WHERE status = 'Active' ORDER BY positionName ASC";
				$resultPosition = mysql_query($queryPosition);
				$rowPosition = mysql_num_rows($resultPosition);
				if(!$resultPosition) die ("Table access failed: " . mysql_error());
				// $role = $_SESSION['ROLE'];
				if(isset($_POST['fname']) && isset($_POST['lname'])) {
					$fname = ucwords(mysql_real_escape_string($_POST['fname']));
					$lname = ucwords(mysql_escape_string($_POST['lname']));
					$login = strtolower(mysql_escape_string($_POST['login']));
					$departments = mysql_escape_string($_POST['departments']);
					$position = mysql_escape_string($_POST['position']);
					$role = mysql_escape_string($_POST['roles']);
					// Get the gid from tables
					//$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
					if($role == "Managers") {
						$query = "SELECT * FROM userAccounts WHERE roles = '$role' ORDER BY gid DESC LIMIT 1";
						$result = mysql_query($query);
						$row = mysql_num_rows($result);
						if(!$result) die ("Table access failed: " . mysql_error());
						if($row == 0) {
							$gid = 1001;
						} else {
							$row = mysql_fetch_array($result);
							$gid = $row['gid'];
							$gid++;
						}
					} elseif($role == "Systems") {
						$query = "SELECT * FROM userAccounts WHERE roles = '$role' ORDER BY gid DESC LIMIT 1";
						$result = mysql_query($query);
						$row = mysql_num_rows($result);
						if(!$result) die ("Table access failed: " . mysql_error());
						if($row == 0) {
							$gid = 2001;
						} else {
							$row = mysql_fetch_array($result);
							$gid = $row['gid'];
							$gid++;
						}
					} elseif($role == "Users") {
						$query = "SELECT * FROM userAccounts WHERE roles = '$role' ORDER BY gid DESC LIMIT 1";
						$result = mysql_query($query);
						$row = mysql_num_rows($result);
						if(!$result) die ("Table access failed: " . mysql_error());
						if($row == 0) {
							$gid = 3001;
						} else {
							$row = mysql_fetch_array($result);
							$gid = $row['gid'];
							$gid++;
						}
					}
					$passwd = md5(mysql_escape_string($_POST['passwd']));
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "INSERT INTO userAccounts(dateTime, gid, firstName, lastName, emailAdd, departments,
									position, roles, passwd, status, sessionTimeout)
								VALUES('$time', '$gid', '$fname', '$lname', '$login', '$departments', '$position', '$role',
									'$passwd', 'Active', 600)";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// User created and redirected to dashboard.
						/*
						echo '<script type="text/javascript">';
						echo 'submitUser();';
						echo '</script>';
						*/
						$_SESSION['STATUS'] = 3;
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
				<h1>User Accounts Registration <small>register user accounts</small></h1>
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
					<a href="javascript:;">Users</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">All Users</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Registration
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">User Account Registration</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="add_user.php" method="post" onsubmit="return validate();">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>First Name</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-user"></i>
													<input type="text" class="form-control input-lg" placeholder="First Name" name="fname" autofocus="on" required>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Last Name</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-user"></i>
													<input type="text" class="form-control input-lg" placeholder="Last Name" name="lname" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label>Email Address (Login ID)</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-envelope-o"></i>
													<input type="email" class="form-control input-lg" placeholder="Email Address" name="login" id="emailAdd" onkeyup="checkEmail();" autocomplete="on" required>
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Roles</label>
												<select name="roles" class="form-control input-lg" required>
													<option value="">Select Roles</option>
													<option value="Users">Users</option>
													<option value="Systems">Systems</option>
													<option value="Managers">Managers</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Departments <!-- <small class="dept-not-found">Create <a href="add_department.php">here!</a></small> --></label>
												<select name="departments" class="form-control input-lg" required>
													<?php
														if($rowDept < 1) {
															/**
																No departments were created.
															**/
															echo "<option value=''>No Department Found</option>";
														} else {
															/**
																Found departments lists.
															**/
															echo "<option value=''>Select Department</option>";
															for($i = 0; $i < $rowDept; ++$i) {
																$deptCode = mysql_result($resultDept, $i, 'deptCode');
																echo "<option value='$deptCode'>$deptCode</option>";
															}
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Position</label>
												<select name="position" class="form-control input-lg" required>
													<?php
														if($rowPosition < 1) {
															/**
																No departments were created.
															**/
															echo "<option value=''>No Position Found</option>";
														} else {
															/**
																Found departments lists.
															**/
															echo "<option value=''>Select Position</option>";
															for($i = 0; $i < $rowPosition; ++$i) {
																$positionName = mysql_result($resultPosition, $i, 'positionName');
																echo "<option value='$positionName'>$positionName</option>";
															}
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Password</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-key"></i>
													<input type="password" class="form-control input-lg" placeholder="Password" name="passwd" id="oriPass" required>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Confirm Password</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-key"></i>
													<input type="password" class="form-control input-lg" placeholder="Confirm Password" id="cmpPass" onchange="checkPasswordMatch()" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="emailAdd_status"></span>
											<span class="error-status" id="passwd_status"></span>
										</div>
										<div class="col-md-6"></div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input id="submitUser" type="submit" value="Submit" class="btn blue">
									<a href="users.php"><button type="button" class="btn default">Cancel</button></a>
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
	function checkEmail() {
		var emailAdd = document.getElementById("emailAdd").value;
		if(emailAdd) {
			$.ajax({
				type: 'post',
				url: 'check_data.php',
				data: {
					emailAdd: emailAdd,
				},
				success: function (response) {
					if(response == "true") {
						$('#emailAdd_status').html("");
						return true;
					} else {
						$('#emailAdd_status').html(response);
						return false;
	               }
	            }
			});
		} else {
			$('#emailAdd_status').html("");
			return false;
		}
	}
	function checkPasswordMatch() {
		var oriPass = document.getElementById("oriPass").value;
		var cmpPass = document.getElementById("cmpPass").value;
	    if (oriPass != cmpPass)
	    		document.getElementById("passwd_status").innerHTML = "[Error] Password not match!";
	    else
	        document.getElementById("passwd_status").innerHTML = "";
	}

	$(document).ready(function() {
		$("#cmpPass").keyup(checkPasswordMatch);
	});

	function validate() {
		var emailAdd_status=document.getElementById("emailAdd_status").innerHTML;
		var passwd_status=document.getElementById("passwd_status").innerHTML;
		if(emailAdd_status =="" && passwd_status == "") {
			return true;
		} else {
	          return false;
		}
	}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>