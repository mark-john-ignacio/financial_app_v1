<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	
	$sql = "select A.*, B.cacctno, B.cacctid, B.cacctdesc from items A left join accounts B on A.compcode=B.compcode and A.cacctcodewrr=B.cacctno where A.compcode='$company' and A.citemdesc LIKE '%".$_REQUEST['query']."%' and A.cstatus='ACTIVE'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$json['id'] = $rs['cpartno'];
		$json['cname'] = $rs['citemdesc'];
		$json['cunit'] = $rs['cunit'];
		$json['cskucode'] = $rs['cskucode'];
		$json['cacctno'] = $rs['cacctno'];
		$json['cacctid'] = $rs['cacctid'];
		$json['cacctdesc'] = $rs['cacctdesc'];
		$json2[] = $json;
	
	}


echo json_encode($json2);
//echo $sql;

?>
