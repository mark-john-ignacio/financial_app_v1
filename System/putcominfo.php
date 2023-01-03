<?php
if(!isset($_SESSION)){
session_start();
}

require_once "../Connection/connection_string.php";

 $c_id = mysqli_real_escape_string($con, strtoupper($_REQUEST['nme']));
 $c_desc = mysqli_real_escape_string($con, strtoupper($_REQUEST['desc']));
 $c_add = mysqli_real_escape_string($con, strtoupper($_REQUEST['add']));
 $c_tin = mysqli_real_escape_string($con, strtoupper($_REQUEST['tin']));
 $code = $_SESSION['companyid'];
 
 
 	mysqli_query($con,"Update company set compname ='$c_id', compdesc ='$c_desc', compadd ='$c_add', comptin ='$c_tin' Where compcode='$code'");

 
 
 
 $result = mysqli_query($con,"SELECT * FROM `company` WHERE compcode='$code'"); 
 
 //echo "SELECT isnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate=STR_TO_DATE('$ddate', '%m/%d/%Y')"; 
 if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
	// $nvalue = $all_course_data['cvalue']; 
		
	 echo "Company Information Updated";
 }
 else{
	 echo "NO VALUE";
 }
 exit();  
 
?>
