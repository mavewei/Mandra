<?php include('pages/page_header.php'); ?>
<link href="css/flexbox.css" rel="stylesheet" type="text/css" />
<link href="css/reset-passwd.css" rel="stylesheet" type="text/css" />
<?php include('pages/page_meta.php'); ?>
<?php
	require_once('db/db_config.php');

	// Logout if no id and hash.
	if(empty($_GET['id']) && empty($_GET['hash'])) {
		header('Location: logout.php');
	}
/*
	if(isset($_GET['id']) && isset($_GET['hash'])) {
		$emailAdd = base64_decode($_GET['id']);
		$hash = $_GET['hash'];
		echo $emailAdd . " / " . $hash;
	}
*/

	if(isset($_GET['id']) && isset($_GET['hash'])) {
		$emailAdd = base64_decode($_GET['id']);
		$hash = $_GET['hash'];
		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		$querrySelect = "SELECT * FROM userAccounts WHERE emailAdd = '$emailAdd' AND hash = '$hash' AND status = 'Active'";
		$resultSelect = mysql_query($querrySelect);
		$rowsSelect = mysql_num_rows($resultSelect);
		if($rowsSelect == 1) {
			// Account found!
			if(isset($_POST['confirmPass'])) {
				$passwd = md5($_POST['confirmPass']);
				$querryUpdate = "UPDATE userAccounts SET passwd = '$passwd', hash = NULL WHERE emailAdd = '$emailAdd' AND status = 'Active'";
				$resultUpdate = mysql_query($querryUpdate);
				if(!$resultUpdate) {
					die ("Table access failed: " . mysql_error());
				} else {
					$_SESSION['STATUS'] = 46;
					header('Location: status.php');
				}
			}
		}
	}
?>
<div class="container">
	<div class="row vcenter">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<form class="form-login" action="" method="post" onsubmit="return validate();">
				<h2 style="text-align: center">Mandra Forestry Liberia Limited</h2>
				<h4 class="form-login-bottom" style="text-align: center">Management Information System</h4>
				<h4 style="text-align: center; color: #337ab7">Reset Your Password</h4>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<input type="password" id="currentPass" class="form-control currentPass" placeholder="New Password" name="currentPass" required autofocus autocomplete="off">
						</div>
						<div class="col-md-6">
							<input type="password" id="confirmPass" class="form-control confirmPass" placeholder="Confirm Password" name="confirmPass" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-8">
							<span class="error-status" id="passwd_status"></span>
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<button class="btn btn-lg btn-primary btn-block" type="submit" id="submit-btn">SUBMIT</button>
			</form>
		</div>
	</div>
</div>
<?php include('pages/page_jquery.php'); ?>
<script>
	function checkPasswordMatch() {
		var currentPass = document.getElementById("currentPass").value;
		var confirmPass = document.getElementById("confirmPass").value;
	    if (currentPass != confirmPass)
	    		document.getElementById("passwd_status").innerHTML = "[Error] Password not match!";
	    else
	        document.getElementById("passwd_status").innerHTML = "";
	}
	$(document).ready(function() {
		$("#confirmPass").keyup(checkPasswordMatch);
	});
	function validate() {
		var passwd_status = document.getElementById("passwd_status").innerHTML;
		if(passwd_status == "") {
			return true;
		} else {
	          return false;
		}
	}
</script>
</body>
</html>