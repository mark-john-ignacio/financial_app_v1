<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");

	$json2 = array();

	if($_REQUEST['typ']=="DR"){
		$sql = "select a.ctranno, a.nident as crefident, a.crefident as crefSOident, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, 0 as ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, b.ctype, b.ctaxcode, d.ccurrencycode, d.ccurrencydesc, d.nexchangerate, a.creference, e.cacctno, e.cacctid, e.cacctdesc, b.ctaxcode as cvattype, IFNULL(b.cskucode,'') as cskucode, IFNULL(b.cnotes,'') as cnotes
		from dr_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join so d on a.compcode=d.compcode and a.creference=d.ctranno
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From sales_t x
			 left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.compcode='$company' and x.creference='".$_REQUEST['id']."' and y.lcancelled=0 and y.lvoid=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		left join accounts e on b.compcode=e.compcode and b.cacctcodesales=e.cacctno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.nident = '".$_REQUEST['itm']."'";

		@$arrefsos = array();
			$ressos = mysqli_query ($con, "Select A.*, B.cpono From so_t A left join so B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company'");
			if (mysqli_num_rows($ressos)!=0){
				while($row = mysqli_fetch_array($ressos, MYSQLI_ASSOC)){
					@$arrefsos[]=$row;
				}
			}

	}elseif($_REQUEST['typ']=="QO"){
		$sql = "select a.ctranno, a.nident as crefident, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, 0 as ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, b.ctype, b.ctaxcode, d.ccurrencycode, d.ccurrencydesc, d.cvattype, e.nrate, d.nexchangerate, d.cterms, f.cacctno, f.cacctid, f.cacctdesc, IFNULL(b.cskucode,'') as cskucode, IFNULL(b.cnotes,'') as cnotes
		from quote_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join quote d on a.compcode=d.compcode and a.ctranno=d.ctranno
		left join taxcode e on b.compcode=e.compcode and b.ctaxcode=e.ctaxcode
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From sales_t x
			 left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.compcode='$company' and x.creference='".$_REQUEST['id']."' and y.lcancelled=0 and y.lvoid=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		left join accounts f on b.compcode=f.compcode and b.cacctcodesales=f.cacctno	
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.nident = '".$_REQUEST['itm']."'";
	}elseif($_REQUEST['typ']=="SO"){
		$sql = "select a.ctranno, a.creference, a.nident as crefident, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, 0 as ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, b.ctype, d.ccurrencycode, d.ccurrencydesc, a.nrate, d.nexchangerate, a.ctaxcode, e.cacctno, e.cacctid, e.cacctdesc, b.ctaxcode as cvattype, IFNULL(b.cskucode,'') as cskucode, IFNULL(b.cnotes,'') as cnotes
		from so_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join so d on a.compcode=d.compcode and a.ctranno=d.ctranno
		left join taxcode e on b.compcode=e.compcode and b.ctaxcode=e.ctaxcode
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From sales_t x
			 left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.compcode='$company' and x.creference='".$_REQUEST['id']."' and y.lcancelled=0 and y.lvoid=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		left join accounts f on b.compcode=f.compcode and b.cacctcodesales=f.cacctno	
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.nident = '".$_REQUEST['itm']."'";
	}

		

	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 



	//$json2 = array();
	//$json = [];
// echo	mysqli_num_rows($result);
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
			$nqty1 = $row['totqty'];
			$nqty2 = $row['totqty2']; 

			$json['id'] = $row['cpartno'];

			if(isset($_REQUEST['cdoc'])){
				if($_REQUEST['cdoc']=="Doc2"){
					$json['systemno'] = "";
					$json['desc'] = ($row['cskucode']!="") ? $row['cskucode'] : $row['citemdesc'];
				}else{
					$json['systemno'] = $row['cskucode'];
					$json['desc'] = ($row['cnotes']!="") ? $row['cnotes'] : $row['citemdesc'];
				}
			}else{				
				$json['systemno'] = $row['cskucode'];
				$json['desc'] = $row['citemdesc'];
			}

			$json['cunit'] = $row['cunit'];
			$json['cqtyunit'] = $row['qtyunit'];
			$json['nfactor'] = $row['nfactor'];
			$json['nqty'] = $row['nqty']; 
			$json['totqty'] = number_format($nqty1 - $nqty2); 

			if($_REQUEST['typ']=="DR"){
				foreach(@$arrefsos as $rowx){

					if($row['creference'] == $rowx['ctranno'] && $row['crefSOident'] == $rowx['nident']){

						$xnamt = ($nqty1 - $nqty2) * floatval($rowx['nprice']);
						$json['nprice'] = number_format($rowx['nprice'],4);
						//$json['namount'] = $rowx['namount'];
						$json['nbaseamount'] = number_format($xnamt,2);
						$json['namount'] = number_format($xnamt * floatval($row['nexchangerate']),2);
						$json['ctaxcode'] = $row['cvattype'];
						$json['cpono'] = $rowx['ditempono'];
					}
				}
			}elseif($_REQUEST['typ']=="QO"){
				$xnamt = ($nqty1 - $nqty2) * floatval($row['nprice']);

				$json['nprice'] = number_format($row['nprice'],4);
				//$json['namount'] = $row['namount'];
				$json['nbaseamount'] = number_format($xnamt,2);
				$json['namount'] = number_format($xnamt * floatval($row['nexchangerate']),2);
				$json['ctaxcode'] = ($row['cvattype']=="VatIn") ? "VT" : "NT";
				$json['cpono'] = "";
			}else{
				$xnamt = ($nqty1 - $nqty2) * floatval($row['nprice']);

				$json['nprice'] = number_format($row['nprice'],4);
				//$json['namount'] = $row['namount'];
				$json['nbaseamount'] = number_format($xnamt,2);
				$json['namount'] = number_format($xnamt * floatval($row['nexchangerate']),2);
				$json['cpono'] = $row['ditempono'];
				$json['ctaxcode'] = $row['cvattype'];
			}

			$json['xref'] = $row['ctranno'];
			$json['citmcls'] = $row['ctype'];

			$json['ccurrencycode'] = $row['ccurrencycode']; 
			$json['ccurrencydesc'] = $row['ccurrencydesc']; 
			$json['nexchangerate'] = $row['nexchangerate'];
			$json['crefident'] = $row['crefident'];
			$json['cacctno'] = $row['cacctno'];
			$json['cacctid'] = $row['cacctid'];
			$json['cacctdesc'] = $row['cacctdesc'];

			$json2[] = $json;
	
		}

	}
	
	echo json_encode($json2);

?>
