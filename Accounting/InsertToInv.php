<?php
//session_start();
include('../Connection/connection_string.php');
$compcode = $_SESSION['companyid']; ;

function ToInv($ctranno,$typ,$INOUT,$dcut){
	//get Item entry
	
	global $con;
	global $compcode;
	
	//IN
	mysqli_query($con,"DELETE FROM `tblinventory` where `ctranno` = '$ctranno'");
	 
	if($INOUT=="IN"){
		mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '001', '$ctranno', NOW(),'$dcut','$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, A.ncost, A.nretail, 0, 0, 0 From receive_t A where A.ctranno='$ctranno'");
	}
	
	else if($INOUT=="OUT"){
		mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '001', '$ctranno', NOW(),'$dcut','$typ', A.citemno, A.cunit, A.nqty, A.cunit,1, 0, 0, 0, A.nqty, A.ncost, A.nprice From sales_t A where A.csalesno='$ctranno'");
	}
	
	

}
?>
