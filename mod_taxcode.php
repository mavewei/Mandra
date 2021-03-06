<?php include('pages/page_header.php'); ?>
<link href="css/employee-settings.css" rel="stylesheet" type="text/css" />
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
				$taxCodeId = $_GET['taxCodeId'];
				$lastPage = $_SESSION['LAST_PAGE'];
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
							Session time out time 5min.
						**/
						//$_SESSION['EXPIRETIME'] = time() + 300;
						$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
					};
				};
				/**
					Remove record.
				**/
				if($_GET['delTaxCodeId']) {
					$delTaxCodeId = $_GET['delTaxCodeId'];
					deleteRecord($delTaxCodeId);
				}
				/**
					Select tax code lists.
		   		**/
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		   		$query = "SELECT * FROM taxCode WHERE taxCodeId = '$taxCodeId'";
		   		$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				$data = mysql_fetch_array($result);
				$taxCodeName = $data['taxCodeName'];
				if(isset($_POST['taxCodeId']) && isset($_POST['taxCodeName'])) {
					$taxCodeId = $_POST['taxCodeId'];
					$taxCodeName = mysql_escape_string($_POST['taxCodeName']);
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "UPDATE taxCode SET taxCodeName = '$taxCodeName' WHERE taxCodeId = '$taxCodeId'";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						/**
							Tax code information updated and redirected to previous page.
						**/
						$_SESSION['STATUS'] = 21;
						header("Location: status.php");
					};
				};
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
	function deleteRecord($delTaxCodeId) {
		$query = "UPDATE taxCode SET status = 'Cancel' WHERE taxCodeId = '$delTaxCodeId'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($result) {
			header('Location: general_settings.php');
		}
	}
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Modify Tax Code <small>modify tax code information</small></h1>
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
				<li class="active">
					Modify Tax Code
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Modify Tax Code</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post" onsubmit="return validate()">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Tax Code ID</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="taxCodeId" id="taxCodeId" value="<?php echo $taxCodeId; ?>" readonly>
											</div>
										</div>
										<div class="col-md-8">
											<div class="form-group">
												<label>Name</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-code"></i>
													<input type="text" class="form-control input-lg" id="taxCodeName" placeholder="Tax Code Name" name="taxCodeName" value="<?php echo $taxCodeName; ?>" onkeyup="checkTaxCode();" autofocus="on" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="taxCode_status"></span>
										</div>
										<div class="col-md-6"></div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Update" class="btn blue">
									<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Close</button></a>
									<label class="cancel-or-padding">or</label>
									<!-- <input type="button" value="DELETE" class="btn red" onclick="return deleteData();"> -->
									<input type="button" id="taxCodeDel" value="DELETE" class="btn red">
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-3"></div>
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
/**
   Bootbox alert customize.
**/
/*
$(function() {
	$('.logoutAlert').click(function(){
		bootbox.confirm("Are you sure you want to LOGOUT?", function(result) {
			if(result) {
				window.location = "logout.php";
			}
		});
	})
})
*/
$(function() {
	var taxCodeId = document.getElementById("taxCodeId").value;
	$('#taxCodeDel').click(function(){
		// bootbox.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
		alertify.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
			if(result) {
				window.location="mod_taxcode.php?delTaxCodeId=" + taxCodeId;
			}
		});
	})
})
function checkTaxCode() {
	var taxCodeName = document.getElementById("taxCodeName").value;
	if(taxCodeName) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				taxCodeName: taxCodeName,
			},
			success: function (response) {
				if(response == "true") {
					$('#taxCode_status').html("");
					return true;
				} else {
					$('#taxCode_status').html(response);
					return false;
               }
            }
		});
	} else {
		$('#taxCode_status').html("");
		return false;
	}
}
function validate() {
	var taxCode_status=document.getElementById("taxCode_status").innerHTML;
	if(taxCode_status =="") {
		return true;
	} else {
          return false;
	}
}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>