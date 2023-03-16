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
		
	$result = mysqli_query ($con, "Select * From contacts_types where compcode='$company' and cid='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO contacts_types (`compcode`,`cdesc`,`cstatus`) values ('$company','$desc','ACTIVE')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','CONTACTS DETAILS','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
				$olddesc = $row['cdesc'];
								
			}

		
			if (!mysqli_query($con,"UPDATE contacts_types set `cdesc` = '$desc' where `compcode` = '$company' and `cid` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc || $olddesc!=$acctid){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','CONTACTS DETAILS','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
