<?php include('pages/page_header.php'); ?>
<link href="css/components.css" rel="stylesheet" type="text/css" />
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript">
	history.pushState(null, null, '');
	window.addEventListener('popstate', function(event) {
		history.pushState(null, null, '');
	});
</script>
<?php include('pages/page_meta.php'); ?>
<?php
	require_once('db/db_config.php');
	// Check session id.
	$login = $_SESSION['LOGIN_ID'];
	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	$query = "SELECT * FROM tempSession WHERE emailAdd = '$login'";
	$result = mysql_query($query);
	if(!$result) die ("Table access failed: " . mysql_error());
	$data = mysql_fetch_assoc($result);
	$sid = $data['sid'];
	if($sid == $_SESSION['SID']) {
		if(isset($_SESSION['LOGGEDIN']) && isset($_SESSION['SID'])) {
			if($_SESSION['GID'] < 3000) {
				$fname = $_SESSION['FNAME'];
				$uid = $_SESSION['UID'];
				$sessionTimeout = $_SESSION['SESSIONTIMEOUT'];
				// Lifetime added 5min.
				if(isset($_SESSION['EXPIRETIME'])) {
					if($_SESSION['EXPIRETIME'] < time()) {
						unset($_SESSION['EXPIRETIME']);
						header('Location: logout.php?TIMEOUT');
						exit(0);
					} else {
						// Session time out.
						$_SESSION['EXPIRETIME'] = time() + $sessionTimeout;
					};
				};
				// Select parts lists.
				mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
				$query = "SELECT * from partsMasterFile ORDER BY id DESC";
				$result = mysql_query($query);
				$row = mysql_num_rows($result);
				if(!$result) die ("Table access failed: " . mysql_error());
				if($row == 0) {
					$partsId = 'P000001';
				} else {
					$row++;
					$partsId = 'P' . sprintf('%06d', $row);
				}
				// Get post data and submit to database.
				if(isset($_POST['serialNumber']) && isset($_POST['partsNumber'])) {
					$partsId = $_POST['serialNumber'];
					$partsNumber = ucwords(strtolower(mysql_escape_string($_POST['partsNumber'])));
					$partsDescription = ucwords(strtolower(mysql_escape_string($_POST['description'])));
					$partsUom = mysql_escape_string($_POST['uom']);
					$partsCategory = mysql_escape_string($_POST['category']);
					$partsBrand = mysql_escape_string($_POST['brand']);
					$partsModel = mysql_escape_string($_POST['model']);
					$partsEquipType = mysql_escape_string($_POST['equipType']);
					$partsWhereUsedI = ucwords(strtolower(mysql_escape_string($_POST['whereUsedI'])));
					$partsWhereUsedII = ucwords(strtolower(mysql_escape_string($_POST['whereUsedII'])));
					// Insert record.
					$query = "SELECT DATE_ADD(NOW(), INTERVAL 13 HOUR) as 'dateTime'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$time = $row['dateTime'];
					$query = "INSERT INTO partsMasterFile
									(dateTime, partsId, partsNumber, partsDescription, partsUom, partsCategory, partsBrand,
									partsModel, partsEquipType, partsWhereUsedI, partsWhereUsedII, status, createdBy)
								VALUES
									('$time', '$partsId', '$partsNumber', '$partsDescription', '$partsUom', '$partsCategory',
									'$partsBrand', '$partsModel', '$partsEquipType', '$partsWhereUsedI', '$partsWhereUsedII',
									'Active', '$uid')";
					$result = mysql_query($query);
					if(!$result) die ("Table access failed: " . mysql_error());
					if($result) {
						// Parts information created and redirected to previous page.
						$_SESSION['STATUS'] = 26;
						header("Location: status.php");
					};
				};
			} else {
				// Redirect to dashboard if not Superuser or Manager
				$_SESSION['STATUS'] = 10;
				header('Location: status.php');
			}
		} else {
			unset($_SESSION['STATUS']);
			header('Location: status.php');
		};
	} else {
		unset($_SESSION['STATUS']);
		header('Location: status.php');
	}
