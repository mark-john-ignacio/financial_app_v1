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
		$sql = "select  A.cpartno, A.citemdesc, A.cunit, 1 as nqty, A.cunit as qtyunit, A.ctype, A.ctaxcode, B.cacctno, B.cacctid, B.cacctdesc, C.cgroupdesc
		from items A 
		left join accounts B on A.compcode=B.compcode and A.cacctcodesales=B.cacctno
		left join items_groups C on A.compcode=C.compcode and A.cGroup1=C.ccode and C.cgroupno='cGroup1'
		where A.compcode='$company' and A.ctradetype='Trade' and (LOWER(A.citemdesc) LIKE '%".strtolower($_GET['query'])."%' OR LOWER(A.cpartno) LIKE '%".strtolower($_GET['query'])."%') and A.cstatus='ACTIVE' and A.csalestype='".$styp."'";
	}
	else{ //B.cunit as qtyunit , (TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nqty)AS char)))) AS nqty
		$sql = "select A.cpartno, A.citemdesc, A.cunit, ifnull(B.cunit,'') as qtyunit, ifnull(B.nqty,0) as nqty, A.ctype, A.ctaxcode, C.cacctno, C.cacctid, C.cacctdesc, D.cgroupdesc
		from items A 
		left join 
			(
					select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit as cunit, X.citemno, X.nfactor
					From tblinventory X
					where X.compcode='$company' and X.dcutdate <= '$date1'
					Group by X.cmainunit, X.citemno
			) B on A.cpartno=B.citemno
		left join accounts C on A.compcode=C.compcode and A.cacctcodesales=C.cacctno
		left join items_groups D on A.compcode=D.compcode and A.cGroup1=D.ccode and D.cgroupno='cGroup1'
		where A.compcode='$company' and A.ctradetype='Trade' and (LOWER(A.citemdesc) LIKE '%".strtolower($_GET['query'])."%' OR LOWER(A.cpartno) LIKE '%".strtolower($_GET['query'])."%') and A.cstatus='ACTIVE' and A.csalestype='".$styp."'";
	}
	
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
			$json['cacctno'] = $rs['cacctno'];
			$json['cacctid'] = $rs['cacctid'];
			$json['cacctdesc'] = $rs['cacctdesc'];
			$json['makebuy'] = $rs['cgroupdesc'];
			$json2[] = $json;
		
	//	}
	
	}


echo json_encode($json2);
//echo $sql;

?>
