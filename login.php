<?php include('pages/page_header.php'); ?>
<link href="css/flexbox.css" rel="stylesheet" type="text/css" />
<link href="css/login.css" rel="stylesheet" type="text/css" />
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
			/**
			 Add login information to loginDetails.
			 //
			$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$timeLogin = $row['dateTime'];
			$query = "INSERT INTO loginDetails (dateTimeLogin, emailAdd, ipAdd, sid, loginStatus) VALUES('$timeLogin', '$login', '$ipaddr', '$sid', 'Success')";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$_SESSION['STATUS'] = 2;
			header('Location: status.php');
			**/
			$query = "SELECT sid FROM loginDetails WHERE sid = '$sid'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$status = mysql_result($result, 0);
			if($status) {
				// SID exists!
				$_SESSION['STATUS'] = 2;
				header('Location: status.php');
			} else {
				// SID not found. Add it!
				$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) AS 'dateTime'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$time = $row['dateTime'];
				// Insert login details.
				$query = "INSERT INTO loginDetails(dateTimeLogin, emailAdd, ipAdd, sid, loginStatus)
							VALUES('$time', '$login', '$ipaddr', '$sid', 'Success')";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				// Update other user logout time.
				$query = "UPDATE loginDetails SET dateTimeLast = '$time'
							WHERE dateTimeLast IS NULL AND emailAdd = '$login' AND sid <> '$sid'";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				// Insert session to tempSession.
				$query = "INSERT INTO tempSession(dateTime, emailAdd, sid)
							VALUES('$time', '$login', '$sid')";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				// Delete other user login session.
				$query = "DELETE FROM tempSession
							WHERE emailAdd = '$login' AND sid <> '$sid'";
				$result = mysql_query($query);
				if(!$result) die ("Table access failed: " . mysql_error());
				$_SESSION['STATUS'] = 2;
				header('Location: status.php');
			}
		};
	} elseif(isset($_POST['login']) && isset($_POST['passwd'])) {
		// Received user login information
		$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		if($dbSelected) {
			$login = strtolower(mysql_escape_string($_POST['login']));
			$passwd = md5(mysql_escape_string($_POST['passwd']));
			$query = "SELECT * FROM userAccounts WHERE emailAdd = '$login' AND passwd = '$passwd' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			if(mysql_num_rows($result) == 1) {
				$row = mysql_fetch_array($result);
				$_SESSION['UID'] = $row['id'];
				$_SESSION['GID'] = $row['gid'];
				$_SESSION['FNAME'] = $row['firstName'];
				$_SESSION['LNAME'] = $row['lastName'];
				$_SESSION['LOGIN_ID'] = $row['emailAdd'];
				$_SESSION['DEPARTMENT'] = $row['departments'];
				$_SESSION['POSITION'] = $row['position'];
				$_SESSION['SESSIONTIMEOUT'] = $row['sessionTimeout'];
				$_SESSION['ROLE'] = $row['roles'];
				$_SESSION['LOGGEDIN'] = 1;
				$randSeed = mt_rand(1000, 99999999);
				$sid = md5(time() . $_SERVER['HTTP_ACCEPT_CHARSET'] . $_SERVER['HTTP_ACCEPT_ENCODING'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $randSeed);
				$_SESSION['SID'] = $sid;
				//$_SESSION['SID'] = mt_rand(1000, 99999999);
				// Lifetime restricted to 5mins. Just remark if don't want to restrict.
				$_SESSION['EXPIRETIME'] = time() + 300;
				header("Location: login.php");
			} else {
				// User information not found!
				unset($_SESSION['STATUS']);
				header('Location: status.php');
			};
		};
	};
?>
<div class="container">
	<div class="row vcenter">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<form class="form-login" action="login.php" method="post">
				<h2 style="text-align: center">Mandra Forestry Liberia Limited</h2>
				<h4 class="form-login-bottom" style="text-align: center">Management Information System</h4>
				<input type="email" id="input_email" class="form-control" placeholder="User ID (email address)" name="login" required autofocus autocomplete="off">
				<input type="password" id="input_password" class="form-control" placeholder="Password" name="passwd" required>
				<div class="forgot">
					<label class="forgot-passwd"><a href="forgot_passwd.php">Forgot Password?</a></label>
				</div>
				<button class="btn btn-lg btn-primary btn-block" type="submit" id="submit-btn">Sign in</button>
			</form>
		</div>
	</div>
</div>
<?php include('pages/page_jquery.php'); ?>
</body>
</html>