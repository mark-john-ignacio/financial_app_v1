<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	if($_REQUEST['typ']=="DR"){   //DELIVERY REFERENCE

		//get all dr
		$resq = mysqli_query ($con, "Select ctranno, nident,citemno,nqty From ntdr_t where compcode='$company'");
		if (mysqli_num_rows($resq)!=0){
			while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
				@$arrresq[]=$row;
			}
		}

		$result = mysqli_query ($con, "select * from ntdr where compcode='$company' and lapproved=1 and lvoid=0 and ccode='".$_REQUEST['x']."' order by ddate desc, ctranno desc"); 

	}elseif($_REQUEST['typ']=="QO"){ // BILLING QUOTE REFEERENCE

		//get all quotation
		$resq = mysqli_query ($con, "Select ctranno, nident,citemno,nqty From quote_t where compcode='$company'");
		if (mysqli_num_rows($resq)!=0){
			while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
				@$arrresq[]=$row;
			}
		}

		//get all quote na nsa SO na
		@$arrefquotes = array();
		$resquortes = mysqli_query ($con, "Select creference From so_t where compcode='$company' and IFNULL(creference,'') <> '' UNION ALL Select creference From ntso_t where compcode='$company' and IFNULL(creference,'') <> ''");
		if (mysqli_num_rows($resquortes)!=0){
			while($row = mysqli_fetch_array($resquortes, MYSQLI_ASSOC)){
				@$arrefquotes[]=$row['creference'];
			}
		}

		$result = mysqli_query ($con, "select * from quote where compcode='$company' and lapproved=1 and lvoid=0 and ccode='".$_REQUEST['x']."' and quotetype='billing' and csalestype='".$_REQUEST['styp']."' and ctranno not in ('".implode("','", @$arrefquotes)."') order by ddate desc, ctranno desc"); 

	}elseif($_REQUEST['typ']=="SO"){// SALES ORDER REFEERENCE

		//get all quotation
		$resq = mysqli_query ($con, "Select ctranno, nident,citemno,nqty From ntso_t where compcode='$company'");
		if (mysqli_num_rows($resq)!=0){
			while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
				@$arrresq[]=$row;
			}
		}

		$result = mysqli_query ($con, "select * from ntso where compcode='$company' and lapproved=1 and lvoid=0 and ccode='".$_REQUEST['x']."' and csalestype='Services' order by ddate desc, ctranno desc"); 
	}


	//get all existing SI
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, nrefident,citemno,sum(nqty) as nqty From ntsales_t a left join ntsales b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, nrefident,citemno UNION ALL Select creference, nrefident,citemno,sum(nqty) as nqty From sales_t a left join sales b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, nrefident,citemno");
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
