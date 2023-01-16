<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];

		$avail = $_REQUEST['itmbal'];
		$date1 = date("Y-m-d");
		
		if($avail==1){
			$sql = "select a.nident, a.ctranno, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, a.namount, a.nbaseamount, a.cmainunit as qtyunit, 
			a.nfactor, ifnull(c.nqty,0) as totqty2 
			from quote_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join
				(Select x.creference,x.citemno,sum(x.nqty) as nqty
				 From so_t x
				 left join so y on x.compcode=y.compcode and x.ctranno=y.ctranno
				 Where x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 group by x.creference,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.citemno = '".$_REQUEST['itm']."'";
		}
		else{
			$sql = "select a.nident, a.ctranno, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, a.nprice, a.namount, a.nbaseamount, a.cmainunit as qtyunit,
			a.nfactor, ifnull(c.nqty,0) as totqty2, ifnull(d.nqty,0) AS nqty
			from quote_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join
				(Select x.creference,x.citemno,sum(x.nqty) as nqty
				 From so_t x
				 left join so y on x.compcode=y.compcode and x.ctranno=y.ctranno
				 Where x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 group by x.creference,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno
			left join 
				(
					select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit, X.citemno, X.nfactor
					From tblinventory X
					where X.compcode='$company' and X.dcutdate <= '$date1'
					Group by X.cmainunit, X.citemno
				) d on a.citemno=d.citemno
	
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.citemno = '".$_REQUEST['itm']."'";

		}
		
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
		 $json['nbaseamount'] = $row['nbaseamount'];
		 $json['namount'] = $row['namount'];	
		 $json['xref'] = $row['ctranno'];
		 $json['nident'] = $row['nident'];
		 $json2[] = $json;

	//}
	
	}


	echo json_encode($json2);


?>
