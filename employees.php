<?php include('pages/page_header.php'); ?>
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/center.css" rel="stylesheet" type="text/css">
<link href="css/signin.css" rel="stylesheet" type="text/css">
<script type = "text/javascript">
	history.pushState(null, null, 'employees.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'employees.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 4000) {
		$fname = $_SESSION['FNAME'];
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
			$query = "SELECT
   							employees.id AS id, employees.empName AS empName, employees.empSex AS empSex,
   							employees.empBirth AS empBirth, employees.empNationality AS empNationality,
   							employees.empCounty AS empCounty, employees.empDateJoin AS empDateJoin,
   							employees.empSource AS empSource,	company.comCode AS empCompanyCode,
   							departments.deptCode AS empDepartment, position.positionName AS empPosition
   						FROM
   							employees
   						INNER JOIN
   							company ON employees.empCompanyCode = company.comId
   						INNER JOIN
   							departments ON employees.empDepartment = departments.deptId
   						INNER JOIN
   								position ON employees.empPosition = position.positionId
   						ORDER BY
   							empName
   						ASC";
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
				<div class="col-md-12">
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
							<div class="table-scrollable table-scrollable-borderless">
								<table class="table table-hover table-light">
									<?php
									if($rows > 0) {
										echo "<thead><tr class='uppercase'><th class='th-width-18'>Name</th>";
										echo "<th class='center th-width-6'>Sex</th><th class='center th-width-9'>Birth</th>";
										echo "<th class='center th-width-10'>Nationality</th>";
										echo "<th class='center th-width-10'>County</th>";
										echo "<th class='center th-width-9'>Date Join</th><th class='center th-width-8'>Source</th>";
										echo "<th class='center th-width-8'>Company</th>";
										echo "<th class='center th-width-10'>Department</th>";
										echo "<th class='center th-width-12'>Position</th>";
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
											$empCounty = mysql_result($result, $j, 'empCounty');
											$empDateJoin = mysql_result($result, $j, 'empDateJoin');
											$empSource = mysql_result($result, $j, 'empSource');
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
											echo "<td align='center'>$empSex</td><td align='center'>$empBirth</td><td align='center'>$empNationality</td><td align='center'>$empCounty</td><td align='center'>$empDateJoin</td><td align='center'>$empSource</td><td align='center'>$empCompanyCode</td><td align='center'>$empDepartment</td><td align='center'>$empPosition</td></tr>";
										};
										echo "</tbody>";
									} else {
										/**
										 No employee information.
										 **/
										echo "<div class='block' style='height:100%'><div class='centered-users'>";
										echo "<h3 class='no-users'> No employess information found!</h3></tbody></div></div>";
									}
									?>
								</table>
							</div>
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