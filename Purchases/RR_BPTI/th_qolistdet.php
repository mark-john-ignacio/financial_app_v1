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

	$items = mysqli_query ($con, "Select * From items where compcode='$company' and cstatus='ACTIVE'");
	if (mysqli_num_rows($items)!=0){
		while($row = mysqli_fetch_array($items, MYSQLI_ASSOC)){
			@$arrresq[$row['cpartno']]=$row['citemdesc'];
		}
	}

	@$receiveds = array();
	$received = mysqli_query ($con, "Select x.nrefidentity, x.creference,x.citemno,sum(x.nqty) as nqty From receive_t x left join receive y on x.compcode=y.compcode and x.ctranno=y.ctranno Where x.compcode='$company' and  x.creference='".$_REQUEST['x']."' and y.lcancelled=0 and y.lvoid=0 group by x.nrefidentity, x.creference,x.citemno");
	if (mysqli_num_rows($received)!=0){
		while($row = mysqli_fetch_array($received, MYSQLI_ASSOC)){
			@$receiveds[]=$row;
		}
	}

	/*`
		$sql = "select a.nident,a.citemno,a.cunit,a.nqty,a.cpono,ifnull(c.nqty,0) as nqty2,if(a.citemno='NEW_ITEM',a.citemdesc,b.citemdesc) as citemdesc, 1 as navail, d.ccurrencycode, a.nprice, a.nbaseamount
		from purchase_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join purchase d on a.compcode=d.compcode and a.cpono=d.cpono
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From receive_t x
			 left join receive y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.compcode='$company' and  x.creference='".$_REQUEST['x']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			) c on a.cpono=c.creference and a.citemno=c.citemno
		WHERE a.compcode='$company' and a.cpono = '".$_REQUEST['x']."' ".$qry;
	*/

	$sql = "select a.nident,a.citemno,a.cunit,a.nqty,a.cpono, a.nprice, a.nbaseamount,b.ccurrencycode, a.citemdesc from purchase_t a left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono WHERE a.compcode='$company' and a.cpono = '".$_REQUEST['x']."' ".$qry;

	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	//$json2 = array();
	//$json = [];
// echo	mysqli_num_rows($result);
$remain = 0;
$json = array();
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
			$isitem = 0;
			foreach(@$receiveds as $rsc){
				if($rsc['citemno']==$row['citemno'] && $rsc['nrefidentity']==$row['nident'] ){
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
				$json['nqty'] = $remain;
				$json['cunit'] = $row['cunit'];
				$json['nprice'] = $row['nprice'];
				$json['nbaseamount'] = $row['nbaseamount'];
				$json['ccurrencycode'] = $row['ccurrencycode'];
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
