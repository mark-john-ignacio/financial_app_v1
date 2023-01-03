<?php
require_once "../Connection/connection_string.php";
 $c_id = $_REQUEST['code'];
 $ddate = $_REQUEST['date'];

 //get value if Daily or Per Cutoff ang reset ng Limit
 $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='POSCLMT'");
	 if (mysqli_num_rows($result)!=0) {
		 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 $nvalue = $all_course_data['cvalue']; 
			
	 //echo $ngross;
	 }
	 else{
		 $nvalue = "Daily";
		 //echo 0;
	 }
 
 if($nvalue=="Cutoff"){
	 $result = mysqli_query($con,"SELECT * FROM pos_cutoff Order By postdate desc");
	 if (mysqli_num_rows($result)!=0) {
		 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 $ndtefrom= $all_course_data['ddatefrom'];
		 $ndteto= $all_course_data['ddateto']; 
			
	 //echo $ngross;
	 }

	 $sql = "SELECT ifnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate between '$ndtefrom' and '$ndteto' and lcancelled=0"; 
	//echo "SELECT ifnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate between '$ndtefrom' and '$ndteto'";
 }
 
 else{
	 
	 $sql = "SELECT ifnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate=STR_TO_DATE('$ddate', '%m/%d/%Y')"; 
	// echo "SELECT ifnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate=STR_TO_DATE('$ddate', '%m/%d/%Y')";

 }
 //echo "SELECT isnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate=STR_TO_DATE('$ddate', '%m/%d/%Y')"; 
 //echo $sql;
 
 if (!mysqli_query($con, $sql)) {
	printf("Errormessage: %s\n", mysqli_error($con));
 } 


 $result1 =  mysqli_query($con,$sql);
 if (mysqli_num_rows($result1)!=0) {
	 $all_course_data = mysqli_fetch_array($result1, MYSQLI_ASSOC);
	 
	 $ngross = $all_course_data['ngross']; 
		
	 echo $ngross;
 }
 else{
	 echo 0;
 }
 
 exit();  
 
?>
