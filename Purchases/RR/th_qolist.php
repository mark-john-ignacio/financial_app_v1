<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	//get all dr
	$resq = mysqli_query ($con, "Select cpono as ctranno, nident,citemno,(nqty*nfactor) as nqty From purchase_t where compcode='$company'");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	//get all existing RR
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, nrefidentity,citemno,sum(nqty*nfactor) as nqty From receive_t a left join receive b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 group by creference, nrefidentity, citemno");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

	//$qry = "select B.cpono as ctranno, B.ddate, B.ngross, sum(A.nqty*A.nfactor), ifnull(sum(C.nqty*C.nfactor),0) from purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono left join receive_t C on A.compcode=C.compcode and A.cpono=C.creference and A.citemno=C.citemno and A.nident = C.nrefidentity where A.compcode='$company' and B.lapproved=1 and B.ccode='".$_REQUEST['x']."' Group by B.cpono, B.ddate, B.ngross HAVING (sum(A.nqty*A.nfactor) - ifnull(sum(C.nqty*C.nfactor),0)) > 0 order by B.ddate desc, A.cpono desc ";
	
	$qry = "select * from purchase where compcode='$company' and lapproved=1 and ccode='".$_REQUEST['x']."' order by ddate desc, cpono desc"; 
	$result = mysqli_query ($con, $qry); 


	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			$remqty = 0;
			foreach(@$arrresq as $rsc){
				if($rsc['ctranno']==$row['cpono']){

					$inarray = "No";

					foreach(@$arrinv as $rsibnv){
						if($rsc['ctranno']==$rsibnv['creference']){
							if($rsc['citemno']==$rsibnv['citemno'] && $rsc['nident']==$rsibnv['nrefidentity']){
								$inarray = "Yes";

								$rem = floatval($rsc['nqty']) - floatval($rsibnv['nqty']);
								if($rem>=1){
									$remqty++;
								}
							}
						}
					}

					if($inarray=="No"){
						$remqty++;
					}

				}
			}
			
			if($remqty>=1 || count(@$arrinv)==0){

			 $json['cpono'] = $row['cpono'];
			 $json['dcutdate'] = $row['ddate'];
			 $json['dneeded'] = $row['dneeded'];
			 $json['ngross'] = $row['ngross'];
			 $json2[] = $json;

			}
	
		}

		if(count($json)==0){
			$json['cpono'] = "NONE";
			$json2[] = $json;
		}
	}
	else{
		$json['cpono'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
