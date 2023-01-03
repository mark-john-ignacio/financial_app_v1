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
	$divs = $_REQUEST['divs'];
	$vbase = $_REQUEST['vbase'];
		
	$result = mysqli_query ($con, "Select * From wtaxcodes where compcode='$company' and ctaxcode='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO wtaxcodes (`compcode`,`ctaxcode`,`nrate`,`nratedivisor`,`cbase`,`cdesc`,`cstatus`) values ('$company','$code','$rate','$divs','$vbase','$desc','ACTIVE')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','EWT TABLE','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
							$olddesc = $row['cdesc'];
							$olddivior = $row['nratedivisor'];
							$oldbase = $row['cbase'];
							$oldrate = $row['nrate'];
								
			}

		
			if (!mysqli_query($con,"UPDATE wtaxcodes set `cdesc` = '$desc', `nratedivisor` = '$divs', `cbase` = '$vbase', `nrate` = $lcomp where `compcode` = '$company' and `ctaxcode` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc || $olddivior!=$divs || $oldbase!=$vbase || $oldrate!=$rate){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','EWT TABLE','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
