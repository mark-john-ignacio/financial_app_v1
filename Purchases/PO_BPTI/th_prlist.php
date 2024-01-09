<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	//get all pr
	$resq = mysqli_query ($con, "Select A.ctranno, A.nident, A.citemno, (A.nqty*A.nfactor) as nqty From purchrequest_t A where A.compcode='$company'");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	//get all existing PO
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select a.creference, a.nrefident, a.citemno_old as citemno, sum(a.nqty*a.nfactor) as nqty, b.lcancelled From purchase_t a left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, nrefident, citemno");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}
	
	$qry = "select A.*, B.cdesc from purchrequest A left join locations B on A.compcode=B.compcode and A.locations_id=B.nid where A.compcode='$company' and A.lapproved=1 order by A.ddate desc, A.ctranno desc"; 
	$result = mysqli_query ($con, $qry); 

	$json = array();
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			$remqty = 0;
			foreach(@$arrresq as $rsc){
				if($rsc['ctranno']==$row['ctranno']){

					$inarray = "No";

					foreach(@$arrinv as $rsibnv){
						if($rsc['ctranno']==$rsibnv['creference']){
							if($rsc['citemno']==$rsibnv['citemno'] && $rsc['nident']==$rsibnv['nrefident']){
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

			 $json['cprno'] = $row['ctranno'];
			 $json['dcutdate'] = $row['ddate'];
			 $json['dneeded'] = $row['dneeded'];
			 $json['cdesc'] = $row['cdesc'];
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
