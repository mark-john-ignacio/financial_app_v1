<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = $_POST['selm'];
$dyear = $_POST['sely']; 
$cremarks = $_POST['txtrem'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];


$ctranno = $_POST['txtctranno'];
	//INSERT DETAILS
	//echo $_REQUEST['hdnrowcnt'];
	
	
	mysqli_query($con,"Update adjustments set cremarks='$cremarks', dmonth='$dmonth', dyear='$dyear' where compcode='$company' and ctrancode='$ctranno'");
	
	mysqli_query($con,"DELETE FROM adjustments_t where compcode='$company' and ctrancode='$ctranno'");
	
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


	mysqli_query($con,"INSERT INTO adjustments_t(`compcode`, `ctrancode`, `citemno`, `cunit`, `nqty`, `nactual`, `nadj`) values('$company', '$ctranno', '$cItemNo', '$cUnit', '$nQty', '$nActual', '$nDiff')");
	
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