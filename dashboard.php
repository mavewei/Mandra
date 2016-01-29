<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/custom_metro.css" rel="stylesheet" type="text/css" /> -->
<!-- <link  href="css/plugins_metro.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'dashboard.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'dashboard.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	$fname = $_SESSION['FNAME'];
	$role = $_SESSION['ROLE'];
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
} else {
	header('Location: status.php');
}
?>
<?php include("pages/page_menu.php"); ?>

<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Dashboard <small>statistics & reports</small></h1>
			</div>
			<div class="page-toolbar">
				<!-- <div class="btn-group btn-theme-panel"><a href="settings.php" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="glyphicon glyphicon-cog"></i></a></div> -->
				<div class="btn-group btn-theme-panel"><a href="settings.php" class="btn"><i class="glyphicon glyphicon-cog"></i></a></div>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<!--
			<ul class="breadcrumb">
				<li>
					<a href="index.php">Home</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Dashboard
				</li>
			</ul>
			-->
			<div class="block" style="height: 100%">
				<div class="centered-index">
					<div class="row">
						<div class="col-md-12">
							<a href="javascript:;"><img src="images/rsz_studio_logo_tr.gif" alt /></a>
						</div>
						<!-- <div class="col-md-12"><a href="login.php"><img src="images/index_hr.jpg" width="342" height="297" alt /></a></div> -->
						<div class="col-md-12">
							<h5><small>Please note: These system still in BETA testing. Use it wisely at your own risk! Copyright &copy; 2014-2019 by Ms Wan-Jin, Sio. All Rights Reserved.</small></h5>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('pages/page_footer.php'); ?>
<?php include('pages/page_jquery.php'); ?>
</body>
</html>