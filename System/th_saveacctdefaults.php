<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
 
	$company = $_SESSION['companyid'];
	
	$code = $_REQUEST['ccode'];
	$desc = $_REQUEST['cdesc'];
	$acctid = $_REQUEST['cacctid'];
	$nidentz = $_REQUEST['trancode'];
		
	$result = mysqli_query ($con, "Select * From accounts_default where compcode='$company' and ccode='$code' and nidentity='$nidentz'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO accounts_default (`compcode`,`ccode`,`cdescription`,`cacctno`) values ('$company','$code','$desc',$acctid)")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','DEF ACCT CODE','$compname','Inserted New Record')");


					echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					$olddesc = $row['cdescription'];
					$oldacctid = $row['cacctno'];
								
			}

		
			if (!mysqli_query($con,"UPDATE accounts_default set `cdescription` = '$desc', cacctno = '$acctid' where `compcode` = '$company' and `ccode` = '$code' and nidentity='$nidentz'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc || $oldacctid!=$acctid){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','DEF ACCT CODE','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
