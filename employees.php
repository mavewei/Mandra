<?php include('pages/page_header.php'); ?>
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/center.css" rel="stylesheet" type="text/css">
<link href="css/employees.css" rel="stylesheet" type="text/css">
<script type = "text/javascript">
	history.pushState(null, null, 'employees.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'employees.php');
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
	$query = "SELECT * FROM tempSession WHERE emailAdd = '$login'";
	$result = mysql_query($query);
	if(!$result) die ("Table access failed: " . mysql_error());
	$data = mysql_fetch_assoc($result);
	$sid = $data['sid'];
	if($sid == $_SESSION['SID']) {
		if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
			if($_SESSION['GID'] < 4000) {
				$fname = $_SESSION['FNAME'];
				$_SESSION['LAST_PAGE'] = 'employees.php';
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
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
						/**
							Session time out time 5min.
						**/
						//$_SESSION['EXPIRETIME'] = time() + 300;
						$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
					};
				};
				$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				if($dbSelected) {
					$query = "SELECT * FROM employees WHERE status = 'Active' ORDER BY empName ASC";
								/*
								INNER JOIN
									position ON employees.empPosition = position.positionId
								INNER JOIN
									status ON employees.empStatus = status.statusId
								*/
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					$rows = mysql_num_rows($result);
					/**
						Pagination
					**/
					/**
						number of rows to show per page.
					**/
					$rowsPerPage = 8;
					/**
						find out total pages
					**/
					$totalPages = ceil($rows / $rowsPerPage);
					/**
						get the current page or set a default
					**/
					if(isset($_GET['currentPage']) && is_numeric($_GET['currentPage'])) {
						/**
							cast var as int
						**/
						$currentPage = (int) $_GET['currentPage'];
					} else {
						/**
							default page num
						**/
						$currentPage = 1;
					}
					/**
						if current page is greater than total pages
					**/
					if($currentPage > $totalPages) {
						/**
							set current page to last page
						**/
						$currentPage = $totalPages;
					}
					/**
						if current page is less than first page
					**/
					if($currentPage < 1) {
						/**
							set current page to first page
						**/
						$currentPage = 1;
					}
					/**
						The offset of the list, based on current page
					**/
					$offset = ($currentPage - 1) * $rowsPerPage;
					/**
						get the info from the db
					**/
					$query = "SELECT * FROM employees WHERE status = 'Active' ORDER BY id ASC LIMIT $offset, $rowsPerPage";

					$result = mysql_query($query);
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
				<h1>Employees <small> employees data and informations.</small></h1>
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
					<a href="javascript:;">Human Resources</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">HR Data</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Employees
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Employees</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_employee.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<!-- <div id="slimScrollEmployees"> -->
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
										if($rows > 0) {
											echo "<thead><tr class='uppercase'><th class='th-width-26'>Name</th>";
											echo "<th class='center th-width-8'>Sex</th>";
											echo "<th class='center th-width-12'>Nationality</th>";
											echo "<th class='center th-width-24'>Company</th>";
											echo "<th class='center th-width-10'>Department</th>";
											echo "<th class='center th-width-20'>Position</th>";
											echo "</tr></thead><tbody>";
											/*
											echo "<thead><tr class='uppercase'><th colspan='2'>Name</th>";
											echo "<th class='center'>Sex</th><th class='center'>Birth</th>";
											echo "<th class='center'>Nationality</th><th class='center'>Country</th>";
											echo "<th class='center'>Date Join</th><th class='center'>Source</th>";
											echo "<th class='center'>Category</th><th class='center'>Company Code</th>";
											echo "<th class='center'>Department</th><th class='center'>Unit</th>";
											echo "<th class='center'>Position</th><th class='center'>Basic Salary</th>";
											echo "<th class='center'>Tax Code</th></tr></thead><tbody>";
											*/
											for($j = 0; $j < $rows; ++$j) {
												$id = mysql_result($result, $j, 'id');
												$empName = ucfirst(mysql_result($result, $j, 'empName'));
												$empSex = mysql_result($result, $j, 'empSex');
												$empBirth = mysql_result($result, $j, 'empBirth');
												$empNationality = mysql_result($result, $j, 'empNationality');
												//$empCounty = mysql_result($result, $j, 'empCounty');
												//$empDateJoin = mysql_result($result, $j, 'empDateJoin');
												$empStatus = mysql_result($result, $j, 'empStatus');
												// $empCategory = mysql_result($result, $j, 'empCategory');
												$empCompanyCode = mysql_result($result, $j, 'empCompanyCode');
												$empDepartment = mysql_result($result, $j, 'empDepartment');
												// $empUnit = mysql_result($result, $j, 'empUnit');
												$empPosition = mysql_result($result, $j, 'empPosition');
												// $empBasicSalary = mysql_result($result, $j, 'empBasicSalary');
												// $empTaxCode = mysql_result($result, $j, 'empTaxCode');
												/*
												$string = mysql_result($result, $j, 'dateTime');
												if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
													$datejoin = $match[1];
												};
												*/
												echo "<tr><td class='fit'><img class='user-pic' src='images/user_unknown.png'><a href='mod_employee.php?uid=$id' class='name-padding primary-link'>$empName</a></td>";
												echo "<td align='center'>$empSex</td><td align='center'>$empNationality</td><td align='center'>$empCompanyCode</td><td align='center'>$empDepartment</td><td align='center'>$empPosition</td></tr>";
											};
											echo "</tbody>";
										} else {
											/**
											 No employee information.
											 **/
											echo "<div class='block' style='height:100%'><div class='centered-users'>";
											echo "<h3 class='no-users'> No employess information found!</h3></div></div>";
										}
										?>
									</table>
									<nav style="text-align: center; <?php if($rows < 1) echo 'display: none' ?>">
										<ul class="pagination">
											<?php
											/**
												Pagination: http:\/\/www\.phpfreaks.com\/tutorial/basic-pagination
											**/
											if($currentPage == 1) {
												echo "<li class='disabled'>";
												echo "<a href='#' aria-label='Previous'";
												echo "<span aria-hidden='true'>&laquo;</span>";
												echo "</a></li>";
											} else {
												echo "<li>";
												echo "<a href='{$_SERVER['PHP_SELF']}?currentPage=1' aria-label='Previous'>";
												echo "<span aria-hidden='true'>&laquo;</span>";
												echo "</a></li>";
											}
											/**
												range of num links to show
											**/
											$range = 3;
											/**
												loop to show links to range of pages around current page
											**/
											for($x = ($currentPage - $range); $x < (($currentPage + $range) + 1); $x++) {
												/**
													if it's a valid page number...
												**/
												if(($x > 0) && ($x <= $totalPages)) {
													/**
														if we're on current page
													**/
													if($x == $currentPage) {
														/**
															'highlight' it but don't make a link
														**/
														echo "<li><a href='#'>$x</a></li>";
													} else {
														/**
															make it a link
														**/
														echo "<li><a href='{$_SERVER['PHP_SELF']}?currentPage=$x'>$x</a></li>";
													}
												}
											}
											echo "<li>";
											echo "<a href='{$_SERVER['PHP_SELF']}?currentPage=$totalPages'>&raquo;</a>";
											echo "</li>";
											?>
										</ul>
									</nav>
								</div>
							<!-- </div> -->
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
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
$(function(){
	var row = <?php echo $rows; ?>;
	if(row < 7) {	} else {
		$('#slimScrollEmployees').slimScroll({
	    		height: '335px'
    		});
	}
});
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>