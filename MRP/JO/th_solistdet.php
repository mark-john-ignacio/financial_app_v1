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

	$result = mysqli_query ($con, "select a.ctranno, a.citemno, a.cunit, a.nqty, a.nident, a.nqty, IFNULL(c.nworkhrs,0) as nworkhrs, IFNULL(c.nsetuptime,0) as nsetuptime, IFNULL(c.ncycletime,0) as ncycletime, d.citemdesc from so_t a left join so b on a.compcode=b.compcode and a.ctranno=b.ctranno left join mrp_items_parameters c on a.compcode=c.compcode and a.citemno=c.citemno left join items d on a.compcode=d.compcode and a.citemno=d.cpartno where a.compcode='$company' and b.lapproved=1 and a.ctranno='".$_REQUEST['x']."' order by a.ctranno"); 

	$json = array();
	$json2 = array();
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$inarray = "No";

			foreach(@$arrinv as $xcr){
				if($row['ctranno']==$xcr['crefSO'] && $rsc['citemno']==$xcr['citemno'] && $rsc['nident']==$xcr['nrefident']){
					$inarray = "Yes";
				}
			}
			
			
			if($inarray == "No"){

				$json['csono'] = $row['ctranno'];
				$json['nident'] = $row['nident'];
				$json['citemno'] = $row['citemno'];
				$json['cdesc'] = $row['citemdesc'];
				$json['cunit'] = $row['cunit'];
				$json['nqty'] = number_format($row['nqty'],2);
				$json['nworkhrs'] = number_format($row['nworkhrs'],2);
				$json['nsetuptime'] = number_format($row['nsetuptime'],2);
				$json['ncycletime'] = number_format($row['ncycletime'],2);
				$json2[] = $json;
	
			}

		}

	}

	
	
	echo json_encode($json2);


?>
