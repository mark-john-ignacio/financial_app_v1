<?php
	if(!isset($_SESSION)){
		session_start();
	}

	require_once "../Connection/connection_string.php";
	$company = $_SESSION['companyid'];

	$c_mainid = $_REQUEST['m_id'];
	$c_id = $_REQUEST['c_id'];

	$result = mysqli_query($con,"SELECT * FROM customers_secondary WHERE compcode='".$company."' and cmaincode='$c_mainid' and ccode = '$c_id' and cstatus = 'ACTIVE'"); 
	
	if (mysqli_num_rows($result)!=0) {
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$ccode = $row['ccode']; 
		$cname = $row['cname']; 
		$caddr = $row['caddress']; 
		$ccity = $row['ccity']; 
		$cstat = $row['cstate']; 
		$ccntr = $row['ccountry']; 
		$czips = $row['czip']; 
		$ctins = $row['ctin']; 
		
		echo $ccode.":".$cname.":".$caddr.":".$ccity.":".$cstat.":".$ccntr.":".$czips.":".$ctins;
	}
	else{
		echo "";
	}
	exit();  
 
?>
