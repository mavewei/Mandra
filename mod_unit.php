<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/setadmin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'mod_unit.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'mod_unit.php');
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
			$unitId = $_GET['unitId'];
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
			if($_GET['delUnitId']) {
				$delUnitId = $_GET['delUnitId'];
				deleteRecord($delUnitId);
			}
			/**
				Select unit lists.
	   		**/
	   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	   		$query = "SELECT * FROM unit WHERE unitId = '$unitId'";
	   		$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$data = mysql_fetch_array($result);
			$unitName = $data['unitName'];
			if(isset($_POST['unitId']) && isset($_POST['unitName'])) {
				$unitId = $_POST['unitId'];
				$unitName = ucwords(mysql_escape_string($_POST['unitName']));
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				$query = "UPDATE unit SET unitName = '$unitName' WHERE unitId = '$unitId'";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					/**
						Unit information updated and redirected to previous page.
					**/
					$_SESSION['STATUS'] = 22;
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
function deleteRecord($delUnitId) {
	$query = "UPDATE unit SET status = 'Cancel' WHERE unitId = '$delUnitId'";
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
				<h1>Modify Unit <small>modify unit information</small></h1>
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
					Modify Unit
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-usereg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Modify Unit</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="" method="post" onsubmit="return validate()">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Unit ID</label>
														<input type="text" class="form-control input-lg" style="text-align: center" name="unitId" id="unitId" value="<?php echo $unitId; ?>" readonly>
													</div>
												</div>
												<div class="col-md-8">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg">
															<i class="fa fa-code"></i>
															<input type="text" class="form-control input-lg" id="unitName" placeholder="Unit Name" name="unitName" value="<?php echo $unitName; ?>" onkeyup="checkUnitName();" autofocus="on" required>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<span class="error-status" id="unit_status"></span>
												</div>
												<div class="col-md-6"></div>
											</div>
										</div>
										<div class="form-actions">
											<input type="submit" value="Update" class="btn blue">
											<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Close</button></a>
											<label class="cancel-or-padding">or</label>
											<input type="button" id="unitDel" value="DELETE" class="btn red">
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
$(function() {
	var unitId = document.getElementById("unitId").value;
	$('#unitDel').click(function(){
		bootbox.confirm("[CAUTION]  Are you sure you want to DELETE this record?", function(result) {
			if(result) {
				window.location="mod_unit.php?delUnitId=" + unitId;
			}
		});
	})
})
$(function(){
    $('#logout').click(function(){
        if(confirm('Are you sure you want to LOGOUT?')) {
            return true;
        }
        return false;
    });
});
function deleteData() {
	var unitId = document.getElementById("unitId").value;
	if( confirm("Are you sure to DELETE this record?") == true)
		window.location="mod_unit.php?delUnitId=" + unitId;
	return false;
}
function checkUnitName() {
	var unitName = document.getElementById("unitName").value;
	if(unitName) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				unitName: unitName,
			},
			success: function (response) {
				if(response == "true") {
					$('#unit_status').html("");
					return true;
				} else {
					$('#unit_status').html(response);
					return false;
               }
            }
		});
	} else {
		$('#unit_status').html("");
		return false;
	}
}
function validate() {
	var unit_status = document.getElementById("unit_status").innerHTML;
	if(unit_status =="") {
		return true;
	} else {
          return false;
	}
}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>