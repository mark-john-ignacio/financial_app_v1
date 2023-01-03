<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];

		//$avail = $_REQUEST['itmbal'];
		$date1 = date("Y-m-d");
		
			$sql = "select a.ctranno, a.crefident, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, 0 as ndiscount, a.nbaseamount, a.namount, a.cmainunit as qtyunit, a.nfactor, ifnull(c.nqty,0) as totqty2, b.ctype, b.ctaxcode, d.ccurrencycode, d.ccurrencydesc, d.nexchangerate
			from dr_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join so d on a.compcode=d.compcode and a.creference=d.ctranno
			left join
				(
				 	Select x.creference,x.citemno,sum(x.nqty) as nqty
				 	From sales_t x
				 	left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno
					Where x.compcode='$company' and x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 	group by x.creference,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.citemno = '".$_REQUEST['itm']."'";
		
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
		 $json['nprice'] = $row['nprice'];
		 $json['ndisc'] = $row['ndiscount'];
		 $json['nbaseamount'] = $row['nbaseamount'];
		 $json['namount'] = $row['namount'];
		 $json['xref'] = $row['ctranno'];
		 $json['citmcls'] = $row['ctype'];
		 $json['ctaxcode'] = $row['ctaxcode'];
		 $json['ccurrencycode'] = $row['ccurrencycode']; 
		 $json['ccurrencydesc'] = $row['ccurrencydesc']; 
		 $json['nexchangerate'] = $row['nexchangerate'];
		 $json['crefident'] = $row['crefident'];
		 $json2[] = $json;
	
	}


	echo json_encode($json2);


?>
