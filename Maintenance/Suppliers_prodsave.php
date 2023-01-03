<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

function chkgrp($valz) {
	global $con;
	
	if($valz==''){
		return "NULL";
	}else{
    	return "'".mysqli_real_escape_string($con, $valz)."'";
	}
}

	$cCustCode = strtoupper($_REQUEST['id']);
	$company = $_SESSION['companyid'];
	
	$ItemCode = $_REQUEST['itm'];
	$Remarks = chkgrp($_REQUEST['rem']);
	
	$preparedby = $_SESSION['employeeid'];


			$sql = "INSERT INTO `items_suppliers` (`compcode`, `cpartno`, `ccode`, `cremarks`) VALUES ('$company', '$ItemCode', '$cCustCode', $Remarks)";	
		
		
			if (!mysqli_query($con, $sql)) {
				if(mysqli_error($con)!=""){
					echo "Error Product: ".mysqli_error($con);
				}
			}
							
							
			//INSERT LOGFILE
			$compname = php_uname('n');
			
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company', '$cCustCode','$preparedby',NOW(),'INSERTED','SUPPLIERS ITEM','$compname','Insert New Supplier Product')");
			
			echo "True";



?>