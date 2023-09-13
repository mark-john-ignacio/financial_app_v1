<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	//get all existing SO
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select crefSO, citemno, nrefident From mrp_jo a where a.compcode='$company' and a.lcancelled=0");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

	$result = mysqli_query ($con, "select a.ctranno, b.dcutdate, a.citemno, a.nident, a.nqty from so_t a left join so b on a.compcode=b.compcode and a.ctranno=b.ctranno left join mrp_items_parameters c on a.compcode=c.compcode and a.citemno=c.citemno where a.compcode='$company' and lapproved=1 and ccode='".$_REQUEST['x']."' order by a.ctranno"); 

	$json = array();
	$json2 = array();
	$soarr = array();
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$inarray = "No";

			foreach(@$arrinv as $xcr){
				if($row['ctranno']==$xcr['crefSO'] && $row['citemno']==$xcr['citemno'] && $row['nident']==$xcr['nrefident']){
					$inarray = "Yes";
				}
			}
			
			
			if($inarray == "No"){

				if(!in_array($row['ctranno'],$soarr)){
					$soarr[] = $row['ctranno'];
					$json['csono'] = $row['ctranno'];
					$json['dcutdate'] = $row['dcutdate'];
					$json2[] = $json;
				}
	
			}

		}

	}

	
	
	echo json_encode($json2);


?>
