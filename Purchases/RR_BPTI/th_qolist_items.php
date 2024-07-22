<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	//get all SO
	@$arrresq = array();
	$resq = mysqli_query ($con, "Select cpono as ctranno, nident,citemno,(nqty*nfactor) as nqty From purchase_t where compcode='$company' and citemdesc like '%".$_REQUEST['itm']."%'");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	$result = mysqli_query ($con, "select * from purchase where compcode='$company' and lapproved=1 and lvoid=0 and ccode='".$_REQUEST['x']."' order by ddate desc, cpono desc"); 
	
	//get all existing RR
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, nrefidentity,citemno,sum(nqty*nfactor) as nqty From receive_t a left join receive b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, nrefidentity, citemno");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

	$json = array();
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
