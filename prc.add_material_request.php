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
				$mrDepartment = $_SESSION['DEPARTMENT'];
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
				// Get MRF details.
				$query = "SELECT * FROM prcMaterialRequestForm ORDER BY id DESC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($row == 0) {
					$prcMaterialFormId = 'MR0001';
				} else {
					$row++;
					$prcMaterialFormId = 'MR' . sprintf('%04d', $row);
				}
				// Get MR number details.
				$queryCount = "SELECT * FROM prcMaterialRequestForm WHERE mrDepartment = '$mrDepartment'";
				$resultCount = mysql_query($queryCount);
				$rowCount = mysql_num_rows($resultCount);
				if(!$result) die ("Table access failed: " . mysql_error());
				$mrNumber = getMrNumber($rowCount, $mrDepartment);
				// Select part no
				$queryParts = "SELECT partsNumber, partsDescription FROM partsMasterFile
								   WHERE status = 'Active' ORDER BY partsNumber ASC";
				$resultParts = mysql_query($queryParts);
				$rowsParts = mysql_num_rows($resultParts);
				if(!$resultParts) die ("Table access failed: " . mysql_error());
				// Check partsDetails file exists.
				$filename = 'partsDetails.csv';
				if(!file_exists($filename)) {
					// Write parts number to file.
					$fh = fopen('partsDetails.csv', 'w');
					// keeps getting the next row until there are no more to get
					while($row = mysql_fetch_array($resultParts)) {
				    		fputcsv($fh, array($row['partsNumber'], $row['partsDescription']), ",");
					}
					fclose($fh);
				}
				// Select equipNumber From equipLists
				$queryEquip = "SELECT equipNumber, equipDescription FROM equipLists ORDER BY equipNumber ASC";
				$resultEquip = mysql_query($queryEquip);
				$rowsEquip = mysql_num_rows($resultEquip);
				if(!$resultEquip) die ("Table access failed: " . mysql_error());
				// Check equipLists file exists.
				/*
				$filename = 'equipLists.txt';
				if(!file_exists($filename)) {
					$fh = fopen('equipLists.txt', 'w');
					while($data = mysql_fetch_array($resultEquip)) {
						fwrite($fh, $data[0]);
					    fwrite($fh, "\n");
					}
					fclose($fh);
				}
				*/
				$filename = 'equipLists.csv';
				if(!file_exists($filename)) {
					// Write equipment number to file.
					$fh = fopen('equipLists.csv', 'w');
					// keeps getting the next row until there are no more to get
					while($row = mysql_fetch_array($resultEquip)) {
				    		fputcsv($fh, array($row['equipNumber'], $row['equipDescription']), ",");
					}
					fclose($fh);
				}

				// Form submitted.
				if(isset($_POST['mrNumber']) && isset($_POST['mrPurpose'])) {
					$mrDateTime = mysql_escape_string($_POST['mrDateTime']);
					$mrSN = mysql_escape_string($_POST['mrSN']);
					$mrNumber = strtoupper(mysql_escape_string($_POST['mrNumber']));
					$mrDepartment = mysql_escape_string($_POST['mrDepartment']);
					$mrPurpose = mysql_escape_string($_POST['mrPurpose']);
					$mrDateReq = mysql_escape_string($_POST['mrDateReq']);
					$mrTotal = mysql_escape_string($_POST['mrTotal']);
					$mrRemark = ucfirst(strtolower(mysql_escape_string($_POST['mrRemark'])));
					$mrRequestBy = $login;
					$mrReviewStatus = "No Status";
					$mrApproveStatus = "No Status";
					$status = "Active";
					// Set n/a to null input
					if($mrRemark == "") $mrRemark = "N/A";
					// Array for each parts details
					$mrfDetailsPartsNumberArray = array();
					$mrfDetailsDescriptionArray = array();
					$mrfDetailsQtyArray = array();
					$mrfDetailsUomArray = array();
					$mrfDetailsStockQtyArray = array();
					$mrfDetailsEquipTypeArray = array();
					$mrfDetailsModelArray = array();
					$mrfDetailsEquipNoArray = array();
					// $mrfDetailsStockQtyArray = array();
					for($i = 0; $i < $mrTotal; $i++) {
						$mrfDetailsPartsNumber = "partNo" . $i;
						$mrfDetailsDescription = "partsDescription" . $i;
						$mrfDetailsQty = "prcQty" . $i;
						$mrfDetailsUom = "partsUom" . $i;
						$mrfDetailsStockQty = "prcStockQty" . $i;
						$mrfDetailsEquipType = "partsEquipType" . $i;
						$mrfDetailsModel = "partsModel" . $i;
						$mrfDetailsEquipNo = "prcEquipNo" . $i;
						$mrfDetailsPartsNumberArray[] = mysql_escape_string($_POST[$mrfDetailsPartsNumber]);
						$mrfDetailsDescriptionArray[] = ucwords(strtolower(mysql_escape_string($_POST[$mrfDetailsDescription])));
						$mrfDetailsQtyArray[] = mysql_escape_string($_POST[$mrfDetailsQty]);
						$mrfDetailsUomArray[] = ucfirst(strtolower(mysql_escape_string($_POST[$mrfDetailsUom])));
						$mrfDetailsStockQtyArray[] = mysql_escape_string($_POST[$mrfDetailsStockQty]);
						$mrfDetailsEquipTypeArray[] = ucwords(strtolower(mysql_escape_string($_POST[$mrfDetailsEquipType])));
						$mrfDetailsModelArray[] = mysql_escape_string($_POST[$mrfDetailsModel]);
						$mrfDetailsEquipNoArray[] = mysql_escape_string($_POST[$mrfDetailsEquipNo]);
					};
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "INSERT INTO prcMaterialRequestForm(dateTime, mrSN, mrNumber, mrDepartment, mrPurpose,
									mrDateReq, mrTotal, mrRemark, mrRequestBy, mrReviewStatus, mrApproveStatus, status)
								VALUES('$time', '$mrSN', '$mrNumber', '$mrDepartment', '$mrPurpose', '$mrDateReq',
									'$mrTotal', '$mrRemark', '$mrRequestBy', '$mrReviewStatus', '$mrApproveStatus',
									'$status')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// Material request form created and redirected to previous page.
						for($j = 0; $j < $mrTotal; $j++) {
							$mrfDetailsPartsNumber = substr($mrfDetailsPartsNumberArray[$j], 0, -1);
							$mrfDetailsDescription = $mrfDetailsDescriptionArray[$j];
							$mrfDetailsQty = $mrfDetailsQtyArray[$j];
							$mrfDetailsUom = $mrfDetailsUomArray[$j];
							$mrfDetailsStockQty = $mrfDetailsStockQtyArray[$j];
							$mrfDetailsEquipType = $mrfDetailsEquipTypeArray[$j];
							$mrfDetailsModel = $mrfDetailsModelArray[$j];
							$mrfDetailsEquipNo = substr($mrfDetailsEquipNoArray[$j], 0, -1);
							/*
							$mrfDetailsPartsNumber = explode("#", $mrfDetailsPartsNumberArray[$j]);
							$mrfDetailsDescription = explode("#", $mrfDetailsDescriptionArray[$j]);
							$mrfDetailsQty = explode("#", $mrfDetailsQtyArray[$j]);
							$mrfDetailsUom = explode("#", $mrfDetailsUomArray[$j]);
							$mrfDetailsStockQty = explode("#", $mrfDetailsStockQtyArray[$j]);
							$mrfDetailsEquipType = explode("#", $mrfDetailsEquipTypeArray[$j]);
							$mrfDetailsModel = explode("#", $mrfDetailsModelArray[$j]);
							$mrfDetailsEquipNo = explode("#", $mrfDetailsEquipNoArray[$j]);
							*/
							// Set n/a to null input
							if($mrfDetailsDescription == "") $mrfDetailsDescription = "N/A";
							if($mrfDetailsUom == "") $mrfDetailsUom = "N/A";
							if($mrfDetailsStockQty == "") $mrfDetailsStockQty = "N/A";
							if($mrfDetailsEquipType[0] == "") $mrfDetailsEquipType = "N/A";
							if($mrfDetailsModel == "") $mrfDetailsModel = "N/A";
							//if($mrfDetailsEquipNo == "") $mrfDetailsEquipNo = "N/A";
							// if($mrfDetailsEquipNo[0] == "") $mrfDetailsEquipNo[0] = "N/A";
							$query = "INSERT INTO prcMaterialRequestFormDetails
											(dateTime, mrfDetailsSN, mrfDetailsNumber, mrfDetailsPartsNumber,
											 mrfDetailsDescription, mrfDetailsQty, mrfDetailsUom, mrfDetailsStockQty,
											 mrfDetailsEquipType, mrfDetailsModel, mrfDetailsEquipNo)
										VALUES('$time', '$mrSN', '$j', '$mrfDetailsPartsNumber', '$mrfDetailsDescription',
												  '$mrfDetailsQty', '$mrfDetailsUom', '$mrfDetailsStockQty',
												  '$mrfDetailsEquipType', '$mrfDetailsModel', '$mrfDetailsEquipNo')";
							// Query with explode # in above.
							/*
							$query = "INSERT INTO prcMaterialRequestFormDetails
											(dateTime, mrfDetailsSN, mrfDetailsNumber, mrfDetailsPartsNumber,
											 mrfDetailsDescription, mrfDetailsQty, mrfDetailsUom, mrfDetailsStockQty,
											 mrfDetailsEquipType, mrfDetailsModel, mrfDetailsEquipNo)
										VALUES('$time', '$mrSN', '$j', '$mrfDetailsPartsNumber[0]', '$mrfDetailsDescription[0]',
												  '$mrfDetailsQty[0]', '$mrfDetailsUom[0]', '$mrfDetailsStockQty[0]',
												  '$mrfDetailsEquipType[0]', '$mrfDetailsModel[0]', '$mrfDetailsEquipNo[0]')";
							*/
							$result = mysql_query($query);
							if(!$result) die ("Database access failed: " . mysql_error());
							if($result) {
								$_SESSION['STATUS'] = 42;
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
	function getMrNumber($row, $mrDepartment) {
		if($row == 0) {
			if($mrDepartment == "M. Office") {
				$mrNumber = "MO" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Workshop") {
				$mrNumber = "WS" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Warehouse") {
				$mrNumber = "WH" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Log Pond") {
				$mrNumber = "LB" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Log Pond GV") {
				$mrNumber = "LV" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Camp B") {
				$mrNumber = "CB" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Camp C") {
				$mrNumber = "CC" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Camp I") {
				$mrNumber = "CI" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Camp L") {
				$mrNumber = "CH" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} else {
				$mrNumber = "Invalid Location!";
			}
			return $mrNumber;
		} else {
			$row = $row + 1;
			if($mrDepartment == "M. Office") {
				$mrNumber = "MO" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Workshop") {
				$mrNumber = "WS" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Warehouse") {
				$mrNumber = "WH" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Log Pond") {
				$mrNumber = "LB" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Log Pond GV") {
				$mrNumber = "LV" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Camp B") {
				$mrNumber = "CB" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Camp C") {
				$mrNumber = "CC" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Camp I") {
				$mrNumber = "CI" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Camp L") {
				$mrNumber = "CH" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} else {
				$mrNumber = "Invalid Location!";
			}
			return $mrNumber;
		}
	}
?>
<? include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Material Request Form <small>add new material request</small></h1>
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
				<div class="col-md-12">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Material Request Form</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-2">
											<div class="form-group" style="display: none">
												<label>MR Date</label>
												<input type="text" class="form-control input-lg" name="mrDateTime" style="text-align: center" value="<? echo date("d/m/Y"); ?>" readonly>
											</div>
										</div>
										<div class="col-md-8"></div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Series Number</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="mrSN" value="<?php echo $prcMaterialFormId; ?>" readonly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-2"></div>
										<div class="col-md-2">
											<div class="form-group">
												<label>MR Date</label>
												<input type="text" class="form-control input-lg" name="mrDateTime" style="text-align: center" value="<? echo date("Y-m-d"); ?>" readonly>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>MR No</label>
												<input type="text" class="form-control input-lg" name="mrNumber" value="<?php echo $mrNumber; ?>">
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Department</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="mrDepartment" value="<? echo $mrDepartment; ?>" readonly>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Purpose</label>
												<select name="mrPurpose" class="form-control input-lg" required>
													<option value="">Select Purpose</option>
													<option value="Stock">Stock</option>
													<option value="Repair">Repair</option>
													<option value="Supply">Supply</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Date Required</label>
												<select name="mrDateReq" class="form-control input-lg" required>
													<option value="">Select Date Required</option>
													<option value="Immediate">Immediate</option>
													<option value="One Week">One Week</option>
													<option value="Two Weeks">Two Weeks</option>
													<option value="One Month">One Month</option>
													<option value="Two Months">Two Months</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row margin-top-30">
										<div class="col-md-12">
											<table class="table table-hover table-light">
												<thead>
													<tr class="uppercase">
														<th class="center" style="width: 1%">#</th>
														<th class="left" style="width: 16%">Part No</th>
														<th class="left" style="width: 16%">Description</th>
														<th class="center" style="width: 8%">Qty</th>
														<th class="center" style="width: 8%">UOM</th>
														<th class="center" style="width: 10%">Stk. Qty</th>
														<th class="center" style="width: 13%">Type</th>
														<th class="center" style="width: 13%">Model</th>
														<th class="center" style="width: 11%">Equip. No</th>
														<th class="center" style="width: 3%"></th>
														<!--
														<th class="center" style="width: 1%">#</th>
														<th class="left" style="width: 17%">Part No</th>
														<th class="left" style="width: 18%">Description</th>
														<th class="center" style="width: 9%">Qty</th>
														<th class="center" style="width: 9%">UOM</th>
														<th class="center" style="width: 10%">Stk. Qty</th>
														<th class="center" style="width: 13%">Type</th>
														<th class="center" style="width: 13%">Model</th>
														<th class="center" style="width: 9%">Plate No</th>
														<th class="center" style="width: 1%"></th>
														-->
													</tr>
												</thead>
												<tbody>
													<tr id="row0">
														<td align="center">1</td>
														<td align="center" id="select_1">
															<select id="partNo0" name="partNo0" class="form-control input-sm" onchange="partDetails(this.value);" required>
																<option value='--0'>Select Part No</option>
																<?php
																	$handle = fopen("partsDetails.csv", "r");
																	if ($handle !== FALSE) {
																	    //$row = 0;
																	    while(($data = fgetcsv($handle, 100, ",")) !== FALSE ) {
																	        //printf('<option value="%s">%s</option>', $data[0], $data[1]);
																	        $newPartsNumber = $data[0] . "0";
																	        echo "<option value='$newPartsNumber'>$data[0]\t ($data[1])</option>";
																	    }
																	    fclose($handle);
																	}
																?>
																<?php
																	/*
																	if($rowsParts < 1) {
																		// No part no were created.
																		echo "<option value=''>No Parts Found</option>";
																	} else {
																		// Parts lists.
																		echo "<option value='--0'>Select Part No</option>";
																		for($i = 0; $i < $rowsParts; ++$i) {
																			$partsNumber = mysql_result($resultParts, $i, 'partsNumber');
																			$partsDescription = mysql_result($resultParts, $i, 'partsDescription');
																			$newPartsNumber = $partsNumber . "0";
																			echo "<option value='$newPartsNumber'>$partsNumber\t ($partsDescription)</option>";
																		}
																	}
																	*/
																?>
															</select>
														</td>
														<td align="center"><input id="partsDescription0" name="partsDescription0" type="text" class="form-control input-sm"></td>
														<td align="center"><input id="prcQty0" name="prcQty0" type="text" class="form-control input-sm" style="text-align: center" required></td>
														<td align="center"><input id="partsUom0" name="partsUom0" type="text" class="form-control input-sm" style="text-align: center"></td>
														<td align="center"><input id="prcStockQty0" name="prcStockQty0" type="text" class="form-control input-sm"></td>
														<td align="center"><input id="partsEquipType0" name="partsEquipType0" type="text" class="form-control input-sm" style="text-align: center"></td>
														<td align="center"><input id="partsModel0" name="partsModel0" type="text" class="form-control input-sm" style="text-align: center"></td>
														<!-- <td align="center"><input id="prcPlateNo0" type="text" name="prcPlateNo0" class="form-control input-sm" style="text-align: center"></td> -->
														<td align="center">
															<select id="prcEquipNo0" name="prcEquipNo0" class="form-control input-sm" required>
																<option value="">Select Equip No</option>
																<?php
																	/*
																	$list = array();
																	$list = explode("\n", file_get_contents('equipLists.txt'));
																	$length = count($list);
																	for($i = 0; $i < $length-1; ++$i) {
																		$newEquipNumber = $list[$i] . "0";
																		echo "<option value='$newEquipNumber'>$list[$i]</option>";
																	}
																	*/
																?>
																<?php
																	$handle = fopen("equipLists.csv", "r");
																	if ($handle !== FALSE) {
																	    //$row = 0;
																	    while(($data = fgetcsv($handle, 100, ",")) !== FALSE ) {
																	        $newEquipNumber = $data[0] . "0";
																	        echo "<option value='$newEquipNumber'>$data[0]\t($data[1])</option>";
																	    }
																	    fclose($handle);
																	}

																?>
															</select>
														</td>
														<td id="removeField0" style="text-align: center"><img id="removeFieldFunc0" class='field-remove' src='images/minus2.jpg' onclick="removeRow();"></td>
													</tr>
													<tr id="addNewField"></tr>
												</tbody>
											</table>
											<button id="btnNewField" type="button" class="btn btn-sm default" onclick="addNewField();">Add</button>
										</div>
									</div>
									<div class="row margin-top-30">
										<div class="col-md-2">
											<div class="form-group">
												<label>Total Request</label>
												<input id="mrTotal" type="text" class="form-control input-lg" name="mrTotal" style="text-align: center" placeholder="Total Request">
											</div>
										</div>
										<div class="col-md-10">
											<div class="form-group">
												<label>Remarks</label>
												<input id="mrRemark" type="text" class="form-control input-lg" name="mrRemark" placeholder="Remarks">
											</div>
										</div>
									</div>
									<div class="row margin-top-30" style="display: none">
										<div class="col-md-4">
											<div class="form-group" style="text-align: center">
												<label>Requested By</label>
												<input type="text" class="form-control input-lg" name="mrRequestBy" style="text-align: center" placeholder="Requested By" value="<? echo $fname; ?>">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group" style="text-align: center">
												<label>Reviewed by Department Head</label>
												<input type="text" class="form-control input-lg" name="mrReviewStatus" style="text-align: center">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group" style="text-align: center">
												<label>Approved by Operation Manager</label>
												<input type="text" class="form-control input-lg" name="mrApproveStatus" style="text-align: center">
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Submit" class="btn blue">
									<a href="prc.material_request.php"><button type="button" class="btn default">Cancel</button></a>
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
<?php include('pages/page_jquery.php'); ?>
<script>
	var totalRow = 0;
	var totalRequest = 0;
	var addField = false;
	var index = 0;
	var indexOld = 0;
	partFlag = [false, false, false, false, false, false, false, false, false, false];
	function partDetails(idx) {
		// Remove last char.
		var partNumber = idx.substring(0, idx.length - 1);
		//alert(partNumber);
		index = idx.slice(-1);
		var nullPartNo = "--" + index;
		var newDescription = "#partsDescription" + index;
		var newUom = "#partsUom" + index;
		var newEquipType = "#partsEquipType" + index;
		var newModel = "#partsModel" + index;
		var newEquipNo = "#prcEquipNo" + index;
		// Remove all value on text field.
		$(newDescription).val("");
		$(newUom).val("");
		$(newEquipType).val("");
		$(newModel).val("");
		$(newEquipNo).val("");
		// Select null.
		if(idx == nullPartNo) {
			if(totalRequest == 0) {
				totalRequest = 0;
			} else {
				totalRequest--;
				partFlag[index] = false;
			}
			var removeFieldFuncIdx = "removeFieldFunc" + index;
			var removeFieldFunc = document.getElementById(removeFieldFuncIdx);
			removeFieldFunc.onclick = null;
			addField = false;
			$('#mrTotal').val(totalRequest);
			return
		} else {
			if(!partFlag[index]) {
				totalRequest++;
				partFlag[index] = true;
			}
			if(partNumber != "--") {
				$.ajax({
					type: 'post',
					url: 'check_data.php',
					data: {
						mrfPartNumber: partNumber,
					},
					success: function (response) {
						if(response) {
							var data = $.parseJSON(response);
							$(newDescription).val(data[0].partsDescription);
							$(newUom).val(data[0].partsUom);
							$(newEquipType).val(data[0].partsEquipType);
							$(newModel).val(data[0].partsModel);
							// Enable removeFieldFunc.
							var removeFieldFuncIdx = "removeFieldFunc" + index;
							var removeFieldFunc = document.getElementById(removeFieldFuncIdx);
							removeFieldFunc.onclick = removeRow;
							// Check open select dropdown.
							if(totalRequest > totalRow) {
								addField = true;
							} else {
								addField = false;
							}
						} else {
							alert("Parts Number Not Found!");
						}
		            }
				});
				$('#mrTotal').val(totalRequest);
			}
		}
	}
	/*
	function partDetails(idx) {
		var str = idx.split("#");
		var partNo = str[0];
		var nullPartNo = "--" + "#" +str[1];
		var newDescription = "partsDescription" + "#" + str[1];
		var newUom = "partsUom" + "#" + str[1];
		var newEquipType = "partsEquipType" + "#" + str[1];
		var newModel = "partsModel" + "#" + str[1];
		var newPartNo = "partNo" + "#" + str[1];
		//var newRemoveField = "removeField#" + str[1];
		if(idx == nullPartNo) {
			if(totalRequest == 0) {
				totalRequest = 0;
			} else {
				totalRequest--;
				partFlag[str[1]] = false;
			}
			$('#mrTotal').val(totalRequest);
			//document.getElementById("mrTotal").value = totalRequest;
			return
		} else {
			if(!partFlag[str[1]]) {
				totalRequest++;
				partFlag[str[1]] = true;
			}
			if(partNo != "--") {
				$.ajax({
					type: 'post',
					url: 'check_data.php',
					data: {
						partNo: partNo,
						totalRequest: totalRequest,
					},
					success: function (response) {
						if(response) {
							//alert(response);
							var data = $.parseJSON(response);
							var totalRequestIdx = totalRequest - 1;
							var idx = "removeField" + totalRequestIdx;
							//var removeFieldFunc = document.getElementById(idx);
							document.getElementById(newDescription).value = data[0].partsDescription;
							document.getElementById(newUom).value = data[0].partsUom;
							document.getElementById(newEquipType).value = data[0].partsEquipType;
							document.getElementById(newModel).value = data[0].partsModel;
							//removeFieldFunc.onclick = removeField;
							addField = true;
						} else {
							alert("Parts Number Not Found!");
						}
		            }
				});
			}
			$("#select_1").replaceWith("<td align='left'><input name='" + newPartNo + "' type='text' value='" + partNo + "' class='form-control input-sm' readonly></td>");
			$('#mrTotal').val(totalRequest);
		}
	}
	*/
	// Add new field
	function addNewField() {
		if(addField) {
			var removeFieldName = "removeField" + totalRow;
			var removeFieldFuncName = "removeFieldFunc" + totalRow;
			var removeFieldIdx = "#removeField" + totalRow;
			$(removeFieldIdx).replaceWith("<td id='" + removeFieldName + "' style='text-align: center; display: none'><img id='" + removeFieldFuncName + "' class='field-remove' src='images/minus2.jpg' onclick='removeRow();'></td>");
			$('#btnNewField').attr("disabled", true);
			if(totalRequest) {
				$.ajax({
					type: 'post',
					url: 'prc.add_field.php',
					data: {
						totalRequest: totalRequest,
					},
					success: function (response) {
						totalRow++;
						$('#addNewField').replaceWith(response);
						var removeFieldFuncIdx = "removeFieldFunc" + totalRow;
						var removeFieldFunc = document.getElementById(removeFieldFuncIdx);
						removeFieldFunc.onclick = null;
						addField = false;
						$('#btnNewField').attr("disabled", false);
		            }
				});
			}
		}
	}
	// Remove field
	function removeRow() {
		if(totalRow == 0) {
			$('#partNo0').prop('selectedIndex',0);
			$('#partsDescription0').val("");
			$('#prcQty0').val("");
			$('#partsUom0').val("");
			$('#prcStockQty0').val("");
			$('#partsEquipType0').val("");
			$('#partsModel0').val("");
			$('#prcEquipNo0').val("");
			totalRequest--;
			partFlag[index] = false;
			addField = false;
			var removeFieldFunc = document.getElementById("removeFieldFunc0");
			removeFieldFunc.onclick = null;
		}
		if(totalRow > 0) {
			var removeRowIdx = "#row" + totalRow;
			$(removeRowIdx).replaceWith("");
			totalRequest--;
			totalRow--;
			partFlag[index] = false;
			addField = true;
		}
		$('#mrTotal').val(totalRequest);
		/*
		var totalRequest = document.getElementById("mrTotal").value;
		if(totalRequest == 1) {
			$.ajax({
				type: 'post',
				url: 'prc.remove_field.php',
				data: {
					totalRequest: totalRequest,
				},
				success: function (response) {
					totalRequest = 0;
					var removeFieldFunc = document.getElementById('removeField0');
					removeFieldFunc.onclick = null;
					$('#mrTotal').val(totalRequest);
					$('#row0').replaceWith(response);
					addField = false;
	            }
			});
		}
		if(totalRequest > 1) {
			var totalRequestIdx = totalRequest - 1;
			var idx = "#row" + totalRequestIdx;
			$.ajax({
				type: 'post',
				url: 'prc.remove_field.php',
				data: {
					totalRequest: totalRequest,
				},
				success: function (response) {
					totalRequest = totalRequest - 1;
					var idx = "removeField" + totalRequest;
					var rowIdx = "#row" + totalRequest;
					$(rowIdx).replaceWith(response);
					var removeFieldFunc = document.getElementById(idx);
					removeFieldFunc.onclick = null;
					$('#mrTotal').val(totalRequest);
					addField = false;
	            }
			});
		}
		*/
	}
	// Disable all onclick function
	$(function() {
		var removeFieldFunc = document.getElementById("removeFieldFunc0");
		removeFieldFunc.onclick = null;
	})
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
</script>
<? include('pages/page_footer.php'); ?>
</body>
</html>