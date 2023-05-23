<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		//items
		$arritm = array();
		$result = mysqli_query ($con, "select * from items WHERE compcode='$company'"); 
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arritmdsc[$row['cpartno']] = $row['citemdesc'];
		}

		//rritems
		$rrdetails = array();
		$ponos = array();
		$resrr = mysqli_query ($con, "select * from receive_t WHERE compcode='$company' and ctranno = '".$_REQUEST['id']."'"); 
		while($rowrr = mysqli_fetch_array($resrr, MYSQLI_ASSOC)){
			$rrdetails[] = $rowrr;
			$ponos[] = $rowrr['creference'];
		}

		//poitems
		$podetails = array();
		$resrr = mysqli_query ($con, "select A.*, B.ladvancepay from purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono WHERE A.compcode='$company' and A.cpono in ('".implode("','", $ponos)."')"); 
		while($rowrr = mysqli_fetch_array($resrr, MYSQLI_ASSOC)){
			$podetails[] = $rowrr;
		}

		//existing suppliers invoices
		$invdetails = array();
		$resinv = mysqli_query ($con, "select a.* from suppinv_t a left join suppinv b on a.compcode=b.compcode and a.ctranno=b.ctranno WHERE a.compcode='$company' and a.creference = '".$_REQUEST['id']."' and b.lcancelled=0"); 
		while($rowinv = mysqli_fetch_array($resinv, MYSQLI_ASSOC)){
			$invdetails[] = $rowinv;
		}

	$json2 = array();
	foreach($rrdetails as $row){
		$nqty1 = $row['nqty'];

			//get qty ng suppinv if meron
			if(count($invdetails)!=0){
				$nqty2 = 0;
				foreach($invdetails as $rowinvs){
					if($row['citemno']==$rowinvs['citemno'] && $row['nident']==$rowinvs['nrefidentity']){
						$nqty2 = $nqty2 + floatval($rowinvs['nqty']);
					}
				}
			}else{
				$nqty2 = 0;
			}	

			$totqty = $nqty1 - $nqty2;

		if($totqty>0){
			$json['totqty'] = $totqty;

			$json['id'] = $row['citemno'];
			$json['desc'] = $arritmdsc[$row['citemno']];
			$json['nqty'] = $row['nqty'];		
			$json['cunit'] = $row['cunit'];
			$json['cmainunit'] = $row['cmainunit'];
			$json['nfactor'] = $row['nfactor'];	
			$json['xref'] = $row['ctranno'];
			$json['xrefident'] = $row['nident'];

			$json['xrefPO'] = $row['creference'];
			$json['xrefidentPO'] = $row['nrefidentity'];

			foreach($podetails as $rowpo){ // find price sa PO
				if($row['citemno']==$rowpo['citemno'] && $row['nrefidentity']==$rowpo['nident'] && $row['creference']==$rowpo['cpono']){
					$json['nprice'] = $rowpo['nprice'];
					$json['namount'] = $rowpo['namount'];	
					$json['nbaseamount'] = $rowpo['nbaseamount'];	
					$json['ctaxcode'] = $rowpo['ctaxcode'];	
					$json['cewtcode'] = $rowpo['cewtcode'];
					$json['ladvancepay'] = $rowpo['ladvancepay'];
				}
			}
		
		$json2[] = $json;
		}
	
	}


	echo json_encode($json2);


?>
