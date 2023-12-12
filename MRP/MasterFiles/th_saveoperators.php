<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	require_once ('../../Model/helper.php');

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	//print_r($_REQUEST);
	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$desc = strtoupper($_REQUEST['desc']);
	
	if($code=="new"){
		
			if (!mysqli_query($con,"INSERT INTO mrp_operators (`compcode`,`cdesc`) values ('$company','$desc')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				$last_row = mysqli_insert_id($con);

				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$last_row','$preparedby',NOW(),'INSERTED','MES (EMPLOYEES)','$compname','Inserted New Record')");

				echo "True";
			}

			//print_r($_FILES);

			if(count($_FILES) != 0){
				$directory = "../../Components/assets/Employees_Sign/";
				if(!is_dir($directory)){
					mkdir($directory, 0777);
				}
				$directory .= "{$last_row}/";
				upload_image($_FILES, $directory);
				
				mysqli_query($con,"UPDATE mrp_operators set csign='".$directory.$_FILES['file-0']['name']."' Where compcode='".$company."' and nid='$last_row'");
			}

			
	}
	else{
	
			if (!mysqli_query($con,"UPDATE mrp_operators set cdesc = '$desc' where `compcode` = '$company' and `nid` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
										
				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$code','$preparedby',NOW(),'UPDATED','MES (EMPLOYEES)','$compname','Update Record')");
				
				echo "True";
			}

			if(count($_FILES) != 0){
				$directory = "../../Components/assets/Employees_Sign/";
				if(!is_dir($directory)){
					mkdir($directory, 0777);
				}
				$directory .= "{$code}/";
				upload_image($_FILES, $directory);
				
				mysqli_query($con,"UPDATE mrp_operators set csign='".$directory.$_FILES['file-0']['name']."' Where compcode='".$company."' and nid='$code'");
			}
		
	}

?>
