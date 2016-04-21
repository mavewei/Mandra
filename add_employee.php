<?php include('pages/page_header.php'); ?>
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
			if($_SESSION['GID'] < 3000) {
				$fname = $_SESSION['FNAME'];
				$uid = $_SESSION['UID'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				$_SESSION['LAST_PAGE'] = 'add_employee.php';
				$year = date("Y");
				$month = date("m");
				$day = date("d");
				$maxDoB = $year . '-' . $month . '-' . $day;
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
				// mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				// Select nationality lists.
				$queryNationality = "SELECT * FROM nationality ORDER BY nationalityName ASC";
				$resultNationality = mysql_query($queryNationality);
				$rowNationality = mysql_num_rows($resultNationality);
				if(!$resultNationality) die ("Table access failed: " . mysql_error());
				// Select taxcode lists.
				$queryTaxCode = "SELECT * FROM taxCode WHERE status = 'Active' ORDER BY taxCodeId ASC";
				$resultTaxCode = mysql_query($queryTaxCode);
				$rowTaxCode = mysql_num_rows($resultTaxCode);
				if(!$resultTaxCode) die ("Table access failed: " . mysql_error());
				// Select position lists.
		   		$queryPosition = "SELECT * FROM position WHERE status = 'Active' ORDER BY positionName ASC";
				$resultPosition = mysql_query($queryPosition);
				$rowPosition = mysql_num_rows($resultPosition);
				if(!$resultPosition) die ("Table access failed: " . mysql_error());
				// Select unit lists.
		   		$queryUnit = "SELECT * FROM unit WHERE status = 'Active' ORDER BY unitName ASC";
				$resultUnit = mysql_query($queryUnit);
				$rowUnit = mysql_num_rows($resultUnit);
				if(!$resultUnit) die ("Table access failed: " . mysql_error());
				// Select company lists.
				$queryCom = "SELECT * FROM	company WHERE status = 'Active' ORDER BY comId ASC";
				$resultCom = mysql_query($queryCom);
				$rowCom = mysql_num_rows($resultCom);
				if(!$resultCom) die ("Table access failed: " . mysql_error());
				// Select department lists.
				$queryDept = "SELECT * FROM departments WHERE status = 'Active' ORDER BY deptName ASC";
				$resultDept = mysql_query($queryDept);
				$rowDept = mysql_num_rows($resultDept);
				if(!$resultDept) die ("Table access failed: " . mysql_error());

				// Select status lists.
				/*
				$queryStatus = "SELECT * FROM status WHERE status = 'Active' ORDER BY statusName ASC";
				$resultStatus = mysql_query($queryStatus);
				$rowStatus = mysql_num_rows($resultStatus);
				if(!$resultStatus) die ("Table access failed: " . mysql_error());
				*/

				// Select employee lists.
				$query = "SELECT * FROM employees ORDER BY id DESC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($row == 0) {
					$empId = 'E0001';
				} else {
					$row++;
					$empId = 'E' . sprintf('%04d', $row);
				}

				if(isset($_POST['empName'])) {
					$empName = ucwords(mysql_escape_string($_POST['empName']));
					$empSex = $_POST['empSex'];
					$empBirth = $_POST['empBirth'];
					$empNationality = $_POST['empNationality'];
					$empCounty = ucwords(mysql_escape_string($_POST['empCounty']));
					$empDateJoin = $_POST['empDateJoin'];
					$empStatus = $_POST['empStatus'];
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
					$query = "INSERT INTO employees
									(dateTime, empId, empName, empSex, empBirth, empNationality, empCounty, empDateJoin,
									empStatus, empCategory, empCompanyCode, empDepartment, empUnit, empPosition,
									empBasicSalary, empTaxCode, status, createdBy)
								VALUES
									('$time', '$empId', '$empName', '$empSex', '$empBirth', '$empNationality', '$empCounty',
									'$empDateJoin', '$empStatus', '$empCategory', '$empCompanyCode', '$empDepartment',
									'$empUnit', '$empPosition', '$empBasicSalary', '$empTaxCode', 'Active', '$uid')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// Employee created and redirected to previous page.
						$_SESSION['STATUS'] = 15;
						header("Location: status.php");
					};
				};
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
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Add Employee <small>add new employee</small></h1>
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
				<li>
					<a href="javascript:;">Employees</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Add Employee
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Employee</span></div>
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
													<input type="text" class="form-control input-lg" placeholder="Name" name="empName" id="empName" onkeyup="checkEmpName();" autofocus="on" required>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Sex</label>
												<select name="empSex" id="empSex" class="form-control input-lg" required>
													<option value="">Select Sex</option>
													<option value="Male">Male</option>
													<option value="Female">Female</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Date of Birth</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-calendar"></i>
													<input type="date" class="form-control input-lg" name="empBirth" id="empBirth" style="text-align: center" max="<?php echo $maxDoB; ?>">
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
															// No nationality were created.
															echo "<option value=''>No Nationality Found</option>";
														} else {
															// Found nationality lists.
															echo "<option value=''>Select Nationality</option>";
															for($i = 0; $i < $rowNationality; ++$i) {
																$nationalityName = mysql_result($resultNationality, $i, 'nationalityName');
																echo "<option value='$nationalityName'>$nationalityName</option>";
															}
														}
														?>

													<!--
													<select name="empNationality" id="empNationality" class="form-control input-lg" onchange="showCounty(this.value);" required>

														<script>
															//var country = new Array("Select Country", "China", "Hong Kong", "Liberia", "Malaysia", "Singapore");
															var country = new Array("Select Nationality", "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burma", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic", "Congo, Republic of the", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Mongolia", "Morocco", "Monaco", "Mozambique", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Samoa", "San Marino", " Sao Tome", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe");
															for(var cnt = 0; cnt < country.length; cnt++) {
																document.write('<option value="'+country[cnt]+'">'+country[cnt]+'</option>');
															}
														</script>
														-->
													</select>
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>County</label>
													<select id="empCounty" name="empCounty" class="form-control input-lg">
														<option value=" ">Select County</option>
													</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Date of Join</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-calendar"></i>
													<input type="date" class="form-control input-lg" name="empDateJoin" style="text-align: center" max="<?php echo $maxDoB; ?>">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Status</label>
												<select name="empStatus" class="form-control input-lg" required>
													<option value="">Select Status</option>
													<option value="On Duty">On Duty</option>
													<option value="Long Sick Leave">Long Sick Leave</option>
													<option value="Suspended">Suspended</option>
													<option value="Resigned">Resigned</option>
													<option value="Terminated">Terminated</option>
													<?php
														/*
														if($rowStatus < 1) {
															// No status were created.
															echo "<option value=''>No Status Found</option>";
														} else {
															// Found status lists.
															echo "<option value=''>Select Status</option>";
															for($i = 0; $i < $rowStatus; ++$i) {
																$statusId = mysql_result($resultStatus, $i, 'statusId');
																$statusName = mysql_result($resultStatus, $i, 'statusName');
																echo "<option value=$statusId>$statusName</option>";
															}
														}
														*/
													?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Category</label>
												<select name="empCategory" class="form-control input-lg" required>
													<option value="">Select Category</option>
													<option value="Casual">Casual</option>
													<option value="Temporary">Temporary</option>
													<option value="Contract">Contract</option>
													<option value="Expatriate">Expatriate</option>
													<option value="Contractors">Contractors</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Company Code <!-- <small class="company-not-found">Create <a href="add_company.php">here!</a></small> --></label>
												<select name="empCompanyCode" class="form-control input-lg" required>
													<?php
														if($rowCom < 1) {
															// No company were created.
															echo "<option value=''>No Company Name Found</option>";
														} else {
															// Found company lists.
															echo "<option value=''>Select Company Name</option>";
															for($i = 0; $i < $rowCom; ++$i) {
																$comName = mysql_result($resultCom, $i, 'comName');
																echo "<option value='$comName'>$comName</option>";
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
												<label>Department <!-- <small class="dept-not-found">Create <a href="add_department.php">here!</a></small> --></label>
												<select name="empDepartment" class="form-control input-lg" required>
													<?php
														if($rowDept < 1) {
															// No departments were created.
															echo "<option value=''>No Department Found</option>";
														} else {
															// Found departments lists.
															echo "<option value=''>Select Department</option>";
															for($i = 0; $i < $rowDept; ++$i) {
																$deptCode = mysql_result($resultDept, $i, 'deptCode');
																echo "<option value='$deptCode'>$deptCode</option>";
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
															// No unit were created.
															echo "<option value=''>No Unit Found</option>";
														} else {
															// Found unit lists.
															echo "<option value=''>Select Unit</option>";
															for($i = 0; $i < $rowUnit; ++$i) {
																$unitName = mysql_result($resultUnit, $i, 'unitName');
																echo "<option value='$unitName'>$unitName</option>";
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
															// No position were created.
															echo "<option value=''>No Position Found</option>";
														} else {
															// Found position lists.
															echo "<option value=''>Select Position</option>";
															for($i = 0; $i < $rowPosition; ++$i) {
																$positionName = mysql_result($resultPosition, $i, 'positionName');
																echo "<option value='$positionName'>$positionName</option>";
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
												<div class="input-icon input-icon-lg">
													<i class="fa fa-money"></i>
													<input type="text" class="form-control input-lg" name="empBasicSalary" placeholder="Basic Salary in USD" required>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Tax Code</label>
												<select name="empTaxCode" class="form-control input-lg" required>
													<?php
														if($rowTaxCode < 1) {
															// No taxcode were created.
															echo "<option value=''>No Tax Code Found</option>";
														} else {
															// Found taxcode lists.
															echo "<option value=''>Select Tax Code</option>";
															for($i = 0; $i < $rowTaxCode; ++$i) {
																$taxCodeName = mysql_result($resultTaxCode, $i, 'taxCodeName');
																echo "<option value='$taxCodeName'>$taxCodeName</option>";
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
										<div class="col-md-6"></div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Submit" class="btn blue">
									<a href="employees.php"><button type="button" class="btn default">Cancel</button></a>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	</div>
</div>
<?php include('pages/page_jquery.php'); ?>
<script>
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
	// Bootbox alert customize.
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
		var ddl = document.getElementById("empNationality");
		var selectedValue = ddl.options[ddl.selectedIndex].value;
		if(selectedValue == "Select Nationality") {
			alert("[ERROR] Please select Nationality!");
			return false;
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
						$('#empCounty').replaceWith('<div class="input-icon input-icon-lg" id="empDivCounty"><input type="text" class="form-control input-lg" name="empCounty" id="empCounty" placeholder="County / State" required></div>');
					} else {
						var county = $('#empDivCounty');
						county.replaceWith('<select id="empCounty" name="empCounty" class="form-control input-lg">');
						var county = $('#empCounty');
						county.empty();
						county.append('<option value="">Select County</option>');
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
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>