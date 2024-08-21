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

		//pag walng error GENERATE the PICKLIST
		// 1. get all items muna
		$sqlhead = mysqli_query($con,"Select A.*, B.citemdesc from invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='".$_REQUEST['id']."'");
		if (mysqli_num_rows($sqlhead)!=0) {

			$arritms = array();
			$arr_t_list = array();
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
				$arritms[] = $row['citemno'];
				$arr_t_list = $row;
			}
		}

		//2. Check if all items have record sa tblinv and A.citemno in ('".implode("','", $arritms)."')
		$arrIN = array();
		$arrINITMS = array();
		$sqlIN = "select A.nidentity, A.dcutdate, A.citemno, C.citemdesc, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, B.cdesc, SUM(A.ntotqty) as nqty
		from tblinvin A 
		left join mrp_locations B on A.compcode=B.compcode and A.nlocation=B.nid
		left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
		where A.compcode='$company' and A.citemno in ('".implode("','", $arritms)."')
		Group BY A.nidentity, A.dcutdate, A.citemno, C.citemdesc, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, B.cdesc
		Order by A.dcutdate ASC";
		$rsdin = mysqli_query($con,$sqlIN);
		while($row = mysqli_fetch_array($rsdin, MYSQLI_ASSOC)){
			$arrIN[] = $row;
			$arrINITMS[$row['citemno']][] =  $row;
		}

		$arrOut = array();
		$arrOutITMS = array();
		$sqlOUT = "select A.dcutdate, A.tblinvin_nidentity, A.citemno, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, SUM(A.ntotqty) as nqty
		from tblinvout A 
		where A.compcode='$company' and A.citemno in ('".implode("','", $arritms)."')
		Group BY A.dcutdate, tblinvin_nidentity, A.citemno, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation";
		$rsdout = mysqli_query($con,$sqlOUT);
		while($row = mysqli_fetch_array($rsdout, MYSQLI_ASSOC)){
			$arrOut[] = $row;
			$arrOutITMS[$row['citemno']][] =  $row;
		}

		//3. If meron
		if(count($arrIN)>0){

			foreach($arr_t_list as $rsitm){
				if(isset($arrINITMS[$rsitm['citemno']])){

				}
			}

		}

		function recomps($arrIn, $arrOut, $itmcode, $itmQty){
			foreach($arrIn as $rx){
				if
			}
		}
	
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

