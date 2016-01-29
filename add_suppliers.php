<?php include("page_head.php"); ?>
<link href="css/center.css" rel="stylesheet" type="text/css" />
<link href="css/components_metro.css" rel="stylesheet" type="text/css" />
<link href="css/layout_metro.css" rel="stylesheet" type="text/css" />
<title>Mandra - Backend system for management.</title>
</head>
<body>
<?php
require_once("db_config.php");

if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
	if($_SESSION['GID'] < 3000) {
		if($_SESSION['DEPARTMENT'] == "Procurements" || $_SESSION['DEPARTMENT'] == "All") {
			$fname = $_SESSION['FNAME'];
			if(isset($_POST['suppCode']) && isset($_POST['country'])) {
				$login = $_SESSION['LOGIN_ID'];
				$code = strtoupper(mysql_escape_string($_POST['suppCode']));
				$country = $_POST['country'];
				$area = $_POST['area'];
				$suppName = ucwords(strtolower(mysql_escape_string($_POST['suppName'])));
				$contactNum = mysql_escape_string($_POST['contactNum']);
				$faxNum = mysql_escape_string($_POST['faxNum']);
				$address = ucwords(strtolower(mysql_escape_string($_POST['address'])));
				$email = strtolower($_POST['email']);
				$website = mysql_escape_string($_POST['website']);
				$bussScope = ucfirst(strtolower(mysql_escape_string($_POST['bussScope'])));
				// echo "<h1> {$login} / {$code} / {$country} / {$area} / {$suppName} / {$contactNum} / {$faxNum} / {$address} / {$email} / {$website} / {$bussScope} </h1>";
				$dbSelected = mysql_select_db($dbNAME) or die("Unable to select database: " . mysql_error());
				if($dbSelected) {
					$query = "INSERT INTO supplierLISTS (code, name, scope, area, country, address, phone, fax, email, website, status, add_P) VALUES('$code', '$suppName', '$bussScope', '$area', '$country', '$address', '$contactNum', '$faxNum', '$email', '$website', 'Active', '$login')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						$_SESSION['STATUS'] = 5;
						header("Location: status.php");
					};
				};
			};
		} else {
			/**
			 From not allow department.
			 **/
			$_SESSION['STATUS'] = 10;
			header('Location: status.php');
		};
	} else {
		/**
		 Users are not allow!
		 **/
		$_SESSION['STATUS'] = 10;
		header('Location: status.php');
	}
} else {
	header('Location: status.php');
};
?>
<?php include("page_menu.php"); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Suppliers <small>register suppliers.</small></h1>
			</div>
			<div class="page-toolbar">
				<div class="btn-group btn-theme-panel"><a href="javascript:;" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="glyphicon glyphicon-cog"></i></a></div>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<ul class="breadcrumb">
				<li>
					<a href="index.php">Home</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Maintenance</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Purchasing</a><i class="fa fa-circle"></i>
				</li>
				<li>
					<a href="javascript:;">Suppliers</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Add Suppliers
				</li>
			</ul>
			<div class="block" style="height:100%">
				<div class="centered-supreg">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Suppliers</span></div>
									<div class="tools"></div>
								</div>
								<div class="portlet-body form">
									<form role="form" action="add_suppliers.php" method="post">
										<div class="form-body text-left">
											<div class="row">
												<div class="col-md-5">
													<div class="form-group">
														<label>Code</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-code"></i>
															<input type="text" class="form-control input-lg" placeholder="Supplier Code" name="suppCode" required>
														</div>
													</div>
												</div>
												<div class="col-md-5">
													<div class="form-group">
														<label>Country</label>
														<select class="form-control input-lg" name="country" id="country" required>
															<script>
																var country = new Array("Please select country", "China", "Hong Kong", "Liberia", "Malaysia", "Singapore");
																// var country = new Array("Please select country ", "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burma", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic", "Congo, Republic of the", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Mongolia", "Morocco", "Monaco", "Mozambique", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Samoa", "San Marino", " Sao Tome", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe");
																for(var cnt = 0; cnt < country.length; cnt++) {
																	document.write('<option value="'+country[cnt]+'">'+country[cnt]+'</option>');
																}
															</script>
														</select>
														<!--
														<div class="input-icon input-icon-lg"><i class="fa fa-envelope-o"></i>
															<input type="text" class="form-control input-lg" name="country">
														</div>
														-->
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label>Area</label>
														<select class="form-control input-lg" name="area" required>
															<option value="F">Foreign</option>
															<option value="L">Local</option>
														</select>
														<!--
														<div class="input-icon input-icon-lg"><i class="fa fa-envelope-o"></i>
															<input type="text" class="form-control input-lg" name="area" required>
														</div>
														-->
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Name</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-user"></i>
															<input type="text" class="form-control input-lg" placeholder="Supplier Name" name="suppName" required>
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>Contact Num.</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-phone"></i>
															<input type="number" class="form-control input-lg" placeholder="Contact Number" name="contactNum" required>
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>Fax Num.</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-fax"></i>
															<input type="number" class="form-control input-lg" placeholder="Fax Number" name="faxNum">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label>Address</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-map-marker"></i>
															<input type="text" class="form-control input-lg" placeholder="Address" name="address" required>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Email</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-envelope-o"></i>
															<input type="email" class="form-control input-lg" placeholder="Email Address" name="email" required>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Website</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-home"></i>
															<input type="url" class="form-control input-lg" placeholder="Website" name="website">
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label>Business Scope</label>
														<div class="input-icon input-icon-lg"><i class="fa fa-briefcase"></i>
															<input type="text" class="form-control input-lg" placeholder="Business Scope" name="bussScope">
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-actions">
											<input type="submit" value="Submit" class="btn blue" onclick="countrySelect()">
											<a href="suppliers.php"><button type="button" class="btn default">Cancel</button></a>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
<script>
	function countrySelect() {
		var ddl = document.getElementById("country");
		var selectedValue = ddl.options[ddl.selectedIndex].value;
		if(selectedValue == "Please select country") {
			alert("[ERROR] Please select country!");
		}
	}
</script>
<?php include("page_footer.php"); ?>
<?php include("page_jquery.php"); ?>
</body>
</html>