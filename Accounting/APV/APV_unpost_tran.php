<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "APV_unpost.php";

	require_once "../../Connection/connection_string.php";
	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	//POST RECORD
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$status = "True";

			if (!mysqli_query($con,"Update apv set lapproved=0,lcancelled=0 where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')")){
				$status = "False";	
			}else{

				$status = "True";

				foreach($_POST["allbox"] as $rz){
					mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
				values('$rz','$preparedby',NOW(),'UNPOST','AP VOUCHER','$compname','UnPost Record')");
				}

			}

			if($status=="True"){
?>

				<script>
					alert('Records Succesfully Un-Posted');
					window.location.href="APV_unpost.php";
				</script>
<?php
			}else{
?>
				<script>
					alert('Error Un-Posting transactions!');
					window.location.href="APV_unpost.php";
				</script>
<?php
			}
?>