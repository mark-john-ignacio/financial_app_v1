<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];

//echo "<pre>";
//print_r($_REQUEST);
//echo "</pre>";

$chkSales = mysqli_query($con,"select * from purchrequest where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "PR".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "PR".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "PR".$dmonth.$dyear.$baseno;
	}
}

	
	$cReqBy =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dDateNeed = $_REQUEST['date_needed'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cSection =  mysqli_real_escape_string($con, $_REQUEST['selwhfrom']);

	if (!mysqli_query($con, "INSERT INTO `purchrequest`(`compcode`, `ctranno`, `dneeded`, `cremarks`, `cpreparedby`, `locations_id`) values('$company', '$cSINo', STR_TO_DATE('$dDateNeed', '%m/%d/%Y'),'$cRemarks', '$cReqBy', '$cSection')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
				
		$itmpart = mysqli_real_escape_string($con, $_REQUEST['txtcpartdesc'.$z]);
		$itmdesc= mysqli_real_escape_string($con, $_REQUEST['txtcitemdesc'.$z]);
		$itmcode = mysqli_real_escape_string($con, $_REQUEST['txtitemcode'.$z]);
		$seluom = mysqli_real_escape_string($con, $_REQUEST['seluom'.$z]);		
		$nqty = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnqty'.$z]));		
		$nmainuom = mysqli_real_escape_string($con, $_REQUEST['hdnmainuom'.$z]);
		$nfactor = mysqli_real_escape_string($con, $_REQUEST['hdnfactor'.$z]);
		$drmrks = mysqli_real_escape_string($con, $_REQUEST['dremarks'.$z]);

		$refcidenttran = $cSINo."P".$z;
	
		if(!mysqli_query($con,"INSERT INTO `purchrequest_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `citemno`, `cpartdesc`, `citemdesc`, `cunit`, `cmainunit`, `nfactor`, `nqty`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$itmcode', '$itmpart', '$itmdesc', '$seluom', '$nmainuom', '$nfactor', '$nqty', '$drmrks')")){ 
			
			printf("Errormessage: %s\n", mysqli_error($con));
		}

	}


		//insert attachment

		$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
		// Count the number of uploaded files in array
		$total_count = count($_FILES['upload']['name']);

		if($total_count>=1){
			mkdir('../../Components/assets/PReq/'.$company.'_'.$cSINo.'/',0777);
		}

		// Loop through every file
		for( $i=0 ; $i < $total_count ; $i++ ) {
			//The temp file path is obtained
			$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
			//A file path needs to be present
			if ($tmpFilePath != ""){
					//Setup our new file path
					$newFilePath = "../../Components/assets/PReq/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
					//File is uploaded to temp dir
					move_uploaded_file($tmpFilePath, $newFilePath);
					
			}
		}

		
	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PURCHASE REQUEST','$compname','Inserted New Record')");

?>
<form action="PR_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
 	document.forms['frmpos'].submit();
</script>