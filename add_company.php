<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/setadmin.css" rel="stylesheet" type="text/css" /> -->
<script type = "text/javascript">
	history.pushState(null, null, 'add_company.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'add_company.php');
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
				Select company lists.
	   		**/
	   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
			$query = "SELECT * from company ORDER BY id DESC";
			$result = mysql_query($query);
			$row = mysql_num_rows($result);
			if(!$result) die ("Table access failed: " . mysql_error());
			if($row == 0) {
				$comId = 'C01';
			} else {
				if($row < 9) {
					$row++;
					$comId = 'C0' . $row;
				} else {
					$row++;
					$comId = 'C' . $row;
				}
			}

			if(isset($_POST['comCode']) && isset($_POST['comName'])) {
				$comId = $_POST['comId'];
				$comCode = strtoupper(mysql_escape_string($_POST['comCode']));
				$comName = ucwords(mysql_escape_string($_POST['comName']));
				$comLocation = $_POST['comLocation'];
				/**
					Get the gid from tables
				**/
				// $dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				$query = "INSERT INTO company
								(dateTime, comId, comCode, comName, comLocation, status, createdBy)
							VALUES
								('$time', '$comId', '$comCode', '$comName', '$comLocation', 'Active', '$uid')";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					/**
						Company information created and redirected to previous page.
					**/
					$_SESSION['STATUS'] = 14;
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
?>
<?php include('pages/page_menu.php'); ?>

<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Add Company <small>add new company</small></h1>
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
					Add Company
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-usereg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Company</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="add_company.php" method="post" onsubmit="return validate()">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Company ID</label>
														<input type="text" class="form-control input-lg" style="text-align: center" name="comId" value="<?php echo $comId; ?>" readonly>
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
															<input type="text" class="form-control input-lg" id="comCode" placeholder="Code" name="comCode" onkeyup="checkComCode();" autofocus="on" required>
														</div>
													</div>
												</div>
												<div class="col-md-5">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-building-o"></i>
															<input type="text" class="form-control input-lg" placeholder="Company Name" name="comName" required>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Location</label>
														<select name="comLocation" class="form-control input-lg" required>
															<option value="">Select Location</option>
															<option value="China">China</option>
															<option value="Hong Kong">Hong Kong</option>
															<option value="Liberia">Liberia</option>
															<option value="Malaysia">Malaysia</option>
															<option value="Singapore">Singapore</option>
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
											<input type="submit" value="Submit" class="btn blue">
											<!-- <button type="submit" class="btn blue">Submit</button> -->
											<a href="<?php echo $lastPage; ?>"><button type="button" class="btn default">Cancel</button></a>
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
$(function(){
    $('#logout').click(function(){
        if(confirm('Are you sure you want to LOGOUT?')) {
            return true;
        }
        return false;
    });
});
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