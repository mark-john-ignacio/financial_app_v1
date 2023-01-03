<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = $_REQUEST["mo"];
$dyear = $_REQUEST["yr"];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];


$chkSales = mysqli_query($con,"select * from invcount where compcode='$company' and YEAR(ddatetime) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "IC".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "IC".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "IC".$dmonth.$dyear.$baseno;
	}
}
	
	//INSERT HEADER

	if (!mysqli_query($con, "INSERT INTO invcount(`compcode`, `ctranno`, `dmonth`, `dyear`, `ddatetime`, `cpreparedby`) values('$company', '$cSINo', '$dmonth', '$dyear', NOW(), '$preparedby')")) {
		echo "False";
		//echo mysqli_error($con);
	} 
	else {
		echo $cSINo;
	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','INVENTORY COUNT','$compname','Inserted New Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from invcount_t Where compcode='$company' and ctranno='$cSINo'");

?>