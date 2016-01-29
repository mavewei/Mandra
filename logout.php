<?php
require_once('db/db_config.php');
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	$email = $_SESSION['LOGIN_ID'];
	$sid = $_SESSION['SID'];
	/**
	 Logout information recorded!
	 **/
	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	if($dbSelected) {
		$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$time = $row['dateTime'];
		$query = "UPDATE loginDetails SET dateTimeLast = '$time' WHERE emailAdd = '$email' AND sid = '$sid'";
		//$query = "UPDATE loginDETAILS SET date_t_Logout = CURRENT_TIMESTAMP WHERE emailAdd = '$email' AND sid = '$sid'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
	};
};
$_SESSION = array();
session_destroy();
?>
<meta http-equiv="refresh" content="0;index.php">