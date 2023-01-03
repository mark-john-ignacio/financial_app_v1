<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$itm = $_REQUEST['code'];
	$uom = $_REQUEST['uom'];
	$val = $_REQUEST['val'];
	$code = $_REQUEST['tran'];
	
	$nident = $_REQUEST['ident'];
			
//Insert Header
	$refcidenttran = $code."P".$nident;
	
	if (!mysqli_query($con,"INSERT INTO items_pm_t (`compcode`,`cidentity`,`ctranno`,`citemno`,`cunit`,`nprice`, `nident`) values ('$company','$refcidenttran','$code','$itm','$uom',$val,$nident)")) {
		echo "Errormessage: ". mysqli_error($con);
	} 
	else{
		echo "True";
	}

?>
