<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$deffect = $_REQUEST['deffect'];
	$descrip = $_REQUEST['desc'];
	$typ = $_REQUEST['typ'];	
	$code = $_REQUEST['tran'];
		
//Update Header	
	if($descrip==""){
		$descrip = "For ".date_format(date_create($deffect), "F j\, Y")." Effectivity";
	}

			if (!mysqli_query($con,"UPDATE items_pm set `cremarks` = '$descrip', `deffectdate` = STR_TO_DATE('$deffect', '%m/%d/%Y') where `compcode` = '$company' and `ctranno` = '$code' and `cversion` = '$typ'")) {
				echo "False";
			} 
			else{
					

				if (!mysqli_query($con,"DELETE FROM items_pm_t where `compcode` = '$company' and `ctranno` = '$code'")) {
					echo "False";
				}
				else{
					echo $code;
				}

			}



?>
