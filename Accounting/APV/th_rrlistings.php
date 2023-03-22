<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if($_REQUEST['y']!=""){
		$qry = "and A.ctranno not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}

	if($_REQUEST['typ']!="PettyCash"){

		$qrycust = "and A.ccode='".$_REQUEST['cust']."'";
	}
	else{

		$qrycust = "";
	}

	@$refrrlistMAIN = array();
	$resrefrr = mysqli_query($con, "Select DISTINCT ctranno, IFNULL(creference,'') as creference from suppinv_t where compcode='$company' Group By ctranno");
	if(mysqli_num_rows($resrefrr)!=0){
		while($rowrrref = mysqli_fetch_array($resrefrr, MYSQLI_ASSOC)){
			@$refrrlistMAIN[] = $rowrrref;
		}
	}

	@$refpolistMAIN = array();
	$resrefpo = mysqli_query($con, "Select ctranno, ifnull(creference,'') as creference from receive_t where compcode='$company'");
	if(mysqli_num_rows($resrefpo)!=0){
		while($rowporef = mysqli_fetch_array($resrefpo, MYSQLI_ASSOC)){
			@$refpolistMAIN[] = $rowporef;
		}
	}

	@$refPOTYPMAIN = array();
	$resrefpo = mysqli_query($con, "Select cpono, ladvancepay from purchase where compcode='$company'");
	if(mysqli_num_rows($resrefpo)!=0){
		while($rowporef = mysqli_fetch_array($resrefpo, MYSQLI_ASSOC)){
			@$refPOTYPMAIN[] = $rowporef;
		}
	}

	@$refpaylistMAIN = array();
	$resrefpay = mysqli_query($con, "Select capvno, cacctno, sum(napplied) as napplied from paybill_t where compcode='$company' Group By cacctno, capvno");
	if(mysqli_num_rows($resrefpay)!=0){
		while($rowpayref = mysqli_fetch_array($resrefpay, MYSQLI_ASSOC)){
			@$refpaylistMAIN[] = $rowpayref; 
		}
	}
	
	$arrRRLISTING = array();
	$qryres = "select A.*, B.cacctid, B.cacctdesc, C.cname, IFNULL(D.napplied,0) as napplied, IFNULL(D.newtamt,0) as newtamt, C.cvattype, C.nvatrate, E.lcompute,ifnull(A.crefsi,'') as crefsi from suppinv A left join accounts B on A.compcode=B.compcode and A.ccustacctcode=B.cacctno left join suppliers C on A.compcode=C.compcode and A.ccode=C.ccode left join vatcode E on C.compcode=E.compcode and C.cvattype=E.cvatcode left join (Select A.crefno, sum(A.napplied) as napplied, sum(A.newtamt) as newtamt from apv_d A left join apv B on A.ctranno=B.ctranno Where A.compcode='$company' and B.lcancelled=0 Group By A.crefno) as D on A.ctranno=D.crefno where A.compcode='$company' ".$qrycust." and A.lapproved=1 ".$qry;
	$result = mysqli_query($con, $qryres); 
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arrRRLISTING[] = $row;
		}
	}

	$cntr = 0;
		foreach($arrRRLISTING as $row){

			//get reference RR
			@$refrrlist = array();
			foreach(@$refrrlistMAIN as $rowrrref){
				if($rowrrref['ctranno']==$row['ctranno'] && $rowrrref['creference']!==""){
					@$refrrlist[] = $rowrrref['creference'];
				}
			}

			//rrlist
			@$refpolist = array();
			foreach(@$refpolistMAIN as $rowporef){
				if(in_array($rowporef['ctranno'],@$refrrlist) && $rowporef['creference']!==""){
					@$refpolist[] = $rowporef['creference'];
				}
			}

			$nadvpay = "NO";
			foreach(@$refPOTYPMAIN as $rowposz){
				if(in_array($rowposz['cpono'],@$refpolist) && $rowposz['ladvancepay']==1){
					$nadvpay = "YES";
				}
			}

			if($nadvpay=="YES" && $_REQUEST['typ']=="PurchAdv"){
				$nblance = floatval($row['ngross']) - (floatval($row['napplied']) + floatval($row['newtamt']));
				if($nblance > 0) {
					$cntr = $cntr + 1;
					$json['crrno'] = $row['ctranno'];
					$json['ngross'] = $row['ngross'];
					$json['napplied'] = $row['napplied'];
					$json['newtamt'] = $row['newtamt'];
					$json['balance'] = $nblance;
					$json['ddate'] = $row['dreceived'];
				
					if($_REQUEST['typ']=="PettyCash"){
						$json['cremarks'] = $row['cname'];
					}
					else{
						$json['cremarks'] = $row['cremarks'];
					}
				 
					$json['ctitle'] = $row['cacctdesc'];
					$json['vatyp'] = $row['lcompute'];
					$json['vatrte'] = $row['nvatrate']; 
					$json['crefsi'] = $row['crefsi']; 
					
					$json['nadvpay'] = 0;
					if($_REQUEST['typ']=="PurchAdv"){			
						//payment list
						foreach(@$refpaylistMAIN as $rowpayref){
							if(in_array($rowpayref['capvno'],@$refpolist)){
								$json['nadvpay'] = $rowpayref['napplied']; 
								$json['cacctno'] = $rowpayref['cacctno'];
							}	
						}
					}
							
					$json2[] = $json;
				}
			}elseif($nadvpay=="NO" && $_REQUEST['typ']=="Purchases"){
					$nblance = floatval($row['ngross']) - (floatval($row['napplied']) + floatval($row['newtamt']));
					if($nblance > 0) {
						$cntr = $cntr + 1;
						$json['crrno'] = $row['ctranno'];
						$json['ngross'] = $row['ngross'];
						$json['napplied'] = $row['napplied'];
						$json['newtamt'] = $row['newtamt'];
						$json['balance'] = $nblance;
						$json['ddate'] = $row['dreceived'];
					
						if($_REQUEST['typ']=="PettyCash"){
							$json['cremarks'] = $row['cname'];
						}
						else{
							$json['cremarks'] = $row['cremarks'];
						}
					 
						$json['cacctno'] = $row['cacctid'];
						$json['ctitle'] = $row['cacctdesc'];
						$json['vatyp'] = $row['lcompute'];
						$json['vatrte'] = $row['nvatrate']; 
						$json['crefsi'] = $row['crefsi']; 
						$json['nadvpay'] = 0;
						
						$json2[] = $json;
			
					}
			}

			
	}
	
	if($cntr<=0){
			 $json['crrno'] = "NONE";
			 $json['ngross'] = "";
			 $json['ddate'] = "";
			 $json['cremarks'] = "";
			 $json['cacctno'] = "";
			 $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
