<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$name = mysqli_real_escape_string($con, $_POST['txtcompanycom']);
	$desc = mysqli_real_escape_string($con, $_POST['txtcompanydesc']);
	$add = mysqli_real_escape_string($con, $_POST['txtcompanyadd']);
	$tin = mysqli_real_escape_string($con,  $_POST['txtcompanytin']);
	$vat = $_POST['selcompanyvat']; 
	$RDOC = $_POST['selcompanyrdo']; 
	$BUSTY = $_POST['selcompanybust']; 
	$email = mysqli_real_escape_string($con, $_POST['txtcompanyemail']);
	$cpnum = mysqli_real_escape_string($con, $_POST['txtcompanycpnum']);
	$czip = $_POST['txtcompanyzip'];
	$ptucode = $_POST['ptucode'];
	$ptudate = $_POST['ptudate'];
	
	$bir_sig_name = mysqli_real_escape_string($con, $_POST['bir_sig_name']);
	$bir_sig_role = mysqli_real_escape_string($con, $_POST['bir_sig_role']);
	$bir_sig_tin = mysqli_real_escape_string($con, $_POST['bir_sig_tin']);
	$bir_sig_email = mysqli_real_escape_string($con,  $_POST['bir_sig_email']);
	$bir_sig_numbr = mysqli_real_escape_string($con,  $_POST['bir_sig_phone']);

	if (!mysqli_query($con,"UPDATE company set `compname` = '$name', `compdesc` = '$desc', `compadd` = '$add', `comptin` = '$tin', `compvat` = '$vat', `compzip` = '$czip', `email` = '$email', `cpnum` = '$cpnum', `ptucode` = '$ptucode', `ptudate` = '$ptudate', `comprdo` = '$RDOC', `compbustype` = '$BUSTY', `bir_sig_name` = '$bir_sig_name', `bir_sig_role` = '$bir_sig_role', `bir_sig_tin` = '$bir_sig_tin', `bir_sig_phone` = '$bir_sig_numbr', `bir_sig_email` = '$bir_sig_email' where `compcode` = '$company'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	else{
		
//		if(isset($_FILES['img_sign']['name'])){
//
//			/* Getting file name */
//			$filename = $_FILES['img_sign']['name'];
//
//			/* Location */
//			$location = "../bir_forms/sign/".$filename;
//			$imageFileType = pathinfo($location,PATHINFO_EXTENSION);
//			$imageFileType = strtolower($imageFileType);
//
//			/* Valid extensions */
//			$valid_extensions = array("jpg","jpeg","png");
//
//			$response = 0;
//			/* Check file extension */
//			if(in_array(strtolower($imageFileType), $valid_extensions)) {
//			   /* Upload file */
//			   if(move_uploaded_file($_FILES['img_sign']['tmp_name'],$location)){
//				  $response = $location;
//			   }
//
//					 $company = $_SESSION['companyid'];
//
//			   mysqli_query($con,"UPDATE company set `bir_sig_sign` = '$location' where `compcode`='$company'");
//			}
//
//			//echo $response;
//			//exit;
//		}

		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$company','$preparedby',NOW(),'UPDATED','COMPANY DETAILS','$compname','Updated Company Detail')");
			
		echo "True";
	}
		
?>
