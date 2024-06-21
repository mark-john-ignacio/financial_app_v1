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

	$company = $_SESSION['companyid'];
	$rwcnt = filter_input(INPUT_POST, 'rowcnt', FILTER_SANITIZE_STRING);
	//$selwh = filter_input(INPUT_POST, 'selwhfrom', FILTER_SANITIZE_STRING);
	$seltype = filter_input(INPUT_POST, 'selcntyp', FILTER_SANITIZE_STRING);
	$hdremarks = filter_input(INPUT_POST, 'txtccrems', FILTER_SANITIZE_STRING);
	$hddatecnt = filter_input(INPUT_POST, 'txtdtrandate', FILTER_SANITIZE_STRING);

	$preparedby = $_SESSION['employeeid'];

	//`section_nid` = '$selwh',
	
	$witherrr = 0;
	if (!mysqli_query($con,"UPDATE invcount set `ctype` = '$seltype', `cremarks` = '$hdremarks', `dcutdate` = STR_TO_DATE('$hddatecnt', '%m/%d/%Y') where `compcode` = '$company' and `ctranno` = '$MainTranNo'")){
		//echo "Errormessage: %s\n", mysqli_error($con);
		$witherrr++;
	}

	$cntr = 0;

	mysqli_query($con, "UPDATE invcount_t set compcode='xxx', cidentity=CONCAT('xxx',cidentity) where `compcode` = '$company' and `ctranno` = '$MainTranNo'");

	for($i = 1 ; $i<=$rwcnt ; $i++ ){

		$cntr++;

		$citemno = filter_input(INPUT_POST, 'txtitmcode'.$i, FILTER_SANITIZE_STRING);
		$citemunit = filter_input(INPUT_POST, 'txtcunit'.$i, FILTER_SANITIZE_STRING);
		$citemqty = filter_input(INPUT_POST, 'txtnqty'.$i, FILTER_SANITIZE_STRING);

		$citemqty = str_replace(",","",$citemqty);

		$cident = $MainTranNo."P".$cntr;
		if (!mysqli_query($con,"INSERT INTO invcount_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty`) values('$company', '$MainTranNo', '$cident','$cntr', '$citemno', '$citemunit', '$citemqty')")){
			//echo "Errormessage: %s\n", mysqli_error($con);
			$witherrr++;
		}
	}

	//echo $witherrr;

	if($witherrr==0){
		mysqli_query($con, "DELETE FROM invcount_t where `compcode` = 'xxx' and `ctranno` = '$MainTranNo'");
?>

	<script>
		alert('Record Succesfully Saved');
		window.location="InvCnt_Edit.php?id=<?=$MainTranNo?>";
	</script>

<?php
	}else{
?>

	<script>
		alert('There are errors saving your data!');
		window.location="InvCnt_New.php";
	</script>

<?php
	}
	

?>
