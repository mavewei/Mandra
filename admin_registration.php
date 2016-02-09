<?php include('pages/page_header.php'); ?>
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/setadmin.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, 'admin_registration.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'admin_registration.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['INIT'])) {
	if(isset($_POST['email'])) {
		$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		if($dbSelected) {
			/**
				Administrator account add to table
			**/
			$fname = ucwords(mysql_escape_string($_POST['fname']));
			$lname = ucfirst(mysql_escape_string($_POST['lname']));
			$email = mysql_escape_string($_POST['email']);
			$roles = "Superuser";
			$passwd = md5(mysql_escape_string($_POST['passwd']));
			$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$time = $row['dateTime'];
			$query = "INSERT INTO userAccounts
							(dateTime, gid, firstName, lastName, emailAdd, departments, roles, passwd, status, sessionTimeout)
						VALUES
							('$time', 1, '$fname', '$lname', '$email', 'Full', '$roles', '$passwd', 'Active', 900)";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			if($result) {
				/**
					Set initFLAG to status = 2
				**/
				$query = "UPDATE initFlag SET status = 2	WHERE id = 1";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					$_SESSION['STATUS'] = 1;
					header("Location: status.php");
				};
			};
		};
	};
} else {
	header('Location: logout.php');
}
?>
<div class="block" style="height:100%">
	<div class="centered-usereg">
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>
			<h2><strong>You're almost Done!</strong></h2>Please register an administrator account to complete system initialize. Be advised that this account are full privilege on system, please create strong pin and keep it secure.
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="portlet light">
					<div class="portlet-title">
						<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Administrator Account Setup</span></div>
						<div class="tools"></div>
					</div>
					<div class="portlet-body form">
						<form role="form" action="admin_registration.php" method="post" onsubmit="return cmpPasswd()">
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
									<div class="col-md-6">
										<div class="form-group">
											<label>Email Address (Login ID)</label>
											<div class="input-icon input-icon-lg"><i class="fa fa-envelope-o"></i>
												<input type="email" class="form-control input-lg" placeholder="Email Address" name="email" required>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Roles</label>
											<div class="input-icon input-icon-lg"><i class="fa fa-users"></i>
												<input type="text" class="form-control input-lg" name="roles" placeholder="Superuser" disabled>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Password</label>
											<div class="input-icon input-icon-lg"> <i class="fa fa-key"></i>
												<input type="password" class="form-control input-lg" placeholder="Password" name="passwd" id="oriPass" required>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Confirm Password</label>
											<div class="input-icon input-icon-lg"> <i class="fa fa-key"></i>
												<input type="password" class="form-control input-lg" placeholder="Confirm Password" id="cmpPass" required>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<input type="submit" value="Submit" class="btn blue">
								<!-- <button type="submit" class="btn blue">Submit</button> -->
								<!-- <button type="button" class="btn default">Cancel</button> -->
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function cmpPasswd() {
   	var oriPass = document.getElementById("oriPass").value;
	var cmpPass = document.getElementById("cmpPass").value;
	var ok = true;
	if (oriPass != cmpPass) {
       	alert("[ERROR] Please comfirm both password input are match!");
       	document.getElementById("oriPass").style.borderColor = "#E34234";
		document.getElementById("cmpPass").style.borderColor = "#E34234";
		ok = false;
	} else {};
	return ok;
};
</script>
<?php include('pages/page_jquery.php'); ?>
</body>
</html>