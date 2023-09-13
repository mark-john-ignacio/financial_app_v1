<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$sql = "select  A.cpartno, A.citemdesc, A.cunit, 0 as nident, 1 as nqty, IFNULL(B.nworkhrs,0) as nworkhrs, IFNULL(B.nsetuptime,0) as nsetuptime, IFNULL(B.ncycletime,0) as ncycletime
		from items A 
		left join mrp_items_parameters B on A.compcode=B.compcode and A.cpartno=B.citemno
		where A.compcode='$company' and A.ctradetype='Trade' and (LOWER(A.citemdesc) LIKE '%".strtolower($_GET['query'])."%' OR LOWER(A.cpartno) LIKE '%".strtolower($_GET['query'])."%') and A.cstatus='ACTIVE' and A.csalestype='Goods'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		
		//if($rs['nqty']>=1){
			
			$json['nident'] = $rs['nident'];
			$json['id'] = $rs['cpartno'];
			$json['desc'] = $rs['citemdesc'];
			$json['cunit'] = $rs['cunit'];
			$json['nqty'] = number_format($rs['nqty'],2);
			$json['nworkhrs'] = number_format($rs['nworkhrs'],2);
			$json['nsetuptime'] = number_format($rs['nsetuptime'],2);
			$json['ncycletime'] = number_format($rs['ncycletime'],2);
		
			$json2[] = $json;
		
	//	}
	
	}


echo json_encode($json2);
//echo $sql;

?>
