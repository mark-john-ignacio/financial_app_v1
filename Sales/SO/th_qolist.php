<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	//$result = mysqli_query ($con, "select DISTINCT B.ctranno, B.ddate, B.ngross, B.ccurrencycode, B.nexchangerate, A.nqty, A.nfactor, C.nqty, C.nfactor from quote_t A left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno left join so_t C on A.compcode=C.compcode and A.ctranno=C.creference and A.citemno=C.citemno where A.compcode='".$company."' and B.lapproved=1 and B.ccode='".$_REQUEST['x']."' and B.csalestype='".$_REQUEST['selsi']."' and B.quotetype='quote' HAVING ((A.nqty*A.nfactor) - ifnull((C.nqty*C.nfactor),0)) > 0 order by B.ddate desc, A.ctranno desc"); 


	//get all quotation
	$resq = mysqli_query ($con, "Select ctranno, nident,citemno,nqty From quote_t where compcode='$company'");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	//get all existing SO
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, nrefident,citemno,sum(nqty) as nqty From so_t a left join so b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, nrefident,citemno");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}


	$result = mysqli_query ($con, "select * from quote where compcode='$company' and lapproved=1 and ccode='".$_REQUEST['x']."' and csalestype='".$_REQUEST['selsi']."' order by ddate desc, ctranno desc"); 


	$json = array();
	$json2 = array();

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
				$json['ccurrencycode'] = $row['ccurrencycode'];
				$json['nexchangerate'] = $row['nexchangerate'];
				$json2[] = $json;
	
			}

		}

	}	
	
	echo json_encode($json2);


?>
