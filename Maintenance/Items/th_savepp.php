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
	$code = $_REQUEST['ccode'];
	
	$dmonth = date("m");
	$dyear = date("y");

		
	$result = mysqli_query ($con, "SELECT * FROM items_purch_cost where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1"); 
	
	
	//echo $row['prefx'];
	
	if(mysqli_num_rows($result)==0){
		$cSINo = "PP".$dmonth.$dyear."00000";
	}
	else {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		
		if(substr($lastSI,2,2) <> $dmonth){
			$cSINo = "PP".$dmonth.$dyear."00000";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "PP".$dmonth.$dyear.$baseno;
		}
	}
	
	
//Insert Header	
	if($descrip==""){
		$descrip = "For ".date_format(date_create($deffect), "F j\, Y")." Effectivity";
	}

			if (!mysqli_query($con,"INSERT INTO items_purch_cost (`compcode`,`ctranno`,`ccode`,`cremarks`,`ddate`,`deffectdate`) values ('$company','$cSINo','$code','$descrip',NOW(),STR_TO_DATE('$deffect', '%m/%d/%Y'))")) {
				echo "False";
			} 
			else{
				echo $cSINo;	
			}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PURCHASE PRICELIST','$compname','Inserted New Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from items_purch_cost_t Where compcode='$company' and ctranno='$cSINo'");

?>