?>
<?php include('pages/page_menu.php'); ?>
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<div class="page-title">
				<h1>Add Parts <small>add parts to master file</small></h1>
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
					<a href="javascript:;">Procurement</a><i class="fa fa-circle"></i>
				</li>
				<li class="active">
					Add Parts
				</li>
			</ul>
			<div class="row margin-top-10">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><span class="caption-subject font-green-sharp bold uppercase">Add Parts</span></div>
							<div class="tools"></div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="add_parts.php" method="post" onsubmit="return validate();">
								<div class="form-body text-left">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label>S/N</label>
												<input type="text" class="form-control input-lg" name="serialNumber" style="text-align: center" value="<?php echo $partsId; ?>" readonly>
											</div>
										</div>
										<div class="col-md-9" style="display: none">
											<div class="form-group">
												<label>Date</label>
												<input type="text" class="form-control input-lg" name="dateTime" style="text-align: center" value="<?php echo date("d/m/Y"); ?>" disabled>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Parts Number</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-code"></i>
													<input type="text" class="form-control input-lg" name="partsNumber" placeholder="Parts Number" id="partsNumber" onkeyup="checkPartsNumber();" autofocus="on" required>
												</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group">
												<label>Description</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-info"></i>
													<input type="text" class="form-control input-lg" name="description" placeholder="Description" required>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>UOM</label>
												<select name="uom" class="form-control input-lg" required>
													<option value="">Select UOM</option>
													<option value="Bags">Bags</option>
													<option value="Bails">Bails</option>
													<option value="Bales">Bales</option>
													<option value="Books">Books</option>
													<option value="Bottles">Bottles</option>
													<option value="Boxes">Boxes</option>
													<option value="Buckets">Buckets</option>
													<option value="Bundles">Bundles</option>
													<option value="Cans">Cans</option>
													<option value="Cartons">Cartons</option>
													<option value="Coils">Coils</option>
													<option value="Cups">Cups</option>
													<option value="Dozens">Dozens</option>
													<option value="Feet">Feet</option>
													<option value="Gallons">Gallons</option>
													<option value="Kgs">Kgs</option>
													<option value="Litres">Litres</option>
													<option value="Meters">Meters</option>
													<option value="Packs">Packs</option>
													<option value="Pairs">Pairs</option>
													<option value="Pcs">Pcs</option>
													<option value="Quarts">Quarts</option>
													<option value="Reams">Reams</option>
													<option value="Rolls">Rolls</option>
													<option value="Sets">Sets</option>
													<option value="Sheets">Sheets</option>
													<option value="Suits">Suits</option>
													<option value="Tins">Tins</option>
													<option value="Units">Units</option>
													<option value="Yards">Yards</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Category</label>
												<select name="category" class="form-control input-lg" required>
													<option value="">Select Category</option>
													<option value="Asset">Asset</option>
													<option value="Battery">Battery</option>
													<option value="Bolt & Nut">Bolt & Nut</option>
													<option value="Camp Supply">Camp Supply</option>
													<option value="Electrical Supply">Electrical Supply</option>
													<option value="Gears">Gears</option>
													<option value="General Supply">General Supply</option>
													<option value="Lubricant & Oil">Lubricant & Oil</option>
													<option value="Oil Seal">Oil Seal</option>
													<option value="Parts">Parts</option>
													<option value="Parts Book">Parts Book</option>
													<option value="Production Supply">Production Supply</option>
													<option value="Stationary">Stationary</option>
													<option value="Tools">Tools</option>
													<option value="Tyre">Tyre</option>
													<option value="Workshop Supply">Workshop Supply</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Brand</label>
												<select name="brand" class="form-control input-lg" required>
													<option value="">Select Brand</option>
													<option value="Beiben Mecedez">Beiben Mecedez</option>
													<option value="Camings">Camings</option>
													<option value="CASE">CASE</option>
													<option value="CAT">CAT</option>
													<option value="Chine White">Chine White</option>
													<option value="DAYUN">DAYUN</option>
													<option value="Dong Feng">Dong Feng</option>
													<option value="Good Year">Good Year</option>
													<option value="ISUZU">ISUZU</option>
													<option value="Kama">Kama</option>
													<option value="Komatsu">Komatsu</option>
													<option value="Mercedez">Mercedez</option>
													<option value="Mitsubishi">Mitsubishi</option>
													<option value="Nissan">Nissan</option>
													<option value="Perkins">Perkins</option>
													<option value="Rhino">Rhino</option>
													<option value="SAE">SAE</option>
													<option value="SEM">SEM</option>
													<option value="Shan Tui">Shan Tui</option>
													<option value="Suzuki">Suzuki</option>
													<option value="Toyota">Toyota</option>
													<option value="XG3200S">XG3200S</option>
													<option value="Xu Gong">Xu Gong</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Model</label>
												<select name="model" class="form-control input-lg" required>
													<option value="">Select Model</option>
													<option value="528B">528B</option>
													<option value="Beiben 2541KZ">Beiben 2541KZ</option>
													<option value="C6121">C6121</option>
													<option value="CASE 580SL">CASE 580SL</option>
													<option value="CAT 140G">CAT 140G</option>
													<option value="CAT 528">CAT 528</option>
													<option value="CAT 962G">CAT 962G</option>
													<option value="CAT 966C">CAT 966C</option>
													<option value="CAT D6G">CAT D6G</option>
													<option value="CAT D7G">CAT D7G</option>
													<option value="DY125-B">DY125-B</option>
													<option value="EQ1258KB">EQ1258KB</option>
													<option value="Mitsubishi L200">Mitsubishi L200</option>
													<option value="Montero">Montero</option>
													<option value="Nissan Frontier">Nissan Frontier</option>
													<option value="Nissan March">Nissan March</option>
													<option value="Nissan V8">Nissan V8</option>
													<option value="PC200-6">PC200-6</option>
													<option value="SC8DK230Q3">SC8DK230Q3</option>
													<option value="SD16">SD16</option>
													<option value="SD22">SD22</option>
													<option value="SEM 660B">SEM 660B</option>
													<option value="TACOMA">TACOMA</option>
													<option value="Toyota 4 Runner">Toyota 4 Runner</option>
													<option value="Toyota Fortuna">Toyota Fortuna</option>
													<option value="Toyota Hilux">Toyota Hilux</option>
													<option value="Toyota Land Cruiser">Toyota Land Cruiser</option>
													<option value="TS654">TS654</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label>Equipment Type</label>
												<select name="equipType" class="form-control input-lg" required>
													<option value="">Select Equipment Type</option>
													<option value="Air Compressor">Air Compressor</option>
													<option value="Backhoe">Backhoe</option>
													<option value="Bulldozer">Bulldozer</option>
													<option value="Chain Saw">Chain Saw</option>
													<option value="Crane">Crane</option>
													<option value="Cutting Machine">Cutting Machine</option>
													<option value="Dump Truck">Dump Truck</option>
													<option value="Excavator">Excavator</option>
													<option value="Farm Tractor">Farm Tractor</option>
													<option value="Forklift">Forklift</option>
													<option value="Fuel Tanker">Fuel Tanker</option>
													<option value="Gasoline Car">Gasoline Car</option>
													<option value="Generator">Generator</option>
													<option value="Jeep">Jeep</option>
													<option value="Lathe Machine">Lathe Machine</option>
													<option value="Logging Truck">Logging Truck</option>
													<option value="Lorry">Lorry</option>
													<option value="Low Bed">Low Bed</option>
													<option value="Motor Bike">Motor Bike</option>
													<option value="Motor Grader">Motor Grader</option>
													<option value="Pickup">Pickup</option>
													<option value="Radio">Radio</option>
													<option value="Skid Tanker">Skid Tanker</option>
													<option value="Skidder">Skidder</option>
													<option value="Small Car">Small Car</option>
													<option value="Wheel Loader">Wheel Loader</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Where Used I</label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-map-marker"></i>
													<input type="text" class="form-control input-lg" name="whereUsedI" placeholder="Where Used I">
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label>Where Used II <small class="addParts-optional"> - Optional</small></label>
												<div class="input-icon input-icon-lg">
													<i class="fa fa-map-marker"></i>
													<input type="text" class="form-control input-lg" name="whereUsedII" placeholder="Where Used II - Optional">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<span class="error-status" id="partsNumber_status"></span>
										</div>
									</div>
								</div>
								<div class="form-actions" style="text-align: center">
									<input type="submit" value="Submit" class="btn blue">
									<a href="parts_mfile.php"><button type="button" class="btn default">Cancel</button></a>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	</div>
