<?php include('pages/page_header.php'); ?>
<link href="css/parts-mfile.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
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
				$fname = $_SESSION['FNAME'];
				$uid = $_SESSION['UID'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
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
				/**
					Get parts details from master file.
				**/
				mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * from partsMasterFile WHERE status = 'Active' ORDER BY id DESC";
				$result = mysql_query($query);
				$rows = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				/**
					Pagination
				**/
				/**
					number of rows to show per page.
				**/
				$rowsPerPage = 8;
				/**
					find out total pages
				**/
				$totalPages = ceil($rows / $rowsPerPage);
				/**
					get the current page or set a default
				**/
				if(isset($_GET['currentPage']) && is_numeric($_GET['currentPage'])) {
					/**
						cast var as int
					**/
					$currentPage = (int) $_GET['currentPage'];
				} else {
					/**
						default page num
					**/
					$currentPage = 1;
				}
				/**
					if current page is greater than total pages
				**/
				if($currentPage > $totalPages) {
					/**
						set current page to last page
					**/
					$currentPage = $totalPages;
				}
				/**
					if current page is less than first page
				**/
				if($currentPage < 1) {
					/**
						set current page to first page
					**/
					$currentPage = 1;
				}
				/**
					The offset of the list, based on current page
				**/
				$offset = ($currentPage - 1) * $rowsPerPage;
				/**
					get the info from the db
				**/
				$query = "SELECT * FROM partsMasterFile WHERE status = 'Active' ORDER BY id DESC LIMIT $offset, $rowsPerPage";

				$result = mysql_query($query);
				$rows = mysql_num_rows($result);
				// $result = mysql_query($sql, $conn) or trigger_error("SQL", E_USER_ERROR);
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
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Parts Master File <small>manage parts master file</small></h1>
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
					Parts Master File
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Search</span>
							</div>
							<div class="actions btn-set">
								<a class="btn" href=""><i class="glyphicon glyphicon-refresh"></i></a>
							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-md-4">
									<label class="control-label label-space">By Parts Number</label>
									<div class="input-group">
										<input type="text" class="form-control input-sm" id="searchPartsNumber" name="searchPartsNumber" placeholder="By Parts Number">
										<span class="input-group-btn">
											<button class="btn blue btn-sm" onclick="searchPartsNumber();">Go!</button>
										</span>
									</div>
								</div>
								<div class="col-md-4">
									<label class="control-label label-space">By Description</label>
									<div class="input-group">
										<input type="text" class="form-control input-sm" id="searchDescription" name="searchDescription" placeholder="By Description">
										<span class="input-group-btn">
											<button class="btn blue btn-sm" onclick="searchDescription();">Go!</button>
										</span>
									</div>
								</div>
								<div class="col-md-4">
									<label class="control-label label-space">By Brand</label>
									<div class="input-group">
										<input type="text" class="form-control input-sm" id="searchBrand" name="searchBrand" placeholder="By Brand">
										<span class="input-group-btn">
											<button class="btn blue btn-sm" onclick="searchBrand();">Go!</button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<span class="caption-subjet font-green-sharp bold uppercase">Parts Master File</span>
							</div>
							<div class="actions btn-set">
								<a class="btn green-haze btn-circle" href="add_parts.php"><i class="fa fa-plus"></i> Add </a>
							</div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body">
							<!-- <div id="slimScrollParts"> -->
								<div id="searchDiv" class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
										if($rows > 0) {
											echo "<thead><tr class='uppercase'><th style='width: 20%'>S/N</th>";
											//echo "<thead><tr class='uppercase'><th colspan='2'>S/N</th>";
											echo "<th class='left' style='width: 30%'>Parts Number</th>";
											echo "<th class='left' style='width: 30%'>Description</th>";
											echo "<th class='center' style='width: 20%'>Date Created</th>";
											echo "</tr></thead><tbody>";
											for($j = 0; $j < $rows; ++$j) {
												$partsId = ucfirst(mysql_result($result, $j, 'partsId'));
												$partsNumber = ucfirst(mysql_result($result, $j, 'partsNumber'));
												$partsDescription = mysql_result($result, $j, 'partsDescription');
												// $partsUom = mysql_result($result, $j, 'partsUom');
												$partsBrand = mysql_result($result, $j, 'partsBrand');
												$partsModel = mysql_result($result, $j, 'partsModel');
												// $createdBy = mysql_result($result, $j, 'createdBy');
												$string = mysql_result($result, $j, 'dateTime');
												if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
													$dateCreated = $match[1];
												};
												echo "<tr><td class='fit'><img class='parts-pic' src='images/parts.png'>";
												echo "<a href='mod_parts.php?partsId=$partsId' class='name-padding primary-link'>$partsId</a></td>";
												echo "<td align='left'>$partsNumber</td>";
												echo "<td align='left'>$partsDescription</td>";
												echo "<td align='center'>$dateCreated</td></tr>";
											};
											echo "</tbody>";
										} else {
											/**
											 No master file.
											 **/
											echo "<h3 class='no-parts'>No parts master file found!</h3>";
										}
										?>
									</table>
									<nav style="text-align: center; <?php if($rows < 1) echo 'display: none' ?>">
										<ul class="pagination">
											<?php
											/**
												Pagination: http:\/\/www\.phpfreaks.com\/tutorial/basic-pagination
											**/
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
											/**
												range of num links to show
											**/
											$range = 3;
											/**
												loop to show links to range of pages around current page
											**/
											for($x = ($currentPage - $range); $x < (($currentPage + $range) + 1); $x++) {
												/**
													if it's a valid page number...
												**/
												if(($x > 0) && ($x <= $totalPages)) {
													/**
														if we're on current page
													**/
													if($x == $currentPage) {
														/**
															'highlight' it but don't make a link
														**/
														echo "<li><a href='#'>$x</a></li>";
													} else {
														/**
															make it a link
														**/
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

							<!-- </div> -->
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
function searchPartsNumber() {
	var searchPartsNumber = document.getElementById("searchPartsNumber").value;
	if(searchPartsNumber) {
		$.ajax({
			type: 'post',
			url: 'search_data.php',
			data: {
				searchPartsNumber: searchPartsNumber,
			},
			success: function (response) {
				$('#searchDiv').html(response);
            }
		});
	} else {}
}
function searchDescription() {
	var searchDescription = document.getElementById("searchDescription").value;
	if(searchDescription) {
		$.ajax({
			type: 'post',
			url: 'search_data.php',
			data: {
				searchDescription: searchDescription,
			},
			success: function (response) {
				$('#searchDiv').html(response);
            }
		});
	} else {}
}
function searchBrand() {
	var searchBrand = document.getElementById("searchBrand").value;
	if(searchBrand) {
		$.ajax({
			type: 'post',
			url: 'search_data.php',
			data: {
				searchBrand: searchBrand,
			},
			success: function (response) {
				$('#searchDiv').html(response);
            }
		});
	} else {}
}
$(function(){
    var row = <?php echo $rows; ?>;
	if(row < 7) {	} else {
		$('#slimScrollParts').slimScroll({
	    		height: '325px'
    		});
	}
});
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>