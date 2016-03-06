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
						position VARCHAR(50) NULL,
						roles VARCHAR(25) NOT NULL,
						passwd VARCHAR(100) NOT NULL,
						status VARCHAR(15) NULL,
						sessionTimeout INT(10) NULL,
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
						sid VARCHAR(100) NOT NULL,
						loginStatus VARCHAR(20) NOT NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		/**
			tempSession
			IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		**/
		$query = "CREATE TABLE tempSession (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						emailAdd VARCHAR(50) NOT NULL,
						sid VARCHAR(100) NOT NULL,
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
						status VARCHAR(15) NULL,
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
						status VARCHAR(15) NULL,
						createdBy INT NOT NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		/**
			nationality
			IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		**/
		$query = "CREATE TABLE nationality (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						nationalityName VARCHAR(50) NOT NULL,
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
						countyCode VARCHAR(50) NOT NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		/**
			unit
			IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		**/
		$query = "CREATE TABLE unit (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						unitId VARCHAR(15) NOT NULL,
						unitName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		/**
			position
			IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		**/
		$query = "CREATE TABLE position (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						positionId VARCHAR(15) NOT NULL,
						positionName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		/**
			status for Employee
			IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		**/
		/*
		$query = "CREATE TABLE status (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						statusId VARCHAR(15) NOT NULL,
						statusName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		*/
		/**
			Tax Code
			IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		**/
		$query = "CREATE TABLE taxCode (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						taxCodeId VARCHAR(15) NOT NULL,
						taxCodeName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
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
						empBirth DATE NULL,
						empNationality VARCHAR(50) NOT NULL,
						empCounty VARCHAR(50) NULL,
						empDateJoin DATE NULL,
						empStatus VARCHAR(50) NULL,
						empCategory VARCHAR(20) NOT NULL,
						empCompanyCode VARCHAR(50) NOT NULL,
						empDepartment VARCHAR(50) NOT NULL,
						empUnit VARCHAR(50) NOT NULL,
						empPosition VARCHAR(50) NOT NULL,
						empBasicSalary DECIMAL(9,2) NOT NULL,
						empTaxCode VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						createdBy INT NOT NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		/**
			Parts Master File
		**/
		$query = "CREATE TABLE partsMasterFile (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						partsId VARCHAR(15) NOT NULL,
						partsNumber VARCHAR(100) NOT NULL,
						partsDescription VARCHAR(100) NULL,
						partsUom VARCHAR(10) NULL,
						partsCategory VARCHAR(50) NULL,
						partsBrand VARCHAR(50) NULL,
						partsModel VARCHAR(50) NULL,
						partsEquipType VARCHAR(50) NULL,
						partsWhereUsedI VARCHAR(100) NULL,
						partsWhereUsedII VARCHAR(100) NULL,
						status VARCHAR(15) NULL,
						createdBy INT NOT NULL,
						PRIMARY KEY (id))";
						// idZeroFill INT(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		// Parts Master File - Uom
		// IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		$query = "CREATE TABLE partsUom (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						partsUomId VARCHAR(15) NOT NULL,
						partsUomName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		// Parts Master File - Category
		// IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		$query = "CREATE TABLE partsCategory (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						partsCategoryId VARCHAR(15) NOT NULL,
						partsCategoryName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		// Parts Master File - Brand
		// IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		$query = "CREATE TABLE partsBrand (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						partsBrandId VARCHAR(15) NOT NULL,
						partsBrandName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		// Parts Master File - Model
		// IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		$query = "CREATE TABLE partsModel (
						id INT NOT NULL AUTO_INCREMENT,
						dateTime TIMESTAMP NULL,
						partsModelId VARCHAR(15) NOT NULL,
						partsModelName VARCHAR(50) NOT NULL,
						status VARCHAR(15) NULL,
						PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		// Parts Master File - Equipment Type
		// IMPORTANT: Please use "dateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP" if timezone setting are correct.
		$query = "CREATE TABLE partsEquipType (
					 id INT NOT NULL AUTO_INCREMENT,
					 dateTime TIMESTAMP NULL,
					 partsEquipTypeId VARCHAR(15) NOT NULL,
					 partsEquipTypeName VARCHAR(50) NOT NULL,
					 status VARCHAR(15) NULL,
					 PRIMARY KEY (id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		// Material Request Form
		$query = "CREATE TABLE prcMaterialRequestForm (
					 id INT NOT NULL AUTO_INCREMENT,
					 dateTime TIMESTAMP NULL,
					 mrSN VARCHAR(20) NOT NULL,
					 mrNumber VARCHAR(35) NULL,
					 mrDepartment VARCHAR(35) NULL,
					 mrPurpose VARCHAR(20) NULL,
					 mrDateReq VARCHAR(20) NULL,
					 mrTotal INT(10) NOT NULL,
					 mrRemark VARCHAR(100) NULL,
					 mrRequestBy VARCHAR(35) NOT NULL,
					 mrReviewStatus VARCHAR(20) NULL,
					 mrReviewedPerson VARCHAR(50) NULL,
					 mrReviewedDateTime TIMESTAMP NULL,
					 mrApproveStatus VARCHAR(20) NULL,
					 mrApprovedPerson VARCHAR(50) NULL,
					 mrApprovedDateTime TIMESTAMP NULL,
					 status VARCHAR(15) NULL,
					 PRIMARY KEY(id))";
		$result = mysql_query($query);
		if(!$result) die ("Tables create failed: " . mysql_error());
		// Material Request Form details
		$query = "CREATE TABLE prcMaterialRequestFormDetails (
					 id INT NOT NULL AUTO_INCREMENT,
					 dateTime TIMESTAMP NULL,
					 mrfDetailsSN VARCHAR(20) NOT NULL,
					 mrfDetailsNumber VARCHAR(10) NOT NULL,
					 mrfDetailsPartsNumber VARCHAR(100) NOT NULL,
					 mrfDetailsDescription VARCHAR(100) NULL,
					 mrfDetailsQty VARCHAR(15) NOT NULL,
					 mrfDetailsUom VARCHAR(10) NULL,
					 mrfDetailsStockQty VARCHAR(15) NULL,
					 mrfDetailsEquipType VARCHAR(50) NULL,
					 mrfDetailsModel VARCHAR(50) NULL,
					 mrfDetailsPlateNo VARCHAR(20) NULL,
					 PRIMARY KEY(id))";
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
		$query = "INSERT INTO userAccounts
						(dateTime, gid, firstName, lastName, emailAdd, departments, roles, passwd, status, sessionTimeout)
					VALUES
						('$time', 0, 'Root', 'Toor', 'root@mandra', 'Full', 'root', '$password', 'Active', 3600)";
		$result = mysql_query($query);
		if(!$result) die ("Tables access failed: " . mysql_error());
		/**
			Insert nationality information.
		**/
		$query = "INSERT INTO nationality
						(nationalityName)
					VALUES
						('Afghanistan'), ('Albania'), ('Algeria'), ('Andorra'), ('Angola'), ('Antarctica'),
						('Antigua and Barbuda'), ('Argentina'), ('Armenia'), ('Australia'), ('Austria'), ('Azerbaijan'),
						('Bahamas'), ('Bahrain'), ('Bangladesh'), ('Barbados'), ('Belarus'), ('Belgium'), ('Belize'),
						('Benin'), ('Bermuda'), ('Bhutan'), ('Bolivia'), ('Bosnia and Herzegovina'), ('Botswana'),
						('Brazil'), ('Brunei'), ('Bulgaria'), ('Burkina Faso'), ('Burma'), ('Burundi'), ('Cambodia'),
						('Cameroon'), ('Canada'), ('Cape Verde'), ('Central African Republic'), ('Chad'), ('Chile'), ('China'),
						('Colombia'), ('Comoros'), ('Congo, Democratic Republic'), ('Congo, Republic of the'), ('Costa Rica'),
						('Cote d\'Ivoire'), ('Croatia'), ('Cuba'), ('Cyprus'), ('Czech Republic'), ('Denmark'), ('Djibouti'),
						('Dominica'), ('Dominican Republic'), ('East Timor'), ('Ecuador'), ('Egypt'), ('El Salvador'),
						('Equatorial Guinea'), ('Eritrea'), ('Estonia'), ('Ethiopia'), ('Fiji'), ('Finland'), ('France'),
						('Gabon'), ('Gambia'), ('Georgia'), ('Germany'), ('Ghana'), ('Greece'), ('Greenland'), ('Grenada'),
						('Guatemala'), ('Guinea'), ('Guinea-Bissau'), ('Guyana'), ('Haiti'), ('Honduras'), ('Hong Kong'),
						('Hungary'), ('Iceland'), ('India'), ('Indonesia'), ('Iran'), ('Iraq'), ('Ireland'), ('Israel'),
						('Italy'), ('Jamaica'), ('Japan'), ('Jordan'), ('Kazakhstan'), ('Kenya'), ('Kiribati'),
						('Korea, North'), ('Korea, South'), ('Kuwait'), ('Kyrgyzstan'), ('Laos'), ('Latvia'), ('Lebanon'),
						('Lesotho'), ('Liberia'), ('Libya'), ('Liechtenstein'), ('Lithuania'), ('Luxembourg'), ('Macedonia'),
						('Madagascar'),	('Malawi'), ('Malaysia'), ('Maldives'), ('Mali'), ('Malta'), ('Marshall Islands'),
						('Mauritania'), ('Mauritius'), ('Mexico'), ('Micronesia'), ('Moldova'), ('Mongolia'), ('Morocco'),
						('Monaco'), ('Mozambique'), ('Namibia'), ('Nauru'), ('Nepal'), ('Netherlands'), ('New Zealand'),
						('Nicaragua'), ('Niger'), ('Nigeria'), ('Norway'), ('Oman'), ('Pakistan'), ('Panama'),
						('Papua New Guinea'),	('Paraguay'), ('Peru'), ('Philippines'), ('Poland'), ('Portugal'), ('Qatar'),
						('Romania'), ('Russia'), ('Rwanda'), ('Samoa'), ('San Marino'), ('Sao Tome'), ('Saudi Arabia'),
						('Senegal'),	('Serbia and Montenegro'), ('Seychelles'), ('Sierra Leone'), ('Singapore'),
						('Slovakia'), ('Slovenia'), ('Solomon Islands'), ('Somalia'), ('South Africa'), ('Spain'),
						('Sri Lanka'), ('Sudan'), ('Suriname'), ('Swaziland'), ('Sweden'), ('Switzerland'),
						('Syria'), ('Taiwan'), ('Tajikistan'), ('Tanzania'), ('Thailand'), ('Togo'), ('Tonga'),
						('Trinidad and Tobago'), ('Tunisia'), ('Turkey'), ('Turkmenistan'), ('Uganda'), ('Ukraine'),
						('United Arab Emirates'), ('United Kingdom'), ('United States'), ('Uruguay'), ('Uzbekistan'),
						('Vanuatu'), ('Venezuela'), ('Vietnam'), ('Yemen'), ('Zambia'), ('Zimbabwe')";
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
						('Liberia', 'Nimba'), ('Liberia', 'Rivercess'), ('Liberia', 'River Gee'), ('Liberia', 'Sinoe')";
		/**
			Liberia and Malaysia county and state.
		**/
		/*
		$query = "INSERT INTO county
						(countyId, countyCode)
					VALUES
						('Liberia', 'Bomi'), ('Liberia', 'Bong'), ('Liberia', 'Gbarpolu'), ('Liberia', 'Grand Bassa'),
						('Liberia', 'Grand Cape Mount'), ('Liberia', 'Grand Gedeh'), ('Liberia', 'Grand Kru'),
						('Liberia', 'Lofa'), ('Liberia', 'Margibi'), ('Liberia', 'Maryland'), ('Liberia', 'Grand Montserrado'),
						('Liberia', 'Nimba'), ('Liberia', 'Rivercess'), ('Liberia', 'River Gee'), ('Liberia', 'Sinoe'),
						('Malaysia', 'Johor'), ('Malaysia', 'Kedah'), ('Malaysia', 'Kelantan'), ('Malaysia', 'Kuala Lumpur'),
						('Malaysia', 'Labuan'), ('Malaysia', 'Melaka'), ('Malaysia', 'Negeri Sembilan'),
						('Malaysia', 'Pahang'), ('Malaysia', 'Perak'), ('Malaysia', 'Perlis'), ('Malaysia', 'Penang'),
						('Malaysia', 'Sabah'), ('Malaysia', 'Sarawak'), ('Malaysia', 'Selangor'),
						('Malaysia', 'Terengganu')";
		*/
		$result = mysql_query($query);
		if(!$result) die ("Tables access failed: " . mysql_error());

		// Parts Uom details
		$query = "INSERT INTO partsUom
						(dateTime, partsUomId, partsUomName, status)
					VALUES
						('$time', 'PU001', 'Bags', 'Active'), ('$time', 'PU002', 'Bails', 'Active'),
						('$time', 'PU003', 'Bales', 'Active'), ('$time', 'PU004', 'Books', 'Active'),
						('$time', 'PU005', 'Bottles', 'Active'), ('$time', 'PU006', 'Boxes', 'Active'),
						('$time', 'PU007', 'Buckets', 'Active'), ('$time', 'PU008', 'Bundles', 'Active'),
						('$time', 'PU009', 'Cans', 'Active'), ('$time', 'PU010', 'Cartons', 'Active'),
						('$time', 'PU011', 'Coils', 'Active'), ('$time', 'PU012', 'Cups', 'Active'),
						('$time', 'PU013', 'Dozens', 'Active'), ('$time', 'PU014', 'Feet', 'Active'),
						('$time', 'PU015', 'Gallons', 'Active'), ('$time', 'PU016', 'Kgs', 'Active'),
						('$time', 'PU017', 'Litres', 'Active'), ('$time', 'PU018', 'Meters', 'Active'),
						('$time', 'PU019', 'Packs', 'Active'), ('$time', 'PU020', 'Pairs', 'Active'),
						('$time', 'PU021', 'Pcs', 'Active'), ('$time', 'PU022', 'Quarts', 'Active'),
						('$time', 'PU023', 'Reams', 'Active'), ('$time', 'PU024', 'Rolls', 'Active'),
						('$time', 'PU025', 'Sets', 'Active'), ('$time', 'PU026', 'Sheets', 'Active'),
						('$time', 'PU027', 'Suits', 'Active'), ('$time', 'PU028', 'Tins', 'Active'),
						('$time', 'PU029', 'Units', 'Active'), ('$time', 'PU030', 'Yards', 'Active')";
		$result = mysql_query($query);
		if(!$result) die ("Tables access failed: " . mysql_error());
		// Parts Category details
		$query = "INSERT INTO partsCategory
						(dateTime, partsCategoryId, partsCategoryName, status)
					VALUES
						('$time', 'PC001', 'Asset', 'Active'), ('$time', 'PC002', 'Battery', 'Active'),
						('$time', 'PC003', 'Bolt & Nut', 'Active'), ('$time', 'PC004', 'Camp Supply', 'Active'),
						('$time', 'PC005', 'Electrical Supply', 'Active'), ('$time', 'PC006', 'Gears', 'Active'),
						('$time', 'PC007', 'General Supply', 'Active'), ('$time', 'PC008', 'Lubricant & Oil', 'Active'),
						('$time', 'PC009', 'Oil Seal', 'Active'), ('$time', 'PC010', 'Parts', 'Active'),
						('$time', 'PC011', 'Parts Book', 'Active'), ('$time', 'PC012', 'Production Supply', 'Active'),
						('$time', 'PC013', 'Stationary', 'Active'), ('$time', 'PC014', 'Tools', 'Active'),
						('$time', 'PC015', 'Tyre', 'Active'), ('$time', 'PC016', 'Workshop Supply', 'Active')";
		$result = mysql_query($query);
		if(!$result) die ("Tables access failed: " . mysql_error());
		// Parts Brand details
		$query = "INSERT INTO partsBrand
						(dateTime, partsBrandId, partsBrandName, status)
					VALUES
						('$time', 'PB001', 'Beiben Mecedez', 'Active'), ('$time', 'PB002', 'Camings', 'Active'),
						('$time', 'PB003', 'CASE', 'Active'), ('$time', 'PB004', 'CAT', 'Active'),
						('$time', 'PB005', 'Chine White', 'Active'), ('$time', 'PB006', 'DAYUN', 'Active'),
						('$time', 'PB007', 'Dong Feng', 'Active'), ('$time', 'PB008', 'Good Year', 'Active'),
						('$time', 'PB009', 'ISUZU', 'Active'), ('$time', 'PB010', 'Kama', 'Active'),
						('$time', 'PB011', 'Komatsu', 'Active'), ('$time', 'PB012', 'Mercedez', 'Active'),
						('$time', 'PB013', 'Mitsubishi', 'Active'), ('$time', 'PB014', 'Nissan', 'Active'),
						('$time', 'PB015', 'Perkins', 'Active'), ('$time', 'PB016', 'Rhino', 'Active'),
						('$time', 'PB017', 'SAE', 'Active'), ('$time', 'PB018', 'SEM', 'Active'),
						('$time', 'PB019', 'Shan Tui', 'Active'), ('$time', 'PB020', 'Suzuki', 'Active'),
						('$time', 'PB021', 'Toyota', 'Active'), ('$time', 'PB022', 'XG3200S', 'Active'),
						('$time', 'PB023', 'Xu Gong', 'Active')";
		$result = mysql_query($query);
		if(!$result) die ("Tables access failed: " . mysql_error());
		// Parts Model details
		$query = "INSERT INTO partsModel
						(dateTime, partsModelId, partsModelName, status)
					VALUES
						('$time', 'PM001', '528B', 'Active'), ('$time', 'PM002', 'Beiben 2541KZ', 'Active'),
						('$time', 'PM003', 'C6121', 'Active'), ('$time', 'PM004', 'CASE 580SL', 'Active'),
						('$time', 'PM005', 'CAT 140G', 'Active'), ('$time', 'PM006', 'CAT 528', 'Active'),
						('$time', 'PM007', 'CAT 962G', 'Active'), ('$time', 'PM008', 'CAT 966C', 'Active'),
						('$time', 'PM009', 'CAT D6G', 'Active'), ('$time', 'PM010', 'CAT D7G', 'Active'),
						('$time', 'PM011', 'DY125-B', 'Active'), ('$time', 'PM012', 'EQ1258KB', 'Active'),
						('$time', 'PM013', 'Mitsubishi L200', 'Active'), ('$time', 'PM014', 'Montero', 'Active'),
						('$time', 'PM015', 'Nissan Frontier', 'Active'), ('$time', 'PM016', 'Nissan March', 'Active'),
						('$time', 'PM017', 'Nissan V8', 'Active'), ('$time', 'PM018', 'PC200-6', 'Active'),
						('$time', 'PM019', 'SC8DK230Q3', 'Active'), ('$time', 'PM020', 'SD16', 'Active'),
						('$time', 'PM021', 'SD22', 'Active'), ('$time', 'PM022', 'SEM 660B', 'Active'),
						('$time', 'PM023', 'TACOMA', 'Active'), ('$time', 'PM024', 'Toyota 4 Runner', 'Active'),
						('$time', 'PM025', 'Toyota Fortuna', 'Active'), ('$time', 'PM026', 'Toyota Hilux', 'Active'),
						('$time', 'PM027', 'Toyota Land Cruiser', 'Active'), ('$time', 'PM028', 'TS654', 'Active')";
		$result = mysql_query($query);
		if(!$result) die ("Tables access failed: " . mysql_error());
		// Parts Equipment Type details
		$query = "INSERT INTO partsEquipType
						(dateTime, partsEquipTypeId, partsEquipTypeName, status)
					VALUES
						('$time', 'PQ001', 'Air Compressor', 'Active'), ('$time', 'PQ002', 'Backhoe', 'Active'),
						('$time', 'PQ003', 'Bulldozer', 'Active'), ('$time', 'PQ004', 'Chain Saw', 'Active'),
						('$time', 'PQ005', 'Crane', 'Active'), ('$time', 'PQ006', 'Cutting Machine', 'Active'),
						('$time', 'PQ007', 'Dump Truck', 'Active'), ('$time', 'PQ008', 'Excavator', 'Active'),
						('$time', 'PQ009', 'Farm Tractor', 'Active'), ('$time', 'PQ010', 'Forklift', 'Active'),
						('$time', 'PQ011', 'Fuel Tanker', 'Active'), ('$time', 'PQ012', 'Gasoline Car', 'Active'),
						('$time', 'PQ013', 'Generator', 'Active'), ('$time', 'PQ014', 'Jeep', 'Active'),
						('$time', 'PQ015', 'Lathe Machine', 'Active'), ('$time', 'PQ016', 'Logging Truck', 'Active'),
						('$time', 'PQ017', 'Lorry', 'Active'), ('$time', 'PQ018', 'Low Bed', 'Active'),
						('$time', 'PQ019', 'Motor Bike', 'Active'), ('$time', 'PQ020', 'Motor Grader', 'Active'),
						('$time', 'PQ021', 'Pickup', 'Active'), ('$time', 'PQ022', 'Radio', 'Active'),
						('$time', 'PQ023', 'Skid Tanker', 'Active'), ('$time', 'PQ024', 'Skidder', 'Active'),
						('$time', 'PQ025', 'Small Car', 'Active'), ('$time', 'PQ026', 'Wheel Loader', 'Active')";
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