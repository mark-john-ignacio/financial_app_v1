<?php
if(!isset($_SESSION)){
session_start();
}

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

 $c_id = $_POST['c_id'];
 $result = mysqli_query($con,"select A.*, B.cunit as pounit, B.nfactor from items A left join items_factor B on A.compcode=B.compcode and A.cpartno=B.cpartno where A.cpartno = '$c_id'"); 
 
 while($all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
 
 $c_prodid = $all_course_data['cpartno'];
 $c_prodnme = $all_course_data['citemdesc']; 
 $c_price = $all_course_data['npurchcost']; 
 $c_factor = $all_course_data['nfactor']; 
 $c_unit = $all_course_data['pounit']; 
 
				if(is_null($c_factor)){
					$c_factor = 1;
					$c_unit = $all_course_data['cunit'];
				}

 $npricefin = $c_price * $c_factor;

 echo $c_prodid.",".$c_prodnme.",".$npricefin.",".$c_unit.",".$c_factor;
 exit();  
 
 }
?>
