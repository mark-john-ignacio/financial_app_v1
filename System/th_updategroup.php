<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	$company = $_SESSION['companyid'];
	$x = $_REQUEST['val'];
	$y = $_REQUEST['nme'];
	
	$result = mysqli_query ($con, "Select * From parameters where compcode='$company' and ccode='$y'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO parameters (`compcode`,`ccode`,`cvalue`,`norder`) values ('$company','$y','$x',1)")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				echo "True";
	
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$y','$preparedby',NOW(),'INSERTED','ITEM GROUPINGS','$compname','Inserted New Record')");

			}
	}
	else {
		
		if($x==""){

			if (!mysqli_query($con,"DELETE FROM parameters where `compcode` = '$company' and `ccode` = '$y'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$y','$preparedby',NOW(),'DELETED','ITEM GROUPINGS','$compname','Update Record')");
	
				echo "True";
		
			}


		}
		else{


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
							$valz = $row['cvalue'];
								
			}
		
			if (!mysqli_query($con,"UPDATE parameters set `cvalue` = '$x' where `compcode` = '$company' and `ccode` = '$y'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

				if($valz!=$x){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$y','$preparedby',NOW(),'UPDATED','ITEM GROUPINGS','$compname','Update Record')");
	
				}

				echo "True";
		
			}
		}
		
	}




?>
