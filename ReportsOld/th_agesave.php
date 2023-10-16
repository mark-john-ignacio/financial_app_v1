<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../Connection/connection_string.php');
	include('../include/denied.php');
	
	$company = $_SESSION['companyid'];

	$rowcnt = $_REQUEST['hdncnts'];
	$agtyp = $_REQUEST['hndtyp'];
		 
	if (!mysqli_query($con, "DELETE FROM `ageing_days` Where `compcode` = '$company' and cagetype='$agtyp'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	for($z=1; $z<=$rowcnt; $z++){
		$cdesc = mysqli_real_escape_string($con,$_REQUEST['cdesc'.$z]);
		$nfrom = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['dfrom'.$z]));
		$nto = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['dto'.$z]));

		if(!mysqli_query($con,"INSERT INTO `ageing_days`(`compcode`, `cdesc`, `fromdays`, `todays`, `cagetype`) values('$company', '$cdesc', '$nfrom', '$nto','$agtyp')")){
			
			printf("Errormessage: %s\n", mysqli_error($con));
		}
	}

?>

<script>
	alert('Record Succesfully Updated');
  window.location.href = '<?=$agtyp?>Ageing.php';
</script>
