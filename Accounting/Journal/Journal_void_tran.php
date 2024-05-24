<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "Journal_unpost";

	require_once "../../Connection/connection_string.php";

	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	//POST RECORD
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$status = "True";

	if (!mysqli_query($con,"Update journal set lvoid=1 where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')")){
		$status = "False";	
	}else{

		$status = "True";

		foreach($_POST["allbox"] as $rz){
			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$rz','$preparedby',NOW(),'VOID','JOURNAL','$compname','Void Record')");
		}

	}

	if($status=="True"){

		mysqli_query($con,"Delete FROM glactivity where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')");
?>

		<script>
			alert('Records Succesfully Voided');
			window.location.href="Journal_void.php";
		</script>
<?php
	}else{
?>
		<script>
			alert('Error Voiding transactions!');
			window.location.href="Journal_void.php";
		</script>
<?php
	}
?>