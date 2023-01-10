<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$id = $_REQUEST['c_id'];
	$cpybl = $_REQUEST['codez'];
	
	$sqldefacc = mysqli_query($con,"Select A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctno where A.compcode='$company' and A.cacctno='$id' and A.ccode='$cpybl'");
		if (mysqli_num_rows($sqldefacc)!=0) {
			echo "Payables";
		}else{
			echo "Others";
		}
	
	


?>
