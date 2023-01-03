<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$desc = $_REQUEST['desc'];
	$typ = $_REQUEST['typ'];

	if(isset($_REQUEST['chkSI'])){
		$chkSIAllow = $_REQUEST['chkSI'];
	}else{
		$chkSIAllow = 0;
	}
		
	$result = mysqli_query ($con, "Select * From groupings where compcode='$company' and ccode='$code' and ctype='$typ'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO groupings (`compcode`,`ccode`,`cdesc`,`ctype`,`nallow`) values ('$company','$code','$desc','$typ','$chkSIAllow')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','ITEM (".$typ.")','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
							$olddesc = $row['cdesc'];
								
			}

		
			if (!mysqli_query($con,"UPDATE groupings set cdesc = '$desc',`nallow` = '$chkSIAllow' where `compcode` = '$company' and `ccode` = '$code' and ctype='$typ'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','ITEM (".$typ.")','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
