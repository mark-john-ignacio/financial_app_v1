<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];

	$acctid = $_POST['acctidcode'];
	$cdesc = $_POST['cdesc2'];
	$seltyp = $_POST['radtype2'];
	$selcat = $_POST['selcat2'];  
	$sellvl = $_POST['selvl2']; 
	$iscontra = (isset($_POST['chkcontra2'])) ? 1 : 0; 
	$selhdr = (isset($_POST['selhdr2'])) ? $_POST['selhdr2']: 0;

	if($selcat=="ASSETS" || $selcat=="LIABILITIES" || $selcat=="EQUITY"){
		$selfingrp = "Balance Sheet";
	}else{
		$selfingrp = "Income Statement";
	}

	//echo "Update accounts set cacctdesc='$cdesc', mainacct='$selhdr', ccategory='$selcat', ctype='$seltyp', nlevel=$sellvl, cFinGroup='$selfingrp', lcontra=$iscontra where compcode='$company' and cacctno=$acctid";

	if (!mysqli_query($con, "Update accounts set cacctdesc='$cdesc', mainacct='$selhdr', ccategory='$selcat', ctype='$seltyp', nlevel=$sellvl, cFinGroup='$selfingrp', lcontra=$iscontra where compcode='$company' and cacctno=$acctid")) {
		
		echo "False";
		
	}
	else{
		//echo "Update accounts set cacctdesc='$cdesc', mainacct=$selmain, ccategory='$selcat', ctype='$seltyp', nlevel=$sellvl  where compcode='$company' and cacctno='$acctid'\n\n";
		
		echo "Account successfully updated!";
	}


?>					

