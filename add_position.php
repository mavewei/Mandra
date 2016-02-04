<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, 'add_position.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'add_position.php');
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
			//$_SESSION['LAST_PAGE'] = 'add_department.php';
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
				Select taxCode lists.
	   		**/
	   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
			$query = "SELECT * from position ORDER BY id DESC";
			$result = mysql_query($query);
			$row = mysql_num_rows($result);
			if(!$result) die ("Table access failed: " . mysql_error());
			if($row == 0) {
				$taxCodeId = 'P01';
			} else {
				if($row < 9) {
					$row++;
					$taxCodeId = 'P0' . $row;
				} else {
					$row++;
					$taxCodeId = 'P' . $row;
				}
			}

			if(isset($_POST['positionId']) && isset($_POST['positionName'])) {
				$positionId = $_POST['positionId'];
				$positionName = ucwords(mysql_escape_string($_POST['positionName']));
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				$query = "INSERT INTO position
								(dateTime, positionId, positionName, status)
							VALUES
								('$time', '$positionId', '$positionName', 'Active')";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($result) {
					/**
						taxCode created and redirected to previous page.
					**/
					$_SESSION['STATUS'] = 18;
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
				<h1>Add Position <small>add new position information</small></h1>
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
					<a href="javascript:;">Extras</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">General</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Settings</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Add Position
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-usereg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Position</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="" method="post" onsubmit="return validate()">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label>Position ID</label>
														<input type="text" class="form-control input-lg" style="text-align: center" name="positionId" value="<?php echo $taxCodeId; ?>" readonly>
													</div>
												</div>
												<div class="col-md-8">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-code"></i>
															<input type="text" class="form-control input-lg" placeholder="Position Name" id="positionName" name="positionName" onkeyup="checkPositionName();" autofocus="on" required>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<span class="error-status" id="positionName_status"></span>
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
function checkPositionName() {
	var positionName = document.getElementById("positionName").value;
	if(positionName) {
		$.ajax({
			type: 'post',
			url: 'check_data.php',
			data: {
				positionName: positionName,
			},
			success: function (response) {
				if(response == "true") {
					$('#positionName_status').html("");
					return true;
				} else {
					$('#positionName_status').html(response);
					return false;
               }
            }
		});
	} else {
		$('#positionName_status').html("");
		return false;
	}
}
function validate() {
	var positionName_status = document.getElementById("positionName_status").innerHTML;
	if(positionName_status == "") {
		return true;
	} else {
        return false;
	}
}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>