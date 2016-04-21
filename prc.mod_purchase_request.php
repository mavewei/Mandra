<?php include('pages/page_header.php'); ?>
<link href="css/purchase-request.css" rel="stylesheet" type="text/css" />
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
				$prSN = mysql_escape_string($_GET['prSN']);
				$prSubstr = substr($prSN, -2);
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
				// Select purchase request.
				$queryPR = "SELECT * FROM purchaseRequest WHERE prSn = '$prSN' AND status = 'Active'";
				$resultPR = mysql_query($queryPR);
				$rowsPR = mysql_num_rows($resultPR);
				if(!$resultPR) {
					die ("Table access failed: " . mysql_error());
				} else {
					$dataPR = mysql_fetch_assoc($resultPR);
					$string = $dataPR['dateTime'];
					if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
						$prDateTime = $match[1];
					};
					$prNumber = $dataPR['prNumber'];
					$mrNumber = $dataPR['mrNumber'];
					$prDepartment = $dataPR['prDepartment'];
					$prPurpose = $dataPR['prPurpose'];
					$prDateReq = $dataPR['prDateReq'];
					$prModeDelivery = $dataPR['prModeDelivery'];
					$prTotal = $dataPR['prTotal'];
					$prReasonPurchase = $dataPR['prReasonPurchase'];
					$prRequestBy = $dataPR['prRequestBy'];
					$prWarehouseStatus = $dataPR['prWarehouseStatus'];
					$prHeadWorkshopStatus = $dataPR['prHeadWorkshopStatus'];
					$prApproveStatus = $dataPR['prApproveStatus'];
					$prFinalize = $dataPR['prFinalize'];
				}
				// Purchase request details
				$queryDetails = "SELECT * FROM purchaseRequestDetails WHERE prDetailsSN = '$prSN'";
				$resultDetails = mysql_query($queryDetails);
				$rowDetails = mysql_num_rows($resultDetails);
				if(!$resultDetails) die ("Table access failed: " . mysql_error());
				// Form submitted.
				if(isset($_POST['prNumber']) && isset($_POST['prModeDelivery'])) {
					$prSN = mysql_escape_string($_POST['prSN']);
					$prNumber = mysql_escape_string($_POST['prNumber'])	;
					$prPurpose = mysql_escape_string($_POST['prPurpose']);
					$prDateReq = mysql_escape_string($_POST['prDateReq']);
					$prModeDelivery = mysql_escape_string($_POST['prModeDelivery']);
					$prReasonPurchase = ucwords(strtolower(mysql_escape_string($_POST['prReasonPurchase'])));
					$queryFin = "UPDATE purchaseRequest SET prNumber = '$prNumber', prPurpose = '$prPurpose',
									 prDateReq = '$prDateReq', prModeDelivery = '$prModeDelivery',
									 prReasonPurchase = '$prReasonPurchase', prFinalize = 1
									 WHERE prSN = '$prSN'";
					$resultFin = mysql_query($queryFin);
					if(!$resultFin) {
						die ("Table access failed: " . mysql_error());
					} else {
						header('Location: prc.purchase_request.php');
					}
				}
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
	};
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Purchase Request <small>modify purchase request</small></h1>
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
					<a href="javascript:;">Purchase Request</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Purchase Request
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-xs-12 col-md-12">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Purchase Request</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>PR Date</label>
												<input type="text" class="form-control input-lg" name="prDateTime" style="text-align: center" value="<? echo $prDateTime; ?>" readonly>
											</div>
										</div>
										<div class="col-xs-8 col-md-8"></div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label style="color: red"><b>Series Number</b></label>
												<input id="prSN" type="text" class="form-control input-lg" style="text-align: center" name="prSN" value="<?php echo $prSN; ?>" readonly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>PR NO</label>
												<select name="prNumber" class="form-control input-lg" required>
													<option value="">Select PR NO</option>
													<?php
														$prNumListLP = "LP-" . date("Y") . "-" . date("m") . "-" . $prSubstr;
														$prNumListCN = "CN-" . date("Y") . "-" . date("m") . "-" . $prSubstr;
														$prNumListMY = "MY-" . date("Y") . "-" . date("m") . "-" . $prSubstr;
														$list = array($prNumListLP, $prNumListCN, $prNumListMY);
														$length = count($list);
														for($i = 0; $i < $length; ++$i) {
															if($prNumber == $list[$i]) {
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
												<label>MR NO</label>
												<input type="text" class="form-control input-lg" name="mrNumber" value="<? echo $mrNumber; ?>">
											</div>
										</div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Department</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="prDepartment" value="<? echo $prDepartment; ?>" readonly>
											</div>
										</div>
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Purpose</label>
												<select name="prPurpose" class="form-control input-lg" required>
													<option value="">Select Purpose</option>
													<?php
														$list = array("Stock", "Repair", "Supply");
														$length = count($list);
														for($i = 0; $i < $length; ++$i) {
															if($prPurpose == $list[$i]) {
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
												<select name="prDateReq" class="form-control input-lg" required>
													<option value="">Select Date Required</option>
													<?php
														$list = array("Immediate", "One Week", "Two Weeks", "One Month", "Two Months");
														$length = count($list);
														for($i = 0; $i < $length; ++$i) {
															if($prDateReq == $list[$i]) {
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
												<label>Delivery Mode</label>
												<select name="prModeDelivery" class="form-control input-lg" required>
													<option value="">Select Mode</option>
													<?php
														$list = array("By Air", "By Sea", "Handcarry", "Local Collection");
														$length = count($list);
														for($i = 0; $i < $length; ++$i) {
															if($prModeDelivery == $list[$i]) {
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
														<th class="center" style="width: auto">#</th>
														<th class="left" style="width: auto">Part No</th>
														<th class="left" style="width: auto">Description</th>
														<th class="center" style="width: auto">Qty</th>
														<th class="center" style="width: auto">UOM</th>
														<th class="center" style="width: auto">Type / Model / Equip. No</th>
														<!--
														<th class="center" style="width: 5%">#</th>
														<th class="left" style="width: 18%">Part No</th>
														<th class="left" style="width: 28%">Description</th>
														<th class="center" style="width: 5%">Qty</th>
														<th class="center" style="width: 5%">UOM</th>
														<th class="center" style="width: 10%">Stk. Qty</th>
														<th class="center" style="width: 14%">Type / Model / Equip. No</th>
														<th class="center" style="width: 14%">Type</th>
														<th class="center" style="width: 14%">Model</th>
														<th class="center" style="width: 11%">Equip. No</th>
														-->
													</tr>
												</thead>
												<tbody>
													<?php
														for($j = 0; $j < $rowDetails; ++$j) {
															$sn = $j + 1;
															$prDetailsPartsNumber = mysql_result($resultDetails, $j, 'prDetailsPartsNumber');
															$prDetailsDescription = mysql_result($resultDetails, $j, 'prDetailsDescription');
															$prDetailsQty = mysql_result($resultDetails, $j, 'prDetailsQty');
															$prDetailsUom = mysql_result($resultDetails, $j, 'prDetailsUom');
															$prDetailsEquipType = mysql_result($resultDetails, $j, 'prDetailsEquipType');
															$prDetailsModel = mysql_result($resultDetails, $j, 'prDetailsModel');
															$prDetailsEquipNo = mysql_result($resultDetails, $j, 'prDetailsEquipNo');
															echo "<tr>";
															echo "<td align='center'>$sn</td>";
															echo "<td align='left'>$prDetailsPartsNumber</td>";
															echo "<td align='left'>$prDetailsDescription</td>";
															echo "<td align='center'>$prDetailsQty</td>";
															echo "<td align='center'>$prDetailsUom</td>";
															echo "<td align='center'>$prDetailsEquipType  /  $prDetailsModel  /  $prDetailsEquipNo</td>";
															echo "</tr>";
														};
													?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-2 col-md-2">
											<div class="form-group">
												<label>Total Request</label>
												<input id="prTotal" type="text" class="form-control input-sm" name="prTotal" style="text-align: center" placeholder="Total Request" value="<?php echo $prTotal; ?>">
											</div>
										</div>
										<div class="col-xs-10 col-md-10">
											<div class="form-group">
												<label>Reason for Purchase Request</label>
												<input id="prReasonPurchase" type="text" class="form-control input-sm" name="prReasonPurchase" placeholder="Reason for Purchase Request" value="<?php echo $prReasonPurchase; ?>">
											</div>
										</div>
									</div>
									<div class="row margin-top-65">
										<div class="col-xs-3 col-md-3">
											<div class="form-group" style="text-align: center">
												<label>Requested By</label>
												<input id="prRequestBy" type="text" class="form-control input-lg" name="prRequestBy" style="text-align: center" placeholder="Requested By" value="<?php echo $prRequestBy; ?>">
											</div>
										</div>
										<div class="col-xs-3 col-md-3">
											<div class="form-group" style="text-align: center">
												<?php
													$msg = "";
													if($prFinalize == FALSE) {
														// Do nothing.
													} else {
														if($prWarehouseStatus == "No Status") {
															if(in_array($position, (array("Root", "Su", "Asst Warehouse Clerk")))) {
																$msg .= "<label style='display: block; text-align: center; margin: auto; padding-bottom: 12px;'>Warehouse</label>";
																$msg .= "<input id='mrReviewed' type='button' value='Review' class='btn btn-success'>";
															} else {
																$msg .= "<label>Warehouse</label>";
																$msg .= "<input type='text' class='form-control input-lg' name='mrReviewStatus' style='text-align: center' value='$prWarehouseStatus'>";
															}
														} else {
															$msg .= "<label>Warehouse</label>";
															$msg .= "<input type='text' class='form-control input-lg' name='mrReviewStatus' style='text-align: center' value='$prWarehouseStatus'>";
														}
													}
													echo $msg;
												?>
											</div>
										</div>
										<div class="col-xs-3 col-md-3">
											<div class="form-group" style="text-align: center">
												<?php
													$msg = "";
													if($prWarehouseStatus == "No Status") {
														// Do nothing.
													} else {
														if($prHeadWorkshopStatus == "No Status") {
															if(in_array($position, (array("Root", "Su", "Workshop Manager")))) {
																$msg .= "<label style='display: block; text-align: center; margin: auto; padding-bottom: 12px;'>Head of Workshop</label>";
																$msg .= "<input id='mrfApproved' type='button' value='Approve' class='btn btn-success'>";
																$msg .= "<input id='mrfReject' type='button' value='REJECT' class='btn btn-default'>";
															} else {
																$msg .= "<label>Head of Workshop</label>";
																$msg .= "<input type='text' class='form-control input-lg' name='mrApproveStatus' style='text-align: center' value='$mrApproveStatus'>";
															}
														} else {
															$msg .= "<label>Head of Workshop</label>";
															$msg .= "<input type='text' class='form-control input-lg' name='mrApproveStatus' style='text-align: center' value='$mrApproveStatus'>";
														}
													}
													echo $msg;
												?>
											</div>
										</div>
										<div class="col-xs-3 col-md-3">
											<div class="form-group" style="text-align: center">
												<?php
													$msg = "";
													if($prHeadWorkshopStatus == "No Status") {
														// Do nothing.
													} else {
														if($prApproveStatus == "No Status") {
															if(in_array($position, (array("Root", "Su", "Operation Manager")))) {
																$msg .= "<label style='display: block; text-align: center; margin: auto; padding-bottom: 12px;'>Approved by</label>";
																$msg .= "<input id='mrfApproved' type='button' value='Approve' class='btn btn-success'>";
																$msg .= "<input id='mrfReject' type='button' value='REJECT' class='btn btn-default'>";
															} else {
																$msg .= "<label>Approved by</label>";
																$msg .= "<input type='text' class='form-control input-lg' name='mrApproveStatus' style='text-align: center' value='$mrApproveStatus'>";
															}
														} else {
															$msg .= "<label>Approved by</label>";
															$msg .= "<input type='text' class='form-control input-lg' name='mrApproveStatus' style='text-align: center' value='$mrApproveStatus'>";
														}
													}
													echo $msg;
												?>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<?php
										$msg = "";
										if($prFinalize == FALSE) {
											// PR not yet finalize.
											$msg .= "<input type='submit' value='Finalize' class='btn blue'>";
										} else {
											$msg .= "<a href='prc.purchase_request.php'><button type='button' class='btn default'>Close</button></a>";
										}
										echo $msg;
									?>
								</div>
							</form>
						</div>
					</div>
				</div>
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
</script>
<? include('pages/page_footer.php'); ?>
</body>
</html>