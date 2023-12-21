<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");

	if($_REQUEST['y']!=""){
		$qry = "and a.citemno not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}

	@$receiveds = array();
	$received = mysqli_query ($con, "Select x.nrefident, x.creference,x.citemno,sum(x.nqty) as nqty From purchase_t x left join purchase y on x.compcode=y.compcode and x.cpono=y.cpono Where x.compcode='$company' and  x.creference='".$_REQUEST['x']."' and y.lcancelled=0 and y.lvoid=0 group by x.nrefident, x.creference,x.citemno");
	if (mysqli_num_rows($received)!=0){
		while($row = mysqli_fetch_array($received, MYSQLI_ASSOC)){
			@$receiveds[]=$row;
		}
	}

	$sql = "select a.nident,a.citemno,a.citemdesc,a.cpartdesc,a.cunit,a.nqty,a.ctranno from purchrequest_t a left join purchrequest b on a.compcode=b.compcode and a.ctranno=b.ctranno WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry;

	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	$remain = 0;
	$json = array();
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
			$isitem = 0;
			foreach(@$receiveds as $rsc){
				if($rsc['citemno']==$row['citemno'] && $rsc['nrefident']==$row['nident'] ){
					$isitem++;

					$nqtyPO = $row['nqty'];
					$nqtyRR = $rsc['nqty'];
					$remain = floatval($nqtyPO) - floatval($nqtyRR);

				}
			}

			if($isitem==0){
				$remain = $row['nqty'];
			}

			if($remain>0){
				$json['nident'] = $row['nident'];
				$json['citemno'] = $row['citemno'];
				$json['cdesc'] = $row['citemdesc'];
				$json['cpartdesc'] = $row['cpartdesc'];
				$json['nqty'] = $remain;
				$json['cunit'] = $row['cunit'];
				$json2[] = $json;
			}
		}

		if(count($json)==0){
			$json['citemno'] = "";
			$json2[] = $json;
		}

	}
	else{
			$json['citemno'] = "";
			$json2[] = $json;
	}
	
	echo json_encode($json2);

?>
