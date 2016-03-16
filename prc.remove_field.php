<?php
	require_once('db/db_config.php');

	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	if($dbSelected) {
		// Select part no
		$queryParts = "SELECT * FROM partsMasterFile WHERE status = 'Active' ORDER BY partsNumber ASC";
		$resultParts = mysql_query($queryParts);
		$rowsParts = mysql_num_rows($resultParts);
		if(!$resultParts) die ("Table access failed: " . mysql_error());
		// add new field
		if(!empty($_POST['totalRequest'])) {
			$totalRequest = mysql_escape_string($_POST['totalRequest']);
			if($totalRequest == 1) {
				//$tmp = $totalRequest + 1;
				$msg .= "<tr id='row0'>";
				$msg .= "<td align='center'>1</td>";
				$msg .= "<td align='center' id='select_1'><select name='partNo#0' class='form-control input-sm' onchange='partDetails(this.value);' required>";
				if($rowsParts < 1) {
					// No part no were created.
					$msg .= "<option value=''>No Parts Found</option>";
				} else {
					// Found parts no lists.
					$msg .= "<option value='--#0'>Select Part No</option>";
					for($i = 0; $i < $rowsParts; ++$i) {
						$partsNumber = mysql_result($resultParts, $i, 'partsNumber');
						$partsDescription = mysql_result($resultParts, $i, 'partsDescription');
						$msg .= "<option value='$partsNumber#0'>$partsNumber\t ($partsDescription)</option>";
					}
				}
				$msg .= "</select></td>";
				$msg .= "<td align='center'><input id='partsDescription#0' name='partsDescription#0' type='text' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input type='text' class='form-control input-sm' name='prcQty#0' style='text-align: center' required></td>";
				$msg .= "<td align='center'><input id='partsUom#0' name='partsUom#0' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input type='text' name='prcStockQty#0' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input id='partsEquipType#0' name='partsEquipType#0' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input id='partsModel#0' name='partsModel#0' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input type='text' name='prcPlateNo#0' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td id='removeField' style='text-align: center'><img id='removeField0' class='field-remove' src='images/minus2.jpg' onclick='removeField();'></td>";
				$msg .= "</tr>";
				echo $msg;
			}
			if($totalRequest > 1 && $totalRequest < 10) {
				$tmp = $totalRequest;
				$totalRequest = $totalRequest - 1;
				//$tmp = $totalRequest + 1;
				$msg .= "<tr id='row$totalRequest'>";
				$msg .= "<td align='center'>$tmp</td>";
				$msg .= "<td align='center' id='select_1'><select name='partNo#$totalRequest' class='form-control input-sm' onchange='partDetails(this.value);' required>";
				if($rowsParts < 1) {
					// No part no were createds.
					$msg .= "<option value=''>No Parts Found</option>";
				} else {
					// Found parts no lists.
					$msg .= "<option value='--#$totalRequest'>Select Part No</option>";
					for($i = 0; $i < $rowsParts; ++$i) {
						$partsNumber = mysql_result($resultParts, $i, 'partsNumber');
						$partsDescription = mysql_result($resultParts, $i, 'partsDescription');
						$msg .= "<option value='$partsNumber#$totalRequest'>$partsNumber\t ($partsDescription)</option>";
					}
				}
				$msg .= "</select></td>";
				$msg .= "<td align='center'><input id='partsDescription#$totalRequest' name='partsDescription#$totalRequest' type='text' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input type='text' class='form-control input-sm' name='prcQty#$totalRequest' style='text-align: center' required></td>";
				$msg .= "<td align='center'><input id='partsUom#$totalRequest' name='partsUom#$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input type='text' name='prcStockQty#$totalRequest' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input id='partsEquipType#$totalRequest' name='partsEquipType#$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input id='partsModel#$totalRequest' name='partsModel#$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input type='text' name='prcPlateNo#$totalRequest' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td id='removeField' style='text-align: center'><img id='removeField$totalRequest' class='field-remove' src='images/minus2.jpg' onclick='removeField();'></td>";
				$msg .= "</tr>";
				//$msg .= "<tr id='addNewField'></tr>";
				echo $msg;
			}


/*
			if($totalRequest < 10) {
				$tmp = $totalRequest + 1;
				$msg .= "<tr id='row#$totalRequest'>";
				$msg .= "<td align='center'>$tmp</td>";
				$msg .= "<td align='center' id='select_1'><select name='partNo#$totalRequest' class='form-control input-sm' onchange='partDetails(this.value);' required>";
				if($rowsParts < 1) {
					// No part no were created.
					$msg .= "<option value=''>No Parts Found</option>";
				} else {
					// Found parts no lists.
					$msg .= "<option value='--#$totalRequest'>Select Part No</option>";
					for($i = 0; $i < $rowsParts; ++$i) {
						$partsNumber = mysql_result($resultParts, $i, 'partsNumber');
						$partsDescription = mysql_result($resultParts, $i, 'partsDescription');
						$msg .= "<option value='$partsNumber#$totalRequest'>$partsNumber\t ($partsDescription)</option>";
					}
				}
				$msg .= "</select></td>";
				$msg .= "<td align='center'><input id='partsDescription#$totalRequest' name='partsDescription#$totalRequest' type='text' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input type='text' class='form-control input-sm' name='prcQty#$totalRequest' style='text-align: center' required></td>";
				$msg .= "<td align='center'><input id='partsUom#$totalRequest' name='partsUom#$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input type='text' name='prcStockQty#$totalRequest' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input id='partsEquipType#$totalRequest' name='partsEquipType#$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input id='partsModel#$totalRequest' name='partsModel#$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input type='text' name='prcPlateNo#$totalRequest' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td id='removeField' style='text-align: center'><a href=''><img class='field-remove' src='images/minus2.jpg'></td>";
				$msg .= "</tr>";
				$msg .= "<tr id='addNewField'></tr>";
				echo $msg;
			}
*/




		}
	}
?>