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
	$rate = $_REQUEST['rate'];
		
	$result = mysqli_query ($con, "Select * From taxcode where compcode='$company' and ctaxcode='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO taxcode (`compcode`,`ctaxcode`,`ctaxdesc`,`nrate`) values ('$company','$code','$desc',$rate)")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','TAX TABLE','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
							$olddesc = $row['ctaxdesc'];
							$oldrate = $row['nrate'];
								
			}

		
			if (!mysqli_query($con,"UPDATE taxcode set `ctaxdesc` = '$desc', nrate = '$rate' where `compcode` = '$company' and `ctaxcode` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc || $oldrate!=$rate){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','TAX TABLE','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