</div>
<?php include('pages/page_jquery.php'); ?>
<script>
	// Alertify confirm logout.
	$(function() {
		$('.logoutAlert').click(function() {
			alertify.confirm("[ALERT]  Are you sure you want to LOGOUT?", function(result) {
				if(result) {
					window.location = "logout.php";
				}
			})
		})
	})
	// Bootbox alert customize.
	/*
	$(function() {
		$('.logoutAlert').click(function(){
			bootbox.confirm("Are you sure you want to LOGOUT?", function(result) {
				if(result) {
					window.location = "logout.php";
				}
			});
		})
	})
	*/
	function checkPartsNumber() {
		var partsNumber = document.getElementById("partsNumber").value;
		if(partsNumber) {
			$.ajax({
				type: 'post',
				url: 'check_data.php',
				data: {
					partsNumber: partsNumber,
				},
				success: function (response) {
					if(response == "true") {
						$('#partsNumber_status').html("");
						return true;
					} else {
						$('#partsNumber_status').html(response);
						return false;
	               }
	            }
			});
		} else {
			$('#partsNumber_status').html("");
			return false;
		}
	}
	function validate() {
		var partsNumber_status = document.getElementById("partsNumber_status").innerHTML;
		if(partsNumber_status == "") {
			return true;
		} else {
	          return false;
		}
	}
</script>
<?php include('pages/page_footer.php'); ?>
</body>
</html>