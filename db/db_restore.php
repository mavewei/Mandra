<?php
require_once('db_config.php');

if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 1000) {
		/**
		 Change directory
		 **/
		chdir('db_backup');
		/**
		 Call function to restore all informations from tables.
		 **/
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-company.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-county.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-departments.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-employees.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-initFlag.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-loginDetails.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-position.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-taxCode.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-unit.sql');
		restoreTables($dbHost, $dbUsername, $dbPasswd, $dbName, 'db-backup-userAccounts.sql');
	} else {
		/**
		 Redirect to dashboard if not Superuser.
		 **/
		$_SESSION['STATUS'] = 10;
		header('Location: status.php');
	}
} else {
	header('Location: status.php');
}

function restoreTables($host, $user, $pass, $name, $filename) {
	$link = mysql_connect($host, $user, $pass);
	mysql_select_db($name, $link);
	/**
	 Temporary variable, used to store current query
	 **/
	$templine = '';
	/**
	 Read in entire file
	 **/
	$lines = file($filename);
	/**
	 Loop through each line
	 **/
	foreach ($lines as $line) {
		/**
		 Skip it if it's a comment
		 **/
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		/**
		 Add this line to the current segment
		 **/
		$templine .= $line;
		/**
		 If it has a semicolon at the end, it's the end of the query
		 **/
		if (substr(trim($line), -1, 1) == ';') {
			/**
			 Perform the query
			 **/
			mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
			/**
			 Reset temp variable to empty
			 **/
			$templine = '';
		}
	}
}
?>
<meta http-equiv="refresh" content="5;../logout.php">