<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$typ = $_REQUEST['typ'];	

	if($typ=='post'){
		$_SESSION['pageid'] = "PP_post";
	}elseif($typ=='cancel'){
		$_SESSION['pageid'] = "PP_cancel";
	}

	include('../../include/accessinner.php');

	
	if($typ=='post'){
		$toupdate = "`lapproved` = 1";
		$tostat = "POSTED";
	}
	elseif($typ=='cancel'){
		$toupdate = "`lcancelled` = 1";
		$tostat = "CANCELLED";
	}

	if (!mysqli_query($con,"UPDATE `items_purch_cost` set ".$toupdate." where `compcode` = '$company' and `ctranno` = '$code'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	else{

			$compname = php_uname('n');
			$preparedby = $_SESSION['employeeid'];
			
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','$code','$preparedby',NOW(),'".$tostat."','ITEMS PURCH PRICE','$compname','".$tostat." Record')");
			

				if($typ=='post'){
					echo "Posted";
				}
				elseif($typ=='cancel'){
					echo "Cancelled";						
				}

	}
		

?>
