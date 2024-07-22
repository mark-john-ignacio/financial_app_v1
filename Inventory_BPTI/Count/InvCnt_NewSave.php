<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	if(isset($_SESSION['myxtoken']) && !empty($_SESSION['myxtoken'])){

		$token = filter_input(INPUT_POST, 'hdnmyxfin', FILTER_SANITIZE_STRING);

		if (!$token || $token !== $_SESSION['myxtoken']) {	
			// show an error message
			//echo '<p class="error">Error: invalid form submission</p>';
			// return 405 http status code
			//echo $_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed';
			exit;

			?>

				<script>
					alert('Invalid Form Submission!');
					window.location="InvCnt_New.php";
				</script>

			<?php

		}else{
			unset($_SESSION['myxtoken']);
		}

	}else{

		?>

				<script>
					alert('Invalid Form Submission!');
					window.location="InvCnt_New.php";
				</script>

			<?php

	}

	$dmonth = date("m");
	$dyear = date("y");

	$company = $_SESSION['companyid'];
	$rwcnt = filter_input(INPUT_POST, 'rowcnt', FILTER_SANITIZE_STRING);
	$selwh = filter_input(INPUT_POST, 'selwhfrom', FILTER_SANITIZE_STRING);
	$seltype = filter_input(INPUT_POST, 'selcntyp', FILTER_SANITIZE_STRING);
	$hdremarks = filter_input(INPUT_POST, 'txtccrems', FILTER_SANITIZE_STRING);
	$hddatecnt = filter_input(INPUT_POST, 'txtdtrandate', FILTER_SANITIZE_STRING);
	$seltempid = filter_input(INPUT_POST, 'seltempname', FILTER_SANITIZE_STRING);

	$preparedby = $_SESSION['employeeid'];

	$chkSales = mysqli_query($con,"select * from invcount where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ddate desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cTranNo = "IC".$dyear."000000001";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		
		if(substr($lastSI,2,2) <> $dyear){
			$cTranNo = "IC".$dyear."000000001";
		}
		else{
			$baseno = intval(substr($lastSI,4,9)) + 1;
			$zeros = 9 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cTranNo = "IC".$dyear.$baseno;
		}
	}

	if($seltempid==""){
		$seltempid = "NULL";
	}

	$witherrr = 0;
	if (!mysqli_query($con,"INSERT INTO invcount(`compcode`, `ctranno`, `section_nid`, `template_id`, `ctype`, `cremarks`, `dcutdate`, `cpreparedby`) values('$company', '$cTranNo', '$selwh', $seltempid,'$seltype', '$hdremarks', STR_TO_DATE('$hddatecnt', '%m/%d/%Y'), '$preparedby')")){
		//echo "Errormessage: %s\n", mysqli_error($con);
		$witherrr++;
	}

	$cntr = 0;
	for($i = 1 ; $i<=$rwcnt ; $i++ ){

		$cntr++;

		$citemno = filter_input(INPUT_POST, 'txtitmcode'.$i, FILTER_SANITIZE_STRING);
		$citemunit = filter_input(INPUT_POST, 'txtcunit'.$i, FILTER_SANITIZE_STRING);
		$citemqty = filter_input(INPUT_POST, 'txtnqty'.$i, FILTER_SANITIZE_STRING);

		$citemqty = str_replace(",","",$citemqty);

		$cident = $cTranNo."P".$cntr;
		if (!mysqli_query($con,"INSERT INTO invcount_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty`) values('$company', '$cTranNo', '$cident','$cntr', '$citemno', '$citemunit', '$citemqty')")){
			//echo "Errormessage: %s\n", mysqli_error($con);
			$witherrr++;
		}
	}

	if($witherrr==0){
?>

	<script>
		alert('Record Succesfully Saved');
		window.location="InvCnt_Edit.php?id=<?=$cTranNo?>";
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
