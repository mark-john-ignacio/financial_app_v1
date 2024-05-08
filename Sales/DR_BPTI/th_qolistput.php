<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$avail = $_REQUEST['itmbal'];
	$date1 = date("Y-m-d");

	//get all so
	$resq = mysqli_query ($con, "Select A.nqty, A.cmainunit, A.cunit, A.nfactor, A.nprice, A.nbaseamount, A.namount, A.ctranno, A.nident, IFNULL(A.ditempono, '') as ditempono, B.cvattype, C.cpartno, IFNULL(C.cskucode,'') as cskucode, C.citemdesc, IFNULL(C.cnotes,'') as cnotes From so_t A left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno left join items C on A.compcode=C.compcode and A.citemno=C.cpartno  where A.compcode='$company' and A.ctranno = '".$_REQUEST['id']."' and A.nident = '".$_REQUEST['itm']."'");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	//get all existing dr
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, crefident, citemno, sum(nqty) as nqty From dr_t a left join dr b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, crefident,citemno");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

	if($avail==0){
		//get items inventory
		@$arrinventiry = array();
		$resinv = mysqli_query ($con, "select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit, X.citemno, X.nfactor From tblinventory X where X.compcode='$company' and X.dcutdate <= '$date1' Group by X.cmainunit, X.citemno");
		if (mysqli_num_rows($resinv)!=0){
			while($rowinv = mysqli_fetch_array($resinv, MYSQLI_ASSOC)){
				@$arrinventiry[]=$rowinv;
			}
		}
	}

	$json2 = array();
	foreach($arrresq as $row){

		//Serach item if existing in DR
		$inarray = "No";
		$nqty2 = 0;
		foreach(@$arrinv as $rsibnv){
			if($row['ctranno']==$rsibnv['creference']){
				if($row['citemno']==$rsibnv['citemno'] && $row['nident']==$rsibnv['crefident']){
					$nqty2 = $rsibnv['nqty']; 
				}
			}
		}

		//if for inventory cheking, search sa inventory if exist
		$availinvtory = 1;
		if($avail==0){
			foreach(@$arrinventiry as $rxinv){
				if($row['citemno']==$rxinv['citemno']){
					$availinvtory = $rxinv['nqty']; 
				}
			}
		}

		$nqty1 = $row['nqty'];
		$xremain = $nqty1 - $nqty2;
		if($xremain>0){
			$json['id'] = $row['cpartno'];
			$json['desc'] = $row['citemdesc'];
			$json['cpartname'] = $row['cnotes'];
			$json['nqty'] = $row['nqty'];
			$json['totqty'] = $nqty1 - $nqty2;
			$json['cqtyunit'] = $row['cmainunit'];
			$json['cunit'] = $row['cunit'];
			$json['nfactor'] = $row['nfactor'];
			$json['nprice'] = $row['nprice'];
			$json['nbaseamount'] = $row['nbaseamount'];
			$json['namount'] = $row['namount'];
			$json['xref'] = $row['ctranno'];
			$json['xrefident'] = $row['nident'];

			$json['xcskucode'] = $row['cskucode'];
			$json['xcpono'] = $row['ditempono'];
			$json2[] = $json;
		}

	}
		

	echo json_encode($json2);


?>