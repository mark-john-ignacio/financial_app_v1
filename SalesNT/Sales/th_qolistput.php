<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];

		//$avail = $_REQUEST['itmbal'];
		$date1 = date("Y-m-d");
		
		if($_REQUEST['typ']=="DR"){
			$sql = "select a.ctranno, a.crefident, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, 0 as ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, b.ctype, b.ctaxcode, d.ccurrencycode, d.ccurrencydesc, d.nexchangerate, a.creference
			from ntdr_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join ntso d on a.compcode=d.compcode and a.creference=d.ctranno
			left join
				(
				 	Select x.creference,x.nrefident,x.citemno,sum(x.nqty) as nqty
				 	From ntsales_t x
				 	left join ntsales y on x.compcode=y.compcode and x.ctranno=y.ctranno
					Where x.compcode='$company' and x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 	group by x.creference,x.nrefident,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno and a.nident=c.nrefident
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.nident = '".$_REQUEST['itm']."'";

			//kunin ang SO details for the price and taxcode
			@$arrefsos = array();
			$ressos = mysqli_query ($con, "Select * From ntso_t where compcode='$company'");
			if (mysqli_num_rows($ressos)!=0){
				while($row = mysqli_fetch_array($ressos, MYSQLI_ASSOC)){
					@$arrefsos[]=$row;
				}
			}

		}elseif($_REQUEST['typ']=="QO"){
			if($_REQUEST['itm']=="ALL"){
				$itmvar = "";
			}else{
				$itmvar = " and a.nidentity = '".$_REQUEST['itm']."'";
			}

			$sql = "select a.ctranno, a.nident as crefident, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, 0 as ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, b.ctype, b.ctaxcode, d.ccurrencycode, d.ccurrencydesc, d.cvattype, e.nrate, d.nexchangerate
			from quote_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join quote d on a.compcode=d.compcode and a.ctranno=d.ctranno
			left join taxcode e on b.compcode=e.compcode and b.ctaxcode=e.ctaxcode
			left join
				(
				 	Select x.creference,x.nrefident,x.citemno,sum(x.nqty) as nqty
				 	From sales_t x
				 	left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
					Where x.compcode='$company' and x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 	group by x.creference,x.nrefident,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno and a.nident=c.nrefident
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.nident = '".$_REQUEST['itm']."'";
		}elseif($_REQUEST['typ']=="SO"){
			if($_REQUEST['itm']=="ALL"){
				$itmvar = "";
			}else{
				$itmvar = " and a.nident = '".$_REQUEST['itm']."'";
			}

			$sql = "select a.ctranno, a.creference, a.nident as crefident, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, 0 as ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, b.ctype, d.ccurrencycode, d.ccurrencydesc, a.nrate, d.nexchangerate, a.ctaxcode
			from ntso_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join ntso d on a.compcode=d.compcode and a.ctranno=d.ctranno
			left join
				(
				 	Select x.creference,x.nrefident,x.citemno,sum(x.nqty) as nqty
				 	From ntsales_t x
				 	left join ntsales y on x.compcode=y.compcode and x.ctranno=y.ctranno
					Where x.compcode='$company' and x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 	group by x.creference,x.nrefident,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno and a.nident=c.nrefident
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.nident = '".$_REQUEST['itm']."'";
		}
		
	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
		$nqty1 = $row['totqty'];
		$nqty2 = $row['totqty2']; 
		
		 $json['id'] = $row['cpartno'];
	   $json['desc'] = $row['citemdesc'];
		 $json['nqty'] = $row['nqty'];
		 $json['totqty'] = $nqty1 - $nqty2;
		 $json['cqtyunit'] = $row['qtyunit'];
		 $json['cunit'] = $row['cunit'];
		 $json['nfactor'] = $row['nfactor'];
		 $json['ndisc'] = $row['ndiscount'];

	//	 if($_REQUEST['typ']=="DR"){
	//		$json['nprice'] = $row['nprice'];
	//		$json['nbaseamount'] = $row['nbaseamount'];
	//		$json['namount'] = $row['namount'];
	//	 }elseif($_REQUEST['typ']=="QO"){

		//	if($row['cvattype']=="VatIn"){

			//	$gprice = floatval($row['nprice'])/(1+(floatval($row['nrate'])/100));
		//		$gamount = $gprice*floatval($nqty1 - $nqty2);

		//		$json['nprice'] = round($gprice,2);
		//		$json['namount'] =  round($gamount,2);
		//		$json['nbaseamount'] = round($gamount*floatval($row['nexchangerate']),2);

		//	}else{

			if($_REQUEST['typ']=="DR"){
				
				foreach(@$arrefsos as $rowx){
					if($row['creference'] == $rowx['ctranno'] && $row['crefident'] == $rowx['nident']){
						$json['nprice'] = $rowx['nprice'];
						$json['namount'] = $rowx['namount'];
						$json['nbaseamount'] = $rowx['nbaseamount'];
					}
				}
			}elseif($_REQUEST['typ']=="QO"){
				$json['nprice'] = $row['nprice'];
				$json['namount'] = $row['namount'];
				$json['nbaseamount'] = $row['nbaseamount'];
			}else{
			}
		//	}
	//	 }
		
		 $json['xref'] = $row['ctranno'];
		 $json['citmcls'] = $row['ctype'];

		 $json['ccurrencycode'] = $row['ccurrencycode']; 
		 $json['ccurrencydesc'] = $row['ccurrencydesc']; 
		 $json['nexchangerate'] = $row['nexchangerate'];
		 $json['crefident'] = $row['crefident'];
		 $json2[] = $json;
	
	}


	echo json_encode($json2);


?>
