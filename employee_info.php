<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, 'employee_info.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'employee_info.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 3000) {
		$fname = $_SESSION['FNAME'];
		$id = $_GET['uid'];
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
		/**
		 Select employee information.
   		**/
   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
   		$query = "SELECT * from employees WHERE id = '$id'";
		$result = mysql_query($query);
		$row = mysql_num_rows($result);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($row > 0) {
			$data = mysql_fetch_array($result);
			$empName = $data['empName'];
			$empSex = $data['empSex'];
			$empBirth = $data['empBirth'];
			$empNationality = $data['empNationality'];
			$empCounty = $data['empCounty'];
			$empDateJoin = $data['empDateJoin'];
			$empSource = $data['empSource'];
			$empCategory = $data['empCategory'];
			$empCompanyCode = $data['empCompanyCode'];
			$empDepartment = $data['empDepartment'];
			$empUnit = $data['empUnit'];
			$empPosition = $data['empPosition'];
			$empBasicSalary = $data['empBasicSalary'];
			$empTaxCode = $data['empTaxCode'];
		}
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
				<h1>Employee Information <small>employee information</small></h1>
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
					Employee Information
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-add-employee">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Employee Information</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="" method="post" onsubmit="return cmpPasswd()">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-user"></i>
															<input type="text" class="form-control input-lg" placeholder="Name" name="empName" value="<?php echo $empName; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>Sex</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-user"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empSex; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Date of Birth</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-calendar-check-o"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empBirth; ?>">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Nationality</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-flag"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empNationality; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>County / State</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-flag"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empCounty; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Date of Join</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-calendar-check-o"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empDateJoin; ?>">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Source</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empSource; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Category</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empCategory; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Company Code</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empCompanyCode; ?>">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Department</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empDepartment; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Unit</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-user-secret"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empUnit; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Position</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-user-md"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empPosition; ?>">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Basic Salary</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-money"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empBasicSalary; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Tax Code</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-credit-card"></i>
															<input type="text" class="form-control input-lg" value="<?php echo $empTaxCode; ?>">
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-actions">
											<!-- <input type="submit" value="Submit" class="btn blue" onclick="countrySelect()"> -->
											<!-- <button type="submit" class="btn blue">Submit</button> -->
											<a href="employees.php"><button type="button" class="btn default">Close</button></a>
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
<script>
function countrySelect() {
	var ddl = document.getElementById("empCountry");
	var selectedValue = ddl.options[ddl.selectedIndex].value;
	if(selectedValue == "Select Country") {
		alert("[ERROR] Please select country!");
	}
}
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
<?php include('pages/page_footer.php'); ?>
<?php include('pages/page_jquery.php'); ?>
</body>
</html>