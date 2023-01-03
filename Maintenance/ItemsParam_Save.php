<?php
if(!isset($_SESSION)){
session_start();
}

include('../Connection/connection_string.php');
$company = $_SESSION['companyid'];
$typ = $_REQUEST['txtccode'];
$cnt = $_REQUEST['txtcnt'];
//Delete MUNA

mysqli_query($con,"Delete From groupings Where compcode='$company' and ctype='$typ'");

//Insert
for ($x = 1; $x <= $cnt; $x++) {

$norder = $_REQUEST['nord'.$x];
$cvalue = $_REQUEST['nvalz'.$x];

mysqli_query($con,"insert into groupings(compcode,ccode,cdesc,ctype) 
	values('$company','$norder','$cvalue','$typ')");
	
}

header("Location: ItemsParam.php?x=".$typ."&msg=SAVED!");
die();
?>
