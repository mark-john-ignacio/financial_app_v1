<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$sqlq = "select a.ctranno, a.ngross, IFNULL(b.namount,0) as namount, a.dreceived, a.cremarks
from receive a
left join
	(
		select x.creference, sum(x.namount) as namount
        from purchreturn_t x
        left join purchreturn y on x.compcode=y.compcode and x.ctranno=y.ctranno
        where y.compcode='$company' and y.lapproved=1
        group by x.creference
    ) b on a.ctranno=b.creference
where a.compcode='$company' and a.ctranno like '%".$_REQUEST['query']."%' and a.lapproved=1
and a.ctranno not in (Select crefno from apv_d) order by dreceived desc";

	$result = mysqli_query ($con, $sqlq); 

	//$json2 = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
		if($row['namount']!=0){
			$ngrossamt = (float)$row['ngross'] - (float)$row['namount'];
		}
		else{
			$ngrossamt = $row['ngross'];
		}
		
			 $json['id'] = $row['ctranno'];
			 $json['value'] = number_format($ngrossamt,2,'.','');
			 $json['label'] = $row['dreceived'];
			 $json2[] = $json;
	
		}
	}
	else{
			 $json['id'] = "NO AVAILABLE WRR";
			 $json['value'] = "";
			 $json['label'] = "";
			  $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
