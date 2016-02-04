<?php include('pages/page_header.php'); ?>
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/signin.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, 'changelog.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'changelog.php');
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
$query = "SELECT * FROM tempSession
			 WHERE emailAdd = '$login'";
$result = mysql_query($query);
if(!$result) die ("Table access failed: " . mysql_error());
$data = mysql_fetch_assoc($result);
$sid = $data['sid'];
/**
	Verify sid record with current sid.
**/
if($sid == $_SESSION['SID']) {
	if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
		$fname = $_SESSION['FNAME'];
		$role = $_SESSION['ROLE'];
		$email = $_SESSION['LOGIN_ID'];
		$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
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
	} else {
		unset($_SESSION['STATUS']);
		header('Location: status.php');
	}
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
				<h1>ChangeLog <small> system develop changelog.</small></h1>
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
					<a href="javascript:;">ChangeLog</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					ChangeLog
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-12">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">System ChangeLog</span>
							</div>
							<!--
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_employee.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							-->
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div id="slimScrollChangeLog">
								<h3><b>Version 1.0</b></h3>
								<h5 class="changelog-date">04 Feb, 2016</h5>
								<ul>
									<li class="changelog-li">Add Procurement parts master file page. Support add, modify and delete function.</li>
								</ul>
								<h5 class="changelog-date">03 Feb, 2016</h5>
								<ul>
									<li class="changelog-li">Fix Liberia county and remove Malaysia state.</li>
									<li class="changelog-li">Add jQuery Slimscroll function.</li>
									<li class="changelog-li">Add logout confirmation.</li>
									<li class="changelog-li">Add delete function on modification page.</li>
								</ul>
								<h5 class="changelog-date">02 Feb, 2016</h5>
								<ul>
									<li class="changelog-li">Fix multi-login problem. Each user only have one session.</li>
								</ul>
								<h5 class="changelog-date">30 Jan, 2016</h5>
								<ul>
									<li class="changelog-li">Add session timeout function. Logout user after period inactivity.</li>
									<li class="changelog-li">Add modification for company, department, tax code, unit and position information.</li>
								</ul>
								<h5 class="changelog-date">29 Jan, 2016</h5>
								<ul>
									<li class="changelog-li">Allow adding data to company, department, tax code, unit and position.</li>
									<li class="changelog-li">Superuser allow to backup, restore and destroy database.</li>
									<li class="changelog-li">Employees information can view under Hr Data section.</li>
									<li class="changelog-li">Browse all user accounts available.</li>
								</ul>
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
$(function() {
	$('#slimScrollChangeLog').slimScroll({
    		height: '375px'
	});
});
/**
   Bootbox alert customize.
**/
$(function() {
	$('.logoutAlert').click(function(){
		bootbox.confirm("Are you sure you want to LOGOUT?", function(result) {
			if(result) {
				window.location = "logout.php";
			}
		});
	})
})
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>