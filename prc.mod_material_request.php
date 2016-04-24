<?php include('pages/page_header.php'); ?>
<link href="css/material-request.css" rel="stylesheet" type="text/css" />
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
				$position = $_SESSION['POSITION'];
				$mrSN = mysql_escape_string($_GET['mrSN']);
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
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
				// Remove record.
				if($_GET['delMrSN']) {
					$delId = $_GET['delMrSN'];
					deleteRecord($delId, 'prcMaterialRequestForm', 'mrSN');
				}
				// Reviewed confirm!
				if($_GET['reviewedMrSN']) {
					$reviewedMrSN = $_GET['reviewedMrSN'];
					updateReviewed($reviewedMrSN, 'prcMaterialRequestForm', 'mrSN', $login);
				}
				// Approved confirm!
				if($_GET['approveMrSN']) {
					$approveMrSN = $_GET['approveMrSN'];
					if(updateApproved($approveMrSN, 'prcMaterialRequestForm', 'mrSN', $login)) {
						generatePR($approveMrSN, 'mrfDetailsSN');
					}
				}
				// Select material request form details.
				/*
				$query = "SELECT * FROM prcMaterialRequestForm
							 INNER JOIN userAccounts ON prcMaterialRequestForm.mrRequestBy = userAccounts.emailAdd
							 WHERE mrSn = '$mrSN'";
				*/
				$query = "SELECT MRF.dateTime, mrNumber, mrDepartment, mrPurpose, mrDateReq, mrTotal, mrRemark,
							 mrReviewStatus, mrApproveStatus, UA1.firstName AS mrRequestBy FROM prcMaterialRequestForm MRF
							 JOIN userAccounts UA1 ON UA1.emailAdd = MRF.mrRequestBy
							 WHERE mrSN = '$mrSN'";
				/*
				JOIN userAccounts UA2 ON UA2.emailAdd = MRF.mrReviewedPerson
				JOIN userAccounts UA3 ON UA3.emailAdd = MRF.mrApprovedPerson
				*/
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) {
					die ("Table access failed: " . mysql_error());
				} else {
					$data = mysql_fetch_assoc($result);
					$string = $data['dateTime'];
					if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
						$mrDateTime = $match[1];
					};
					$mrNumber = $data['mrNumber'];
					$mrDepartment = $data['mrDepartment'];
					$mrPurpose = $data['mrPurpose'];
					$mrDateReq = $data['mrDateReq'];
					$mrTotal = $data['mrTotal'];
					$mrRemark = $data['mrRemark'];
					$mrRequestBy = $data['mrRequestBy'];
					$mrReviewStatus = $data['mrReviewStatus'];
					if($mrReviewStatus == "No Status") {} else {
						$queryRev = "SELECT mrReviewedDateTime, UA1.firstName AS mrReviewedPerson
										 FROM prcMaterialRequestForm MRF
										 JOIN userAccounts UA1 ON UA1.emailAdd = MRF.mrReviewedPerson
										 WHERE mrSN = '$mrSN'";
						$resultRev = mysql_query($queryRev);
						$dataRev = mysql_fetch_assoc($resultRev);
						$mrReviewedPerson = $dataRev['mrReviewedPerson'];
						$mrReviewedDateTime = $dataRev['mrReviewedDateTime'];
						$mrReviewStatus = $mrReviewedDateTime . "   (" . $mrReviewedPerson . ")";
					}
					$mrApproveStatus = $data['mrApproveStatus'];
					if($mrApproveStatus == "No Status") {} else {
						$queryApprov = "SELECT mrApprovedDateTime, UA1.firstName AS mrApprovedPerson
										 FROM prcMaterialRequestForm MRF
										 JOIN userAccounts UA1 ON UA1.emailAdd = MRF.mrApprovedPerson
										 WHERE mrSN = '$mrSN'";
						$resultApprov = mysql_query($queryApprov);
						$dataApprov = mysql_fetch_assoc($resultApprov);
						$mrApprovedPerson = $dataApprov['mrApprovedPerson'];
						$mrApprovedDateTime = $dataApprov['mrApprovedDateTime'];
						$mrApproveStatus = $mrApprovedDateTime . "   (" . $mrApprovedPerson . ")";
					}
					// Material request form details
					$queryDetails = "SELECT * FROM prcMaterialRequestFormDetails WHERE mrfDetailsSN = '$mrSN'";
					$resultDetails = mysql_query($queryDetails);
					$rowDetails = mysql_num_rows($resultDetails);
					if(!$resultDetails) die ("Table access failed: " . mysql_error());
				}
				// Form submitted.
				if(isset($_POST['mrNumber']) && isset($_POST['mrPurpose'])) {
					$mrSN = mysql_escape_string($_POST['mrSN']);
					$mrNumber = mysql_escape_string($_POST['mrNumber']);
					$mrPurpose = mysql_escape_string($_POST['mrPurpose']);
					$mrDateReq = mysql_escape_string($_POST['mrDateReq']);
					$mrTotal = mysql_escape_string($_POST['mrTotal']);
					$mrRemark = mysql_escape_string($_POST['mrRemark']);
					if($mrRemark != "N/A") {
						$mrRemark = ucwords(strtolower($mrRemark));
					}
					//$mrReviewStatus = mysql_escape_string($_POST['mrReviewStatus']);
					//$mrApproveStatus = mysql_escape_string($_POST['mrApproveStatus']);
					// Array for each parts details
					$mrfDetailsQtyArray = array();
					$mrfDetailsEquipNoArray = array();
					for($i = 0; $i < $mrTotal; $i++) {
						$mrfDetailsQty = "prcQty" . $i;
						$mrfDetailsEquipNo = "prcEquipNo" . $i;
						$mrfDetailsQtyArray[] = mysql_escape_string($_POST[$mrfDetailsQty]);
						$mrfDetailsEquipNoArray[] = strtoupper(mysql_escape_string($_POST[$mrfDetailsEquipNo]));
					};
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "UPDATE prcMaterialRequestForm SET
									dateTime = '$time', mrNumber = '$mrNumber',
									mrPurpose = '$mrPurpose', mrDateReq = '$mrDateReq', mrRemark = '$mrRemark',
									mrReviewStatus = '$mrReviewStatus', mrApproveStatus = '$mrApproveStatus'
								 WHERE mrSN = '$mrSN'";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// Material request form created and redirected to previous page.
						for($j = 0; $j < $mrTotal; $j++) {
							$mrfDetailsPartsNumber = $mrfDetailsPartsNumberArray[$j];
							$mrfDetailsQty = $mrfDetailsQtyArray[$j];
							$mrfDetailsEquipNo = $mrfDetailsEquipNoArray[$j];
							//$mrfDetailsPartsNumber = $mrfDetailsPartsNumberArray[$j];
							//$mrfDetailsQty = $mrfDetailsQtyArray[$j];
							//$mrfDetailsPlateNo = $mrfDetailsPlateNoArray[$j];
							// $mrfDetailsPartsNumber = explode("&", $mrfDetailsPartsNumber[$j]);
							$query = "UPDATE prcMaterialRequestFormDetails SET
											dateTime = '$time', mrfDetailsQty = '$mrfDetailsQty',
											mrfDetailsEquipNo = '$mrfDetailsEquipNo'
										 WHERE mrfDetailsSN = '$mrSN' AND mrfDetailsNumber = '$j'";
							$result = mysql_query($query);
							if(!$result) die ("Database access failed: " . mysql_error());
							if($result) {
								$_SESSION['STATUS'] = 43;
								header('Location: status.php');
							};
						};
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
	function deleteRecord($idx, $tables, $tableId) {
		$query = "UPDATE $tables SET status = 'Cancel' WHERE $tableId = '$idx'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($result) {
			header('Location: prc.material_request.php');
		}
	}
	// Reviewed updated.
	function updateReviewed($idx, $tables, $tableId, $login) {
		$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$time = $row['dateTime'];
		$query = "UPDATE $tables SET mrReviewStatus = 'Reviewed', mrReviewedPerson = '$login', mrReviewedDateTime = '$time' WHERE $tableId = '$idx'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($result) {
			header('Location: prc.material_request.php');
		}
	}
	// Approved updated.
	function updateApproved($idx, $tables, $tableId, $login) {
		$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$time = $row['dateTime'];
		$query = "UPDATE $tables SET mrApproveStatus = 'Approved', mrApprovedPerson = '$login', mrApprovedDateTime = '$time' WHERE $tableId = '$idx'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($result) {
			return 1;
			//header('Location: prc.material_request.php');
		}
	}
	// Generate PR from MRF
	function generatePR($idx, $tableId) {
		$queryMRF = "SELECT * FROM prcMaterialRequestForm WHERE mrSN = '$idx' AND status = 'Active'";
		$resultMRF = mysql_query($queryMRF);
		$rowsMRF = mysql_num_rows($resultMRF);
		// Get MRF informations.
		$dataMRF = mysql_fetch_assoc($resultMRF);
		$mrNumber = $dataMRF['mrNumber'];
		$prDepartment = $dataMRF['mrDepartment'];
		$prPurpose = $dataMRF['mrPurpose'];
		$prDateReq = $dataMRF['mrDateReq'];
		$prRequestBy = $dataMRF['mrRequestBy'];
		// Get MRF Details informations.
		$queryDetails = "SELECT * FROM prcMaterialRequestFormDetails WHERE $tableId = '$idx' AND mrfDetailsStockQty = 'N/A'";
		$resultDetails = mysql_query($queryDetails);
		$rowsDetails = mysql_num_rows($resultDetails);
		if(!$resultDetails) {
			die ("Table access failed: " . mysql_error());
		} else {
			if($rowsDetails > 0) {
				// Select Purchase Request Series Number.
				$prSNQuery = "SELECT * FROM purchaseRequest";
				$prSNResult = mysql_query($prSNQuery);
				$prSNRows = mysql_num_rows($prSNResult);
				if($prSNRows == 0) {
					$prSN = 'PR0001';
				} else {
					$prSNRows++;
					$prSN = 'PR' . sprintf('%04d', $prSNRows);
				}
				// Get current date time.
				$queryDT = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
				$resultDT = mysql_query($queryDT);
				$rowDT = mysql_fetch_array($resultDT);
				$time = $rowDT['dateTime'];
				// Insert record into purchaseRequest.
				$queryPR = "INSERT INTO purchaseRequest
									(dateTime, prSN, prNumber, mrNumber, prDepartment, prPurpose, prDateReq, prTotal,
									 prReasonPurchase, prRequestBy, prWarehouseStatus, prHeadWorkshopStatus, prApproveStatus,
									 prFinalize, status)
								VALUES
									('$time', '$prSN', 'N/A', '$mrNumber', '$prDepartment', '$prPurpose', '$prDateReq',
									 '$rowsDetails', 'N/A', '$prRequestBy', 'No Status', 'No Status', 'No Status', FALSE,
									 'Active')";
				$resultPR = mysql_query($queryPR);
				if(!$resultPR) die ("Table access failed: " . mysql_error());
				if($resultPR) {
					// Insert record into purchaseRequestDetails
					for($i = 0; $i < $rowsDetails; ++$i) {
						$dateTime = mysql_result($resultDetails, $i, 'dateTime');
						$mrfDetailsSN = mysql_result($resultDetails, $i, 'mrfDetailsSN');
						$prDetailsNumber = mysql_result($resultDetails, $i, 'mrfDetailsNumber');
						$prDetailsPartsNumber = mysql_result($resultDetails, $i, 'mrfDetailsPartsNumber');
						$prDetailsDescription = mysql_result($resultDetails, $i, 'mrfDetailsDescription');
						$prDetailsQty = mysql_result($resultDetails, $i, 'mrfDetailsQty');
						$prDetailsUom = mysql_result($resultDetails, $i, 'mrfDetailsUom');
						$prDetailsEquipType = mysql_result($resultDetails, $i, 'mrfDetailsEquipType');
						$prDetailsModel = mysql_result($resultDetails, $i, 'mrfDetailsModel');
						$prDetailsEquipNo = mysql_result($resultDetails, $i, 'mrfDetailsEquipNo');
						$queryPRDetails = "INSERT INTO purchaseRequestDetails
													(dateTime, prDetailsSN, prDetailsNumber, prDetailsPartsNumber,
													 prDetailsDescription, prDetailsQty, prDetailsUom, prDetailsEquipType,
													 prDetailsModel, prDetailsEquipNo)
												VALUES
													('$time', '$prSN', '$prDetailsNumber', '$prDetailsPartsNumber',
													 '$prDetailsDescription', '$prDetailsQty', '$prDetailsUom',
													 '$prDetailsEquipType', '$prDetailsModel', '$prDetailsEquipNo')";
						$resultPRDetails = mysql_query($queryPRDetails);
						if(!$resultPRDetails) {
							die ("Table access failed: " . mysql_error());
						} else {
							header('Location: prc.material_request.php');
						}
					}
				}
			}
		}
	}
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Material Request Form <small>modify material request form</small></h1>
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
					<a href="javascript:;">Procurement</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Material Request</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Material Request Form
				</li>
			</ul>
			<div class="row margin-top-10">
				<!-- <div class="col-md-1"></div> -->
				<div class="col-xs-12 col-md-12">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Material Request Form</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-xs-2 col-md-2">
											<div class="form-group" style="display: none">
												<label>MR Date</label>
												<input type="text" class="form-control input-lg" name="mrDateTime" style="text-align: center" value="<? echo $dateTime; ?>" readonly>
											</div>
										</div>
										<div class="col-xs-8 col-md-8"></div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Series Number</label>
												<input id="mrSN" type="text" class="form-control input-lg" style="text-align: center" name="mrSN" value="<?php echo $mrSN; ?>" readonly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-2 col-md-2"></div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>MR Date</label>
												<input type="text" class="form-control input-lg" name="mrDateTime" style="text-align: center" value="<? echo $mrDateTime; ?>" readonly>
											</div>
										</div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>MR No</label>
												<input type="text" class="form-control input-lg" name="mrNumber" value="<? echo $mrNumber; ?>">
											</div>
										</div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Department</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="mrDepartment" value="<? echo $mrDepartment; ?>" readonly>
											</div>
										</div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Purpose</label>
												<select name="mrPurpose" class="form-control input-lg" required>
													<option value="">Select Purpose</option>
													<?php
														$list = array("Stock", "Repair", "Supply");
														$length = count($list);
														for($i = 0; $i < $length; ++$i) {
															if($mrPurpose == $list[$i]) {
																echo "<option value='$list[$i]' selected='selected'>$list[$i]</option>";
															} else {
																echo "<option value='$list[$i]'>$list[$i]</option>";
															}
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Date Required</label>
												<select name="mrDateReq" class="form-control input-lg" required>
													<option value="">Select Date Required</option>
													<?php
														$list = array("Immediate", "One Week", "Two Weeks", "One Month", "Two Months");
														$length = count($list);
														for($i = 0; $i < $length; ++$i) {
															if($mrDateReq == $list[$i]) {
																echo "<option value='$list[$i]' selected='selected'>$list[$i]</option>";
															} else {
																echo "<option value='$list[$i]'>$list[$i]</option>";
															}
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="row margin-top-65">
										<div class="col-md-12">
											<table class="table table-hover table-light">
												<thead>
													<tr class="uppercase">
														<th class="center" style="width: 1%">#</th>
														<th class="left" style="width: 16%">Part No</th>
														<th class="left" style="width: 16%">Description</th>
														<th class="center" style="width: 9%">Qty</th>
														<th class="center" style="width: 9%">UOM</th>
														<th class="center" style="width: 10%">Stk. Qty</th>
														<th class="center" style="width: 14%">Type</th>
														<th class="center" style="width: 14%">Model</th>
														<th class="center" style="width: 11%">Equip. No</th>
													</tr>
												</thead>
												<tbody>
													<?php
														for($j = 0; $j < $rowDetails; ++$j) {
															$sn = $j + 1;
															$mrfDetailsPartsNumber = mysql_result($resultDetails, $j, 'mrfDetailsPartsNumber');
															$mrfDetailsDescription = mysql_result($resultDetails, $j, 'mrfDetailsDescription');
															$mrfDetailsQty = mysql_result($resultDetails, $j, 'mrfDetailsQty');
															$mrfDetailsUom = mysql_result($resultDetails, $j, 'mrfDetailsUom');
															$mrfDetailsStockQty = mysql_result($resultDetails, $j, 'mrfDetailsStockQty');
															$mrfDetailsEquipType = mysql_result($resultDetails, $j, 'mrfDetailsEquipType');
															$mrfDetailsModel = mysql_result($resultDetails, $j, 'mrfDetailsModel');
															$mrfDetailsEquipNo = mysql_result($resultDetails, $j, 'mrfDetailsEquipNo');
															echo "<tr>";
															echo "<td align='center'>$sn</td>";
															echo "<td align='left'>$mrfDetailsPartsNumber</td>";
															echo "<td align='left'>$mrfDetailsDescription</td>";
															if($mrReviewStatus == "No Status") {
																echo "<td align='center'><input type='text' class='form-control input-sm' name='prcQty$j' value='$mrfDetailsQty' style='text-align: center' required></td>";
															} else {
																echo "<td align='center'>$mrfDetailsQty</td>";
															}
															echo "<td align='center'>$mrfDetailsUom</td>";
															echo "<td align='center'>$mrfDetailsStockQty</td>";
															echo "<td align='center'>$mrfDetailsEquipType</td>";
															echo "<td align='center'>$mrfDetailsModel</td>";
															if($mrReviewStatus == "No Status") {
																echo "<td align='center'><select id='prcEquipNo$j' name='prcEquipNo$j' class='form-control input-sm' required>";
																echo "<option value=''>Select Equip No</option>";
																// Get equipment number from text file.
																/*
																$list = array();
																$list = explode("\n", file_get_contents('equipLists.txt'));
																$length = count($list);
																for($i = 0; $i < $length-1; ++$i) {
																	if($mrfDetailsEquipNo == $list[$i]) {
																		echo "<option value='$list[$i]' selected='selected'>$list[$i]</option>";
																	} else {
																		echo "<option value='$list[$i]'>$list[$i]</option>";
																	}
																}
																*/
																$handle = fopen("equipLists.csv", "r");
																if ($handle !== FALSE) {
																	while(($data = fgetcsv($handle, 100, ",")) !== FALSE ) {
																		if($mrfDetailsEquipNo == $data[0]) {
																			echo "<option value='$data[0]' selected='selected'>$data[0]\t($data[1])</option>";
																		} else {
																			echo "<option value='$data[0]'>$data[0]\t($data[1])</option>";
																		}
																	}
																	fclose($handle);
																}
																echo "</select></td>";
															} else {
																echo "<td align='center'>$mrfDetailsEquipNo</td>";
															}
															echo "</tr>";
														};
													?>
												</tbody>
											</table>
											<button type="button" class="btn btn-sm default" onclick="addNewField();" style="display: none">Add</button>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Total Request</label>
												<input id="mrTotal" type="text" class="form-control input-sm" name="mrTotal" style="text-align: center" placeholder="Total Request" value="<?php echo $mrTotal; ?>">
											</div>
										</div>
										<div class="col-xs-10 col-md-10">
											<div class="form-group">
												<label>Remarks</label>
												<input id="mrRemark" type="text" class="form-control input-sm" name="mrRemark" placeholder="Remarks" value="<?php echo $mrRemark; ?>">
											</div>
										</div>
									</div>
									<div class="row margin-top-65">
										<div class="col-xs-4 col-md-4">
											<div class="form-group" style="text-align: center">
												<label>Requested By</label>
												<input id="mrRequestBy" type="text" class="form-control input-lg" name="mrRequestBy" style="text-align: center" placeholder="Requested By" value="<?php echo $mrRequestBy; ?>">
											</div>
										</div>
										<div class="col-xs-4 col-md-4">
											<div class="form-group" style="text-align: center">
												<?php
													if($mrReviewStatus == "No Status") {
														if(in_array($position, (array("Root", "Su", "Chief Mechanic")))) {
												?>
															<label style="display: block; text-align: center; margin: auto; padding-bottom: 12px;">Reviewed by Department Head</label>
															<input id="mrReviewed" type="button" value="Review" class="btn btn-success">
														<?php } else { ?>
															<label>Reviewed by Department Head</label>
															<input type="text" class="form-control input-lg" name="mrReviewStatus" style="text-align: center" value="<?php echo $mrReviewStatus; ?>">
														<?php } ?>
													<?php } else { ?>
														<label>Reviewed by Department Head</label>
														<input type="text" class="form-control input-lg" name="mrReviewStatus" style="text-align: center" value="<?php echo $mrReviewStatus; ?>">
												<?php } ?>
											</div>
										</div>
										<div class="col-xs-4 col-md-4">
											<div class="form-group" style="text-align: center">
												<?php if($mrReviewStatus == "No Status") { ?>
												<!--
													<label>Approved by Operation Manager</label>
													<input type="text" class="form-control input-lg" name="mrApproveStatus" style="text-align: center" value="<?php echo $mrApproveStatus; ?>">
												-->
												<?php
													} else {
														if($mrApproveStatus == "No Status") {
												?>
															<?php if(in_array($position, (array("Root", "Su", "Operation Manager")))) {?>
																<label style="display: block; text-align: center; margin: auto; padding-bottom: 12px;">Approved by Operation Manager</label>
																<input id="mrfApproved" type="button" value="Approve" class="btn btn-success">
																<input id="mrfReject" type="button" value="REJECT" class="btn btn-default">
															<?php } else { ?>
																<label>Approved by Operation Manager</label>
																<input type="text" class="form-control input-lg" name="mrApproveStatus" style="text-align: center" value="<?php echo $mrApproveStatus; ?>">
															<?php } ?>
														<?php } else { ?>
															<label>Approved by Operation Manager</label>
															<input type="text" class="form-control input-lg" name="mrApproveStatus" style="text-align: center" value="<?php echo $mrApproveStatus; ?>">
														<?php } ?>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<?php if($mrReviewStatus == "No Status") { ?>
										<input type="submit" value="Update" class="btn blue">
										<a href="prc.material_request.php"><button type="button" class="btn default">Close</button></a>
										<label class="cancel-or-padding">or</label>
										<input type="button" id="mrfDel" value="DELETE" class="btn red">
									<?php } elseif($mrApproveStatus <> "No Status") { ?>
										<a href="prc.material_request.php"><button type="button" class="btn default">Close</button></a>
										<a href="prc.material_request.php"><img class='print-preview' src='images/print_preview.png'>
									<?php } else { ?>
										<a href="prc.material_request.php"><button type="button" class="btn default">Close</button></a>
									<?php } ?>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- <div class="col-md-1"></div> -->
			</div>
		</div>
	</div>
</div>
<? include('pages/page_jquery.php'); ?>
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
	$(function() {
		var mrSN = document.getElementById("mrSN").value;
		$('#mrfDel').click(function(){
			alertify.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
				if(result) {
					window.location="prc.mod_material_request.php?delMrSN=" + mrSN;
				}
			});
		})
	})
	// Review confirm.
	$(function() {
		var mrSN = document.getElementById("mrSN").value;
		$('#mrReviewed').click(function() {
			alertify.confirm("[REVIEW]  Are you sure you have REVIEW this request form?", function(result) {
				if(result) {
					window.location = "prc.mod_material_request.php?reviewedMrSN=" + mrSN;
				}
			})
		})
	})
	// Operation Manager approved.
	$(function() {
		var mrSN = document.getElementById("mrSN").value;
		$('#mrfApproved').click(function() {
			alertify.confirm("[APPROVE]  Are you sure you want to APPROVE this request Form?", function(result) {
				if(result) {
					window.location = "prc.mod_material_request.php?approveMrSN=" + mrSN;
				}
			})
		})
	})
	// Operation Manager reject.
	$(function() {
		var mrSN = document.getElementById("mrSN").value;
		$('#mrfReject').click(function() {
			alertify.confirm("[REJECT]  Are you sure you want to REJECT this request Form?", function(result) {
				if(result) {
					window.location = "prc.mod_material_request.php?reviewedMrSN=" + mrSN;
				}
			})
		})
	})
</script>
<? include('pages/page_footer.php'); ?>
</body>
</html>