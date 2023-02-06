<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];

		$date1 = date("Y-m-d");
		
			$sql = "select a.nident, a.ctranno, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, a.ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, d.ccurrencycode, d.ccurrencydesc, d.nexchangerate
			from ntsales_t a 
			left join ntsales d on a.compcode=d.compcode and a.ctranno=d.ctranno
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join
				(
					Select x.creference,x.citemno,x.nrefident,sum(x.nqty) as nqty
					From ntsalesreturn_t x
					left join ntsalesreturn y on x.compcode=y.compcode and x.ctranno=y.ctranno
					Where x.creference='".$_REQUEST['id']."' and y.lcancelled=0
					group by x.creference,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno and a.nident = c.nrefident
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.nident = '".$_REQUEST['itm']."'";
		
	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
		$nqty1 = $row['totqty'];
		$nqty2 = $row['totqty2']; 


		$remainqty =  floatval($nqty1) - floatval($nqty2);

		$nprice = floatval($row['nprice']) - floatval($row['ndiscount']);
		
		$json['ident'] = $row['nident'];
		$json['id'] = $row['cpartno'];
	  $json['desc'] = $row['citemdesc'];
		$json['totqty'] = $remainqty;
		$json['cqtyunit'] = $row['qtyunit'];
		$json['cunit'] = $row['cunit'];
		$json['nfactor'] = $row['nfactor'];
		$json['nprice'] = $nprice;
		$json['nbaseamount'] = 0;
		$json['namount'] = 0; 
		$json['xref'] = $row['ctranno'];
		$json['ccurrencycode'] = $row['ccurrencycode']; 
		$json['ccurrencydesc'] = $row['ccurrencydesc']; 
		$json['nexchangerate'] = $row['nexchangerate'];
		$json2[] = $json;
	
	}


	echo json_encode($json2);


?>
