<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from customers WHERE compcode='$company' and cempid = '".$_REQUEST['id']."'"); 

	if(mysqli_num_rows($result)==0){
		echo "True";
	}
	else {

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
				 $mem = $row['cGroup2'];
				 $dept = $row['cGroup1'];
				 $yrs = $row['dsince'];
				 
				 //echo $yrs."<br>";
		
			}
		
		//get membership
			$getmem = mysqli_query ($con, "select * from customers_groups WHERE compcode='$company' and ccode = '$mem'"); 
		
			while($rowmem = mysqli_fetch_array($getmem, MYSQLI_ASSOC)){
				
				 $memdesc = $rowmem['cgroupdesc'];
		
			}
		
		//get department
			$getdept = mysqli_query ($con, "select * from customers_groups WHERE compcode='$company' and ccode = '$dept'"); 
		
			while($rowdept = mysqli_fetch_array($getdept, MYSQLI_ASSOC)){
				
				 $deptdesc = $rowdept['cgroupdesc'];
		
			}
		
		//get yrs
			$date1 = date_format(date_create($yrs), "m/d/Y");
			$date2 = date("m/d/Y");

			$datetime1 = new DateTime($date1);
			$datetime2 = new DateTime($date2);
			$interval = $datetime1->diff($datetime2);
			$yrdesc  = $interval->format('%y years %m months and %d days');
			
		//get capital
			if($mem=="MEMBER"){
				$ncap = 0;
			}
			else{
				$ncap = "NA";
			}
			
			
			echo $mem."|".$memdesc."|".$dept."|".$deptdesc."|".$yrdesc."|".$ncap;
			
	}

?>
