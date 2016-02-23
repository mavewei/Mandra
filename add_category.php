<?php include('pages/page_header.php'); ?>
<link href="css/pmf-settings.css" rel="stylesheet" type="text/css" />
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
				// Select parts category lists.
		   		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * FROM partsCategory ORDER BY id DESC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($row == 0) {
					$partsCategoryId = 'PC001';
				} else {
					$row++;
					$partsCategoryId = 'PC' . sprintf('%03d', $row);
				}
				if(isset($_POST['partsCategoryId']) && isset($_POST['partsCategoryName'])) {
					$partsCategoryId = mysql_escape_string($_POST['partsCategoryId']);
					$partsCategoryName = ucwords(strtolower(mysql_escape_string($_POST['partsCategoryName'])));
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "INSERT INTO partsCategory(dateTime, partsCategoryId, partsCategoryName, status)
								VALUES('$time', '$partsCategoryId', '$partsCategoryName', 'Active')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// parts category created and redirected to previous page.
						$_SESSION['STATUS'] = 33;
						header("Location: status.php");
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
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Add Category <small>add new category details</small></h1>
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
					<a href="javascript:;">Setup</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Parts Master File</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Add Category
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Category</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post" onsubmit="return validate()">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Category ID</label>
												<input type="text" class="form-control input-lg" style="text-align: center" name="partsCategoryId" value="<?php echo $partsCategoryId; ?>" readonly>
											</div>
										</div>
										<div class="col-md-8">
											<div class="form-group">
												<label>Name</label>
												<div class="input-icon input-icon-lg"><i class="fa fa-code"></i>
													<input type="text" class="form-control input-lg" placeholder="Category Name" id="partsCategoryName" name="partsCategoryName" onkeyup="checkCategoryName();" autofocus="on" required>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="partsCategoryName_status"></span>
										</div>
										<div class="col-md-6"></div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Submit" class="btn blue">
									<a href="pmf_settings.php"><button type="button" class="btn default">Cancel</button></a>
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
	// Bootbox alert customize.
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
	function checkCategoryName() {
		var partsCategoryName = document.getElementById("partsCategoryName").value;
		if(partsCategoryName) {
			$.ajax({
				type: 'post',
				url: 'check_data.php',
				data: {
					partsCategoryName: partsCategoryName,
				},
				success: function (response) {
					if(response == "true") {
						$('#partsCategoryName_status').html("");
						return true;
					} else {
						$('#partsCategoryName_status').html(response);
						return false;
	               }
	            }
			});
		} else {
			$('#partsCategoryName_status').html("");
			return false;
		}
	}
	function validate() {
		var partsCategoryName_status = document.getElementById("partsCategoryName_status").innerHTML;
		if(partsCategoryName_status == "") {
			return true;
		} else {
	        return false;
		}
	}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>