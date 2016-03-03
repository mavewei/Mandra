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
				// Select material request form details.
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * FROM prcMaterialRequestForm WHERE mrSn = '$mrSN'";
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
					$mrApproveStatus = $data['mrApproveStatus'];
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
					$mrReviewStatus = mysql_escape_string($_POST['mrReviewStatus']);
					$mrApproveStatus = mysql_escape_string($_POST['mrApproveStatus']);
					// Array for each parts details
					$mrfDetailsQtyArray = array();
					$mrfDetailsPlateNoArray = array();
					for($i = 0; $i < $mrTotal; $i++) {
						$mrfDetailsQty = "prcQty#" . $i;
						$mrfDetailsPlateNo = "prcPlateNo#" . $i;
						$mrfDetailsQtyArray[] = mysql_escape_string($_POST[$mrfDetailsQty]);
						$mrfDetailsPlateNoArray[] = strtoupper(mysql_escape_string($_POST[$mrfDetailsPlateNo]));
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
							$mrfDetailsPartsNumber = explode("#", $mrfDetailsPartsNumberArray[$j]);
							$mrfDetailsQty = explode("#", $mrfDetailsQtyArray[$j]);
							$mrfDetailsPlateNo = explode("#", $mrfDetailsPlateNoArray[$j]);
							//$mrfDetailsPartsNumber = $mrfDetailsPartsNumberArray[$j];
							//$mrfDetailsQty = $mrfDetailsQtyArray[$j];
							//$mrfDetailsPlateNo = $mrfDetailsPlateNoArray[$j];
							// $mrfDetailsPartsNumber = explode("&", $mrfDetailsPartsNumber);
							$query = "UPDATE prcMaterialRequestFormDetails SET
											dateTime = '$time', mrfDetailsQty = '$mrfDetailsQty[0]',
											mrfDetailsPlateNo = '$mrfDetailsPlateNo[0]'
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
												<input type="text" class="form-control input-lg" name="mrDateTime" style="text-align: center" value="<? echo $dateTime; ?>" readonly>
											</div>
										</div>
										<div class="col-md-8"></div>
										<div class="col-md-2">
											<div class="form-group">
												<label>Series Number</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="mrSN" value="<?php echo $mrSN; ?>" readonly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-2"></div>
										<div class="col-md-2">
											<div class="form-group">
												<label>MR Date</label>
												<input type="text" class="form-control input-lg" name="mrDateTime" style="text-align: center" value="<? echo $mrDateTime; ?>" readonly>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>MR No</label>
												<input type="text" class="form-control input-lg" name="mrNumber" value="<? echo $mrNumber; ?>">
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
										<div class="col-md-2">
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
									<div class="row margin-top-30">
										<div class="col-md-12">
											<table class="table table-hover table-light">
												<thead>
													<tr class="uppercase">
														<th class="center" style="width: 1%">#</th>
														<th class="left" style="width: 19%">Part No</th>
														<th class="left" style="width: 18%">Description</th>
														<th class="center" style="width: 9%">Qty</th>
														<th class="center" style="width: 9%">UOM</th>
														<th class="center" style="width: 8%">Stk. Qty</th>
														<th class="center" style="width: 12%">Type</th>
														<th class="center" style="width: 14%">Model</th>
														<th class="center" style="width: 10%">Plate No</th>
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
															$mrfDetailsPlateNo = mysql_result($resultDetails, $j, 'mrfDetailsPlateNo');
															echo "<tr>";
															echo "<td align=\"center\">$sn</td>";
															echo "<td align=\"left\">$mrfDetailsPartsNumber</td>";
															echo "<td align=\"left\">$mrfDetailsDescription</td>";
															echo "<td align=\"center\"><input type='text' class='form-control input-sm' name='prcQty#$j' value='$mrfDetailsQty' style='text-align: center' required></td>";
															echo "<td align=\"center\">$mrfDetailsUom</td>";
															echo "<td align=\"center\">$mrfDetailsStockQty</td>";
															echo "<td align=\"center\">$mrfDetailsEquipType</td>";
															echo "<td align=\"center\">$mrfDetailsModel</td>";
															echo "<td align=\"center\"><input type='text' class='form-control input-sm' name='prcPlateNo#$j' value='$mrfDetailsPlateNo' style='text-align: center'></td>";
															echo "</tr>";
														};
													?>
												</tbody>
											</table>
											<button type="button" class="btn btn-sm default" onclick="addNewField();" style="display: none">Add</button>
										</div>
									</div>
									<div class="row">
										<div class="col-md-2">
											<div class="form-group">
												<label>Total Request</label>
												<input id="mrTotal" type="text" class="form-control input-sm" name="mrTotal" style="text-align: center" placeholder="Total Request" value="<?php echo $mrTotal; ?>">
											</div>
										</div>
										<div class="col-md-10">
											<div class="form-group">
												<label>Remarks</label>
												<input id="mrRemark" type="text" class="form-control input-sm" name="mrRemark" placeholder="Remarks" value="<?php echo $mrRemark; ?>">
											</div>
										</div>
									</div>
									<div class="row margin-top-30">
										<div class="col-md-4">
											<div class="form-group" style="text-align: center">
												<label>Requested By</label>
												<input id="mrRequestBy" type="text" class="form-control input-lg" name="mrRequestBy" style="text-align: center" placeholder="Requested By" value="<?php echo $mrRequestBy; ?>">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group" style="text-align: center">
												<label>Reviewed by Department Head</label>
												<input id="mrRequestBy" type="text" class="form-control input-lg" name="mrReviewStatus" style="text-align: center" value="<?php echo $mrReviewStatus; ?>">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group" style="text-align: center">
												<label>Approved by Operation Manager</label>
												<input id="mrRequestBy" type="text" class="form-control input-lg" name="mrApproveStatus" style="text-align: center" value="<?php echo $mrApproveStatus; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Update" class="btn blue">
									<a href="prc.material_request.php"><button type="button" class="btn default">Close</button></a>
									<label class="cancel-or-padding">or</label>
									<input type="button" id="partsDel" value="DELETE" class="btn red">
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
<? include('pages/page_footer.php'); ?>
</body>
</html>