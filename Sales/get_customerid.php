<?php
require_once "../Connection/connection_string.php";
//$q = strtolower($_GET["q"]);

//if (!$q) return;

	//$sql = "select * from items where cpartno LIKE '%$q%'";

	//$rsd = mysqli_query($con,$sql);
	//if (!mysqli_query($con, $sql)) {
	//  printf("Errormessage: %s\n", mysqli_error($con));
	//} 
	
	//while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
	//	$cid = $rs['cpartno'];
	//	$cname = $rs['citemdesc'];
	//	$nprice = $rs['namount'];
	//	echo "$cid|$cname|$nprice\n";
	//}

//echo $sql;

 $c_id = $_REQUEST['c_id'];
 $result = mysqli_query($con,"SELECT A.*, B.cname as smaname FROM customers A left join salesman B on A.csman=B.ccode WHERE A.cempid = '$c_id' and A.cstatus = 'ACTIVE'"); 
 
 if (mysqli_num_rows($result)!=0) {
 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
 $c_prodnme = $row['cname']; 
 $c_limit = $row['nlimit']; 
 $cr_limit = 0;
 $c_pricever = $row['cpricever'];
 $c_chouseno = $row['chouseno'];
 $c_ccity = $row['ccity'];
 $c_cstate = $row['cstate'];
 $c_ccountry = $row['ccountry'];
 $c_czip = $row['czip']; 
 
 $c_smanid = $row['csman'];
 $c_smanme = $row['smaname'];
 
	if(!file_exists("../imgcust/".$c_id .".jpg")){
		$imgsrc = "../../images/emp.jpg";
	}
	else{
		$imgsrc = "../../imgcust/".$c_id .".jpg";
	}
	
 echo $c_prodnme.":".$c_pricever.":".$imgsrc.":".$c_limit.":".$cr_limit.":".$c_chouseno.":".$c_ccity.":".$c_cstate.":".$c_ccountry.":".$c_czip.":".$c_smanid.":".$c_smanme;
 }
 else{
	 echo "";
 }
 exit();  
 
?>
