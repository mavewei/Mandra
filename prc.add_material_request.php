<? include('pages/page_header.php'); ?>
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<? include('pages/page_meta.php'); ?>
<?
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
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
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
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$queryCount = "SELECT * FROM prcMaterialRequestForm WHERE mrDepartment = '$mrDepartment'";
				$resultCount = mysql_query($queryCount);
				$rowCount = mysql_num_rows($resultCount);
				if(!$result) die ("Table access failed: " . mysql_error());
				$mrNumber = getMrNumber($rowCount, $mrDepartment);
				// Select part no
				$queryParts = "SELECT * FROM partsMasterFile WHERE status = 'Active' ORDER BY partsNumber ASC";
				$resultParts = mysql_query($queryParts);
				$rowsParts = mysql_num_rows($resultParts);
				if(!$resultParts) die ("Table access failed: " . mysql_error());
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
					$mrRequestBy = $fname;
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
					$mrfDetailsPlateNoArray = array();
					// $mrfDetailsStockQtyArray = array();
					for($i = 0; $i < $mrTotal; $i++) {
						$mrfDetailsPartsNumber = "partNo#" . $i;
						$mrfDetailsDescription = "partsDescription#" . $i;
						$mrfDetailsQty = "prcQty#" . $i;
						$mrfDetailsUom = "partsUom#" . $i;
						$mrfDetailsStockQty = "prcStockQty#" . $i;
						$mrfDetailsEquipType = "partsEquipType#" . $i;
						$mrfDetailsModel = "partsModel#" . $i;
						$mrfDetailsPlateNo = "prcPlateNo#" . $i;
						$mrfDetailsPartsNumberArray[] = mysql_escape_string($_POST[$mrfDetailsPartsNumber]);
						$mrfDetailsDescriptionArray[] = ucwords(strtolower(mysql_escape_string($_POST[$mrfDetailsDescription])));
						$mrfDetailsQtyArray[] = mysql_escape_string($_POST[$mrfDetailsQty]);
						$mrfDetailsUomArray[] = ucfirst(strtolower(mysql_escape_string($_POST[$mrfDetailsUom])));
						$mrfDetailsStockQtyArray[] = mysql_escape_string($_POST[$mrfDetailsStockQty]);
						$mrfDetailsEquipTypeArray[] = ucwords(strtolower(mysql_escape_string($_POST[$mrfDetailsEquipType])));
						$mrfDetailsModelArray[] = mysql_escape_string($_POST[$mrfDetailsModel]);
						$mrfDetailsPlateNoArray[] = strtoupper(mysql_escape_string($_POST[$mrfDetailsPlateNo]));
					};
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "INSERT INTO prcMaterialRequestForm(dateTime, mrSN, mrNumber, mrDepartment, mrPurpose,
																	  mrDateReq, mrTotal, mrRemark, mrRequestBy, mrReviewStatus,
																	  mrApproveStatus, status)
								VALUES('$time', '$mrSN', '$mrNumber', '$mrDepartment', '$mrPurpose', '$mrDateReq',
										  '$mrTotal', '$mrRemark', '$mrRequestBy', '$mrReviewStatus', '$mrApproveStatus',
										  '$status')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// Material request form created and redirected to previous page.
						for($j = 0; $j < $mrTotal; $j++) {
							$mrfDetailsPartsNumber = explode("#", $mrfDetailsPartsNumberArray[$j]);
							$mrfDetailsDescription = explode("#", $mrfDetailsDescriptionArray[$j]);
							$mrfDetailsQty = explode("#", $mrfDetailsQtyArray[$j]);
							$mrfDetailsUom = explode("#", $mrfDetailsUomArray[$j]);
							$mrfDetailsStockQty = explode("#", $mrfDetailsStockQtyArray[$j]);
							$mrfDetailsEquipType = explode("#", $mrfDetailsEquipTypeArray[$j]);
							$mrfDetailsModel = explode("#", $mrfDetailsModelArray[$j]);
							$mrfDetailsPlateNo = explode("#", $mrfDetailsPlateNoArray[$j]);
							// Set n/a to null input
							if($mrfDetailsDescription[0] == "") $mrfDetailsDescription[0] = "N/A";
							if($mrfDetailsUom[0] == "") $mrfDetailsUom[0] = "N/A";
							if($mrfDetailsStockQty[0] == "") $mrfDetailsStockQty[0] = "N/A";
							if($mrfDetailsEquipType[0] == "") $mrfDetailsEquipType[0] = "N/A";
							if($mrfDetailsModel[0] == "") $mrfDetailsModel[0] = "N/A";
							if($mrfDetailsPlateNo[0] == "") $mrfDetailsPlateNo[0] = "N/A";
							$query = "INSERT INTO prcMaterialRequestFormDetails
											(dateTime, mrfDetailsSN, mrfDetailsNumber, mrfDetailsPartsNumber,
											 mrfDetailsDescription, mrfDetailsQty, mrfDetailsUom, mrfDetailsStockQty,
											 mrfDetailsEquipType, mrfDetailsModel, mrfDetailsPlateNo)
										VALUES('$time', '$mrSN', '$j', '$mrfDetailsPartsNumber[0]', '$mrfDetailsDescription[0]',
												  '$mrfDetailsQty[0]', '$mrfDetailsUom[0]', '$mrfDetailsStockQty[0]',
												  '$mrfDetailsEquipType[0]', '$mrfDetailsModel[0]', '$mrfDetailsPlateNo[0]')";
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
			if($mrDepartment == "Monrovia") {
				$mrNumber = "MO" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Workshop") {
				$mrNumber = "WS" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Warehouse") {
				$mrNumber = "WH" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Log Pond Buchanan") {
				$mrNumber = "LB" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Log Pond Greenville") {
				$mrNumber = "LV" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Camp B") {
				$mrNumber = "CB" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "CC") {
				$mrNumber = "CC" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Camp I") {
				$mrNumber = "CI" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} elseif($mrDepartment == "Camp LHW") {
				$mrNumber = "CH" . "-" . date("Y") . "-" . date("m") . "-" . "01";
			} else {
				$mrNumber = "Invalid Location!";
			}
			return $mrNumber;
		} else {
			$row = $row + 1;
			if($mrDepartment == "Monrovia") {
				$mrNumber = "MO" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Workshop") {
				$mrNumber = "WS" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Warehouse") {
				$mrNumber = "WH" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Log Pond Buchanan") {
				$mrNumber = "LB" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Log Pond Greenville") {
				$mrNumber = "LV" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Camp B") {
				$mrNumber = "CB" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "CC") {
				$mrNumber = "CC" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Camp I") {
				$mrNumber = "CI" . "-" . date("Y") . "-" . date("m") . "-" . sprintf('%02d', $row);
			} elseif($mrDepartment == "Camp LHW") {
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
														<th class="left" style="width: 18%">Part No</th>
														<th class="left" style="width: 18%">Description</th>
														<th class="center" style="width: 9%">Qty</th>
														<th class="center" style="width: 9%">UOM</th>
														<th class="center" style="width: 10%">Stk. Qty</th>
														<th class="center" style="width: 13%">Type</th>
														<th class="center" style="width: 13%">Model</th>
														<th class="center" style="width: 9%">Plate No</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td align="center">1</td>
														<td align="center" id="select_1">
															<select name="partNo#0" class="form-control input-sm" onchange="partDetails(this.value);" required>
																<?
																	if($rowsParts < 1) {
																		// No part no were created.
																		echo "<option value=''>No Parts Found</option>";
																	} else {
																		// Parts lists.
																		echo "<option value='--#0'>Select Part No</option>";
																		for($i = 0; $i < $rowsParts; ++$i) {
																			$partsNumber = mysql_result($resultParts, $i, 'partsNumber');
																			echo "<option value='$partsNumber#0'>$partsNumber</option>";
																		}
																	}
																?>
															</select>
														</td>
														<td align="center"><input id="partsDescription#0" name="partsDescription#0" type="text" class="form-control input-sm"></td>
														<td align="center"><input type="text" class="form-control input-sm" name="prcQty#0" style="text-align: center" required></td>
														<td align="center"><input id="partsUom#0" name="partsUom#0" type="text" class="form-control input-sm" style="text-align: center"></td>
														<td align="center"><input type="text" name="prcStockQty#0" class="form-control input-sm"></td>
														<td align="center"><input id="partsEquipType#0" name="partsEquipType#0" type="text" class="form-control input-sm" style="text-align: center"></td>
														<td align="center"><input id="partsModel#0" name="partsModel#0" type="text" class="form-control input-sm" style="text-align: center"></td>
														<td align="center"><input type="text" name="prcPlateNo#0" class="form-control input-sm" style="text-align: center"></td>
													</tr>
													<tr id="addNewField"></tr>
												</tbody>
											</table>
											<button type="button" class="btn btn-sm default" onclick="addNewField();">Add</button>
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
<? include('pages/page_jquery.php'); ?>
<script>
	totalRequest = 0;
	addField = false;
	partFlag = [false, false, false, false, false, false, false, false, false, false];
	function partDetails(idx) {
		var str = idx.split("#");
		var partNo = str[0];
		var nullPartNo = "--" + "#" +str[1];
		var newDescription = "partsDescription" + "#" + str[1];
		var newUom = "partsUom" + "#" + str[1];
		var newEquipType = "partsEquipType" + "#" + str[1];
		var newModel = "partsModel" + "#" + str[1];
		var newPartNo = "partNo" + "#" + str[1];
		if(idx == nullPartNo) {
			if(totalRequest == 0) {
				totalRequest = 0;
			} else {
				totalRequest--;
				partFlag[str[1]] = false;
			}
			document.getElementById("mrTotal").value = totalRequest;
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
							document.getElementById(newDescription).value = data[0].partsDescription;
							document.getElementById(newUom).value = data[0].partsUom;
							document.getElementById(newEquipType).value = data[0].partsEquipType;
							document.getElementById(newModel).value = data[0].partsModel;
							addField = true;
						} else {
							alert("Parts Number Not Found!");
						}
		            }
				});
			}
			$("#select_1").replaceWith("<td align='left'><input name='" + newPartNo + "' type='text' value='" + partNo + "' class='form-control input-sm' readonly></td>");
			//$('#select_1').replaceWith('<td align="left"><input name="' + newPartNo + '" type="text" value="' + partNo + '" class="form-control input-sm" readonly></td>');
			$('#mrTotal').val(totalRequest);
		}
	}
	// Add new field
	function addNewField() {
		var totalRequest = document.getElementById("mrTotal").value;
		if(addField) {
			if(totalRequest) {
				$.ajax({
					type: 'post',
					url: 'prc.add_field.php',
					data: {
						totalRequest: totalRequest,
					},
					success: function (response) {
						$('#addNewField').replaceWith(response);
						addField = false;
		            }
				});
			}
		}
	}
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