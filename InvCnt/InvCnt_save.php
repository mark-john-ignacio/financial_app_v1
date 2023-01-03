<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');


$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from adjustments where compcode='$company' Order By ctrancode desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "IA".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctrancode'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "IA".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "IA".$dmonth.$dyear.$baseno;
	}
}


$ctranno = $cSINo;

$cremarks = $_POST['txtrem'];
$preparedby = $_SESSION['employeeid'];

//INSERT HEADER
	mysqli_query($con,"INSERT INTO adjustments(`compcode`, `ctrancode`, `cremarks`, `dmonth`, `dyear`, `ddatetime`, `cpreparedby`, lapproved, lcancelled) values('$company', '$ctranno', '$cremarks', '$dmonth', '$dyear', NOW(), '$preparedby',0,0)");


	//INSERT DETAILS
	//echo $_REQUEST['hdnrowcnt'];
	$rowcnt = $_POST['hdnrowcnt'];
	
	//echo $_REQUEST['hdnrowcnt'];
	 
	for($z=1; $z<=$rowcnt; $z++){
		
		//echo 'txtcitemno'.$z.' - '.$_POST['txtcitemno'.$z].'<br>';
		$cItemNo = $_POST['txtcitemno'.$z];
		$cUnit = $_POST['txtcunit'.$z];
		$nQty = $_POST['txtnqty'.$z];
		$nActual = $_POST['txtnqtyact'.$z];
		$nDiff = $_POST['txtdiff'.$z];
		
		if($nDiff==""){
			$nDiff = 0;
		}

	$refidentity = $ctranno."I".$z;
	
	mysqli_query($con,"INSERT INTO adjustments_t(`compcode`, `cidentity`, `ctrancode`, `nidentity`, `citemno`, `cunit`, `nqty`, `nactual`, `nadj`) values('$company', '$refidentity', '$ctranno', '$z', '$cItemNo', '$cUnit', '$nQty', '$nActual', '$nDiff')");
	
	}
		
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$ctranno','$preparedby',NOW(),'INSERTED','INV ADJUSTMENT','$compname','Inserted New Record')");

?>
<form action="InvAdj_rpt.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $ctranno;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>