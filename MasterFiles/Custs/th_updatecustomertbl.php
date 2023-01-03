<?php
if(!isset($_SESSION)){
session_start();
}

	require_once "../../Connection/connection_string.php";

	require_once "../../Connection/connection_HRIS.php";


	$company = $_SESSION['companyid'];
	$cntr = 0;
	
	$result = mysqli_query ($conHRIS, "SELECT emp_id, last_name, first_name, middle_name,emp_status FROM employee_personal_info where emp_status = 'Active'"); 

		mysqli_query($con, "DELETE FROM `customers_update");		
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$ccode = $row['emp_id'];
		$cname = $row['last_name'].", ".$row['first_name']." ".$row['middle_name'];
		$cstatus = strtoupper($row['emp_status']);
		
			$SalesCode =  $_REQUEST['acctcode'];
			$CustTyp = $_REQUEST['ctype'];
			$CustCls = $_REQUEST['cclass'];
			$CreditLimit = $_REQUEST['climit'];
			$PriceVer = $_REQUEST['pricever']; 
			$VatType = $_REQUEST['bustype']; 
			$Terms = $_REQUEST['terms']; 
			
			$Country = "Philippines";
			
			
			$cname = strtoupper(mysqli_real_escape_string($con, $cname));
	
			$result2 = mysqli_query ($con, "SELECT * FROM customers where cempid = '$ccode'"); 
			if(mysqli_num_rows($result2)==0){
				
				 $cntr = $cntr + 1;
				
				 if (!mysqli_query($con, "INSERT INTO `customers`(`compcode`, `cempid`, `cname`, `cacctcodesales`,`ccustomertype`, `ccustomerclass`, `cpricever`, `cvattype`, `cterms`, `ccountry`, `cstatus`, `nlimit`) VALUES ('$company', '$ccode', '$cname', '$SalesCode','$CustTyp', '$CustCls', '$PriceVer', '$VatType', '$Terms', '$Country', '$cstatus', '$CreditLimit')")) {
						printf("Errormessage1: %s\n", mysqli_error($con));
				  } 
			
			}
			else{
				
				
				if (!mysqli_query($con, "Update `customers` set `cstatus` = '$cstatus' where `compcode` = '$company' and `cempid` = '$ccode' ")) {
						printf("Errormessage2: %s\n", mysqli_error($con));
				  } 
				
			}

	}

	echo "DONE";



?>
