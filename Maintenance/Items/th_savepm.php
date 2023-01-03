<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$deffect = $_REQUEST['deffect'];
	$descrip = $_REQUEST['desc'];
	$typ = $_REQUEST['typ'];
	$batchno = $_REQUEST['batchno'];
	
	
	$mo = date("m");
	$dy = date("d");
	$yr = date("y");
	
	$code = $typ.$mo.$dy.$yr;
		
	$result = mysqli_query ($con, "SELECT ctranno, IF(LOCATE('_', ctranno), SUBSTRING_INDEX(ctranno,'_',-1), '1') as prefx FROM items_pm where ctranno like '$code%' order by ctranno DESC LIMIT 1"); 
	
	
	//echo $row['prefx'];
	
	if(mysqli_num_rows($result)==0){
		$code = $code."_1";
	}
	else {
		$row = mysqli_fetch_assoc($result);
		$yz = $row['prefx'];
		
		$prfx = (int)$yz+1;
		
		$code = $code."_".$prfx;
		
		//echo $code;
	}
	
	
//Insert Header	
	if($descrip==""){
		$descrip = "For ".date_format(date_create($deffect), "F j\, Y")." Effectivity";
	}

			if (!mysqli_query($con,"INSERT INTO items_pm (`compcode`,`ctranno`,`cbatchno`,`cversion`,`cremarks`,`ddate`,`deffectdate`) values ('$company','$code','$batchno','$typ','$descrip',NOW(),STR_TO_DATE('$deffect', '%m/%d/%Y'))")) {
				echo "False";
			} 
			else{
				echo $code;	
			}

?>
