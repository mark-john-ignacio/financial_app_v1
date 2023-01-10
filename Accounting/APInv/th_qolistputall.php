<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
			$sql = "select a.nident, a.ctranno, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, a.namount, a.cmainunit as qtyunit, 
			a.nfactor, ifnull(c.nqty,0) as totqty2 
			from receive_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join
				(Select x.creference,x.citemno,sum(x.nqty) as nqty
				 From suppinv_t x
				 left join suppinv y on x.compcode=y.compcode and x.ctranno=y.ctranno
				 Where x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 group by x.creference,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."'";
		
		
	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
	//if($row['nqty']>=1){
			
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
		 $json['namount'] = $row['namount'];
		 $json['xref'] = $row['ctranno'];
		 $json['xrefident'] = $row['nident'];
		 $json2[] = $json;

//	}
	
	}


	echo json_encode($json2);


?>
