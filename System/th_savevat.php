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
	$rem = $_REQUEST['rem'];
	$lcomp = $_REQUEST['lcomp'];
		
	$result = mysqli_query ($con, "Select * From vatcode where compcode='$company' and cvatcode='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO vatcode (`compcode`,`cvatcode`,`cvatdesc`,`cremarks`,`lcompute`) values ('$company','$code','$desc','$rem',$lcomp)")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','VAT TABLE','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
							$olddesc = $row['cvatdesc'];
							$oldrem = $row['cremarks'];
							$oldcomp = $row['lcompute'];
								
			}

		
			if (!mysqli_query($con,"UPDATE vatcode set `cvatdesc` = '$desc', `cremarks` = '$rem', `lcompute` = $lcomp where `compcode` = '$company' and `cvatcode` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc || $oldrem!=$rem || $oldcomp!=$lcomp){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','VAT TABLE','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
