<?php
require_once('db_config.php');

/**
 Remove all tables
 **/
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 1000) {
		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		$query = "DROP TABLE company";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE county";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE departments";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE employees";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE initFlag";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE loginDetails";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE nationality";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE partsMasterFile";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE position";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE status";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE taxCode";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE tempSession";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE unit";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$query = "DROP TABLE userAccounts";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
	} else {
		/**
		 Redirect to dashboard if not Superuser
		 **/
		$_SESSION['STATUS'] = 10;
		header('Location: status.php');
	}
} else {
	header('Location: status.php');
}
?>
<meta http-equiv="refresh" content="5;../index.php">