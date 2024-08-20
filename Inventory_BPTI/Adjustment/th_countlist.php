<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$json2 = array();

			$arrwithref = array();
			$sqlexistng = mysqli_query($con,"select DISTINCT creference from adjustments_t A left join adjustments B on A.compcode=B.compcode and A.ctranno=B.ctranno	where A.compcode='$company' and B.lcancelled=0 and B.section_nid='".$_REQUEST['x']."' and B.ctype='ending'");
			if(mysqli_num_rows($sqlexistng)>=1){
				while($row = mysqli_fetch_array($sqlexistng, MYSQLI_ASSOC)){
					$arrwithref[] = $row['creference'];
				}
			}

			$sqlitm = "select ctranno, dcutdate
			from invcount A where A.compcode='$company' and A.section_nid='".$_REQUEST['x']."' and A.lapproved=1 and A.ctype='ending' and A.ctranno not in ('".implode("','", $arrwithref)."') order by dcutdate DESC";
		
			$rsditm = mysqli_query($con,$sqlitm);
			if(mysqli_num_rows($rsditm)>=1){
				
					while($row = mysqli_fetch_array($rsditm, MYSQLI_ASSOC)){
	
						$json['ctranno'] = $row['ctranno'];
						$json['dcutdate'] = $row['dcutdate'];					
						$json2[] = $json;
				
					}
	
			
			}
	

	echo json_encode($json2);


?>
