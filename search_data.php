<?php
require_once('db/db_config.php');

$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
if($dbSelected) {
	/**
		Validate data for parts details.
	**/
	if(!empty($_POST['searchPartsNumber'])) {
		$partsNumber = $_POST['searchPartsNumber'];
		$query = "SELECT * FROM partsMasterFile WHERE partsNumber LIKE '%$partsNumber%' AND status = 'Active'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$rows = mysql_num_rows($result);
		if($rows > 0) {
			/**
				Parts information found.
			**/
			//echo "<div id='searchDiv' class='table-scrollable table-scrollable-borderless'>";
			echo "<table class='table table-hover table-light'>";
			echo "<thead><tr class='uppercase'><th colspan='2'>S/N</th>";
			echo "<th class='left'>Parts Number</th>";
			echo "<th class='left'>Description</th>";
			echo "<th class='center'>Date Created</th>";
			echo "</tr></thead><tbody>";
			for($j = 0; $j < $rows; ++$j) {
				$partsId = ucfirst(mysql_result($result, $j, 'partsId'));
				$partsNumber = ucfirst(mysql_result($result, $j, 'partsNumber'));
				$partsDescription = mysql_result($result, $j, 'partsDescription');
				$string = mysql_result($result, $j, 'dateTime');
				if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
					$dateCreated = $match[1];
				};
				echo "<tr><td class='fit'>";
				echo "<img class='parts-pic' src='images/parts.png'></td>";
				echo "<td><a href='mod_parts.php?partsId=$partsId' class='primary-link'>$partsId</a></td>";
				echo "<td align='left'>$partsNumber</td>";
				echo "<td align='left'>$partsDescription</td>";
				echo "<td align='center'>$dateCreated</td></tr>";
			}
			echo "</tbody></table>";
			echo "<script src='js/jquery-2.1.3.min.js'></script>";
			echo "<script src='js/jquery.slimscroll.min.js'></script>";
			echo "<script>";
			echo "$(function() {
						var row = $rows;
						if(row < 7) {	} else {
							$('#searchDiv').slimScroll({
								height: '325px'
							});
						}
					});";
			echo "</script>";
		} else {
			/**
				No master file.
			**/
			//echo "<div id='searchDiv' class='table-scrollable table-scrollable-borderless'>";
			$message = "<table class='table table-hover table-light'>";
			$message .= "<div class='block' style='height:100%'><div class='centered-users'>";
			$message .= "<h3 class='no-users'>No parts master file found!</h3></div></div></table></div>";
			echo $message;
			//return true;
		}
	}
	/**
		Validate data for parts details.
	**/
	if(!empty($_POST['searchDescription'])) {
		$partsDescription = $_POST['searchDescription'];
		$query = "SELECT * FROM partsMasterFile WHERE partsDescription LIKE '%$partsDescription%' AND status = 'Active'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$rows = mysql_num_rows($result);
		if($rows > 0) {
			/**
				Parts information found.
			**/
			//echo "<div id='searchDiv' class='table-scrollable table-scrollable-borderless'>";
			echo "<table class='table table-hover table-light'>";
			echo "<thead><tr class='uppercase'><th colspan='2'>S/N</th>";
			echo "<th class='left'>Parts Number</th>";
			echo "<th class='left'>Description</th>";
			echo "<th class='center'>Date Created</th>";
			echo "</tr></thead><tbody>";
			for($j = 0; $j < $rows; ++$j) {
				$partsId = ucfirst(mysql_result($result, $j, 'partsId'));
				$partsNumber = ucfirst(mysql_result($result, $j, 'partsNumber'));
				$partsDescription = mysql_result($result, $j, 'partsDescription');
				$string = mysql_result($result, $j, 'dateTime');
				if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
					$dateCreated = $match[1];
				};
				echo "<tr><td class='fit'>";
				echo "<img class='parts-pic' src='images/parts.png'></td>";
				echo "<td><a href='mod_parts.php?partsId=$partsId' class='primary-link'>$partsId</a></td>";
				echo "<td align='left'>$partsNumber</td>";
				echo "<td align='left'>$partsDescription</td>";
				echo "<td align='center'>$dateCreated</td></tr>";
			}
			echo "</tbody></table>";
			echo "<script src='js/jquery-2.1.3.min.js'></script>";
			echo "<script src='js/jquery.slimscroll.min.js'></script>";
			echo "<script>";
			echo "$(function() {
						var row = $rows;
						if(row < 7) {	} else {
							$('#searchDiv').slimScroll({
								height: '325px'
							});
						}
					});";
			echo "</script>";
		} else {
			/**
				No master file.
			**/
			$message = "<table class='table table-hover table-light'>";
			$message .= "<div class='block' style='height:100%'><div class='centered-users'>";
			$message .= "<h3 class='no-users'>No parts master file found!</h3></div></div></table></div>";
			echo $message;
		}
	}
	/**
		Validate data for parts details.
	**/
	if(!empty($_POST['searchBrand'])) {
		$partsBrand = $_POST['searchBrand'];
		$query = "SELECT * FROM partsMasterFile WHERE partsBrand LIKE '%$partsBrand%' AND status = 'Active'";
		$result = mysql_query($query);
		if(!$result) die ("Table access failed: " . mysql_error());
		$rows = mysql_num_rows($result);
		if($rows > 0) {
			/**
				Parts information found.
			**/
			//echo "<div id='searchDiv' class='table-scrollable table-scrollable-borderless'>";
			echo "<table class='table table-hover table-light'>";
			echo "<thead><tr class='uppercase'><th colspan='2'>S/N</th>";
			echo "<th class='left'>Parts Number</th>";
			echo "<th class='left'>Description</th>";
			echo "<th class='center'>Date Created</th>";
			echo "</tr></thead><tbody>";
			for($j = 0; $j < $rows; ++$j) {
				$partsId = ucfirst(mysql_result($result, $j, 'partsId'));
				$partsNumber = ucfirst(mysql_result($result, $j, 'partsNumber'));
				$partsDescription = mysql_result($result, $j, 'partsDescription');
				$string = mysql_result($result, $j, 'dateTime');
				if(preg_match('/(\d{4}-\d{2}-\d{2})/', $string, $match)) {
					$dateCreated = $match[1];
				};
				echo "<tr><td class='fit'>";
				echo "<img class='parts-pic' src='images/parts.png'></td>";
				echo "<td><a href='mod_parts.php?partsId=$partsId' class='primary-link'>$partsId</a></td>";
				echo "<td align='left'>$partsNumber</td>";
				echo "<td align='left'>$partsDescription</td>";
				echo "<td align='center'>$dateCreated</td></tr>";
			}
			echo "</tbody></table>";
			echo "<script src='js/jquery-2.1.3.min.js'></script>";
			echo "<script src='js/jquery.slimscroll.min.js'></script>";
			echo "<script>";
			echo "$(function() {
						var row = $rows;
						if(row < 7) {	} else {
							$('#searchDiv').slimScroll({
								height: '325px'
							});
						}
					});";
			echo "</script>";
		} else {
			/**
				No master file.
			**/
			$message = "<table class='table table-hover table-light'>";
			$message .= "<div class='block' style='height:100%'><div class='centered-users'>";
			$message .= "<h3 class='no-users'>No parts master file found!</h3></div></div></table></div>";
			echo $message;
		}
	}
}
?>