<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$isokall = "True";
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	$company = $_SESSION['companyid'];

	if($_POST['QOTransTP']=="sign"){
		$sql = "UPDATE quote_approvals_id set sign=null where id=".$_POST['QOTransID']."";

		if ($con->query($sql) === TRUE) {

			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','".$_POST['QOTransID']."','$preparedby',NOW(),'UPDATED','QUOTE APPROVALS','$compname','remove signature')");

			$msg="SIGNATURE REMOVED";
		}else{
			$isokall = "False";
		}

	}elseif($_POST['QOTransTP']=="delete"){

		$sql = "UPDATE quote_approvals_id set compcode='".date('m/d/Y_H:i:s')."' where id=".$_POST['QOTransID']."";

		if ($con->query($sql) === TRUE) {
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','".$_POST['QOTransID']."','$preparedby',NOW(),'UPDATED','QUOTE APPROVALS','$compname','remove approver')");

			$msg="APPROVER REMOVED";
		}else{
			$isokall = "False";
		}

	}


if($isokall=="True"){
	?>
	<script>
		alert("<?=$msg?>");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}else{
	?>
	<script>
		alert("<?=$msg?> (With Error)");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}

?>
