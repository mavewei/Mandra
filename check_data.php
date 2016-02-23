<?php
	require_once('db/db_config.php');

	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	if($dbSelected) {
		/**
			Validate data for email address.
		**/
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
		/**
			Validate data for company information.
		**/
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
		/**
			Validate data for department information.
		**/
		if(!empty($_POST['deptCode'])) {
			$deptCode = $_POST['deptCode'];
			$query = "SELECT * FROM departments WHERE deptCode = '$deptCode'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					Department information found.
				**/
				echo "[Error] Department code already exist!";
			} else {
				return true;
			}
		}
		/**
			Validate data for employee name.
		**/
		if(!empty($_POST['empName'])) {
			$empName = $_POST['empName'];
			$query = "SELECT * FROM employees WHERE empName = '$empName'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					Employee name found.
				**/
				echo "[Error] Employee details already exist!";
			} else {
				return true;
			}
		}
		/**
			Validate data for county.
		**/
		if(!empty($_POST['empNationality'])) {
			$empNationality = $_POST['empNationality'];
			$query = "SELECT countyId, countyCode FROM county WHERE countyId = '$empNationality'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					County found.
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
		/**
			Validate data for tax code.
		**/
		if(!empty($_POST['taxCodeName'])) {
			$taxCodeName = $_POST['taxCodeName'];
			$query = "SELECT * FROM taxCode WHERE taxCodeName = '$taxCodeName'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					Tax Code found.
				**/
				echo "[Error] Tax code already exist!";
			} else {
				return true;
			}
		}
		/**
			Validate data for unit information.
		**/
		if(!empty($_POST['unitName'])) {
			$unitName = $_POST['unitName'];
			$query = "SELECT * FROM unit WHERE unitName = '$unitName'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					Unit information found.
				**/
				echo "[Error] Unit infor already exist!";
			} else {
				return true;
			}
		}
		/**
			Validate data for position information.
		**/
		if(!empty($_POST['positionName'])) {
			$positionName = $_POST['positionName'];
			$query = "SELECT * FROM position WHERE positionName = '$positionName' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					Position information found.
				**/
				echo "[Error] Position infor already exist!";
			} else {
				return true;
			}
		}
		/**
			Validate data for parts details.
		**/
		if(!empty($_POST['partsNumber'])) {
			$partsNumber = $_POST['partsNumber'];
			$query = "SELECT * FROM partsMasterFile WHERE partsNumber = '$partsNumber' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					Parts information found.
				**/
				echo "[Error] Parts number already exist!";
			} else {
				return true;
			}
		}
		/**
			Validate data for employee status.
		**/
		if(!empty($_POST['statusName'])) {
			$statusName = $_POST['statusName'];
			$query = "SELECT * FROM status WHERE statusName = '$statusName' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				/**
					Status details found.
				**/
				echo "[Error] Status already exist!";
			} else {
				return true;
			}
		}
		// Validate data for parts uom.
		if(!empty($_POST['partsUomName'])) {
			$partsUomName = mysql_escape_string($_POST['partsUomName']);
			$query = "SELECT * FROM partsUom WHERE partsUomName = '$partsUomName' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				// Record found.
				echo "[Error] Parts UOM already exist!";
			} else {
				return true;
			}
		}
		// Validate data for parts category.
		if(!empty($_POST['partsCategoryName'])) {
			$partsCategoryName = mysql_escape_string($_POST['partsCategoryName']);
			$query = "SELECT * FROM partsCategory WHERE partsCategoryName = '$partsCategoryName' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				// Record found.
				echo "[Error] Parts category already exist!";
			} else {
				return true;
			}
		}
		// Validate data for parts brand.
		if(!empty($_POST['partsBrandName'])) {
			$partsBrandName = mysql_escape_string($_POST['partsBrandName']);
			$query = "SELECT * FROM partsBrand WHERE partsBrandName = '$partsBrandName' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				// Record found.
				echo "[Error] Parts brand already exist!";
			} else {
				return true;
			}
		}
		// Validate data for parts model.
		if(!empty($_POST['partsModelName'])) {
			$partsModelName = mysql_escape_string($_POST['partsModelName']);
			$query = "SELECT * FROM partsModel WHERE partsModelName = '$partsModelName' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				// Record found.
				echo "[Error] Parts model already exist!";
			} else {
				return true;
			}
		}
		// Validate data for parts equip type.
		if(!empty($_POST['partsEquipTypeName'])) {
			$partsEquipTypeName = mysql_escape_string($_POST['partsEquipTypeName']);
			$query = "SELECT * FROM partsEquipType WHERE partsEquipTypeName = '$partsEquipTypeName' AND status = 'Active'";
			$result = mysql_query($query);
			if(!$result) die ("Table access failed: " . mysql_error());
			$row = mysql_num_rows($result);
			if($row > 0) {
				// Record found.
				echo "[Error] Parts equipment type already exist!";
			} else {
				return true;
			}
		}
	}
?>