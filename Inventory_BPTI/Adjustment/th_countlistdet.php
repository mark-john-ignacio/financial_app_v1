<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$json2 = array();

			$sqlitm = "select A.ctranno, A.nidentity, A.citemno, B.citemdesc, A.cunit, A.nqty
			from invcount_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno = '".$_REQUEST['x']."'";

		
			$rsditm = mysqli_query($con,$sqlitm);
			if(mysqli_num_rows($rsditm)>=1){
				
					while($row = mysqli_fetch_array($rsditm, MYSQLI_ASSOC)){
	
						$json['ctranno'] = $row['ctranno'];
						$json['nidentity'] = $row['nidentity'];
						$json['citemno'] = $row['citemno'];
						$json['citemdesc'] = $row['citemdesc'];	
						$json['cunit'] = $row['cunit'];
						$json['nqty'] = $row['nqty'];				
						$json2[] = $json;
				
					}
	
			
			}
	

	echo json_encode($json2);


?>
