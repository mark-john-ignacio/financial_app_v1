	<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');
require_once('../../Model/helper.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from quote where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "QO".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "QO".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "QO".$dmonth.$dyear.$baseno;
	}
}

	$cCustID = $_REQUEST['txtcustid'];
	$cCustIDel = $_REQUEST['txtcustiddel'];

	$nGross = str_replace(",","",$_REQUEST['txtnGross']);

	$ccontname = $_REQUEST['txtcontactname'];
	$ccontdesg = $_REQUEST['txtcontactdesig'];
	$ccontdept = $_REQUEST['txtcontactdept'];
	$ccontemai = $_REQUEST['txtcontactemail'];
	$ccontsalt = $_REQUEST['txtcontactsalut'];
	$cvattyp = $_REQUEST['selvattype'];
	if(isset($_REQUEST['selterms'])){
		$cterms = $_REQUEST['selterms'];
	}else{
		$cterms = "";
	}
	$cdelinfo = $_REQUEST['txtdelinfo'];
	$cservinfo = $_REQUEST['txtservinfo'];
	$dDelDate = $_REQUEST['date_delivery']; 
	$dQuoteDate = $_REQUEST['date_trans'];
	$cRemarks = $_REQUEST['txtremarks'];  
	$cSITyp= $_REQUEST['selsityp']; 
	$cQOTyp= $_REQUEST['selqotyp'];  
	$cRCTyp= isset($_REQUEST['selrecurrtyp']) ? $_REQUEST['selrecurrtyp'] : "";

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['currdesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']); 


	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER

	if (!mysqli_query($con, "INSERT INTO quote(`compcode`, `ctranno`, `ccode`, `cdelcode`, `ddate`, `ccontactname`, `ccontactdesig`, `ccontactdept`, `ccontactemail`, `ccontactsalut`, `cvattype`, `cterms`, `cdelinfo`, `cservinfo`, `dcutdate`, `ngross`, `nbasegross`, `cremarks`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`, `cpreparedby`, `csalestype`, `quotetype`, `crecurrtype`, `dtrandate`) Values('$company', '$cSINo', '$cCustID', '$cCustIDel', NOW(), '$ccontname', '$ccontdesg', '$ccontdept', '$ccontemai', '$ccontsalt', '$cvattyp', '$cterms', '$cdelinfo', '$cservinfo', STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$BaseGross', '$cRemarks', '$CurrCode', '$CurrDesc', '$CurrRate', '$preparedby','$cSITyp', '$cQOTyp', '$cRCTyp', STR_TO_DATE('$dQuoteDate', '%m/%d/%Y'))")) {
		echo "False";
	} 
	else {
		
			//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','QUOTATION','$compname','Inserted New Record')");
		
		// Delete previous details
		mysqli_query($con, "Delete from quote_t Where compcode='$company' and ctranno='$cSINo'");
		mysqli_query($con, "Delete from quote_t_info Where compcode='$company' and ctranno='$cSINo'");

		
		echo $cSINo;

	}
	
	
	if(count($_FILES) != 0){
		$directory = "../../Components/assets/QO/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}


?>