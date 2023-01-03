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
 $result = mysqli_query($con,"SELECT * FROM suppliers WHERE ccode = '$c_id'"); 
 if (mysqli_num_rows($result)!=0) {
 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
 $c_prodnme = $row['cname']; 
 
 	
	if(!file_exists("../imgcust/".$c_id .".jpg")){
		$imgsrc = "../../images/emp.jpg";
	}
	else{
		$imgsrc = "../../imgcust/".$c_id .".jpg";
	}

	
	
 echo $c_prodnme.":".":".$imgsrc;
 }
 else{
	 echo "";
 }
 exit();  
 
?>
