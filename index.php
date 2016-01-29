<?php include("pages/page_head.php"); ?>
<?php include("pages/page_meta.php"); ?>
<?php
require_once('db/db_config.php');
/**
 Check initial setting, if not set, redirect to admin_registration page; else redirected to login page.
 **/
mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
$query = "SHOW TABLES LIKE 'initFlag'";
$result = mysql_query($query);
$status = mysql_result($result, 0);
if($status) {
	/**
	 Tables exists!. Check initial setting.
	 **/
	$query = 'SELECT status FROM initFlag';
	$result = mysql_query($query);
	if(!$result) die ("Table access failed: " . mysql_error());
	$status = mysql_result($result, 0);
	if($status != 1) {
		/**
		 System was initialized. Redirect to login.php
		 **/
		header('Location: login.php');
	} else {
		/**
		 System not yet initialized. Redirect to admin_registration.php
		 **/
		$_SESSION['INIT'] = 1;
		header('Location: admin_registration.php');
	};
} else {
	/**
	 Tables not found, create tables.
	 **/
	/**
	 Create require tables
	 **/
	/**
	 initFlag
	 **/
	$query = "CREATE TABLE initFlag (
					id INT NOT NULL AUTO_INCREMENT,
					dateTime TIMESTAMP NULL,
					status VARCHAR(10) NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 userAccounts
	 IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
	 **/
	$query = "CREATE TABLE userAccounts (
					id INT NOT NULL AUTO_INCREMENT,
					dateTime TIMESTAMP NULL,
					gid INT NOT NULL,
					firstName VARCHAR(50) NOT NULL,
					lastName VARCHAR(50) NOT NULL,
					emailAdd VARCHAR(50) NOT NULL,
					departments VARCHAR(50) NULL,
					roles VARCHAR(25) NOT NULL,
					passwd VARCHAR(100) NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 loginDetails
	 IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
	 **/
	$query = "CREATE TABLE loginDetails (
					id INT NOT NULL AUTO_INCREMENT,
					dateTimeLogin TIMESTAMP NULL,
					dateTimeLast TIMESTAMP NULL,
					emailAdd VARCHAR(50) NOT NULL,
					ipAdd VARCHAR(50) NOT NULL,
					sid VARCHAR(20) NOT NULL,
					loginStatus VARCHAR(20) NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 departments
	 IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
	 **/
	$query = "CREATE TABLE departments (
					id INT NOT NULL AUTO_INCREMENT,
					dateTime TIMESTAMP NULL,
					deptId VARCHAR(10) NOT NULL,
					deptCode VARCHAR(15) NOT NULL,
					deptName VARCHAR(50) NOT NULL,
					createdBy INT NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 company
	 IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
	 **/
	$query = "CREATE TABLE company (
					id INT NOT NULL AUTO_INCREMENT,
					dateTime TIMESTAMP NULL,
					comId VARCHAR(10) NOT NULL,
					comCode VARCHAR(15) NOT NULL,
					comName VARCHAR(50) NOT NULL,
					comLocation VARCHAR(50) NOT NULL,
					createdBy INT NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 county
	 IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
	 **/
	$query = "CREATE TABLE county (
					id INT NOT NULL AUTO_INCREMENT,
					dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					countyId VARCHAR(15) NOT NULL,
					countyCode VARCHAR(15) NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 Unit
	 IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
	 **/
	$query = "CREATE TABLE unit (
					id INT NOT NULL AUTO_INCREMENT,
					dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					unitId VARCHAR(15) NOT NULL,
					unitName VARCHAR(15) NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 employees
	 IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
	 **/
	$query = "CREATE TABLE employees (
					id INT NOT NULL AUTO_INCREMENT,
					dateTime TIMESTAMP NULL,
					empId VARCHAR(10) NOT NULL,
					empName VARCHAR(50) NOT NULL,
					empSex VARCHAR(10) NOT NULL,
					empBirth DATE,
					empNationality VARCHAR(50) NOT NULL,
					empCounty VARCHAR(50) NOT NULL,
					empDateJoin DATE,
					empSource VARCHAR(50) NOT NULL,
					empCategory VARCHAR(20) NOT NULL,
					empCompanyCode VARCHAR(10) NOT NULL,
					empDepartment VARCHAR(50) NOT NULL,
					empUnit VARCHAR(50) NOT NULL,
					empPosition VARCHAR(50) NOT NULL,
					empBasicSalary DECIMAL(9,2) NOT NULL,
					empTaxCode VARCHAR(50) NOT NULL,
					createdBy INT NOT NULL,
					PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
	/**
	 supplierLISTS
	 **/
	/*
	$query = "CREATE TABLE supplierLISTS (
						date_t TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						id INT NOT NULL AUTO_INCREMENT,
						code VARCHAR(20) NOT NULL,
						name VARCHAR(100) NOT NULL,
						scope VARCHAR(50) NULL,
						area VARCHAR(5) NOT NULL,
						country VARCHAR(15) NOT NULL,
						address VARCHAR(100) NULL,
						phone VARCHAR(15) NULL,
						fax VARCHAR(15) NULL,
						email VARCHAR(20) NULL,
						website VARCHAR(50) NULL,
						status VARCHAR(5) NOT NULL,
						add_P VARCHAR(20) NOT NULL,
						PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
*/
	/**
	 itemsLISTS
	 **/
	/*
	$query = "CREATE TABLE itemsLISTS (
						date_t TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						id INT NOT NULL AUTO_INCREMENT,
						codeNum VARCHAR(20) NOT NULL,
						serialNum VARCHAR(20) NOT NULL,
						partNum VARCHAR(30) NOT NULL,
						fullName VARCHAR(50) NULL,
						description VARCHAR(100) NULL,
						equipSection VARCHAR(50) NULL,
						PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
*/
	/**
	 purchaseREQUEST
	 **/
	/*
	$query = "CREATE TABLE purchaseREQUEST (
						date_t TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						id INT NOT NULL AUTO_INCREMENT,
						prNum VARCHAR(15) NOT NULL,
						department VARCHAR(25) NOT NULL,
						purchaseLocation VARCHAR(15) NOT NULL,
						modeShipment VARCHAR(50) NOT NULL,
						dateDelivery DATE,
						requestPerson VARCHAR(35) NOT NULL,
						reasonPurchase VARCHAR(100) NULL,
						totalOrder VARCHAR(10) NOT NULL,
						aprStatus VARCHAR(20) DEFAULT 'false',
						aprDate DATETIME DEFAULT NULL,
						prcStatus VARCHAR(20) DEFAULT 'false',
						prcDate DATETIME DEFAULT NULL,
						status VARCHAR(10) NOT NULL,
						lastUpdated DATETIME DEFAULT NULL,
						shipTo VARCHAR(20) NOT NULL,
						PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
*/
	/**
	 purchaseRequestDETAILS
	 **/
	/*
	$query = "CREATE TABLE purchaseRequestDETAILS (
						date_t TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						id INT NOT NULL AUTO_INCREMENT,
						prID VARCHAR(15) NOT NULL,
						partNum VARCHAR(30) NOT NULL,
						uom VARCHAR(10) NOT NULL,
						qty VARCHAR(10) NOT NULL,
						itemName VARCHAR(50) NULL,
						description VARCHAR(100) NULL,
						sections VARCHAR(100) NULL,
						requestQuotationID VARCHAR(10) NULL,
						requestQuotationDate DATETIME NULL,
						PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
*/
	/**
	 requestQUOTATION
	 **/
	/*
	$query = "CREATE TABLE requestQUOTATION (
						date_t TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						id INT NOT NULL AUTO_INCREMENT,
						rfqFlag VARCHAR(10) NOT NULL,
						rfqNum VARCHAR(20) NOT NULL,
						poID VARCHAR(10) DEFAULT NULL,
						prNum VARCHAR(15) NOT NULL,
						supplierID VARCHAR(10) DEFAULT NULL,
						supplierExpDateDelivery DATE DEFAULT NULL,
						currency VARCHAR(10) NOT NULL,
						PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
*/
	/**
	 requestQuotationDETAILS
	 **/
	/*
	$query = "CREATE TABLE requestQuotationDETAILS (
						date_t TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						id INT NOT NULL AUTO_INCREMENT,
						rfqID VARCHAR(15) NOT NULL,
						prdID VARCHAR(10) NOT NULL,
						partNum VARCHAR(30) NOT NULL,
						unitPrice VARCHAR(10) NOT NULL,
						totalPrice VARCHAR(10) DEFAULT NULL,
						remark VARCHAR(50) DEFAULT NULL,
						PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
*/
	/**
	 purchaseORDER
	 **/
	/*
	$query = "CREATE TABLE purchaseORDER (
						date_t TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						id INT NOT NULL AUTO_INCREMENT,
						poNum VARCHAR(30) NOT NULL,
						poFlag VARCHAR(10) NOT NULL,
						rfqID  VARCHAR(10) NOT NULL,
						PRIMARY KEY (id))";
	$result = mysql_query($query);
	if(!$result) die ("Tables create failed: " . mysql_error());
*/
	/**
	 Set initFLAG to false; 1 = uninit, 2 = set
	 **/
	$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$time = $row['dateTime'];
	$query = "INSERT INTO initFlag (dateTime, status) VALUES ('$time', 1)";
	$result = mysql_query($query);
	if(!$result) die ("Tables access failed: " . mysql_error());
	/**
	 Insert root account by default for maintenance
	 **/
	$password = md5('toor');
	$query = "INSERT INTO userAccounts (dateTime, gid, firstName, lastName, emailAdd, departments, roles, passwd) VALUES('$time', 0, 'Root', 'Toor', 'root@mandra', 'Full', 'root', '$password')";
	$result = mysql_query($query);
	if(!$result) die ("Tables access failed: " . mysql_error());
	/**
	 Insert county information
	 **/
	$query = "INSERT INTO county
					(countyId, countyCode)
				VALUES
					('Liberia', 'Bomi'), ('Liberia', 'Bong'), ('Liberia', 'Gbarpolu'), ('Liberia', 'Grand Bassa'),
					('Liberia', 'Grand Cape Mount'), ('Liberia', 'Grand Gedeh'), ('Liberia', 'Grand Kru'),
					('Liberia', 'Lofa'), ('Liberia', 'Margibi'), ('Liberia', 'Maryland'), ('Liberia', 'Grand Montserrado'),
					('Liberia', 'Nimba'), ('Liberia', 'Rivercess'), ('Liberia', 'River Gee'), ('Liberia', 'Sinoe'),
					('Malaysia', 'Johor'), ('Malaysia', 'Kedah'), ('Malaysia', 'Kelantan'), ('Malaysia', 'Kuala Lumpur'),
					('Malaysia', 'Labuan'), ('Malaysia', 'Melaka'), ('Malaysia', 'Negeri Sembilan'), ('Malaysia', 'Pahang'),
					('Malaysia', 'Perak'), ('Malaysia', 'Perlis'), ('Malaysia', 'Penang'), ('Malaysia', 'Sabah'),
					('Malaysia', 'Sarawak'), ('Malaysia', 'Selangor'), ('Malaysia', 'Terengganu')";
	$result = mysql_query($query);
	if(!$result) die ("Tables access failed: " . mysql_error());
	/**
	 Redirect to index.php
	 **/
	header('Location: index.php');
}
?>
<?php include("pages/page_jquery.php"); ?>
</body>
</html>