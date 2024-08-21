<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$json2 = array();

	$arrOut = array();
	$sqlOUT = "select A.dcutdate, A.tblinvin_nidentity, A.citemno, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, SUM(A.ntotqty) as nqty
	from tblinvout A 
	where A.compcode='$company' and A.citemno='".$_REQUEST['x']."'
	Group BY A.dcutdate, tblinvin_nidentity, A.citemno, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation";
	$rsdout = mysqli_query($con,$sqlOUT);
	while($row = mysqli_fetch_array($rsdout, MYSQLI_ASSOC)){
		$arrOut[] = $row;
	}
				
	$sqlitm = "select A.nidentity, A.dcutdate, A.citemno, C.citemdesc, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, B.cdesc, SUM(A.ntotqty) as nqty
	from tblinvin A 
	left join mrp_locations B on A.compcode=B.compcode and A.nlocation=B.nid
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and A.citemno='".$_REQUEST['x']."'
	Group BY A.nidentity, A.dcutdate, A.citemno, C.citemdesc, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, B.cdesc
	Order by A.dcutdate ASC";
	
	$rsditm = mysqli_query($con,$sqlitm);
	if(mysqli_num_rows($rsditm)>=1){
		
		while($row = mysqli_fetch_array($rsditm, MYSQLI_ASSOC)){
			$qtyremain = $row['nqty'];

			foreach($arrOut as $rout){
				if($row['nidentity']==$rout['tblinvin_nidentity']){
					$qtyremain = floatval($row['nqty']) - floatval($rout['nqty']);
				}
			}

			if(floatval($qtyremain)>0){
				$json['nidentity'] = $row['nidentity'];
				$json['dcutdate'] = $row['dcutdate'];
				$json['citemno'] = $row['citemno'];
				$json['citemdesc'] = $row['citemdesc'];
				$json['cmainunit'] = $row['cmainunit'];
				$json['clotsno'] = $row['clotsno'];
				$json['cpacklist'] = $row['cpacklist'];
				$json['nlocation'] = $row['nlocation'];
				$json['cdesc'] = $row['cdesc'];
				$json['nqty'] = $qtyremain;
				
				$json2[] = $json;
			}

		}

	
	}


	echo json_encode($json2);


?>
