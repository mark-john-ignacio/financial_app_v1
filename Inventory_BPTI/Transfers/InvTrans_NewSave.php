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
					window.location="InvTrans_New.php";
				</script>

			<?php

		}else{
			unset($_SESSION['myxtoken']);
		}

	}else{

		?>

			<script>
				alert('Invalid Form Submission!');
				window.location="InvTrans_New.php";
			</script>

		<?php

	}

	$dmonth = date("m");
	$dyear = date("y");

	$company = $_SESSION['companyid'];
	$rwcnt = filter_input(INPUT_POST, 'rowcnt', FILTER_SANITIZE_STRING);
	$selwhfrom = filter_input(INPUT_POST, 'selwhfrom', FILTER_SANITIZE_STRING);
	$selwhto = filter_input(INPUT_POST, 'selwhto', FILTER_SANITIZE_STRING);
	$seltype = filter_input(INPUT_POST, 'selcntyp', FILTER_SANITIZE_STRING);
	$hdremarks = filter_input(INPUT_POST, 'txtccrems', FILTER_SANITIZE_STRING);
	$hddatecnt = filter_input(INPUT_POST, 'txtdtrandate', FILTER_SANITIZE_STRING);
	$seltempid = filter_input(INPUT_POST, 'seltempname', FILTER_SANITIZE_STRING);

	$preparedby = $_SESSION['employeeid'];

	$chkSales = mysqli_query($con,"select * from invtransfer where compcode='$company' and YEAR(ddatetime) = YEAR(CURDATE()) Order By ddatetime desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cTranNo = "IT".$dyear."000000001";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		
		if(substr($lastSI,2,2) <> $dyear){
			$cTranNo = "IT".$dyear."000000001";
		}
		else{
			$baseno = intval(substr($lastSI,4,9)) + 1;
			$zeros = 9 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cTranNo = "IT".$dyear.$baseno;
		}
	}

	if($seltempid==""){
		$seltempid = "NULL";
	}

	$witherrr = 0;
	if (!mysqli_query($con,"INSERT INTO invtransfer(`compcode`, `ctranno`, `cremarks`, `dcutdate`, `ctrantype`, `csection1`, `template_id`, `cpreparedby`, `csection2`) values('$company', '$cTranNo', '$hdremarks', STR_TO_DATE('$hddatecnt', '%m/%d/%Y'), '$seltype', '$selwhfrom', $seltempid, '$preparedby', '$selwhto')")){
		//echo "Errormessage: %s\n", mysqli_error($con);
		$witherrr++;
	}

	$cntr = 0;
	for($i = 1 ; $i<=$rwcnt ; $i++ ){

		$cntr++;

		$citemno = filter_input(INPUT_POST, 'txtitmcode'.$i, FILTER_SANITIZE_STRING);
		$citemunit = filter_input(INPUT_POST, 'txtcunit'.$i, FILTER_SANITIZE_STRING);
		$citemqty = str_replace(",","",filter_input(INPUT_POST, 'txtnqty'.$i, FILTER_SANITIZE_STRING));
		$citmremarks = filter_input(INPUT_POST, 'txtcrems'.$i, FILTER_SANITIZE_STRING);

		$cident = $cTranNo."P".$cntr;
		if (!mysqli_query($con,"INSERT INTO invtransfer_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty1`, `nqty2`, `cremarks`) values('$company', '$cTranNo', '$cident','$cntr', '$citemno', '$citemunit', '$citemqty', '$citemqty', '$citmremarks')")){
			//echo "Errormessage: %s\n", mysqli_error($con);
			$witherrr++;
		}
	}

	if($witherrr==0){
?>

	<script>
		alert('Record Succesfully Saved');
		window.location="InvTrans_Edit.php?id=<?=$cTranNo?>";
	</script>

<?php
	}else{
?>

	<script>
		alert('There are errors saving your data!');
		window.location="InvTrans_New.php";
	</script>

<?php
	}
	

?>
