<?php

	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');


	$acctid = $_POST['cacctid'];
	$cdesc = $_POST['cacctdesc'];
	$seltyp = $_POST['radtype'];
	$selcat = $_POST['selcat'];  
	$sellvl = $_POST['selvl']; 
	$iscontra = (isset($_POST['chkcontra2'])) ? 1 : 0; 
	$selhdr = (isset($_POST['selhdr'])) ? $_POST['selhdr']: 0;

	if($selcat=="ASSETS" || $selcat=="LIABILITIES" || $selcat=="EQUITY"){
		$selfingrp = "Balance Sheet";
	}else{
		$selfingrp = "Income Statement";
	}

	if (!mysqli_query($con, "insert into accounts(compcode,cacctid,cacctdesc,mainacct,ccategory,ctype,nlevel,cFinGroup,lcontra) values('".$_SESSION['companyid']."','$acctid','$cdesc','$selhdr','$selcat','$seltyp',$sellvl,'$selfingrp',$iscontra)")) {
		
		
		echo "False";
		//printf("Errormessage: %s\n", mysqli_error($con));
	}
	else{
		echo "New account successfully added!";
	}



?>	

