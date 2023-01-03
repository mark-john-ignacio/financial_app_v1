<?php
require_once "../Connection/connection_string.php";

 $c_id = $_POST['c_id'];
 $result = mysqli_query($con,"SELECT * FROM customers WHERE cempid = '$c_id'"); 
 if (mysqli_num_rows($result)!=0) {
 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
 
 $c_prodnme = $all_course_data['cname']; 	
	
 echo $c_prodnme;
 }
 else{
	 echo "";
 }
 exit();  
 
?>
