<?php include('pages/page_header.php'); ?>
<link href="css/users.css" rel="stylesheet" type="text/css" />
<link href="css/profile.css" rel="stylesheet" type="text/css" />
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
				$firstName = $_SESSION['FNAME'];
				$lastName = $_SESSION['LNAME'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				$_SESSION['LAST_PAGE'] = "users.php";
				$departments = $_SESSION['DEPARTMENT'];
				$position = $_SESSION['POSITION'];
				$role = $_SESSION['ROLE'];
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
				// Save and change user details.
				if($_GET['fname'] && $_GET['lname']) {
					modDetails($_GET['fname'], $_GET['lname']);
				}
				// Form submit.
				/*
				if(isset($_POST['firstName']) && isset($_POST['lastName'])) {
					$firstName = ucwords(strtolower(mysql_escape_string($_POST['firstName'])));
					$lastName = ucwords(strtolower(mysql_escape_string($_POST['lastName'])));
					$query = "UPDATE userAccounts SET firstName = '$firstName', lastName = '$lastName'
								 WHERE emailAdd = '$login'";
					$result = mysql_query($query);
					if(!$result) {
						die ("Table access failed: " . mysql_error());
					} else {
						$_SESSION['FNAME'] = $firstName;
						$_SESSION['LNAME'] = $lastName;
					}
				}
				*/
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
	function modDetails($firstName, $lastName) {
		global $login;
		$firstName = ucwords(strtolower(mysql_escape_string($firstName)));
		$lastName = ucwords(strtolower(mysql_escape_string($lastName)));
		$query = "UPDATE userAccounts SET firstName = '$firstName', lastName = '$lastName'
					 WHERE emailAdd = '$login'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if(!$result) {
			die ("Table access failed: " . mysql_error());
		} else {
			$_SESSION['FNAME'] = $firstName;
			$_SESSION['LNAME'] = $lastName;
			header('Location: dashboard.php');
		}
	}
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Profile <small>overview & account setting.</small></h1>
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
					Profile
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-12">
					<div class="profile-sidebar" style="width:250px;">
						<div class="portlet light profile-sidebar-portlet">
							<div class="profile-userpic"><img src="images/user_unknown.png" class="img-responsive" alt /></div>
							<div class="profile-usertitle">
								<div class="profile-usertitle-name"><?php echo $_SESSION['FNAME']; ?></div>
								<div class="profile-usertitle-job"><?php echo $role; ?></div>
							</div>
							<div class="profile-userbuttons">
								<button type="button" class="btn btn-circle btn-danger btn-sm" disabled>Message</button>
							</div>
							<div class="profile-usermenu">
								<ul class="nav">
									<!--
									<li class="active">
										<a href="javascript:;"><i class="glyphicon glyphicon-home"></i> Overview </a>
									</li>
									-->
									<li class="active">
										<a href="usr.profile.php"><i class="glyphicon glyphicon-cog"></i> Account Settings </a>
									</li>
									<li>
										<a href="javascript:;"><i class="glyphicon glyphicon-info-sign"></i> Help </a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="profile-content">
						<div class="row">
							<div class="col-md-12">
								<div class="portlet light">
									<div class="portlet-title tabbable-line">
										<div class="caption caption-md"><span class="caption-subject font-blue-madison bold uppercase">Account Settings</span></div>
										<ul class="nav nav-tabs">
											<li class="active">
												<a href="#personal_info" data-toggle="tab">Personal Info</a>
											</li>
											<li>
												<a href="#change_passwd" data-toggle="tab">Change Password</a>
											</li>
										</ul>
									</div>
									<div class="portlet-body">
										<div class="tab-content">
											<div class="tab-pane active" id="personal_info">
												<!-- <form role="form"> -->
													<div class="form-group">
														<label class="control-label">First Name</label>
														<input id="firstName" name="firstName" type="text" class="form-control" value="<?php echo $_SESSION['FNAME']; ?>" required />
													</div>
													<div class="form-group">
														<label class="control-label">Last Name</label>
														<input id="lastName" name="lastName" type="text" class="form-control" value="<?php echo $_SESSION['LNAME']; ?>" required />
													</div>
													<!--
													<div class="form-group">
														<label class="control-label">Mobile Number</label>
														<input type="text" class="form-control" name="mobileNumber" value="<?php if($mobilenum != null) { echo $mobilenum; } else { echo "No Record Found!"; }; ?>" />
													</div>
													-->
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label">Email Address</label>
																<input type="text" class="form-control" name="emailAdd" disabled value="<?php echo $login; ?>" />
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label">Department</label>
																<input type="text" class="form-control" name="departments" disabled value="<?php echo $departments; ?>" />
															</div>
														</div>

													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label">Position</label>
																<input type="text" class="form-control" name="position" value="<?php echo $position; ?>" disabled />
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label">Roles</label>
																<input type="text" class="form-control" name="roles" value="<?php echo $role; ?>" disabled />
															</div>
														</div>
													</div>
													<div class="margin-top-30">
														<input type="button" id="detailsSubmit" value="Save Changes" class="btn green-haze" onclick="modDetails();" />
													</div>
												<!-- </form> -->
											</div>
											<div class="tab-pane" id="change_passwd">
												<!-- <form action=""> -->
													<div class="form-group">
														<label class="control-label">Current Password</label>
														<input id="currentPasswd" name="currentPasswd" type="password" class="form-control" placeholder="Current Password" />
													</div>
													<div class="form-group">
														<label class="control-label">New Password</label>
														<input id="newPasswd" name="newPasswd" type="password" class="form-control" placeholder="New Passwotd" />
													</div>
													<div class="form-group">
														<label class="control-label">Re-type New Password</label>
														<input id="confirmPasswd" type="password" class="form-control" placeholder="Re-type New Password"  />
													</div>
													<div class="form-group">
														<span class="error-status" id="passwd_status"></span>
														<input type="hidden" id="emailAdd" value="<?php echo $login; ?>">
													</div>
													<div class="margin-top-30">
														<input type="button" value="Changes Password" class="btn green-haze" onclick="modPasswd();" />
													</div>
												<!-- </form> -->
											</div>
										</div>
									</div>
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
	// Compare both password.
	function checkPasswordMatch() {
		var newPasswd = document.getElementById("newPasswd").value;
		var confirmPasswd = document.getElementById("confirmPasswd").value;
	    if(newPasswd != confirmPasswd)
	    		document.getElementById("passwd_status").innerHTML = "[Error] Password not match!";
	    else
	        document.getElementById("passwd_status").innerHTML = "";
	}
	$(document).ready(function() {
		$("#confirmPasswd").keyup(checkPasswordMatch);
	});
	function modPasswd() {
		var passwd_status=document.getElementById("passwd_status").innerHTML;
		if(passwd_status == "") {
			var emailAdd = document.getElementById("emailAdd").value;
			var currentPasswd = document.getElementById("currentPasswd").value;
			var newPasswd = document.getElementById("newPasswd").value;
			if(newPasswd != "") {
				alertify.confirm("[UPDATE]  Are you sure you want to change password?", function(result) {
					if(result) {
						$.ajax({
							type: 'post',
							url: 'check_data.php',
							dataType: 'text',
							data: {
								emailAddP: emailAdd,
								currentPasswd: currentPasswd,
								newPasswd: newPasswd,
							},
							success: function (response) {
								if(response == "true") {
									alert("[SUCCESS] Password changed! Please re-login again.");
									window.location = "logout.php";
								} else {
									alert("[ERROR] Password not change! Please enter correct password.");
									$('#currentPasswd').val("");
									$('#newPasswd').val("");
									$('#confirmPasswd').val("");
				               }
				            }
						});
					}
				});
			} else {
				alert("[ERROR] Empty password are not allowed!");
			}
		} else {
	          return false;
		}
	}
	// Update user details.
	function modDetails() {
		firstName = $('#firstName').val();
		lastName = $('#lastName').val();
		alertify.confirm("[UPDATE]  Are you sure you want to save the changes?", function(result) {
			if(result) {
				window.location = "usr.profile.php?fname=" + firstName + "&lname=" + lastName;
			}
		});
	}
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
<? include('pages/page_footer.php'); ?>
</body>
</html>