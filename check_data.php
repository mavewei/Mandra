<?php
require_once('db/db_config.php');

$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
if($dbSelected) {
	if(!empty($_POST['emailAdd'])) {
		$emailAdd = $_POST['emailAdd'];
		$query = "SELECT * FROM userAccounts WHERE emailAdd = '$emailAdd'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$row = mysql_num_rows($result);
		if($row > 0) {
			/**
			 Email address found.
			 **/
			echo '[Error] Login ID already exist!';
		} else {
			return true;
		}
	}
	if(!empty($_POST['comCode'])) {
		$comCode = $_POST['comCode'];
		$query = "SELECT * FROM company WHERE comCode = '$comCode'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$row = mysql_num_rows($result);
		if($row > 0) {
			/**
			 Company code found.
			 **/
			echo "[Error] Company code already exist!";
		} else {
			return true;
		}
	}
	if(!empty($_POST['deptCode'])) {
		$deptCode = $_POST['deptCode'];
		$query = "SELECT * FROM departments WHERE deptCode = '$deptCode'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$row = mysql_num_rows($result);
		if($row > 0) {
			/**
			 Company code found.
			 **/
			echo "[Error] Department code already exist!";
		} else {
			return true;
		}
	}

	if(!empty($_POST['empName'])) {
		$empName = $_POST['empName'];
		$query = "SELECT * FROM employees WHERE empName = '$empName'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$row = mysql_num_rows($result);
		if($row > 0) {
			/**
			 Company code found.
			 **/
			echo "[Error] Employee details already exist!";
		} else {
			return true;
		}
	}
	if(!empty($_POST['empNationality'])) {
		$empNationality = $_POST['empNationality'];
		$query = "SELECT countyId, countyCode FROM county WHERE countyId = '$empNationality'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$row = mysql_num_rows($result);
		if($row > 0) {
			/**
			 Company code found.
			 **/
			$rows = array();
			while($data = mysql_fetch_array($result)) {
			 	$rows[] = array("countyCode" => $data['countyCode']);
			};

			echo json_encode($rows);
		} else {
			return false;
		}
	}
}
?>