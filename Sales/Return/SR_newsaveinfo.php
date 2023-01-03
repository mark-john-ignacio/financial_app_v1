<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

		$cSINo = $_REQUEST['trancode'];
		$indexz = $_REQUEST['indx'];
		
		$cinfocode = $_REQUEST['citmno'];
		$cinfofld = $_REQUEST['citmfld'];
		$cinfovlue = $_REQUEST['citmvlz'];
		
		$indexz = $indexz + 1;

		if (!mysqli_query($con,"INSERT INTO salesreturn_t_info(`compcode`, `ctranno`, `nident`, `citemno`, `cfldnme`, `cvalue`) values('$company', '$cSINo', '$z2', '$cinfocode', '$cinfofld', '$cinfovlue')")){
			echo "Errormessage: %s\n", mysqli_error($con);
		}
		else{
			echo "True";
		}
	

?>
