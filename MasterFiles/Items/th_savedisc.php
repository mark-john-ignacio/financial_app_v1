<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$dmonth = date("m");
	$dyear = date("y");
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$desc = $_REQUEST['desc'];
	$label = $_REQUEST['lbl'];
	$val = $_REQUEST['val'];
	$cSINo = $_REQUEST['code'];
	$dDate = $_REQUEST['effdte'];
	$type = $_REQUEST['type'];
	$accts = $_REQUEST['acctcode'];
		
	
	if($cSINo==""){


		$chkSales = mysqli_query($con,"select * from discounts where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
		if (mysqli_num_rows($chkSales)==0) {
			$cSINo = "DC".$dmonth.$dyear."00000";
		}
		else {
			while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
				$lastSI = $row['ctranno'];
			}
			
			
			if(substr($lastSI,2,2) <> $dmonth){
				$cSINo = "DC".$dmonth.$dyear."00000";
			}
			else{
				$baseno = intval(substr($lastSI,6,5)) + 1;
				$zeros = 5 - strlen($baseno);
				$zeroadd = "";
				
				for($x = 1; $x <= $zeros; $x++){
					$zeroadd = $zeroadd."0";
				}
				
				$baseno = $zeroadd.$baseno;
				$cSINo = "DC".$dmonth.$dyear.$baseno;
			}
		}
			
		if (!mysqli_query($con,"INSERT INTO discounts (`compcode`,`ctranno`,`cdescription`,`clabel`,`nvalue`, `ddate`,`deffectdate`, `type`, `cacctcode`) values ('$company','$cSINo','$desc','$label',$val, NOW(), STR_TO_DATE('$dDate', '%m/%d/%Y'), '$type', '$accts')")) {
					printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{

			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','$cSINo','$preparedby',NOW(),'INSERTED','SPECIAL DISCOUNTS','$compname','Inserted New Record')");


			echo "True";
		}
	}
	else {
			
		if (!mysqli_query($con,"UPDATE discounts set `cdescription` = '$desc', `clabel` = '$label', `nvalue` = '$val', `deffectdate` = STR_TO_DATE('$dDate', '%m/%d/%Y'), `type`='$type', `cacctcode`= '$accts' where compcode = '$company' and ctranno = '$cSINo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{
					
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','$cSINo','$preparedby',NOW(),'UPDATED','SPECIAL DICOUNTS','$compname','Update Record')");
			
			echo "True";
		}
			
	}

?>
