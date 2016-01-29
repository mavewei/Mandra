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

		mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
		$query = "select table_name from information_schema.tables where table_schema='$dbName' ORDER BY table_name ASC;";
		$result = mysql_query($query);
		$row = mysql_num_rows($result);
		if(!$result) die ("Table access failed: " . mysql_error());
		if($row > 0) {
			for($i = 0; $i < $row; ++$i) {
				$table_name = mysql_result($result, $i, 'table_name');
				backup_tables($dbHost, $dbUsername, $dbPasswd, $dbName, $table_name);
			}
		} else {
			exit(0);
		}
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
	// $handle = fopen('db-backup-'.(md5(implode(',',$tables))).'.sql','w+');
	// $handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle, $return);
	fclose($handle);
};
?>
<meta http-equiv="refresh" content="5;../dashboard.php">