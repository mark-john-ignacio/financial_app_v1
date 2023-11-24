<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");
		
		$sql = "select a.cpartno, a.cscancode, a.citemdesc, a.nretailcost, a.npurchcost, a.cunit, a.cstatus, a.ltaxinc, ifnull(c.nqty,0) as nqty
		from items a 
		left join
			(
				select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
				From tblinventory a
				right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
				where a.compcode='$company' and  a.dcutdate <= '$date1' and d.cscancode = '".$_REQUEST['x']."'
			 ) c on a.cpartno=c.citemno
		WHERE a.compcode='$company' and a.cscancode = '".$_REQUEST['x']."'";

//	echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	if(mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){


			$json['citemno'] = $row['cpartno'];
			$json['cscancode'] = $row['cscancode'];
			$json['cdesc'] = $row['citemdesc'];
			$json['cunit'] = $row['cunit'];
			$json['ncost'] = $row['npurchcost'];
			$json['nprice'] = $row['nretailcost'];
			$json['nqty'] = $row['nqty'];

			if((float)$row['nqty']<=0){
				 $json['citemno'] = "";
				 $json['cscancode'] = "";
				 $json['cdesc'] = "No more stock available!";
				 $json['cunit'] = "";
				 $json['ncost'] = "";
				 $json['nprice'] = "";
				 $json['nqty'] = "";
			}

			if($row['cstatus']=="INACTIVE"){
				 $json['citemno'] = "";
				  $json['cscancode'] = "";
				 $json['cdesc'] = "Item is currently inactive!";
				 $json['cunit'] = "";
				 $json['ncost'] = "";
				 $json['nprice'] = "";
				 $json['nqty'] = "";
			}
			
			 $json2[] = $json;
	
		}
	
	}
	else{
		 $json['citemno'] = "";
		  $json['cscancode'] = "";
     	 $json['cdesc'] = "Item did not exist!";
		 $json['cunit'] = "";
		 $json['ncost'] = "";
		 $json['nprice'] = "";
		 $json['nqty'] = "";
		 $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
