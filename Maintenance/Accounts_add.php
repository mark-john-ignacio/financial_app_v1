<?php
include('../Connection/connection_string.php');


	$acctid = $_POST['acctid'];
	$cdesc = $_POST['cdesc'];
	$seltyp = $_POST['seltyp'];
	$selcat = $_POST['selcat']; 
	$selmain = $_POST['selmain']; 
	$selfingrp = $_POST['cfingrp'];
	

		$mainacct = explode(":",$selmain);
		//echo $mainacct[0];

		$result = mysqli_query ($con, "select * from accounts WHERE cacctno = '".$mainacct[0]."'"); 

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						
			$selmain = "'".$row['cacctno']."'";
			$sellvl = intval($row['nlevel']) + 1;
		}


	if (!mysqli_query($con, "insert into accounts(compcode,cacctno,cacctdesc,mainacct,ccategory,ctype,nlevel,cFinGroup) values('001','$acctid','$cdesc',$selmain,'$selcat','$seltyp',$sellvl,'$selfingrp')")) {
		
		printf("insert into accounts(compcode,cacctno,cacctdesc,mainacct,ccategory,ctype,nlevel,cFinGroup) values('001','$acctid','$cdesc',$selmain,'$selcat','$seltyp',$sellvl,'$selfingrp')\n\n");
		printf("Errormessage: %s\n", mysqli_error($con));
	}
	else{
		echo "New account successfully added!";
	}


?>					

