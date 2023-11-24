<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if($_POST['y']!=""){
		$qry = "and ctranno not in ('". str_replace(",","','",$_POST['y']) . "')";
	}
	else{
		$qry = "";
	}

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
where compcode='$company' and ccode='".$_POST['x']."' and lapproved=1
and ctranno not in (Select crefno from apv_d) ".$qry." order by dreceived desc";

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
	
			 $json['crrno'] = $row['ctranno'];
			 $json['ngross'] = number_format($ngrossamt,2,'.','');
			 $json['ddate'] = $row['dreceived'];
			 $json['cremarks'] = $row['cremarks'];
			 $json2[] = $json;
	
		}
	}
	else{
			 $json['crrno'] = "NONE";
			 $json['ngross'] = "";
			 $json['ddate'] = "";
			 $json['cremarks'] = "";
			  $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
