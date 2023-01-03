<?php
if(!isset($_SESSION)){
session_start();
}

require_once "../Connection/connection_string.php";
 $c_dte1 = $_REQUEST['dte1'];
 $c_dte2 = $_REQUEST['dte2'];
 
 $preparedby = $_SESSION['employeeid'];
 $company = $_SESSION['companyid'];
 
 
 //echo "SELECT * from FROM `pos_cutoff` Where ddatefrom <= DATE_FORMAT('$c_dte1','%m/%d/%Y') and ddateto >= DATE_FORMAT('$c_dte1','%m/%d/%Y')";
 
  $result = mysqli_query($con,"SELECT * FROM `pos_cutoff` Where '$c_dte1' between DATE_FORMAT(ddatefrom,'%m/%d/%Y')
and DATE_FORMAT(ddateto,'%m/%d/%Y')"); 
 
 //echo "SELECT isnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate=STR_TO_DATE('$ddate', '%m/%d/%Y')"; 
 if (mysqli_num_rows($result)==0) {

 	mysqli_query($con,"INSERT INTO pos_cutoff (`compcode`,`postdate`,`userid`,`ddatefrom`,`ddateto`) Value('$company',NOW(),'$preparedby',STR_TO_DATE('$c_dte1', '%m/%d/%Y'),STR_TO_DATE('$c_dte2', '%m/%d/%Y'))");



 
 
	 $result = mysqli_query($con,"SELECT DATE_FORMAT(ddatefrom,'%m/%d/%Y') as ddatefrom, DATE_FORMAT(ddateto,'%m/%d/%Y') as ddateto FROM `pos_cutoff` Order By postdate Desc"); 
	 
	 //echo "SELECT isnull(SUM(ngross),0) as ngross FROM `sales` WHERE ccode='$c_id' and dcutdate=STR_TO_DATE('$ddate', '%m/%d/%Y')"; 
	 if (mysqli_num_rows($result)!=0) {
		 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 $c_datefr = $all_course_data['ddatefrom']; 
		 $c_dateto = $all_course_data['ddateto']; 
			
		 echo $c_datefr." TO ".$c_dateto;
	 }
	 else{
		 echo "NO VALUE";
	 }
 }
 else{
 		echo "Date range is between a Posted Cutoff!";
 }
 
 exit();  
 
?>
