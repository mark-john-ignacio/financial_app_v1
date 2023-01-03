<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}

$chkSales = mysqli_query($con,"select * from so_pick where compcode='$company' Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SP".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "SP".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SP".$dmonth.$dyear.$baseno;
	}
}


$ctranno = $cSINo;

$rr = $_REQUEST['rr'];
$cremarks = chkgrp($_REQUEST['crem']);
$daterec = $_REQUEST['ddate'];
$preparedby = $_SESSION['employeeid'];

//INSERT HEADER

	if (!mysqli_query($con,"INSERT INTO so_pick(`compcode`, `ctranno`, `crefno`, `cremarks`, `ddate`, `ddeldate`, `cpreparedby`) values('$company', '$ctranno', '$rr', $cremarks, NOW(), STR_TO_DATE('$daterec', '%m/%d/%Y'), '$preparedby')")){
		
		echo "False";
	}
	else{

	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$ctranno','$preparedby',NOW(),'INSERTED','PUTAWAY','$compname','Inserted New Record')");


// Delete previous details
		mysqli_query($con, "Delete from so_pick_t Where compcode='$company' and ctranno='$ctranno'");
		mysqli_query($con, "Delete from so_pick_items Where compcode='$company' and ctranno='$ctranno'");

		echo $ctranno;
	}
?>