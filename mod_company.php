<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/setadmin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'mod_company.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'mod_company.php');
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
			$comId = $_GET['comId'];
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
			if($_GET['delComId']) {
				$delComId = $_GET['delComId'];
				deleteRecord($delComId);
			}
			/**
				Select company lists.
	   		**/
	   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	   		$query = "SELECT * FROM company WHERE comId = '$comId'";
	   		$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$data = mysql_fetch_array($result);
			$comCode = $data['comCode'];
			$comName = $data['comName'];
			$comLocation = $data['comLocation'];
			if(isset($_POST['comCode']) && isset($_POST['comName'])) {
				$comId = $_POST['comId'];
				$comCode = strtoupper(mysql_escape_string($_POST['comCode']));
				$comName = ucwords(mysql_escape_string($_POST['comName']));
				$comLocation = $_POST['comLocation'];
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				$query = "UPDATE company
							SET
								comCode = '$comCode', comName = '$comName', comLocation = '$comLocation'
							WHERE
								comId = '$comId'";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					/**
						Company information updated and redirected to previous page.
					**/
					$_SESSION['STATUS'] = 19;
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
function deleteRecord($delComId) {
	$query = "UPDATE company SET status = 'Cancel' WHERE comId = '$delComId'";
	print($query);
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
				<h1>Modify Company <small>modify company information</small></h1>
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
					Modify Company
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-usereg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Modify Company</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="" method="post" onsubmit="return validate()">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Company ID</label>
														<input type="text" class="form-control input-lg" style="text-align: center" name="comId" id="comId" value="<?php echo $comId; ?>" readonly>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Created By</label>
														<input type="text" class="form-control input-lg" name="uid" style="text-align: center" value="<?php echo $fname; ?>" readonly>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-3">
													<div class="form-group">
														<label>Code</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-code"></i>
															<input type="text" class="form-control input-lg" id="comCode" placeholder="Code" name="comCode" value="<?php echo $comCode; ?>" onkeyup="checkComCode();" autofocus="on" required>
														</div>
													</div>
												</div>
												<div class="col-md-5">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building-o"></i>
															<input type="text" class="form-control input-lg" placeholder="Company Name" name="comName" value="<?php echo $comName; ?>" required>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Location</label>
														<select name="comLocation" class="form-control input-lg" required>
															<option value="">Select Location</option>
															<?php
															$location = array("China", "Hong Kong", "Liberia", "Malaysia", "Singapore");
																$length = count($location);
																for($i = 0; $i < $length; ++$i) {
																	if($comLocation == $location[$i]) {
																		echo "<option value=$location[$i] selected='selected'>$location[$i]</option>";
																	} else {
																		echo "<option value=$location[$i]>$location[$i]</option>";
																	}
																}
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<span class="error-status" id="comCode_status"></span>
												</div>
												<div class="col-md-6"></div>
											</div>
										</div>
										<div class="form-actions">
											<input type="submit" value="Update" class="btn blue">
											<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Close</button></a>
											<label class="cancel-or-padding">or</label>
											<input type="button" id="comDel" value="DELETE" class="btn red">
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
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
	var comId = document.getElementById("comId").value;
	$('#comDel').click(function(){
		// bootbox.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
		alertify.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
			if(result) {
				window.location="mod_company.php?delComId=" + comId;
			}
		});
	})
})
function checkComCode() {
	var comCode = document.getElementById("comCode").value;
	if(comCode) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				comCode: comCode,
			},
			success: function (response) {
				if(response == "true") {
					$('#comCode_status').html("");
					return true;
				} else {
					$('#comCode_status').html(response);
					return false;
               }
            }
		});
	} else {
		$('#comCode_status').html("");
		return false;
	}
}
function validate() {
	var comCode_status=document.getElementById("comCode_status").innerHTML;
	if(comCode_status =="") {
		return true;
	} else {
          return false;
	}
}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>