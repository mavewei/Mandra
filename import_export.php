<?php include('pages/page_header.php'); ?>
<!-- <link href="css/center.css" rel="stylesheet" type="text/css" /> -->
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="css/waiting.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, '');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, '');
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
			$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
			$fname = $_SESSION['FNAME'];
			$uid = $_SESSION['UID'];
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
						Session time out.
					**/
					$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
				};
			};
			$uploadedStatusPMF = 0;
			$uploadedStatusEmp = 0;
			if(isset($_POST["submitPMF"])) {
				if(isset($_FILES["importFilePMF"])) {
					/**
						if there was an error uploading the file
					**/
					if($_FILES["importFilePMF"]["error"] > 0) {
						echo "Return Code: " . $_FILES["importFilePMF"]["error"] . "<br />";
					} else {
						if(file_exists($_FILES["importFilePMF"]["name"])) {
							unlink($_FILES["importFilePMF"]["name"]);
						}
						$storagename = "partsMasterFile.xlsx";
						move_uploaded_file($_FILES["importFilePMF"]["tmp_name"],  $storagename);
						$uploadedStatusPMF = 1;

					}
				} else {
					echo "No file selected <br />";
				}
			}
			if(isset($_POST["submitEMP"])) {
				if(isset($_FILES["importFileEmp"])) {
					/**
						if there was an error uploading the file
					**/
					if($_FILES["importFileEmp"]["error"] > 0) {
						echo "Return Code: " . $_FILES["importFileEmp"]["error"] . "<br />";
					} else {
						if(file_exists($_FILES["importFileEmp"]["name"])) {
							unlink($_FILES["importFileEmp"]["name"]);
						}
						$storagename = "empData.xlsx";
						move_uploaded_file($_FILES["importFileEmp"]["tmp_name"],  $storagename);
						$uploadedStatusEmp = 1;
					}
				} else {
					echo "No file selected <br />";
				}
			}
			/**
				Read parts master file and insert to tables
			**/
			if($uploadedStatusPMF == 1) {
				mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());

				set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
				include 'PHPExcel/IOFactory.php';
				/**
				   This is the file path to be uploaded.
				**/
				$inputFileName = 'partsMasterFile.xlsx';

				try {
					$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}

				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
				for($i = 3; $i <= $arrayCount; ++$i){
					$partsNumber = mysql_escape_string(trim($allDataInSheet[$i]["C"]));
					$partsDescription = mysql_escape_string(trim($allDataInSheet[$i]["D"]));
					$partsUom = mysql_escape_string(trim($allDataInSheet[$i]["E"]));
					$partsBrand = mysql_escape_string(trim($allDataInSheet[$i]["F"]));
					$partsModel = mysql_escape_string(trim($allDataInSheet[$i]["G"]));
					$partsWhereUsedI = mysql_escape_string(trim($allDataInSheet[$i]["H"]));
					$partsWhereUsedII = mysql_escape_string(trim($allDataInSheet[$i]["I"]));
					/**
						Get time now.
					**/
					$queryTime = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$resultTime = mysql_query($queryTime);
					$rowTime = mysql_fetch_array($resultTime);
					$time = $rowTime['dateTime'];
					/**
						Get partsId.
					**/
					$queryPartsId = "SELECT * from partsMasterFile ORDER BY id DESC";
					$resultPartsId = mysql_query($queryPartsId);
					$rowPartsId = mysql_num_rows($resultPartsId);
					if(!$resultPartsId) die ("Table access failed: " . mysql_error());
					if($rowPartsId == 0) {
						$partsId = 'P000001';
					} else {
						$rowPartsId++;
						$partsId = 'P' . sprintf('%06d', $rowPartsId);
					}
					/**
						Check data exists.
					**/
					if($partsNumber != "" && $partsDescription != "") {
						$query = "SELECT * FROM partsMasterFile
									 WHERE partsNumber = '$partsNumber' AND partsBrand = '$partsBrand'";
						$result = mysql_query($query);
						$row = mysql_num_rows($result);
						if($row == 0) {
							$query = "INSERT INTO partsMasterFile
											(dateTime, partsId, partsNumber, partsDescription, partsUom, partsBrand,
											partsModel, partsWhereUsedI, partsWhereUsedII, status, createdBy)
										VALUES
											('$time', '$partsId', '$partsNumber', '$partsDescription', '$partsUom', '$partsBrand',
											'$partsModel', '$partsWhereUsedI', '$partsWhereUsedII', 'Active', '$uid')";
							$result = mysql_query($query);
							if(!$result) die ("Table access failed: " . mysql_error());
						} else {}
					}
				}
				$_SESSION['STATUS'] = 28;
				header('Location: status.php');
			}
			/**
				Read employees data and insert to tables
			**/
			if($uploadedStatusEmp == 1) {
				mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());

				set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
				include 'PHPExcel/IOFactory.php';
				/**
				   This is the file path to be uploaded.
				**/
				$inputFileName = 'empData.xlsx';
				try {
					$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
				for($i = 3; $i <= $arrayCount; ++$i){
					$empName = mysql_escape_string(trim($allDataInSheet[$i]["C"]));
					$empSex = mysql_escape_string(trim($allDataInSheet[$i]["D"]));
					$empBirth = mysql_escape_string(trim($allDataInSheet[$i]["E"]));
					$empNationality = mysql_escape_string(trim($allDataInSheet[$i]["F"]));
					$partsCounty = mysql_escape_string(trim($allDataInSheet[$i]["G"]));
					$partsDateJoin = mysql_escape_string(trim($allDataInSheet[$i]["H"]));
					$partsStatus = mysql_escape_string(trim($allDataInSheet[$i]["I"]));
					$empCategory = mysql_escape_string(trim($allDataInSheet[$i]["J"]));
					$empCompanyCode = mysql_escape_string(trim($allDataInSheet[$i]["K"]));
					$empDepartment = mysql_escape_string(trim($allDataInSheet[$i]["L"]));
					$empUnit = mysql_escape_string(trim($allDataInSheet[$i]["M"]));
					$empPosition = mysql_escape_string(trim($allDataInSheet[$i]["N"]));
					$empBasicSalary = mysql_escape_string(trim($allDataInSheet[$i]["O"]));
					$empTaxCode = mysql_escape_string(trim($allDataInSheet[$i]["P"]));
					/**
						Get time now.
					**/
					$queryTime = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$resultTime = mysql_query($queryTime);
					$rowTime = mysql_fetch_array($resultTime);
					$time = $rowTime['dateTime'];
					/**
						Get partsId.
					**/
					$queryEmp = "SELECT * from employees ORDER BY id DESC";
					$resultEmp = mysql_query($queryEmp);
					$rowEmp = mysql_num_rows($resultEmp);
					if(!$resultEmp) die ("Table access failed: " . mysql_error());
					if($rowEmp == 0) {
						$empId = 'E0001';
					} else {
						$rowEmp++;
						$empId = 'E' . sprintf('%04d', $rowEmp);
					}
					/**
						Check data exists.
					**/
					if($empName != "" && $empSex != "") {
						$query = "SELECT * FROM employees
									 WHERE empName = '$empName' AND empSex = '$empSex'";
						$result = mysql_query($query);
						$row = mysql_num_rows($result);
						if($row == 0) {
							$query = "INSERT INTO employees
											(dateTime, empId, empName, empSex, empBirth, empNationality, empCounty,
											empDateJoin, empStatus, empCategory, empCompanyCode, empDepartment, empUnit,
											empPosition, empBasicSalary, empTaxCode, status, createdBy)
										VALUES
											('$time', '$empId', '$empName', '$empSex', '$empBirth', '$empNationality',
											'$empCounty', '$empDateJoin', '$empStatus', '$empCategory', '$empCompanyCode',
											'$empDepartment', '$empUnit', '$empPosition', '$empBasicSalary', '$empTaxCode',
											'Active', '$uid')";
							$result = mysql_query($query);
							if(!$result) die ("Table access failed: " . mysql_error());
						} else {}
					}
				}
				$_SESSION['STATUS'] = 31;
				header('Location: status.php');
			}
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
};
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Import & Export <small>import & export from / to excel file</small></h1>
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
				<li class="active">
					Import & Export
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div class="portlet light">
						<div class="portlet-title">
							<div id="partsMasterFile" class="caption"><span class="caption-subject font-green-sharp bold uppercase">Parts Master File</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<form role="form" action="" method="post" enctype="multipart/form-data">
									<div class="col-md-6">
										<label id="pmf">Import From</label>
										<div class="fileinput fileinput-new input-group" data-provides="fileinput">
											<div class="form-control" data-trigger="fileinput">
												<i class="glyphicon glyphicon-file fileinput-exists"></i>
												<span class="fileinput-filename"></span>
											</div>
											<span class="input-group-addon btn btn-default btn-file">
												<span class="fileinput-new">Select file</span>
												<span class="fileinput-exists">Change</span>
												<input type="file" name="importFilePMF" id="importFilePMF">
											</span>
											<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>

										</div>
										<div class="form-actions">
											<input type="submit" id="import-export-pmf-submitBtn" name="submitPMF" value="Submit" class="btn blue" style="display: none">
										</div>
									</div>
								</form>
								<div class="col-md-6" style="display: none">
									<label>Export To</label>
									<div class="fileinput fileinput-new input-group" data-provides="fileinput">
										<div class="form-control" data-trigger="fileinput">
											<i class="glyphicon glyphicon-file fileinput-exists"></i>
											<span class="fileinput-filename"></span>
										</div>
										<span class="input-group-addon btn btn-default btn-file">
											<span class="fileinput-new">Select file</span>
											<span class="fileinput-exists">Change</span>
											<input type="file" name="importFile" id="importFile">
										</span>
										<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div class="portlet light">
						<div class="portlet-title">
							<div id="partsMasterFile" class="caption"><span class="caption-subject font-green-sharp bold uppercase">Employees Data</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<form role="form" action="" method="post" enctype="multipart/form-data">
									<div class="col-md-6">
										<label id="emp">Import From</label>
										<div class="fileinput fileinput-new input-group" data-provides="fileinput">
											<div class="form-control" data-trigger="fileinput">
												<i class="glyphicon glyphicon-file fileinput-exists"></i>
												<span class="fileinput-filename"></span>
											</div>
											<span class="input-group-addon btn btn-default btn-file">
												<span class="fileinput-new">Select file</span>
												<span class="fileinput-exists">Change</span>
												<input type="file" name="importFileEmp" id="importFileEmp">
											</span>
											<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>

										</div>
										<div class="form-actions">
											<input type="submit" id="import-export-emp-submitBtn" name="submitEMP" value="Submit" class="btn blue" style="display: none">
										</div>
									</div>
								</form>
								<div class="col-md-6" style="display: none">
									<label>Export To</label>
									<div class="fileinput fileinput-new input-group" data-provides="fileinput">
										<div class="form-control" data-trigger="fileinput">
											<i class="glyphicon glyphicon-file fileinput-exists"></i>
											<span class="fileinput-filename"></span>
										</div>
										<span class="input-group-addon btn btn-default btn-file">
											<span class="fileinput-new">Select file</span>
											<span class="fileinput-exists">Change</span>
											<input type="file" name="importFile" id="importFile">
										</span>
										<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
	</div>
</div>
<div class="modal">
<?php include('pages/page_jquery.php'); ?>
<script src="js/jasny-bootstrap.min.js"></script>
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
$('#importFilePMF').on('change', function(evt) {
    var file = evt.target.files[0];
    if(file)
        $('#import-export-pmf-submitBtn').show();
    else
        $('#import-export-pmf-submitBtn').hide();
});
$('#importFileEmp').on('change', function(evt) {
    var file = evt.target.files[0];
    if(file)
        $('#import-export-emp-submitBtn').show();
    else
        $('#import-export-emp-submitBtn').hide();
});


</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>