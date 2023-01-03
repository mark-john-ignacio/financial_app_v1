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
	$grp = $_REQUEST['grp'];
		
	$result = mysqli_query ($con, "Select * From items_groups where compcode='$company' and ccode='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO items_groups (`compcode`,`cgroupno`,`ccode`,`cgroupdesc`) values ('$company','$grp','$code','$desc')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'INSERTED','ITEM GROUPINGS','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {


			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
							$olddesc = $row['cgroupdesc'];
							$oldgrp = $row['cgroupno'];
								
			}

		
			if (!mysqli_query($con,"UPDATE items_groups set `cgroupno` = '$grp', cgroupdesc = '$desc' where `compcode` = '$company' and `ccode` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($olddesc!=$desc || $oldgrp!=$grp){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','ITEM GROUPINGS','$compname','Update Record')");
	
				}
				
				echo "True";
			}
		
	}

?>
