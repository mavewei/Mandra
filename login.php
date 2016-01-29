<?php include('pages/page_header.php'); ?>
<link href="css/center.css" rel="stylesheet" type="text/css">
<link href="css/signin.css" rel="stylesheet" type="text/css">
<script type = "text/javascript">
	history.pushState(null, null, 'login.php');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, 'login.php');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	/**
	 User was loggedin
	 **/
	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	if($dbSelected) {
		$login = $_SESSION['LOGIN_ID'];
		$ipaddr = @$_SERVER['REMOTE_ADDR'];
		$sid = $_SESSION['SID'];
		$query = "SELECT sid FROM loginDetails WHERE sid = '$sid'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$status = mysql_result($result, 0);
		if($status) {
			/**
			 SID exists!
			 **/
			$_SESSION['STATUS'] = 2;
			header('Location: status.php');
		} else {
			/**
			 SID not found. Add it!
			 **/
			$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$timeLogin = $row['dateTime'];
			$query = "INSERT INTO loginDetails (dateTimeLogin, emailAdd, ipAdd, sid, loginStatus) VALUES('$timeLogin', '$login', '$ipaddr', '$sid', 'Success')";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$_SESSION['STATUS'] = 2;
			header('Location: status.php');
		}
	};
} elseif(isset($_POST['login']) && isset($_POST['passwd'])) {
	/**
	 Received user login information
	 **/
	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	if($dbSelected) {
		$login = strtolower(mysql_escape_string($_POST['login']));
		$passwd = md5(mysql_escape_string($_POST['passwd']));
		$query = "SELECT * FROM userAccounts WHERE emailAdd = '$login' AND passwd = '$passwd'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		if(mysql_num_rows($result) == 1) {
			$row = mysql_fetch_array($result);
			$_SESSION['UID'] = $row['id'];
			$_SESSION['GID'] = $row['gid'];
			$_SESSION['FNAME'] = $row['firstName'];
			$_SESSION['LNAME'] = $row['lastName'];
			//$_SESSION['MOBILE'] = $row['mobileNum'];
			$_SESSION['LOGIN_ID'] = $row['emailAdd'];
			$_SESSION['DEPARTMENT'] = $row['departments'];
			$_SESSION['ROLE'] = $row['roles'];
			//$_SESSION['JOBTITLE'] = $row['jobTitle'];
			// $_SESSION['REMARK'] = $row['remark'];
			$_SESSION['LOGGEDIN'] = 1;
			$_SESSION['SID'] = mt_rand(1000, 99999999);
			/**
			 Lifetime restricted to 5mins. Just remark if don't want to restrict.
			 **/
			$_SESSION['EXPIRETIME'] = time() + 300;
			header("Location: login.php");
		} else {
			/**
			 User information not found!
			 **/
			header('Location: status.php');
		};
	};
	// $query = "SELECT * FROM userDETAILS WHERE emailAdd = '" . $email . "' AND passwd = '" . $passwd . "'";
};
?>
<!-- SIGN-IN BOX BEGIN -->
<div class="block" style="height:100%">
	<div class="centered">
		<form class="form-signin" action="login.php" method="post">
			<h2 class="form-signin-head">Mandra Forestry Liberia Limited</h2>
			<h4 class="form-signin-bottom">Management Information System</h4>
			<!-- <h4 class="form-signin-head">Please sign-in</h4> -->
			<label for="input_email" class="sr-only">Email address</label>
			<input type="email" id="input_email" class="form-control" placeholder="User ID (email address)" name="login" required autofocus autocomplete="off">
			<label for="input_password" class="sr-only">Password</label>
			<input type="password" id="input_password" class="form-control" placeholder="Password" name="passwd" required>
			<!-- <div class="checkbox">
				<label><input type="checkbox" value="remember"> Remember me</label>
			</div> -->
			<button class="btn btn-lg btn-primary btn-block" type="submit" id="submit-btn">Sign in</button>
		</form>
	</div>
</div>
<!-- SIGN-IN BOX END -->
<?php include('pages/page_jquery.php'); ?>
</body>
</html>