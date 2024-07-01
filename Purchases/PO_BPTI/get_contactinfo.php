<?php
require_once "../../Connection/connection_string.php";
//$q = strtolower($_GET["q"]);

 $c_id = $_REQUEST['c_id'];
 $result = mysqli_query($con,"SELECT * FROM customers_contacts WHERE ccode = '$c_id'"); 
 if (mysqli_num_rows($result)==1) {
	 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
	 $cname = $row['cname']; 
	 $cdesig = $row['cdesignation']; 
	 $cdept = $row['cdept'];
	 $cemail = $row['cemail'];
	 $cphone = $row['cphone'];
	 $cmobile = $row['cmobile'];
	 
		
	 echo $cname.":".$cdesig.":".$cdept.":".$cemail;
 }elseif (mysqli_num_rows($result)>1) {
 	 echo "Multi";
 }
 else{
	 echo "";
 }
 exit();  
 
?>
