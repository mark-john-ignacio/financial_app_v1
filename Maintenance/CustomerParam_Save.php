<?php
if(!isset($_SESSION)){
session_start();
}

include('../Connection/connection_string.php');
$company = $_SESSION['companyid'];
$typ = $_REQUEST['txtccode'];
$cnt = $_REQUEST['txtcnt'];
//Delete MUNA

mysqli_query($con,"Delete From parameters Where compcode='$company' and ccode='$typ'");

//Insert
for ($x = 1; $x <= $cnt; $x++) {

$norder = $_REQUEST['nord'.$x];
$cvalue = $_REQUEST['nvalz'.$x];

mysqli_query($con,"insert into parameters(compcode,ccode,cvalue,norder) 
	values('$company','$typ','$cvalue','$norder')");
	
}

header("Location: CustomerParam.php?x=".$typ."&msg=SAVED!");
die();
?>
