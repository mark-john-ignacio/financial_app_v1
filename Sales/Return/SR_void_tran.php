<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "SalesRet_unpost.php";

	require_once "../../Connection/connection_string.php";

	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	//POST RECORD
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$status = "True";

			if (!mysqli_query($con,"Update salesreturn set lvoid=1 where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')")){
				$status = "False";	
			}else{

				$status = "True";

				foreach($_POST["allbox"] as $rz){
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`, `cancel_rem`) 
					values('$company', '$rz','$preparedby',NOW(),'VOID','SALES RETURN','$compname','Void Record','".$_REQUEST['hdnreason']."')");

					mysqli_query($con,"DELETE FROM `tblinventory` where `ctranno` = '$rz'");
					mysqli_query($con,"DELETE FROM `tblinvin` where `ctranno` = '$rz'");
					mysqli_query($con,"DELETE FROM `tblinvout` where `ctranno` = '$rz'");

				}

			}

			if($status=="True"){
?>

				<script>
					alert('Records Succesfully Voided');
					window.location.href="SR_void.php";
				</script>
<?php
			}else{
?>
				<script>
					alert('Error Voiding transactions!');
					window.location.href="SR_void.php";
				</script>
<?php
			}
?>