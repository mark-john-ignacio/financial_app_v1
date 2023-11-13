<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


 	$company = $_SESSION['companyid'];
	$c_id = $_REQUEST['c_id'];
	
		$sql = "select  A.cpartno, A.citemdesc, A.cunit, A.cskucode, B.cacctno, B.cacctid, B.cacctdesc
		from items A
		left join accounts B on A.compcode=B.compcode and A.cacctcodewrr=B.cacctno
		where A.compcode='$company' and (A.cpartno = '".$c_id."' OR A.cskucode = '".$c_id."') and A.cstatus='ACTIVE' and A.csalestype='Services'";

	//echo $sql;
	$rsd = mysqli_query($con,$sql);
	if(mysqli_num_rows($rsd)>=1){
		while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		 
		 $c_prodid = $rs['cpartno'];
		 $c_cskuid = $rs['cskucode'];
		 $c_prodnme = $rs['citemdesc']; 
		 $c_unit = $rs['cunit']; 
		 $cacctno = $rs['cacctno'];
		 $cacctid = $rs['cacctid'];
		 $cacctdesc = $rs['cacctdesc'];
		}
		
		echo $c_prodid.",".$c_prodnme.",".$c_unit.",".$c_cskuid.",".$cacctno.",".$cacctid.",".$cacctdesc;
	} 
	
	else {
		echo "";
	}
	
	 
	 exit();  
 
?>
