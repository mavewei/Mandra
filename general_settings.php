<?php include('pages/page_header.php'); ?>
<!-- <link href="css/center.css" rel="stylesheet" type="text/css" /> -->
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/center.css" rel="stylesheet" type="text/css">
<link href="css/signin.css" rel="stylesheet" type="text/css">
<!-- <link href="css/setadmin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'general_settings.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'general_settings.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 4000) {
		$fname = $_SESSION['FNAME'];
		$_SESSION['LAST_PAGE'] = "general_settings.php";
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
			$query = "SELECT * FROM company ORDER BY id DESC";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$rows = mysql_num_rows($result);
			$queryDept = "SELECT * FROM departments ORDER BY id DESC";
			$resultDept = mysql_query($queryDept);
			if(!$resultDept) die ("Table access failed: " . mysql_error());
			$rowsDept = mysql_num_rows($resultDept);
			$queryUnit = "SELECT * FROM unit ORDER BY id DESC";
			$resultUnit = mysql_query($queryUnit);
			if(!$resultUnit) die ("Table access failed: " . mysql_error());
			$rowsUnit = mysql_num_rows($resultUnit);
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
				<h1>General Settings <small> setup initial information and data.</small></h1>
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
				<li class="active">
					Settings
				</li>
			</ul>
			<div class="row margin-to-10">
				<div class="portlet light">
					<div class="row">
						<div class="col-md-6">
							<div class="portlet-title">
								<div class="caption">
									<span class="caption-subjet font-green-sharp bold uppercase">Company</span>
								</div>
								<div class="actions btn-set">
									<a class="btn green-haze btn-circle" href="add_company.php"><i class="fa fa-plus"></i> Add </a>
								</div>
								<div class="tools"></div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
										if($rows > 0) {
											echo "<thead><tr class='uppercase'><th class='left'>Code</th>";
											echo "<th class='center'>Id</th><th class='left'>Name</th>";
											echo "<th class='center'>Location</th><th class='center'>Date Created</th>";
											echo "</tr></thead><tbody>";
											for($j = 0; $j < $rows; ++$j) {
												$comCode = mysql_result($result, $j, 'comCode');
												$comId = mysql_result($result, $j, 'comId');
												$comName = mysql_result($result, $j, 'comName');
												$comLocation = mysql_result($result, $j, 'comLocation');
												$string = mysql_result($result, $j, 'dateTime');
												if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
													$datejoin = $match[1];
												};
												// $createdBy = mysql_result($result, $j, 'createdBy');
												/*
												$job = ucfirst(mysql_result($result, $j, 'jobTitle'));
												if($job == null) {
													$job = "No record found!";
												}
												*/
												echo "<tr><td align='left'><a href='javascript:;' class='primary-link'>$comCode</td><td align='center'>$comId</td><td align='left'>$comName</td><td align='center'>$comLocation</td><td align='center'>$datejoin</td></tr>";
											};
											echo "</tbody>";
										} else {
											/**
											 No users account.
											 **/
											echo "<div class='block' style='height:100%'><div class='centered-users'>";
											echo "<h3 class='no-users'> No company infor. found!</h3></tbody></div></div>";
										}
										?>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="portlet-title">
								<div class="caption">
									<span class="caption-subjet font-green-sharp bold uppercase">Department</span>
								</div>
								<div class="actions btn-set">
									<a class="btn green-haze btn-circle" href="add_department.php"><i class="fa fa-plus"></i> Add </a>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
										if($rowsDept > 0) {
											echo "<thead><tr class='uppercase'><th class='center'>Code</th>";
											//echo "<thead><tr class='uppercase'><th colspan='2'>Code</th>";
											echo "<th class='center'>Id</th><th class='center'>Name</th>";
											echo "<th class='center'>Date Created</th></tr></thead><tbody>";
											for($j = 0; $j < $rowsDept; ++$j) {
												$deptCode = mysql_result($resultDept, $j, 'deptCode');
												$deptId = mysql_result($resultDept, $j, 'deptId');
												$deptName = mysql_result($resultDept, $j, 'deptName');
												$string = mysql_result($resultDept, $j, 'dateTime');
												if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
													$datejoin = $match[1];
												};
												// $createdBy = mysql_result($result, $j, 'createdBy');
												/*
												$job = ucfirst(mysql_result($result, $j, 'jobTitle'));
												if($job == null) {
													$job = "No record found!";
												}
												*/
												echo "<tr><td align='center'><a href='javascript:;' class='primary-link'>$deptCode</td><td align='center'>$deptId</td><td align='center'>$deptName</td><td align='center'>$datejoin</td></tr>";
													//echo "<tr><td class='fit'><img class='user-pic' src='images/user_unknown.png'></td><td><a href='javascript:;' class='primary-link' style='text-align: left'>$deptCode</td><td align='center'>$deptId</td><td align='center'>$deptName</td><td align='center'>$datejoin</td></tr>";
											};
											echo "</tbody>";
										} else {
											/**
											 No users account.
											 **/
											echo "<div class='block' style='height:100%'><div class='centered-users'>";
											echo "<h3 class='no-users'> No department infor. found!</h3></tbody></div></div>";
										}
										?>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="row margin-top-30">
						<div class="col-md-6">
							<div class="portlet-title">
								<div class="caption">
									<span class="caption-subjet font-green-sharp bold uppercase">Unit</span>
								</div>
								<div class="actions btn-set">
									<a class="btn green-haze btn-circle" href="add_department.php"><i class="fa fa-plus"></i> Add </a>
								</div>
							</div>
							<div class="portlet-body">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
										if($rowsUnit > 0) {
											echo "<thead><tr class='uppercase'><th class='center'>ID</th>";
											echo "<th class='center'>Name</th><th class='center'>Date Created</th>";
											echo "</tr></thead><tbody>";
											for($j = 0; $j < $rowsUnit; ++$j) {
												$unitId = mysql_result($resultUnit, $j, 'unitId');
												$unitName = mysql_result($resultUnit, $j, 'unitName');
												$string = mysql_result($resultUnit, $j, 'dateTime');
												if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
													$datejoin = $match[1];
												};
												// $createdBy = mysql_result($result, $j, 'createdBy');
												/*
												$job = ucfirst(mysql_result($result, $j, 'jobTitle'));
												if($job == null) {
													$job = "No record found!";
												}
												*/
												echo "<tr><td align='center'><a href='javascript:;' class='primary-link'>$unitId</td><td align='center'>$unitName</td><td align='center'>$datejoin</td></tr>";
													//echo "<tr><td class='fit'><img class='user-pic' src='images/user_unknown.png'></td><td><a href='javascript:;' class='primary-link' style='text-align: left'>$deptCode</td><td align='center'>$deptId</td><td align='center'>$deptName</td><td align='center'>$datejoin</td></tr>";
											};
											echo "</tbody>";
										} else {
											/**
											 No users account.
											 **/
											echo "<div class='block' style='height:100%'><div class='centered-users'>";
											echo "<h3 class='no-users'> No unit infor. found!</h3></tbody></div></div>";
										}
										?>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="portlet-title">
									<div class="caption">
										<span class="caption-subjet font-green-sharp bold uppercase">Department</span>
									</div>
									<div class="actions btn-set">
										<a class="btn green-haze btn-circle" href="add_department.php"><i class="fa fa-plus"></i> Add </a>
									</div>
								</div>
								<div class="portlet-body">
									<div class="table-scrollable table-scrollable-borderless">
										<table class="table table-hover table-light">
											<?php
											if($rowsDept > 0) {
												echo "<thead><tr class='uppercase'><th class='center'>Code</th>";
												//echo "<thead><tr class='uppercase'><th colspan='2'>Code</th>";
												echo "<th class='center'>Id</th><th class='center'>Name</th>";
												echo "<th class='center'>Date Created</th></tr></thead><tbody>";
												for($j = 0; $j < $rowsDept; ++$j) {
													$deptCode = mysql_result($resultDept, $j, 'deptCode');
													$deptId = mysql_result($resultDept, $j, 'deptId');
													$deptName = mysql_result($resultDept, $j, 'deptName');
													$string = mysql_result($resultDept, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													// $createdBy = mysql_result($result, $j, 'createdBy');
													/*
													$job = ucfirst(mysql_result($result, $j, 'jobTitle'));
													if($job == null) {
														$job = "No record found!";
													}
													*/
													echo "<tr><td align='center'><a href='javascript:;' class='primary-link'>$deptCode</td><td align='center'>$deptId</td><td align='center'>$deptName</td><td align='center'>$datejoin</td></tr>";
													//echo "<tr><td class='fit'><img class='user-pic' src='images/user_unknown.png'></td><td><a href='javascript:;' class='primary-link' style='text-align: left'>$deptCode</td><td align='center'>$deptId</td><td align='center'>$deptName</td><td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
												 No users account.
												 **/
												echo "<div class='block' style='height:100%'><div class='centered-users'>";
												echo "<h3 class='no-users'> No department infor. found!</h3></tbody></div></div>";
											}
											?>
										</table>
									</div>
								</div>
						</div>
					</div>
				</div>
<!--
			<div class="block" style="height: 100%">
				<div class="centered-companyreg">
					<div class="row margin-top-10">
						<div class="col-md-1"></div>
						<div class="col-md-10">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<span class="caption-subjet font-green-sharp bold uppercase">Company</span>
									</div>
									<div class="actions btn-set">
										<a class="btn green-haze btn-circle" href="add_company.php"><i class="fa fa-plus"></i> Add </a>
									</div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body">
									<div class="table-scrollable table-scrollable-borderless">
										<table class="table table-hover table-light">
											<?php
											if($rows > 0) {
												echo "<thead><tr class='uppercase'><th class='left'>Code</th>";
												echo "<th class='center'>Id</th><th class='left'>Name</th>";
												echo "<th class='center'>Location</th><th class='center'>Date Created</th>";
												echo "</tr></thead><tbody>";
												for($j = 0; $j < $rows; ++$j) {
													$comCode = mysql_result($result, $j, 'comCode');
													$comId = mysql_result($result, $j, 'comId');
													$comName = mysql_result($result, $j, 'comName');
													$comLocation = mysql_result($result, $j, 'comLocation');
													$string = mysql_result($result, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													// $createdBy = mysql_result($result, $j, 'createdBy');
													/*
													$job = ucfirst(mysql_result($result, $j, 'jobTitle'));
													if($job == null) {
														$job = "No record found!";
													}
													*/
													echo "<tr><td align='left'><a href='javascript:;' class='primary-link'>$comCode</td><td align='center'>$comId</td><td align='left'>$comName</td><td align='center'>$comLocation</td><td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
												 No users account.
												 **/
												echo "<div class='block' style='height:100%'><div class='centered-users'>";
												echo "<h3 class='no-users'> No company infor. found!</h3></tbody></div></div>";
											}
											?>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-1"></div>
					</div>
					<div class="row margin-top-10">
						<div class="col-md-1"></div>
						<div class="col-md-10">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<span class="caption-subjet font-green-sharp bold uppercase">Department</span>
									</div>
									<div class="actions btn-set">
										<a class="btn green-haze btn-circle" href="add_department.php"><i class="fa fa-plus"></i> Add </a>
									</div>
								</div>
								<div class="portlet-body">
									<div class="table-scrollable table-scrollable-borderless">
										<table class="table table-hover table-light">
											<?php
											if($rowsDept > 0) {
												echo "<thead><tr class='uppercase'><th class='center'>Code</th>";
												//echo "<thead><tr class='uppercase'><th colspan='2'>Code</th>";
												echo "<th class='center'>Id</th><th class='center'>Name</th>";
												echo "<th class='center'>Date Created</th></tr></thead><tbody>";
												for($j = 0; $j < $rowsDept; ++$j) {
													$deptCode = mysql_result($resultDept, $j, 'deptCode');
													$deptId = mysql_result($resultDept, $j, 'deptId');
													$deptName = mysql_result($resultDept, $j, 'deptName');
													$string = mysql_result($resultDept, $j, 'dateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$datejoin = $match[1];
													};
													// $createdBy = mysql_result($result, $j, 'createdBy');
													/*
													$job = ucfirst(mysql_result($result, $j, 'jobTitle'));
													if($job == null) {
														$job = "No record found!";
													}
													*/
													echo "<tr><td align='center'><a href='javascript:;' class='primary-link'>$deptCode</td><td align='center'>$deptId</td><td align='center'>$deptName</td><td align='center'>$datejoin</td></tr>";
													//echo "<tr><td class='fit'><img class='user-pic' src='images/user_unknown.png'></td><td><a href='javascript:;' class='primary-link' style='text-align: left'>$deptCode</td><td align='center'>$deptId</td><td align='center'>$deptName</td><td align='center'>$datejoin</td></tr>";
												};
												echo "</tbody>";
											} else {
												/**
												 No users account.
												 **/
												echo "<div class='block' style='height:100%'><div class='centered-users'>";
												echo "<h3 class='no-users'> No department infor. found!</h3></tbody></div></div>";
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
-->



		</div>
	</div>
</div>
<?php include('pages/page_footer.php'); ?>
<?php include('pages/page_jquery.php'); ?>
</body>
</html>