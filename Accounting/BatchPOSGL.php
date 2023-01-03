<?php
//if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
$compcode = $_SESSION['companyid'];

function WRREntry($cwrrno){
	//get Item entry
	global $con;
	global $compcode;
	
	mysqli_query($con,"DELETE FROM `glactivity` where `ctranno` = '$cwrrno'");
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','WRR','$cwrrno',B.dcutdate,A.cacctcode,C.cacctdesc,SUM(A.namount),0,0,NOW() From receive_t A left join receive B on A.ctranno=B.ctranno left join accounts C on A.cacctcode=C.cacctno where A.ctranno='$cwrrno' group by B.dcutdate,A.cacctcode,C.cacctdesc");
	

	//get Supplier Entry
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','WRR','$cwrrno',A.dcutdate,A.ccustacctcode,B.cacctdesc,0,A.ngross,0,NOW() From receive A left join accounts B on A.ccustacctcode=B.cacctno where A.ctranno='$cwrrno'");

}


function SIEntry($cposno){
	//get Item entry
	global $con;
	global $compcode;
	
	mysqli_query($con,"DELETE FROM `glactivity` where `ctranno` = '$cposno'");
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','POS','$cposno',B.dcutdate,A.cacctcode,C.cacctdesc,0,SUM(A.namount),0,NOW() From sales_t A left join sales B on A.csalesno=B.csalesno left join accounts C on A.cacctcode=C.cacctno where A.csalesno='$cposno' group by B.dcutdate,A.cacctcode,C.cacctdesc");
	

	//get Customer Entry
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','POS','$cposno',A.dcutdate,A.ccustacctcode,B.cacctdesc,A.ngross,0,0,NOW() From sales A left join accounts B on A.ccustacctcode=B.cacctno where A.csalesno='$cposno'");

}

?>
