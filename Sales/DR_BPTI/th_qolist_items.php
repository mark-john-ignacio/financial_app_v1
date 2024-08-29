<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	//get all SO
	@$arrresq = array();
	$resq = mysqli_query ($con, "Select ctranno, nident,citemno,nqty From so_t where compcode='$company' and citemno='".$_REQUEST['itm']."'");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	$result = mysqli_query ($con, "select * from so where compcode='$company' and lapproved=1 and lvoid=0 and ccode='".$_REQUEST['x']."' order by dcutdate desc, ctranno desc"); 
	
	//get all existing DR
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, crefident,citemno,sum(nqty) as nqty From dr_t a left join dr b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, crefident,citemno");
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

	$json = array();
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

			$remqty = 0;
			foreach(@$arrresq as $rsc){
				if($rsc['ctranno']==$row['ctranno']){

					$inarray = "No";

					foreach(@$arrinv as $rsibnv){
						if($rsc['ctranno']==$rsibnv['creference']){
							if($rsc['citemno']==$rsibnv['citemno'] && $rsc['nident']==$rsibnv['crefident']){
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
			 $json['dcutdate'] = $row['dcutdate'];
			 $json['ccontrolno'] = $row['cpono'];
			 $json['ngross'] = number_format($row['ngross'],2);
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
