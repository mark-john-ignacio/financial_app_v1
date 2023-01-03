<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	
	$sql = "select A.*, B.cname, ifnull(C.ctranno,'') as cref, A.dcutdate from SO A left join Customers B on A.ccode=B.cempid left join so_pick C on A.ctranno=C.crefno and C.lcancelled=0 where A.compcode='$company' and A.lapproved=1 Order By A.dcutdate DESC, A.ctranno DESC";

	$rsd = mysqli_query($con,$sql);
	$cntx = 0;
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {

		if($rs['cref']==""){
		$cntx = $cntx +1;

		$json['id'] = $rs['ctranno'];
		$json['cname'] = $rs['cname'];
		$json['ddate'] = $rs['dcutdate'];

		$json2[] = $json;
		}
	
	}


if($cntx>0){
	echo json_encode($json2);
}
//echo $sql;

?>
