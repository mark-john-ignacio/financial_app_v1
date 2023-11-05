<?php

	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');
	$company = $_SESSION['companyid'];

	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";

	$dDelDate = $_POST['date_delivery'];
	$cRemarks = $_POST['xremarkshdr']; 

	$getbegbaldet = mysqli_query($con,"SELECT * FROM `accounts_beg` WHERE compcode='$company'"); 
	if (mysqli_num_rows($getbegbaldet)!=0) {
		mysqli_query($con, "UPDATE accounts_beg set begbaldate = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), cremarks = '$cRemarks' WHERE compcode='$company'");
	}else{
		mysqli_query($con, "INSERT INTO accounts_beg (compcode, begbaldate, cremarks) VALUES ('$company', STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$cRemarks')");
	}

	$query = mysqli_query($con,"SELECT (CASE WHEN A.mainacct='0' OR ctype='General' THEN A.cacctid ELSE A.mainacct END) as 'main', A.cacctno, A.cacctid, A.cacctdesc, A.ctype, A.ccategory, A.mainacct, A.cFinGroup, A.lcontra, A.nlevel, A.nbalance FROM `accounts` A where A.compcode='".$_SESSION['companyid']."' ORDER BY ccategory, nlevel, cacctid");
	$resallaccts = $query->fetch_all(MYSQLI_ASSOC);

	foreach($resallaccts as $rsz){
		if(isset($_POST["txt".$rsz['cacctid']])){

			$dval = mysqli_real_escape_string($con, $_POST["txt".$rsz['cacctid']]);
			$dval = str_replace( ',', '', $dval);

		//	if(floatval($dval) != floatval($rsz['nbalance'])){
		//		echo $rsz['cacctid'].": ".$dval;
		//	}

			mysqli_query($con, "UPDATE accounts set nbalance = $dval WHERE compcode='$company' and cacctid='".$rsz['cacctid']."'");

		}
	}


	mysqli_query($con,"DELETE FROM glactivity where compcode='".$_SESSION['companyid']."' and cmodule='BEGBAL'");
	//insert/update into glactivity as begbal
	$query = mysqli_query($con,"Select * from accounts where compcode='".$_SESSION['companyid']."'");
	$resallaccts = $query->fetch_all(MYSQLI_ASSOC);

	foreach($resallaccts as $rsz){
		if(floatval($rsz['nbalance']) > 0){
			mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) VALUES ('".$_SESSION['companyid']."','BEGBAL','BEGBAL',STR_TO_DATE('$dDelDate', '%m/%d/%Y'),'".$rsz['cacctid']."','".$rsz['cacctdesc']."',,,0,NOW())");
		}
	}


	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$company','$preparedby',NOW(),'UPDATED','ACCOUNTS BEG BALANCE','$compname','Updated Record')");

?>	

<form action="Accounts.php?f=" name="frmpos" id="frmpos" method="post">
</form>
<script>
	alert('Record Succesfully Updated');
  document.forms['frmpos'].submit();
</script>

