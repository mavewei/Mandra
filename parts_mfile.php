<?php include('pages/page_header.php'); ?>
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/signin.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, 'parts_mfile.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'parts_mfile.php');
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
				<div class="col-md-1"></div>
				<div class="col-md-10">
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
							<div id="slimScrollUsers">
								<div class="table-scrollable table-scrollable-borderless">
									<table class="table table-hover table-light">
										<?php
										if($rows > 0) {
											echo "<thead><tr class='uppercase'><th colspan='2'>S/N</th>";
											echo "<th class='left'>Parts Number</th>";
											echo "<th class='left'>Description</th><th class='center'>Brand</th>";
											echo "<th class='center'>Model</th><th class='center'>Date Created</th>";
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
												echo "<tr><td class='fit'>";
												echo "<img class='parts-pic' src='images/parts2.png'></td>";
												echo "<td><a href='mod_parts.php?partsId=$partsId' class='primary-link'>$partsId</a></td>";
												echo "<td align='left'>$partsNumber</td>";
												echo "<td align='left'>$partsDescription</td>";
												echo "<td align='center'>$partsBrand</td><td align='center'>$partsModel</td>";
												echo "<td align='center'>$dateCreated</td></tr>";
											};
											echo "</tbody>";
										} else {
											/**
											 No users account.
											 **/
											echo "<div class='block' style='height:100%'><div class='centered-users'>";
											echo "<h3 class='no-users'>No parts master file found!</h3></tbody></div></div>";
										}
										?>
									</table>
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
<?php include('pages/page_jquery.php'); ?>
<script>
/**
   Bootbox alert customize.
**/
$(function() {
	$('.logoutAlert').click(function(){
		bootbox.confirm("Are you sure you want to LOGOUT?", function(result) {
			if(result) {
				window.location = "logout.php";
			}
		});
	})
})
$(function(){
    var row = <?php echo $rows; ?>;
	if(row < 7) {	} else {
		$('#slimScrollUsers').slimScroll({
	    		height: '335px'
    		});
	}
});
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>