<?php
	//require_once('db/db_config.php');

	if(!empty($_POST['totalRequest'])) {
		$totalRequest = mysql_escape_string($_POST['totalRequest']);
		if($totalRequest < 10) {
			$tmp = $totalRequest + 1;
			$msg .= "<tr id='row$totalRequest'>";
			$msg .= "<td align='center'>$tmp</td>";
			$msg .= "<td align='center' id='select_1'><select name='partNo$totalRequest' class='form-control input-sm' onchange='partDetails(this.value);' required>";
			$msg .= "<option value='--$totalRequest'>Select Part No</option>";
			$handle = fopen("partsDetails.csv", "r");
			if ($handle !== FALSE) {
			    //$row = 0;
			    while(($data = fgetcsv($handle, 100, ",")) !== FALSE ) {
			        //printf('<option value="%s">%s</option>', $data[0], $data[1]);
			        //$newPartsNumber = $data[0] . "0";
			        $msg .= "<option value='$data[0]$totalRequest'>$data[0]\t ($data[1])</option>";
			    }
			    fclose($handle);
			}
			$msg .= "</select></td>";
			$msg .= "<td align='center'><input id='partsDescription$totalRequest' name='partsDescription$totalRequest' type='text' class='form-control input-sm'></td>";
			$msg .= "<td align='center'><input id='prcQty$totalRequest' name='prcQty$totalRequest' type='text' class='form-control input-sm' style='text-align: center' required></td>";
			$msg .= "<td align='center'><input id='partsUom$totalRequest' name='partsUom$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
			$msg .= "<td align='center'><input id='prcStockQty$totalRequest' name='prcStockQty$totalRequest' type='text' class='form-control input-sm'></td>";
			$msg .= "<td align='center'><input id='partsEquipType$totalRequest' name='partsEquipType$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
			$msg .= "<td align='center'><input id='partsModel$totalRequest' name='partsModel$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
			$msg .= "<td align='center'><input id='prcEquipNo$totalRequest' name='prcEquipNo$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
			$msg .= "<td id='removeField$totalRequest' style='text-align: center'><img id='removeFieldFunc$totalRequest' class='field-remove' src='images/minus2.jpg' onclick='removeRow();'></td>";
			$msg .= "</tr>";
			$msg .= "<tr id='addNewField'></tr>";
			echo $msg;
		}
	}
	/*
	$dbSelected = mysql_select_db($dbName) or die("Unable to select database: " . mysql_error());
	if($dbSelected) {
		// Select part no
		$queryParts = "SELECT partsNumber, partsDescription FROM partsMasterFile WHERE status = 'Active' ORDER BY partsNumber ASC";
		$resultParts = mysql_query($queryParts);
		$rowsParts = mysql_num_rows($resultParts);
		if(!$resultParts) die ("Table access failed: " . mysql_error());
		// add new field
		if(!empty($_POST['totalRequest'])) {
			$totalRequest = mysql_escape_string($_POST['totalRequest']);
			if($totalRequest < 10) {
				$tmp = $totalRequest + 1;
				$msg .= "<tr id='row$totalRequest'>";
				$msg .= "<td align='center'>$tmp</td>";
				$msg .= "<td align='center' id='select_1'><select name='partNo$totalRequest' class='form-control input-sm' onchange='partDetails(this.value);' required>";
				if($rowsParts < 1) {
					// No part no were created.
					$msg .= "<option value=''>No Parts Found</option>";
				} else {
					// Found parts no lists.
					$msg .= "<option value='--$totalRequest'>Select Part No</option>";
					for($i = 0; $i < $rowsParts; ++$i) {
						$partsNumber = mysql_result($resultParts, $i, 'partsNumber');
						$partsDescription = mysql_result($resultParts, $i, 'partsDescription');
						$msg .= "<option value='$partsNumber$totalRequest'>$partsNumber\t ($partsDescription)</option>";
					}
				}
				$msg .= "</select></td>";
				$msg .= "<td align='center'><input id='partsDescription$totalRequest' name='partsDescription$totalRequest' type='text' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input id='prcQty$totalRequest' name='prcQty$totalRequest' type='text' class='form-control input-sm' style='text-align: center' required></td>";
				$msg .= "<td align='center'><input id='partsUom$totalRequest' name='partsUom$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input id='prcStockQty$totalRequest' name='prcStockQty$totalRequest' type='text' class='form-control input-sm'></td>";
				$msg .= "<td align='center'><input id='partsEquipType$totalRequest' name='partsEquipType$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input id='partsModel$totalRequest' name='partsModel$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td align='center'><input id='prcEquipNo$totalRequest' name='prcEquipNo$totalRequest' type='text' class='form-control input-sm' style='text-align: center'></td>";
				$msg .= "<td id='removeField$totalRequest' style='text-align: center'><img id='removeFieldFunc$totalRequest' class='field-remove' src='images/minus2.jpg' onclick='removeRow();'></td>";
				$msg .= "</tr>";
				$msg .= "<tr id='addNewField'></tr>";
				echo $msg;
			}
		}
	}
	*/
?>