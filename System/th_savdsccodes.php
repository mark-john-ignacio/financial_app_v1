<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$desc = $_REQUEST['desc'];
	$acctid = $_REQUEST['acctid'];
		
	$result = mysqli_query ($con, "Select * From discounts_list where compcode='$company' and ccode='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO discounts_list (`compcode`,`ccode`,`cdesc`,`cacctno`,`cstatus`) values ('$company','$code','$desc','$acctid','ACTIVE')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','DISCOUNTS TABLE','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
							$olddesc = $row['cdesc'];
							$oldacctid = $row['cacctno'];
								
			}

		
			if (!mysqli_query($con,"UPDATE discounts_list set `cdesc` = '$desc', `cacctno` = '$acctid' where `compcode` = '$company' and `ccode` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc || $olddesc!=$acctid){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','DISCOUNTS TABLE','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
