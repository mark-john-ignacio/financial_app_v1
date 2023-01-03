<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


	$company = $_SESSION['companyid'];
	$y = $_REQUEST['id'];
	$chkno = $_REQUEST['chkno'];
	$rem = $_REQUEST['rem'];
	
	$cnewchk = (float)$chkno + 1;
	
	$sql = "Update bank set cnxtchkno='$cnewchk' where compcode='$company' and ccode='$y'"; 

	if (!mysqli_query($con, $sql)) {
		if(mysqli_error($con)!=""){
			echo "Error: ".mysqli_error($con);
		}
	}
	else{
		echo $cnewchk;
		
		//insert in void table
		$sql2 = "INSERT INTO bank_voids(`compcode`,`cbankcode`,`ccheckno`,`cremarks`,`ddate`) VALUES('$company','$y','$chkno','$rem',NOW())"; 
		
		if (!mysqli_query($con, $sql2)) {
			if(mysqli_error($con)!=""){
				echo "Error: ".mysqli_error($con)."<br>"."INSERT IINTO bank_voids(`compcode`,`cbankcode`,`ccheckno`,`cremarks`,`ddate`) VALUES('$company','$y','$chkno','$rem',NOW())";
			}
		}
			
		
	}
	
?>
