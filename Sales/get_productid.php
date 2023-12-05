<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


 	$company = $_SESSION['companyid'];
	$avail = $_REQUEST['itmbal'];
	$date1 = date("Y-m-d");
	$styp = $_REQUEST['styp'];
	$c_id = $_REQUEST['c_id'];
	
	if($avail==1){
		$sql = "select  A.cpartno, A.citemdesc, A.cunit, 1 as nqty, A.cunit as qtyunit, A.ctype, A.ctaxcode, B.cacctno, B.cacctid, B.cacctdesc, IFNULL(C.cgroupdesc,'') as cgroupdesc, IFNULL(A.cskucode,'') as cskucode, IFNULL(A.cnotes,'') as cnotes
		from items A 
		left join accounts B on A.compcode=B.compcode and A.cacctcodesales=B.cacctno
		left join items_groups C on A.compcode=C.compcode and A.cGroup1=C.ccode and C.cgroupno='cGroup1'
		where A.compcode='$company' and A.ctradetype='Trade' and A.cpartno = '".$c_id."' and A.cstatus='ACTIVE' and A.csalestype='".$styp."'";
	}
	else{
		$sql = "select A.cpartno, A.citemdesc, A.cunit, B.cunit as qtyunit, A.ctype, A.ctaxcode, C.cacctno, C.cacctid, C.cacctdesc
		, (TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nqty)AS char)))) AS nqty, IFNULL(D.cgroupdesc,'') as cgroupdesc, IFNULL(A.cskucode,'') as cskucode, IFNULL(A.cnotes,'') as cnotes
		from items A 
		left join 
			(
				select COALESCE((Sum(nqtyin*nfactor)-sum(nqtyout*nfactor)),0) as nqty, X.cunit, X.citemno, X.nfactor
				From tblinventory X
				where X.compcode='$company' and X.dcutdate <= '$date1'
				Group by X.cunit, X.citemno
			) B on A.cpartno=B.citemno
		left join accounts C on A.compcode=C.compcode and A.cacctcodesales=C.cacctno
		left join items_groups D on A.compcode=D.compcode and A.cGroup1=D.ccode and D.cgroupno='cGroup1'
		where A.compcode='$company' and A.ctradetype='Trade' and A.cpartno = '".$c_id."' and A.cstatus='ACTIVE' and A.csalestype='".$styp."'";
	}

	//echo $sql;
	$rsd = mysqli_query($con,$sql);
	if(mysqli_num_rows($rsd)>=1){
		while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		 
		 	$c_prodid = $rs['cpartno'];

			if(isset($_REQUEST['cdoc'])){
				if($_REQUEST['cdoc']=="Doc2"){
					$c_skucode = "";
					$c_prodnme = ($rs['cskucode']!="") ? $rs['cskucode'] : $rs['citemdesc'];
				}else{
					$c_skucode = $rs['cskucode'];
					$c_prodnme = ($rs['cnotes']!="") ? $rs['cnotes'] : $rs['citemdesc'];
				}
			}else{				
				$c_skucode = $rs['cskucode'];
				$c_prodnme = $rs['citemdesc'];
			}

		 	$c_unit = $rs['cunit']; 
		 	$c_nqty = $rs['nqty'];
		 	$c_qtyunit = strtoupper($rs['qtyunit']);
		 	$c_typ = strtoupper($rs['ctype']);
		 	$c_taxcode = strtoupper($rs['ctaxcode']);	
		 	$cacctno = $rs['cacctno'];
			$cacctid = $rs['cacctid'];
			$cacctdesc = $rs['cacctdesc'];	 
			$makebuy = $rs['cgroupdesc'];	  
		}
		
		echo $c_prodid.",".$c_prodnme.",".$c_unit.",".$c_nqty.",".$c_qtyunit.",".$c_typ.",".$c_taxcode.",".$cacctno.",".$cacctid.",".$cacctdesc.",".$makebuy.",".$c_skucode;
	} 
	
	else {
		echo "";
	}
	
	 
	 exit();  
 
?>
