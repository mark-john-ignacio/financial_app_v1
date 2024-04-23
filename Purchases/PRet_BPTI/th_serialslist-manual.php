<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.ctranno, a.citemno_nident, a.citemno, (a.nqty * a.nfactor)  as nqty, a.cunit, d.cunit as mainunit, a.clotsno, a.cpacklist, a.nlocation, b.cdesc as locadesc, IFNULL(c.nqty,0) as nqtyout
		from tblinvin a
		left join mrp_locations b on a.compcode=b.compcode and a.nlocation=b.nid
		left join 
			(
				Select citemno, nrefidentity, sum(nqty*nfactor) as nqty
				FROM tblinvout
				WHERE compcode='$company' and citemno = '".$_REQUEST['itm']."'
				Group By citemno, nrefidentity
			) c on a.nidentity=c.nrefidentity
		left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
		WHERE a.compcode='$company' and a.citemno = '".$_REQUEST['itm']."' and a.ctranno = '".$_REQUEST['itmxref']."'
		Order By b.cdesc";

		$result = mysqli_query ($con, $sql); 
		$rowcntr = 0;
		if (mysqli_num_rows($result)!=0) {
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$rowcntr++;
			
				//set ilan nlng natitira		 	
				$nqty = floatval($row['nqty']) - floatval($row['nqtyout']);	

				$json['ctranno'] = $row['ctranno'];
				$json['nrefidentity'] = $row['citemno_nident'];
				$json['citemno'] = $row['citemno'];
				$json['nqty'] = $nqty;
				$json['cunit'] = $row['cunit'];
				$json['mainunit'] = $row['mainunit'];
				$json['clotsno'] = $row['clotsno'];
				$json['cpacklist'] = $row['cpacklist'];
				$json['nlocation'] = $row['nlocation'];
				$json['locadesc'] = $row['locadesc'];

				$json2[] = $json;
			}
		}
	

	if($rowcntr>0){

		echo json_encode($json2);

	}else{
		echo "{}";
	}


?>
