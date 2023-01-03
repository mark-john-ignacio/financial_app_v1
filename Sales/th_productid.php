<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


 	$company = $_SESSION['companyid'];
	$avail = $_REQUEST['itmbal'];
	$date1 = date("Y-m-d");
	$c_id = $_REQUEST['c_id'];
	
	if($avail==1){
		$sql = "select  B.cpartno, B.citemdesc, B.cunit, 1 as nqty, B.cunit as qtyunit
		from invcount_t A 
		left join items B on A.compcode=B.compcode and A.citemno=B.cpartno
		where A.compcode='$company' and A.cscancode = '".$c_id."' and B.cstatus='ACTIVE'";
	}
	else{
		$sql = "select A.cpartno, A.citemdesc, A.cunit, B.cunit as qtyunit
		, (TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nqty)AS char)))) AS nqty
		from items A 
		left join 
			(
					select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit as cunit, X.citemno, X.nfactor
					From tblinventory X
					where X.compcode='$company' and X.dcutdate <= '$date1'
					Group by X.cmainunit, X.citemno
			) B on A.cpartno=B.citemno
		where A.compcode='$company' and A.cpartno = '".$c_id."' and A.cstatus='ACTIVE'";
	}

	//echo $sql;
	$rsd = mysqli_query($con,$sql);
	if(mysqli_num_rows($rsd)>=1){
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		
		//if($rs['nqty']>=1){
		
		if($rs['nqty']=="" || $rs['nqty']==NULL ){
			$json['nqty'] = 0;
		}
		else{
			$json['nqty'] = $rs['nqty'];
		}
		
			$json['id'] = $rs['cpartno'];
			$json['desc'] = $rs['citemdesc'];
			$json['cunit'] = $rs['cunit'];
			$json['cqtyunit'] = strtoupper($rs['qtyunit']);
			$json2[] = $json;
		
		//}
	
	}
	}
	else{
			$json['id'] = "";
			$json['desc'] = "";
			$json['cunit'] = "";
			$json['nqty'] = "";
			$json['cqtyunit'] = "";
			$json2[] = $json;

	}

echo json_encode($json2); 
?>
