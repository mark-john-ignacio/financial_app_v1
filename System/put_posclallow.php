<?php
require_once "../Connection/connection_string.php";
 $c_id = $_REQUEST['code'];
 
 
 	mysqli_query($con,"Update parameters set cvalue='$c_id' Where ccode='ABOVECL'");

 
 
 
 $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ABOVECL'"); 
 
 //echo "SELECT isnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate=STR_TO_DATE('$ddate', '%m/%d/%Y')"; 
 if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
	 $nvalue = $all_course_data['cvalue']; 
		
	 echo "Value changed to ".$nvalue;
 }
 else{
	 echo "NO VALUE";
 }
 exit();  
 
?>
