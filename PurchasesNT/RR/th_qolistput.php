<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
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
	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
	//if($row['nqty']>=1){
			
		$nqty1 = $row['nqty'];
		$nqty2 = $row['nqty2']; 
		
			 $json['nident'] = $row['nident'];
			 $json['id'] = $row['citemno'];
			 $json['cdesc'] = $row['citemdesc'];
			 $json['cunit'] = $row['cunit'];
			 $json['nprice'] = $row['nprice'];
			 $json['nbaseamount'] = $row['nbaseamount'];
			 $json['namount'] = $row['namount'];
			 $json['cmainuom'] = $row['cmainunit'];
			 $json['nfactor'] = $row['nfactor'];
			 $json['nqty'] = $nqty1 - $nqty2;
			 $json['xref'] = $row['cpono'];
			 $json['ccurrencycode'] = $row['ccurrencycode']; 
		 	 $json['ccurrencydesc'] = $row['ccurrencydesc']; 
		 	 $json['nexchangerate'] = $row['nexchangerate'];

		 $json2[] = $json;

//	}
	
	}


	echo json_encode($json2);


?>
