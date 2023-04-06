<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "Quote_unpost.php";

	require_once "../../Connection/connection_string.php";

	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	//POST RECORD
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$status = "True";

			if (!mysqli_query($con,"Update quote set lapproved=0,lcancelled=0 where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')")){
				$status = "False";	
			}else{

				mysqli_query($con,"Update quote_trans_approvals set lapproved=0, lreject=0, ddatetimeapp=null, ddatetimereject=null where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')");

				$status = "True";

				foreach($_POST["allbox"] as $rz){
					mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
				values('$rz','$preparedby',NOW(),'UNPOST','QUOTATION','$compname','UnPost Record')");
				}

			}

			if($status=="True"){
?>

				<script>
					alert('Records Succesfully Un-Posted');
					window.location.href="Quote_unpost.php";
				</script>
<?php
			}else{
?>
				<script>
					alert('Error Un-Posting transactions!');
					window.location.href="Quote_unpost.php";
				</script>
<?php
			}
?>