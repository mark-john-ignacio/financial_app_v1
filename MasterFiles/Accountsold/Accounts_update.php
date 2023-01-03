<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');

$company = $_SESSION['companyid'];

	$acctid = $_POST['acctid2'];
	$acctidcode = $_POST['acctidcode'];
	$cdesc = $_POST['cdesc2'];
	$seltyp = $_POST['seltyp2'];
	$selcat = $_POST['selcat2']; 
	$selmain = $_POST['selmain2']; 
	$selfingrp = $_POST['cfingrp2'];
	
	if(isset($_POST['chkcontra2'])){
		$contraval=1;
		
		$macon = explode(":",$_POST['selcontra']);
		$conacct = "'".$macon[0]."'";
	}
	else{
		$contraval=0;
		$conacct = "NULL";
	}
	

		$mainacct = explode(":",$selmain);
		//echo $mainacct[0];

		$result = mysqli_query ($con, "select * from accounts WHERE compcode='$company' and cacctid = '".$mainacct[0]."'"); 

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						
			$selmain = "'".$row['cacctno']."'";
			$sellvl = intval($row['nlevel']) + 1;
		}


	if (!mysqli_query($con, "Update accounts set cacctid='$acctid', cacctdesc='$cdesc', mainacct=$selmain, ccategory='$selcat', ctype='$seltyp', nlevel=$sellvl, cFinGroup='$selfingrp', lcontra=$contraval, cconacct=$conacct where compcode='$company' and cacctno='$acctidcode'")) {
		
		printf("Update accounts set cacctid='$acctid', cacctdesc='$cdesc', mainacct=$selmain, ccategory='$selcat', ctype='$seltyp', nlevel=$sellvl, cFinGroup='$selfingrp', lcontra=$contraval, cconacct=$conacct where compcode='$company' and cacctno='$acctidcode'\n\n");
		
		printf("Errormessage: %s\n", mysqli_error($con));
		
	}
	else{
		//echo "Update accounts set cacctdesc='$cdesc', mainacct=$selmain, ccategory='$selcat', ctype='$seltyp', nlevel=$sellvl  where compcode='$company' and cacctno='$acctid'\n\n";
		
		echo "Account successfully updated!";
	}


?>					

