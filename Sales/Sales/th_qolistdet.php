<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");

	if($_REQUEST['y']!=""){
		$qry = "and a.nident not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}

	if($_REQUEST['typ']=="DR"){
		$sql = "select a.nident, a.citemno,a.cunit,a.nqty,a.creference,a.crefident,ifnull(c.nqty,0) as nqty2,b.citemdesc, 1 as navail, d.ccurrencycode, a.nprice, a.namount, a.nbaseamount,'' as cvattype, '' as nrate, d.nexchangerate
		from dr_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join so d on a.compcode=d.compcode and a.creference=d.ctranno
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From sales_t x
			 left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.compcode='$company' and x.creference='".$_REQUEST['x']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry;

		@$arrefsos = array();
			$ressos = mysqli_query ($con, "Select A.*, B.cpono From so_t A left join so B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company'");
			if (mysqli_num_rows($ressos)!=0){
				while($row = mysqli_fetch_array($ressos, MYSQLI_ASSOC)){
					@$arrefsos[]=$row;
				}
			}

	}elseif($_REQUEST['typ']=="QO"){
		$sql = "select a.nident, a.citemno,a.cunit,a.nqty,'' as creference,ifnull(c.nqty,0) as nqty2,b.citemdesc, 1 as navail, d.ccurrencycode, a.namount, a.nprice, a.nbaseamount, d.cvattype, e.nrate, d.nexchangerate
		from quote_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join quote d on a.compcode=d.compcode and a.ctranno=d.ctranno
		left join taxcode e on b.compcode=e.compcode and b.ctaxcode=e.ctaxcode
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From sales_t x
			 left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.compcode='$company' and x.creference='".$_REQUEST['x']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry;
	}elseif($_REQUEST['typ']=="SO"){
		$sql = "select a.nident, a.citemno,a.cunit,a.nqty,'' as creference,ifnull(c.nqty,0) as nqty2,b.citemdesc, 1 as navail, d.ccurrencycode, a.namount, a.nprice, a.nbaseamount, b.ctaxcode as cvattype, e.nrate, d.nexchangerate, d.cpono
		from so_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join so d on a.compcode=d.compcode and a.ctranno=d.ctranno
		left join taxcode e on b.compcode=e.compcode and b.ctaxcode=e.ctaxcode
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From sales_t x
			 left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.compcode='$company' and x.creference='".$_REQUEST['x']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry;
	}

		

	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 



	//$json2 = array();
	//$json = [];
// echo	mysqli_num_rows($result);
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
			$nqty1 = $row['nqty'];
			$nqty2 = $row['nqty2']; 
		
			 $json['id'] = $row['nident'];
			 $json['citemno'] = $row['citemno'];
			 $json['cdesc'] = $row['citemdesc'];
			 $json['cunit'] = $row['cunit'];
			 $json['nqty'] = number_format($nqty1 - $nqty2);
			 $json['navail'] = $row['navail'];

			 if($_REQUEST['typ']=="DR"){
				foreach(@$arrefsos as $rowx){
					if($row['creference'] == $rowx['ctranno'] && $row['crefident'] == $rowx['nident']){

						$xnamt = ($nqty1 - $nqty2) * floatval($rowx['nprice']);
						$json['nprice'] = number_format($rowx['nprice'],2);
						//$json['namount'] = $rowx['namount'];
						$json['namount'] = $xnamt;
						$json['nbaseamount'] = number_format($xnamt * floatval($row['nexchangerate']),2);
						$json['ctaxcode'] = $rowx['ctaxcode'];
						$json['cpono'] = $rowx['cpono'];
					}
				}
			}elseif($_REQUEST['typ']=="QO"){
				$xnamt = ($nqty1 - $nqty2) * floatval($rowx['nprice']);

				$json['nprice'] = number_format($row['nprice'],2);
				//$json['namount'] = $row['namount'];
				$json['namount'] = $xnamt;
				$json['nbaseamount'] = number_format($xnamt * floatval($rowx['nexchangerate']),2);
				$json['ctaxcode'] = ($row['cvattype']=="VatIn") ? "VT" : "NT";
				$json['cpono'] = "";
			}else{
				$json['cpono'] = $row['cpono'];
				$json['ctaxcode'] = $row['ctaxcode'];
			}

			 $json['creference'] = $row['creference'];
			 $json2[] = $json;
	
		}

	}
	else{
			$json['citemno'] = "";
			$json['cdesc'] = "";
			$json['cunit'] = "";
			$json['nqty'] = "";
			$json['navail'] = "";
			$json2[] = $json;
	}
	
	echo json_encode($json2);

?>
