<?php
require_once('db_config.php');

/**
 Remove all tables
 **/
if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 1000) {
		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		$query = "DROP TABLE initFlag";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());

		$query = "DROP TABLE loginDetails";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());

		$query = "DROP TABLE departments";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());

		$query = "DROP TABLE userAccounts";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
/*
		$query = "DROP TABLE itemsLISTS";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
*/

/*
		$query = "DROP TABLE purchaseORDER";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
		$query = "DROP TABLE purchaseREQUEST";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
		$query = "DROP TABLE purchaseRequestDETAILS";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
		$query = "DROP TABLE requestQUOTATION";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
		$query = "DROP TABLE requestQuotationDETAILS";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
		$query = "DROP TABLE supplierLISTS";
		$result = mysql_query($query);
		if(!$result) die ("Tables delete failed: " . mysql_error());
*/
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