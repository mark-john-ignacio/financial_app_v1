<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');	
	$preparedby = $_SESSION['employeeid'];
	
	$cSINo = $_REQUEST['tran'];
	$company = $_SESSION['companyid'];
	$deffect = $_REQUEST['deffect'];
	$descrip = $_REQUEST['desc'];
	$code = $_REQUEST['ccode'];
	
	
//Update Header	
	if($descrip==""){
		$descrip = "For ".date_format(date_create($deffect), "F j\, Y")." Effectivity";
	}

			if (!mysqli_query($con,"UPDATE items_purch_cost set ccode='$code', `cremarks`='$descrip', `deffectdate`=STR_TO_DATE('$deffect', '%m/%d/%Y')  where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
				echo "False";
			} 
			else{
				echo $cSINo;	
			}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','PURCHASE PRICELIST','$compname','Update Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from items_purch_cost_t Where compcode='$company' and ctranno='$cSINo'");

?>
