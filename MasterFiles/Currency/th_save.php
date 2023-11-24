<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];

	$xid = $_REQUEST['id'];
	$xsymbol = $_REQUEST['symbol'];
	$xcountry = $_REQUEST['country'];
	$xunit = $_REQUEST['unit'];
	$xrate = $_REQUEST['rate'];
	
	if($xid=="new"){
		
			if (!mysqli_query($con,"INSERT INTO currency_rate (`compcode`,`country`,`unit`,`symbol`,`rate`,`cstatus`) values ('$company','$xcountry','$xunit','$xsymbol','$xrate','ACTIVE')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$xsymbol','$preparedby',NOW(),'INSERTED','CURRENCY','$compname','Inserted New Record')");


				echo "True";
			}
	}
	else {

			$result = mysqli_query($con,"SELECT * FROM `currency_rate` where compcode='$company' and id='$xid'"); 
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
				$oldcountry = $row['country'];
				$oldunit = $row['unit'];
				$oldsymbol = $row['symbol'];
				$oldrate = $row['rate'];		

			}

		
			if (!mysqli_query($con,"UPDATE currency_rate set country = '$xcountry',`unit` = '$xunit',`rate` = '$xrate' where `compcode` = '$company' and id='$xid'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				
				if($oldcountry!=$xcountry || $oldunit!=$xunit || $oldrate!=$xrate){
										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$oldsymbol','$preparedby',NOW(),'UPDATED','CURRENCY','$compname','Update Record')");
	
				}

				if($oldrate!=$xrate){
					mysqli_query($con,"INSERT INTO currency_rate_history(`compcode`, `currency_rate_id`, `old_rate`, `new_rate`, `ddateupdate`) 
					values('$company','$xid','$oldrate','$xrate','".date("Y-m-d")."')");
				}
				
				echo "True";
			}
		
	}

?>
