<?php include('pages/page_header.php'); ?>
<link href="css/purchase-request.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<?php include('pages/page_meta.php'); ?>
<?php
	require_once('db/db_config.php');
	// Check session id.
	$login = $_SESSION['LOGIN_ID'];
	mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	$query = "SELECT * FROM tempSession WHERE emailAdd = '$login'";
	$result = mysql_query($query);
	if(!$result) die ("Table access failed: " . mysql_error());
	$data = mysql_fetch_assoc($result);
	$sid = $data['sid'];
	if($sid == $_SESSION['SID']) {
		if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
			if($_SESSION['GID'] < 4000) {
				$fname = $_SESSION['FNAME'];
				$uid = $_SESSION['UID'];
				$position = $_SESSION['POSITION'];
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
				$queryPR = "SELECT * FROM purchaseRequest
								INNER JOIN userAccounts ON purchaseRequest.prRequestBy = userAccounts.emailAdd
								WHERE purchaseRequest.status = 'Active' ORDER BY prSN DESC";
				$resultPR = mysql_query($queryPR);
				$rowsPR = mysql_num_rows($resultPR);
				if(!$resultPR) die ("Table access failed: " . mysql_error());

				// ==Pagination== //
				// Number of rows to show per page.
				$rowsPerPage = 8;
				// Find out total pages
				$totalPages = ceil($rowsPR / $rowsPerPage);
				// Get the current page or set a default
				if(isset($_GET['currentPage']) && is_numeric($_GET['currentPage'])) {
					// Cast var as int
					$currentPage = (int) $_GET['currentPage'];
				} else {
					// Default page num
					$currentPage = 1;
				}
				// If current page is greater than total pages
				if($currentPage > $totalPages) {
					// Set current page to last page
					$currentPage = $totalPages;
				}
				// If current page is less than first page
				if($currentPage < 1) {
					// Set current page to first page
					$currentPage = 1;
				}
				// The offset of the list, based on current page
				$offset = ($currentPage - 1) * $rowsPerPage;
				// Get the info from the db
				$queryPG = "SELECT * FROM purchaseRequest
								INNER JOIN userAccounts ON purchaseRequest.prRequestBy = userAccounts.emailAdd
								WHERE purchaseRequest.status = 'Active' ORDER BY prSN DESC LIMIT $offset, $rowsPerPage";
				$resultPG = mysql_query($queryPG);
				$rowsPG = mysql_num_rows($resultPG);
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
<? include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Purchase Request <small>manage purchase request</small></h1>
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
				<li class="active">
					Purchase Request
				</li>
			</ul>
			<div class="row margin-top-10">
				<!-- <div class="col-md-1"></div> -->
				<div class="col-md-12">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Purchase Request</span>
							</div>
							<!--
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="prc.add_purchase_request.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							-->
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<div id="searchDiv" class="table-scrollable table-scrollable-borderless">
								<table class="table table-hover table-light">
									<?php
										if($rowsPG > 0) {
											echo "<thead><tr class='uppercase'><th style='width: 10%'>S/N</th>";
											echo "<th class='center' style='width: 11%'>Date</th>";
											echo "<th class='left' style='width: 12%'>PR No</th>";
											echo "<th class='center' style='width: 11%'>Department</th>";
											//echo "<th class='center' style='width: 15%'>Purpose</th>";
											echo "<th class='center' style='width: 11%'>Date Required</th>";
											echo "<th class='center' style='width: 10%'>Total Req.</th>";
											echo "<th class='center' style='width: 11%'>Requester</th>";
											echo "<th class='center' style='width: 12%'>Reviewed</th>";
											echo "<th class='center' style='width: 12%'><font color='blue'>Approved</font> / <font color='red'>Rejected</font></th>";
											echo "</tr></thead><tbody>";
											for($j = 0; $j < $rowsPG; ++$j) {
												$prSN = mysql_result($resultPG, $j, 'prSN');
												$prNumber = mysql_result($resultPG, $j, 'prNumber');
												$prDepartment = mysql_result($resultPG, $j, 'prDepartment');
												$prDateReq = mysql_result($resultPG, $j, 'prDateReq');
												$prTotal = mysql_result($resultPG, $j, 'prTotal');
												$prRequestBy = mysql_result($resultPG, $j, 'firstName');
												$prWarehouseStatus = mysql_result($resultPG, $j, 'prWarehouseStatus');
												if($prWarehouseStatus == "No Status") {
													// Do nothing.
												} else {
													$string = mysql_result($resultPG, $j, 'prWarehouseDateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$prWarehouseStatus = $match[1];
													};
												}
												$prApproveStatus = mysql_result($resultPG, $j, 'prApproveStatus');
												if($prApproveStatus == "No Status") {} else {
													$string = mysql_result($resultPG, $j, 'prApprovedDateTime');
													if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
														$prApproveStatusDT = $match[1];
													};
												}
												$string = mysql_result($resultPG, $j, 'dateTime');
												if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
													$dateCreated = $match[1];
												};
												echo "<tr><td class='fit'><img class='parts-pic' src='images/mrf_form.png'>";
												echo "<a href='prc.mod_purchase_request.php?prSN=$prSN' class='name-padding primary-link'>$prSN</a></td>";
												echo "<td align='center'>$dateCreated</td>";
												echo "<td align='left'>$prNumber</td>";
												echo "<td align='center'>$prDepartment</td>";
												echo "<td align='center'>$prDateReq</td>";
												echo "<td align='center'>$prTotal</td>";
												echo "<td align='center'>$prRequestBy</td>";
												echo "<td align='center'>$prWarehouseStatus</td>";
												if($prApproveStatus == "Approved") {
													echo "<td align='center'><font color='blue'>$prApproveStatusDT</font></td>";
												} elseif($prApproveStatus == "Rejected") {
													echo "<td align='center'><font color='red'>$prApproveStatusDT</font></td>";
												} else {
													echo "<td align='center'>$prApproveStatus</td>";
												}
												echo "</tr>";
											};
											echo "</tbody>";
										} else {
											// No master file.
											echo "<h3 class='no-infor'>No Purchase Request found!</h3>";
										}
									?>
								</table>
								<nav style="text-align: center; <?php if($rowsPG < 1) echo 'display: none' ?>">
									<ul class="pagination">
										<?php
											// Pagination: http:\/\/www\.phpfreaks.com\/tutorial/basic-pagination
											if($currentPage == 1) {
												echo "<li class='disabled'>";
												echo "<a href='#' aria-label='Previous'";
												echo "<span aria-hidden='true'>&laquo;</span>";
												echo "</a></li>";
											} else {
												echo "<li>";
												echo "<a href='{$_SERVER['PHP_SELF']}?currentPage=1' aria-label='Previous'>";
												echo "<span aria-hidden='true'>&laquo;</span>";
												echo "</a></li>";
											}
											// range of num links to show
											$range = 3;
											// loop to show links to range of pages around current page
											for($x = ($currentPage - $range); $x < (($currentPage + $range) + 1); $x++) {
												// if it's a valid page number...
												if(($x > 0) && ($x <= $totalPages)) {
													// if we're on current page
													if($x == $currentPage) {
														// 'highlight' it but don't make a link
														echo "<li><a href='#'>$x</a></li>";
													} else {
														// make it a link
														echo "<li><a href='{$_SERVER['PHP_SELF']}?currentPage=$x'>$x</a></li>";
													}
												}
											}
											echo "<li>";
											echo "<a href='{$_SERVER['PHP_SELF']}?currentPage=$totalPages'>&raquo;</a>";
											echo "</li>";
										?>
									</ul>
								</nav>
							</div>
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
</script>
<? include('pages/page_footer.php'); ?>
</body>
</html>