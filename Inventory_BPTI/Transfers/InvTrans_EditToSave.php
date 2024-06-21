<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$MainTranNo = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

	if(isset($_SESSION['myxtoken']) && !empty($_SESSION['myxtoken']) && $MainTranNo!==""){

		$token = filter_input(INPUT_POST, 'hdnmyxfin', FILTER_SANITIZE_STRING);

		if (!$token || $token !== $_SESSION['myxtoken']) {	
			// show an error message
			//echo '<p class="error">Error: invalid form submission</p>';
			// return 405 http status code
			//echo $_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed';

			?>

				<script>
					alert('Invalid Form Submission!');
					window.location="Inv.php";
				</script>

			<?php
			exit;
		}else{
			unset($_SESSION['myxtoken']);
		}

	}else{

		//echo "HALIMBAWA";

		?>

				<script>
					alert('Invalid Form Submission!');
					window.location="Inv.php";
				</script>

			<?php

	}

	$dmonth = date("m");
	$dyear = date("y");

	$MainTranNo = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

	$company = $_SESSION['companyid'];
	$rwcnt = filter_input(INPUT_POST, 'rowcnt', FILTER_SANITIZE_STRING);

	$preparedby = $_SESSION['employeeid'];

	$witherrr = 0;

	$cntr = 0;
	for($i = 1 ; $i<=$rwcnt ; $i++ ){

		$citemno = filter_input(INPUT_POST, 'txtcidentity'.$i, FILTER_SANITIZE_STRING);
		$citemqty = filter_input(INPUT_POST, 'txtnqty2'.$i, FILTER_SANITIZE_STRING);

		$citemqty = str_replace(",","",$citemqty);

		if (!mysqli_query($con,"UPDATE invtransfer_t set `nqty2` = '$citemqty' Where compcode='$company' and cidentity = '$citemno'")){
			//echo "Errormessage: %s\n", mysqli_error($con);
			$witherrr++;
		}
	}

	//echo $witherrr;

	if($witherrr==0){
?>

	<script>
		alert('Record Succesfully Saved');
		window.location="InvTrans_EditTo.php?id=<?=$MainTranNo?>";
	</script>

<?php
	}else{
?>

	<script>
		alert('There are errors saving your data!');
		window.location="Inv.php";
	</script>

<?php
	}
	

?>
