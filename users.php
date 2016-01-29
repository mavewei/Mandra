<?php include('pages/page_header.php'); ?>
<!-- <link href="css/center.css" rel="stylesheet" type="text/css" /> -->
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/center.css" rel="stylesheet" type="text/css">
<link href="css/signin.css" rel="stylesheet" type="text/css">
<!-- <link href="css/setadmin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'users.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'users.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 4000) {
		$fname = $_SESSION['FNAME'];
		// $role = $_SESSION['ROLE'];
		/**
		 Lifetime added 5min.
		 **/
		if(isset($_SESSION['EXPIRETIME'])) {
			if($_SESSION['EXPIRETIME'] < time()) {
				unset($_SESSION['EXPIRETIME']);
				header('Location: logout.php?TIMEOUT');
				exit(0);
			} else {
				$_SESSION['EXPIRETIME'] = time() + 300;
			};
		};
		$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		if($dbSelected) {
			$query = "SELECT userAccounts.dateTime as dateTime, gid, firstName, lastName, emailAdd, departments.deptName as departments, roles FROM userAccounts INNER JOIN departments on userAccounts.departments = departments.deptId WHERE gid > 1000 ORDER BY dateTime DESC";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$rows = mysql_num_rows($result);
		};
	} else {
		/**
		 Redirect to dashboard if not Superuser or Manager.
		 **/
		$_SESSION['STATUS'] = 10;
		header('Location: status.php');
	};
} else {
	header('Location: status.php');
};
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>All Users <small> all registered user accounts.</small></h1>
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
				<li class="active">
					All Users
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Users</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_user.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div class="table-scrollable table-scrollable-borderless">
								<table class="table table-hover table-light">
									<?php
									if($rows > 0) {
										echo "<thead><tr class='uppercase'><th colspan='2'>User</th>";
										echo "<th class='center'>Email Address</th>";
										echo "<th class='center'>Department</th><th class='center'>Role</th>";
										echo "<th class='center'>Date Created</th>";
										echo "<th class='center'>Status</th></tr></thead><tbody>";
										for($j = 0; $j < $rows; ++$j) {
											$firstname = ucfirst(mysql_result($result, $j, 'firstName'));
											$emailaddress = mysql_result($result, $j, 'emailAdd');
											$departments = mysql_result($result, $j, 'departments');
											$roles = mysql_result($result, $j, 'roles');
											/*
											$job = ucfirst(mysql_result($result, $j, 'jobTitle'));
											if($job == null) {
												$job = "No record found!";
											}
											*/
											$string = mysql_result($result, $j, 'dateTime');
											if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
												$datejoin = $match[1];
											};
											echo "<tr><td class='fit'><img class='user-pic' src='images/user_unknown.png'></td><td><a href='javascript:;' class='primary-link'>$firstname</td>";
											echo "<td align='center'>$emailaddress</td><td align='center'>$departments</td><td align='center'>$roles</td><td align='center'>$datejoin</td><td align='center'><span class='bold theme-font'>Active</span></td></tr>";
										};
										echo "</tbody>";
									} else {
										/**
										 No users account.
										 **/
										echo "<div class='block' style='height:100%'><div class='centered-users'>";
										echo "<h3 class='no-users'> No users account found!</h3></tbody></div></div>";
									}
									?>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-1"></div>

			</div>
		</div>
	</div>
</div>
<?php include('pages/page_footer.php'); ?>
<?php include('pages/page_jquery.php'); ?>
</body>
</html>