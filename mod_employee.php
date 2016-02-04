<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, 'mod_employee.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'mod_employee.php');
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
		if($_SESSION['GID'] < 3000) {
			$fname = $_SESSION['FNAME'];
			$lastPage = $_SESSION['LAST_PAGE'];
			$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
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
					/**
						Session time out time 5min.
					**/
					//$_SESSION['EXPIRETIME'] = time() + 300;
					$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
				};
			};
			/**
				Remove record.
			**/
			if($_GET['delEmpId']) {
				$delEmpId = $_GET['delEmpId'];
				deleteRecord($delEmpId);
			}
			mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
			/**
				Select nationality lists.
	   		**/
	   		$queryNationality = "SELECT * from nationality ORDER BY nationalityName ASC";
			$resultNationality = mysql_query($queryNationality);
			$rowNationality = mysql_num_rows($resultNationality);
			if(!$resultNationality) die ("Table access failed: " . mysql_error());
			/**
				Select company lists.
	   		**/
	   		$queryCompany = "SELECT * from company WHERE status = 'Active' ORDER BY comId ASC";
			$resultCompany = mysql_query($queryCompany);
			$rowCompany = mysql_num_rows($resultCompany);
			if(!$resultCompany) die ("Table access failed: " . mysql_error());
			/**
				Select department lists.
	   		**/
	   		$queryDept = "SELECT * from departments WHERE status = 'Active' ORDER BY deptName ASC";
			$resultDept = mysql_query($queryDept);
			$rowDept = mysql_num_rows($resultDept);
			if(!$resultDept) die ("Table access failed: " . mysql_error());
			/**
				Select unit lists.
	   		**/
	   		$queryUnit = "SELECT * from unit WHERE status = 'Active' ORDER BY unitName ASC";
			$resultUnit = mysql_query($queryUnit);
			$rowUnit = mysql_num_rows($resultUnit);
			if(!$resultUnit) die ("Table access failed: " . mysql_error());
			/**
				Select position lists.
	   		**/
	   		$queryPosition = "SELECT * from position WHERE status = 'Active' ORDER BY positionName ASC";
			$resultPosition = mysql_query($queryPosition);
			$rowPosition = mysql_num_rows($resultPosition);
			if(!$resultPosition) die ("Table access failed: " . mysql_error());
			/**
				Select taxcode lists.
	   		**/
	   		$queryTaxCode = "SELECT * from taxCode WHERE status = 'Active' ORDER BY taxCodeId ASC";
			$resultTaxCode = mysql_query($queryTaxCode);
			$rowTaxCode = mysql_num_rows($resultTaxCode);
			if(!$resultTaxCode) die ("Table access failed: " . mysql_error());
			/**
				Select employee information.
	   		**/
	   		$query = "SELECT
	   						employees.id AS userId, employees.empId AS empId, employees.empName AS empName,
	   						employees.empSex AS empSex, employees.empBirth AS empBirth,
	   						employees.empNationality AS empNationality, employees.empCounty AS empCounty,
	   						employees.empDateJoin AS empDateJoin, employees.empSource AS empSource,
	   						employees.empCategory AS empCategory, company.comCode AS empCompanyCode,
	   						departments.deptCode AS empDepartment, unit.unitName AS empUnit,
	   						position.positionName AS empPosition, employees.empBasicSalary AS empBasicSalary,
	   						taxCode.taxCodeName AS empTaxCode
	   					FROM
	   						employees
	   					INNER JOIN
	   						company ON employees.empCompanyCode = company.comId
	   					INNER JOIN
	   						departments ON employees.empDepartment = departments.deptId
	   					INNER JOIN
	   						unit ON employees.empUnit = unit.unitId
	   					INNER JOIN
	   						position ON employees.empPosition = position.positionId
	   					INNER JOIN
	   						taxCode ON employees.empTaxCode = taxCode.taxCodeId
	   					WHERE
	   						employees.id = '$id'";
			$result = mysql_query($query);
			$row = mysql_num_rows($result);
			if(!$result) die ("Table access failed: " . mysql_error());
			if($row > 0) {
				$data = mysql_fetch_array($result);
				$empId = $data['empId'];
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
			/**
				Update employee information.
			**/
			if(isset($_POST['empName'])) {
				$userId = mysql_escape_string($_POST['userId']);
				$empName = ucwords(mysql_escape_string($_POST['empName']));
				$empSex = $_POST['empSex'];
				$empBirth = $_POST['empBirth'];
				$empNationality = $_POST['empNationality'];
				$empCounty = ucwords(mysql_escape_string($_POST['empCounty']));
				$empDateJoin = $_POST['empDateJoin'];
				$empSource = $_POST['empSource'];
				$empCategory = $_POST['empCategory'];
				$empCompanyCode = $_POST['empCompanyCode'];
				$empDepartment = $_POST['empDepartment'];
				$empUnit = $_POST['empUnit'];
				$empPosition = $_POST['empPosition'];
				$empBasicSalary = mysql_escape_string($_POST['empBasicSalary']);
				$empTaxCode = $_POST['empTaxCode'];
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				$query = "UPDATE
								employees
							SET
								empName = '$empName', empSex = '$empSex', empBirth = '$empBirth',
								empNationality = '$empNationality', empCounty = '$empCounty', empDateJoin = '$empDateJoin',
								empSource = '$empSource', empCategory = '$empCategory', empCompanyCode = '$empCompanyCode',
								empDepartment = '$empDepartment', empUnit = '$empUnit', empPosition = '$empPosition',
								empBasicSalary = '$empBasicSalary', empTaxCode = '$empTaxCode'
							WHERE
								id = '$userId'";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					/**
						Employee updated and redirected to previous page.
					**/
					$_SESSION['STATUS'] = 25;
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
function deleteRecord($delEmpId) {
	$query = "UPDATE employees SET status = 'Cancel' WHERE empId = '$delEmpId'";
	$result = mysql_query($query);
	if(!$result) die ("Table access failed: " . mysql_error());
	if($result) {
		header('Location: employees.php');
	}
}
?>
<?php include('pages/page_menu.php'); ?>

<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Modify Employee <small>modify employee information</small></h1>
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
					<a href="dashboard.php">Home</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Modify Employee
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-add-employee">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Modify Employee</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="" method="post" onsubmit="return validate();">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-user"></i>
															<input type="text" class="form-control input-lg" placeholder="Name" name="empName" id="empName" onkeyup="checkEmpName();" value="<?php echo $empName; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>Sex</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-user"></i>
															<input type="text" class="form-control input-lg" name="empSex" value="<?php echo $empSex; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Date of Birth</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-calendar"></i>
															<input type="date" class="form-control input-lg" name="empBirth" style="text-align: center" value="<?php echo $empBirth; ?>">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Nationality</label>
														<div class="input-icon input-icon-lg">
															<select name="empNationality" id="empNationality" class="form-control input-lg" onchange="showCounty(this.value);" required>
																<?php
																if($rowNationality < 1) {
																	/**
																	 No nationality were created.
																	 **/
																	echo "<option value=' '>No Nationality Found</option>";
																} else {
																	/**
																	 Found nationality lists.
																	 **/
																	echo "<option value=' '>Select Nationality</option>";
																	for($i = 0; $i < $rowNationality; ++$i) {
																		$nationalityName = mysql_result($resultNationality, $i, 'nationalityName');
																		if($empNationality == $nationalityName) {
																			echo "<option value='$nationalityName' selected='selected'>$nationalityName</option>";
																		} else {
																			echo "<option value='$nationalityName'>$nationalityName</option>";
																		}
																	}
																}
																?>
															</select>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>County</label>
														<div id="empDivCounty" class="input-icon input-icon-lg"><i class="fa fa-flag"></i>
															<input name="empCounty" type="text" class="form-control input-lg" value="<?php echo $empCounty; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Date of Join</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-calendar"></i>
															<input name="empDateJoin" type="date" class="form-control input-lg" style="text-align: center" value="<?php echo $empDateJoin; ?>">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Source</label>
														<select name="empSource" class="form-control input-lg" required>
															<option value=" ">Select Source</option>
															<?php
																if($empSource == "Local") {
																	echo "<option value='Local' selected='selected'>Local</option>";
																	echo "<option value='Expatiate'>Expatiate</option>";
																} else {
																	echo "<option value='Local'>Local</option>";
																	echo "<option value='Expatiate' selected='selected'>Expatiate</option>";
																}
															?>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Category</label>
														<select name="empCategory" class="form-control input-lg" required>
															<option value=" ">Select Category</option>
															<?php
															$listCategory = array("Casual", "Temporary", "Contract", "Expatriate", "Contractors");
																$length = count($listCategory);
																for($i = 0; $i < $length; ++$i) {
																	if($empCategory == $listCategory[$i]) {
																		echo "<option value=$listCategory[$i] selected='selected'>$listCategory[$i]</option>";
																	} else {
																		echo "<option value=$listCategory[$i]>$listCategory[$i]</option>";
																	}
																}
															?>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Company Code</label>
														<select name="empCompanyCode" class="form-control input-lg" required>
															<?php
															if($rowCompany < 1) {
																/**
																 No company were created.
																 **/
																echo "<option value=' '>No Company Found</option>";
															} else {
																/**
																 Found company lists.
																 **/
																echo "<option value=' '>Select Company</option>";
																for($i = 0; $i < $rowCompany; ++$i) {
																	$comId = mysql_result($resultCompany, $i, 'comId');
																	$comCode = mysql_result($resultCompany, $i, 'comCode');
																	if($empCompanyCode == $comCode) {
																		echo "<option value='$comId' selected='selected'>$comCode</option>";
																	} else {
																		echo "<option value='$comId'>$comCode</option>";
																	}
																}
															}
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Department</label>
														<select name="empDepartment" class="form-control input-lg" required>
															<?php
															if($rowDept < 1) {
																/**
																 No department were created.
																 **/
																echo "<option value=' '>No Department Found</option>";
															} else {
																/**
																 Found department lists.
																 **/
																echo "<option value=' '>Select Department</option>";
																for($i = 0; $i < $rowDept; ++$i) {
																	$deptId = mysql_result($resultDept, $i, 'deptId');
																	$deptCode = mysql_result($resultDept, $i, 'deptCode');
																	if($empDepartment == $deptCode) {
																		echo "<option value='$deptId' selected='selected'>$deptCode</option>";
																	} else {
																		echo "<option value='$deptId'>$deptCode</option>";
																	}
																}
															}
															?>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Unit</label>
														<select name="empUnit" class="form-control input-lg" required>
															<?php
															if($rowUnit < 1) {
																/**
																 No unit were created.
																 **/
																echo "<option value=' '>No Unit Found</option>";
															} else {
																/**
																 Found unit lists.
																 **/
																echo "<option value=' '>Select Unit</option>";
																for($i = 0; $i < $rowUnit; ++$i) {
																	$unitId = mysql_result($resultUnit, $i, 'unitId');
																	$unitName = mysql_result($resultUnit, $i, 'unitName');
																	if($empUnit == $unitName) {
																		echo "<option value='$unitId' selected='selected'>$unitName</option>";
																	} else {
																		echo "<option value='$unitId'>$unitName</option>";
																	}
																}
															}
															?>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Position</label>
														<select name="empPosition" class="form-control input-lg" required>
															<?php
															if($rowPosition < 1) {
																/**
																 No position were created.
																 **/
																echo "<option value=' '>No Position Found</option>";
															} else {
																/**
																 Found position lists.
																 **/
																echo "<option value=' '>Select Position</option>";
																for($i = 0; $i < $rowPosition; ++$i) {
																	$positionId = mysql_result($resultPosition, $i, 'positionId');
																	$positionName = mysql_result($resultPosition, $i, 'positionName');
																	if($empPosition == $positionName) {
																		echo "<option value='$positionId' selected='selected'>$positionName</option>";
																	} else {
																		echo "<option value='$positionId'>$positionName</option>";
																	}
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
														<label>Basic Salary</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-money"></i>
															<input name="empBasicSalary" type="text" class="form-control input-lg" value="<?php echo $empBasicSalary; ?>">
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Tax Code</label>
														<select name="empTaxCode" class="form-control input-lg" required>
															<?php
															if($rowTaxCode < 1) {
																/**
																 No tax code were created.
																 **/
																echo "<option value=' '>No Tax Code Found</option>";
															} else {
																/**
																 Found tax code information.
																 **/
																echo "<option value=' '>Select Tax Code</option>";
																for($i = 0; $i < $rowTaxCode; ++$i) {
																	$taxCodeId = mysql_result($resultTaxCode, $i, 'taxCodeId');
																	$taxCodeName = mysql_result($resultTaxCode, $i, 'taxCodeName');
																	if($empTaxCode == $taxCodeName) {
																		echo "<option value='$taxCodeId' selected='selected'>$taxCodeName</option>";
																	} else {
																		echo "<option value='$taxCodeId'>$taxCodeName</option>";
																	}
																}
															}
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<span class="error-status" id="empName_status"></span>
												</div>
												<div class="col-md-6">
													<input type="hidden" name="userId" value="<? echo $id; ?>">
													<input type="hidden" id="empId" name="empId" value="<? echo $empId; ?>">
												</div>
											</div>
										</div>
										<div class="form-actions">
											<input type="submit" value="Update" class="btn blue">
											<!-- <button type="submit" class="btn blue">Submit</button> -->
											<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Close</button></a>
											<label class="cancel-or-padding">or</label>
											<input type="button" id="empDel" value="DELETE" class="btn red">
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
$(function() {
	var empId = document.getElementById("empId").value;
	$('#empDel').click(function(){
		alertify.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
			if(result) {
				window.location="mod_employee.php?delEmpId=" + empId;
			}
		});
	})
})
/*
function deleteData() {
	var empId = document.getElementById("empId").value;
	if( confirm("Are you sure to DELETE this record?") == true)
		window.location="mod_employee.php?delEmpId=" + empId;
	return false;
}
*/
function checkEmpName() {
	var empName = document.getElementById("empName").value;
	if(empName) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				empName: empName,
			},
			success: function (response) {
				if(response == "true") {
					$('#empName_status').html("");
					return true;
				} else {
					$('#empName_status').html(response);
					return false;
               }
            }
		});
	} else {
		$('#empName_status').html("");
		return false;
	}
}
function validate() {
	var empName_status=document.getElementById("empName_status").innerHTML;
	if(empName_status == "") {
		return true;
	} else {
        return false;
	}
}
function countrySelect() {
	var ddl = document.getElementById("empCountry");
	var selectedValue = ddl.options[ddl.selectedIndex].value;
	if(selectedValue == "Select Country") {
		alert("[ERROR] Please select country!");
	}
}
function showCounty(idx) {
	var empNationality = document.getElementById("empNationality").value;
	if(empNationality) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				empNationality: empNationality,
			},
			//dataType: 'json',
			success: function (response) {
				if(response == false) {
					$('#empDivCounty').replaceWith('<div class="input-icon input-icon-lg" id="empDivCounty"><input type="text" class="form-control input-lg" name="empCounty" id="empCounty" placeholder="County / State" required></div>');
				} else {
					var county = $('#empDivCounty');
					county.replaceWith('<select id="empCounty" name="empCounty" class="form-control input-lg">');
					var county = $('#empCounty');
					county.empty();
					county.append('<option value=" ">Select County</option>');
					var data = $.parseJSON(response);
					for (var i = 0; i < data.length; ++i) {
						county.append('<option value="' + data[i].countyCode + '">' + data[i].countyCode + '</option>');
        				}
               }
            }
		});
	} else {
		/*
		$('#empName_status').html("");
		return false;
		*/
	}
};
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
</body>
</html>