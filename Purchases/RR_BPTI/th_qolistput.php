<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		

		/*
		$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc, d.ccurrencycode, d.ccurrencydesc, d.nexchangerate
		from purchase_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join purchase d on a.compcode=d.compcode and a.cpono=d.cpono
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From receive_t x
			 left join receive y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.creference='".$_REQUEST['id']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			) c on a.cpono=c.creference and a.citemno=c.citemno
		WHERE a.compcode='$company' and a.cpono = '".$_REQUEST['id']."' and a.nident='".$_REQUEST['itm']."'";
		*/

	//echo $sql;
	
	$items = mysqli_query ($con, "Select cpartno, citemdesc, cunit, IFNULL(cskucode,'') as cskucode From items where compcode='$company' and cstatus='ACTIVE'");
	if (mysqli_num_rows($items)!=0){
		while($row = mysqli_fetch_array($items, MYSQLI_ASSOC)){
			@$arrresq[$row['cpartno']]=$row['citemdesc'];
			@$arrresquinit[$row['cpartno']]=$row['cunit'];
			@$arrcskuid[$row['cpartno']]=$row['cskucode']; //null nalabas
		}
	}

	@$receiveds = array();
	$received = mysqli_query ($con, "Select x.nrefidentity, x.creference,x.citemno,sum(x.nqty) as nqty From receive_t x left join receive y on x.compcode=y.compcode and x.ctranno=y.ctranno Where x.compcode='$company' and  x.creference='".$_REQUEST['id']."' and y.lcancelled=0 group by x.nrefidentity, x.creference,x.citemno");
	if (mysqli_num_rows($received)!=0){
		while($row = mysqli_fetch_array($received, MYSQLI_ASSOC)){
			@$receiveds[]=$row;
		}                                             
	}

	$sql = "select a.cpono,a.nident,a.citemno,a.cpartno,a.citemdesc,a.cunit,a.nqty,a.nfactor,a.cpono, a.nprice, a.namount, a.nbaseamount,b.ccurrencycode, c.location_id, d.cdesc as locdesc from purchase_t a left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono left join purchrequest_t c on a.compcode=c.compcode and a.creference=c.ctranno and a.nrefident=c.nident left join locations d on c.compcode=d.compcode and c.location_id=d.nid WHERE a.compcode='$company' and a.cpono = '".$_REQUEST['id']."' and a.nident='".$_REQUEST['itm']."' ";
	
	$result = mysqli_query ($con, $sql); 

	$remain = 0;
	$json = array();
	$json2 = array();

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
				$json['cskucode'] = $row['cpartno'];
				$json['cdesc'] = $row['citemdesc'];
				$json['nqty'] = $remain;
				$json['cunit'] = $row['cunit'];
				$json['cmainuom'] = @$arrresquinit[$row['citemno']];
				$json['nprice'] = 0;
				$json['nbaseamount'] = 0;
				$json['namount'] = 0;
				$json['ccurrencycode'] = $row['ccurrencycode'];
				$json['xref'] = $row['cpono'];
				$json['nident'] = $row['nident']; 
				$json['nfactor'] = $row['nfactor'];
				$json['nlocation_id'] = $row['location_id'];
				$json['ncostcenter'] = $row['locdesc']; 
				$json2[] = $json;

			}
		}
	}
	
	echo json_encode($json2);


?>
