<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$avail = $_REQUEST['itmbal'];
	$styp = $_REQUEST['styp'];
	$date1 = date("Y-m-d");
	
	if($avail==1){
		$sql = "select  A.cpartno, A.citemdesc, A.cunit, 1 as nqty, A.cunit as qtyunit, A.ctype, A.ctaxcode
		from items A 
		where A.compcode='$company' and A.ctradetype='Trade' and A.citemdesc LIKE '%".$_GET['query']."%' and A.cstatus='ACTIVE' and A.csalestype='".$styp."'";
	}
	else{ //B.cunit as qtyunit , (TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nqty)AS char)))) AS nqty
		$sql = "select A.cpartno, A.citemdesc, A.cunit, ifnull(B.cunit,'') as qtyunit, ifnull(B.nqty,0) as nqty, A.ctype, A.ctaxcode
		from items A 
		left join 
			(
					select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit as cunit, X.citemno, X.nfactor
					From tblinventory X
					where X.compcode='$company' and X.dcutdate <= '$date1'
					Group by X.cmainunit, X.citemno
			) B on A.cpartno=B.citemno
		where A.compcode='$company' and A.ctradetype='Trade' and A.citemdesc LIKE '%".$_GET['query']."%' and A.cstatus='ACTIVE' and A.csalestype='".$styp."'";
	}
	//echo $sql;
	
	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		
		//if($rs['nqty']>=1){
			
			$json['id'] = $rs['cpartno'];
			$json['desc'] = $rs['citemdesc'];
			$json['cunit'] = $rs['cunit'];
			$json['citmcls'] = $rs['ctype'];
			$json['nqty'] = $rs['nqty'];
			$json['cqtyunit'] = strtoupper($rs['qtyunit']); 
			$json['ctaxcode'] = $rs['ctaxcode'];
			$json2[] = $json;
		
	//	}
	
	}


echo json_encode($json2);
//echo $sql;

?>
