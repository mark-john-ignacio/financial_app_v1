<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


	$company = $_SESSION['companyid'];
	$y = $_REQUEST['id'];
	$chkno = $_REQUEST['chkno'];
	$rem = $_REQUEST['rem'];
	$ctyp = $_REQUEST['xtyp']; 
	$authcode = $_REQUEST['authcode']; 

	$cnewchk = (float)$chkno + 1;

	$sql = "Update bank_check set ccurrentcheck='$cnewchk' where compcode='$company' and ccode='$y' and nidentity ='".$_REQUEST['chkbkno']."'"; 

	if (!mysqli_query($con, $sql)) {
		if(mysqli_error($con)!=""){
			echo "Error: ".mysqli_error($con);
		}
	}
	else{
		echo $cnewchk;

		if($ctyp=="void"){

			$sql2 = "INSERT INTO bank_voids(`compcode`,`cbankcode`,`ccheckno`,`cremarks`,`ddate`,`cauthcode`) VALUES('$company','$y','$chkno','$rem',NOW(),'$authcode')"; 

		}elseif($ctyp=="reserve"){

			$sql2 = "INSERT INTO bank_reserves(`compcode`,`cbankcode`,`ccheckno`,`cremarks`,`ddate`,`cauthcode`) VALUES('$company','$y','$chkno','$rem',NOW(),'$authcode')";

		}
		
		//insert in void table
		
		if (!mysqli_query($con, $sql2)) {
			if(mysqli_error($con)!=""){
				echo "Error: ".mysqli_error($con);

				//."<br>"."INSERT INTO bank_voids(`compcode`,`cbankcode`,`ccheckno`,`cremarks`,`ddate`) VALUES('$company','$y','$chkno','$rem',NOW())"
			}
		}
			
		
	}
	
?>
