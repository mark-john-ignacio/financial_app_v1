<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "Deposit_void";

	require_once "../../Connection/connection_string.php";

	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	//POST RECORD
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$status = "True";

			if (!mysqli_query($con,"Update deposit set lvoid=1 where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')")){
				$status = "False";	
			}else{

				$status = "True";

				foreach($_POST["allbox"] as $rz){
					mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
				values('$rz','$preparedby',NOW(),'VOID','BANK DEPOSIT','$compname','Void Record')");
				}

				//set deposited to 0 again
				$sqlbody = mysqli_query($con,"select corno from deposit_t where compcode='$company' and ctranno = '$rz' order by nidentity");
				while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
					mysqli_query($con,"Update receipt set ldeposited=0 where compcode='$company' and ctranno = '".$rowbody['corno']."'");
				}

			}

			if($status=="True"){

				//remove glactivity entry
				mysqli_query($con,"Delete FROM glactivity where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')");

?>

				<script>
					alert('Records Succesfully Voided');
					window.location.href="Deposit_void.php";
				</script>
<?php
			}else{
?>
				<script>
					alert('Error Voiding transactions!');
					window.location.href="Deposit_void.php";
				</script>
<?php
			}
?>