<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
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
$query = "SELECT * FROM tempSession
			 WHERE emailAdd = '$login'";
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
			$uploadedStatus = 0;
			if(isset($_POST["submit"])) {
				if(isset($_FILES["importFile"])) {
					/**
						if there was an error uploading the file
					**/
					if($_FILES["importFile"]["error"] > 0) {
						echo "Return Code: " . $_FILES["importFile"]["error"] . "<br />";
					} else {
						if(file_exists($_FILES["importFile"]["name"])) {
							unlink($_FILES["importFile"]["name"]);
						}
						$storagename = "partsMasterFile.xlsx";
						move_uploaded_file($_FILES["importFile"]["tmp_name"],  $storagename);
						$uploadedStatus = 1;

					}
				} else {
					echo "No file selected <br />";
				}
			}

			if($uploadedStatus == 1) {
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
										<label>Import From</label>
<!--
										<label class="control-label">Select File</label>
<input id="input-1a" type="file" class="file" data-show-preview="false">
-->


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
										<div class="form-actions">
											<input type="submit" id="import-export-submitBtn" name="submit" value="Submit" class="btn blue">
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
				<div class="col-md-1">
					<!-- <input type="text" id="finished" value="<?php echo $finished; ?>"> -->
				</div>
			</div>

<!--
									<form role="form" action="mod_parts.php" method="post" onsubmit="return validate();">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>S/N</label>
															<input type="text" class="form-control input-lg" id="partsId" name="serialNumber" style="text-align: center" value="<?php echo $partsId; ?>" readonly>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Date</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-calendar"></i>
															<input type="text" class="form-control input-lg" name="dateTime" value="<?php echo $dateTime; ?>" disabled>
														</div>
													</div>
												</div>
											</div>
-->




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
$('#importFile').on('change', function(evt) {
    var file = evt.target.files[0];
    if(file)
        $('#import-export-submitBtn').show();
    else
        $('#import-export-submitBtn').hide();
});
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>