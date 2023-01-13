<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	if($_REQUEST['typ']=="DR"){

		//get all quotation
		$resq = mysqli_query ($con, "Select ctranno, nident,citemno,nqty From dr_t where compcode='$company'");
		if (mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				@$arrresq[]=$row;
			}
		}

		$result = mysqli_query ($con, "select * from dr where compcode='$company' and lapproved=1 and ccode='".$_REQUEST['x']."' order by ddate desc, ctranno desc"); 
	}elseif($_REQUEST['typ']=="QO"){

		//get all quotation
		$resq = mysqli_query ($con, "Select ctranno, nident,citemno,nqty From quote_t where compcode='$company'");
		if (mysqli_num_rows($resq)!=0){
			while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
				@$arrresq[]=$row;
			}
		}

		$result = mysqli_query ($con, "select * from quote where compcode='$company' and lapproved=1 and ccode='".$_REQUEST['x']."' and quotetype='billing' and csalestype='".$_REQUEST['styp']."' order by ddate desc, ctranno desc"); 
	}


	//get all existing SI
	$resq = mysqli_query ($con, "Select creference, nrefident,citemno,sum(nqty) as nqty From sales_t a left join sales b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 group by creference, nrefident,citemno");
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

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
	
				$json['cpono'] = $row['ctranno'];
				$json['dcutdate'] = $row['ddate'];
				$json['ngross'] = $row['ngross'];
				$json2[] = $json;

			}else{
				$json['cpono'] = "NONE";
				$json2[] = $json;
			}
	
		}
	}
	else{
		$json['cpono'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
