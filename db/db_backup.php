<?php
require_once('db_config.php');

if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 1000) {
		/**
		 Create backup directory if not exists!
		 **/
		if (!file_exists('db_backup')) {
			mkdir('db_backup', 0755, true);
		}
		/**
		 Change directory
		 **/
		chdir('db_backup');
		/**
		 Call function to backup all tables.
		 **/
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'initFlag');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'userAccounts');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'loginDetails');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'departments');
		/*
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'supplierLISTS');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'itemsLISTS');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'purchaseREQUEST');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'purchaseRequestDETAILS');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'requestQUOTATION');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'requestQuotationDETAILS');
		backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, 'purchaseORDER');
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
/**
 Backup the db OR just a table.
 **/
function backup_tables($host, $user, $pass, $name, $tables) {
	$link = mysql_connect($host, $user, $pass);
	mysql_select_db($name, $link);

	/**
	 Get all of the tables
	 **/
	if($tables == '*') {
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result)) {
			$tables[] = $row[0];
		}
	} else {
		$tables = is_array($tables) ? $tables : explode(',', $tables);
	};

	/**
	 Cycle through
	 **/
	foreach($tables as $table) {
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);

		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";

		for ($i = 0; $i < $num_fields; $i++) {
			while($row = mysql_fetch_row($result)) {
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n", "\\n", $row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	};

	/**
	 Save file
	 **/
	$handle = fopen('db-backup-'.(implode(',', $tables)).'.sql', 'w+');
	//$handle = fopen('db-backup-'.(md5(implode(',',$tables))).'.sql','w+');
	// $handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle, $return);
	fclose($handle);
};
?>
<meta http-equiv="refresh" content="5;../dashboard.php">