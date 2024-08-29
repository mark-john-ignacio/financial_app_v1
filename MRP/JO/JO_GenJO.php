<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	$tranno = $_REQUEST['txtctranno'];
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$dmonth = date("m");
	$dyear = date("y");


	$msgz = "";
	$status = "True";

	$sql = "select X.*, A.citemdesc
	from mrp_jo X
	left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
	where X.compcode='$company' and X.ctranno = '$tranno'";

	$dmainitms = "";
	$dmainaryy = array();
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$dmainitms = $row2['citemno'];
		$dmainaryy = $row2;
	}

	$totdcount = array();
	$sqllabelnme = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and ldefault = 1");
	while($row2 = mysqli_fetch_array($sqllabelnme, MYSQLI_ASSOC)){
		$totdcount[$row2['citemno']] = $row2['nversion'];
	}
	//echo "<pre>";
	//print_r($totdcount);
	//echo "</pre>";
	$getbomshrs = array();
	$chbom = mysqli_query($con,"select * from mrp_items_parameters where compcode='$company'");
	while($row2 = mysqli_fetch_array($chbom, MYSQLI_ASSOC)){
		$getbomshrs[$row2['citemno']] = $row2;
	}

	$getallboms = array();
	$chbom = mysqli_query($con,"select * from mrp_bom where compcode='$company' Order By cmainitemno,nitemsort");
	while($row2 = mysqli_fetch_array($chbom, MYSQLI_ASSOC)){
		$getallboms[] = $row2;
	}

	//echo "<pre>";
	//print_r($getallboms);
	//echo "<br>";

	mysqli_query($con,"DELETE FROM mrp_jo_process where compcode='$company' and mrp_jo_ctranno ='$tranno'");
	mysqli_query($con,"DELETE FROM mrp_jo_process_m where compcode='$company' and mrp_jo_ctranno ='$tranno'"); 
	mysqli_query($con,"DELETE FROM mrp_jo_process_mrs where compcode='$company' and mrp_jo_ctranno ='$tranno'"); 

	getsubs($dmainitms,$dmainaryy['ctranno'],$dmainaryy['crefSO'],1,$dmainaryy['nqty'],$dmainaryy['ctranno']);

	function getsubs($itm,$maintran,$soref,$lvl,$qty,$sicurr){
		global $totdcount;
		global $getallboms;
		global $getbomshrs;
		global $con;
		global $company;

		//get version
		$xcver = 1;
		$cnt = 0;

		if(isset($totdcount[$itm])){
			$xcver = $totdcount[$itm];
		}else{
			$xcver = 1;
		}

		$nxtlvl = 0;
		foreach($getallboms as $rs2){
			$nxtlvl = intval($lvl)+1;
			
		
			$totqty = round(floatval($qty)*floatval($rs2['nqty1']));

			//echo $itm."==".$rs2['cmainitemno']."&&". $rs2['nversion']."==".$xcver."<br><br>";
			if($itm==$rs2['cmainitemno'] && $rs2['nversion']==$xcver){

				//echo $rs2['cmainitemno'] ." : " . $rs2['citemno'] .": ".$rs2['ctype']." <br><br>";				
				
				if($rs2['ctype']=="MAKE"){
					$cnt++;
					$SINo = $sicurr."-".$cnt;

					//echo $rs2['citemno']."-".$SINo."<br>";

					if(isset($getbomshrs[$rs2['citemno']])){
						$nwork = $getbomshrs[$rs2['citemno']]['nworkhrs'];
						$nsetup = $getbomshrs[$rs2['citemno']]['nsetuptime'];
						$ncycle = $getbomshrs[$rs2['citemno']]['ncycletime'];

						$nxtot = (floatval($totqty)*floatval($ncycle)) + floatval($nsetup);
					}else{
						$nwork = 0;
						$nsetup = 0;
						$ncycle = 0;
						$nxtot = 0;
					}

					if (!mysqli_query($con, "INSERT INTO mrp_jo_process(`compcode`, `mrp_jo_ctranno`, `ctranno`, `nrefident`, `citemno`, `cunit`, `nqty`, `nworkhrs`, `nsetuptime`, `ncycletime`, `ntottime`) values('$company', '$maintran', '$SINo', '".$rs2['nid']."', '".$rs2['citemno']."', '".$rs2['cunit']."', '".$totqty."', $nwork, $nsetup, $ncycle, $nxtot)")){
						echo "Errormessage: ". mysqli_error($con);
					}

					// INSERT FOR MRS //
					if (!mysqli_query($con, "INSERT INTO mrp_jo_process_mrs(`compcode`, `mrp_jo_ctranno`, `mrp_jo_sub`, `crefitem`, `nreference_id`, `citemno`, `cunit`, `nqty`, `cremarks`) values('$company', '".$maintran."', '".$sicurr."', '".$itm."', '".$rs2['nid']."', '".$rs2['citemno']."', '".$rs2['cunit']."','".$totqty."', 'BUILDABLE ".$totqty.$rs2['cunit']."')")) {
					
						$status = "False";
						$msgz = $msgz . "<b>ERROR ON ".$rs2['citemno'].": </b>There's a problem generating your material!";
		
					}
					//END MRS

					getsubs($rs2['citemno'],$maintran,$soref,$nxtlvl,$totqty,$SINo);

				}elseif($rs2['ctype']=="BUY"){

					if (!mysqli_query($con, "INSERT INTO mrp_jo_process_m(`compcode`, `mrp_jo_ctranno`, `nrefident`, `citemno`, `cunit`, `nqty`) values('$company', '".$maintran."', '".$rs2['nid']."', '".$rs2['citemno']."', '".$rs2['cunit']."','".$totqty."')")) {
					
						$status = "False";
						$msgz = $msgz . "<b>ERROR ON ".$rs2['citemno'].": </b>There's a problem generating your material!";
		
					}

					// INSERT FOR MRS //
					if (!mysqli_query($con, "INSERT INTO mrp_jo_process_mrs(`compcode`, `mrp_jo_ctranno`, `mrp_jo_sub`, `crefitem`, `nreference_id`, `citemno`, `cunit`, `nqty`, `cremarks`) values('$company', '".$maintran."', '".$sicurr."', '".$itm."', '".$rs2['nid']."', '".$rs2['citemno']."', '".$rs2['cunit']."','".$totqty."', '')")) {
					
						$status = "False";
						$msgz = $msgz . "<b>ERROR ON ".$rs2['citemno'].": </b>There's a problem generating your material!";
		
					}
					//END MRS

				}

			}
		}

	}

	//process of the main item
	$rpromain= mysqli_query($con,"select * from mrp_process_t where compcode='$company' and citemno='$dmainitms' and cstatus='ACTIVE' Order By nid");
	while($row2 = mysqli_fetch_array($chbom, MYSQLI_ASSOC)){
		$getallboms[] = $row2;
	}


	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	//echo json_encode($json2);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<script src="../../Bootstrap/js/jquery-3.6.0.min.js"></script>
</head>

<body style="padding-top:20px">

<h3><center>GENERATING SUB - JOB ORDERS LIST...</center><h3>
<h1><center><span id="counter"> 0 </span></center><h1>

<form action="JO_GenJOSub.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?=$tranno;?>" />
</form>

</body>
</html>

<script type="text/javascript">

	var count = 1;

	$(document).ready(function() {
		counter();
	});

	function counter()
  {
		if ( count > 0 )
    {
			count--;
      		document.querySelector("#counter").innerHTML = count;
			var c = setTimeout( counter, 500 );
		}else
    {
			document.forms['frmpos'].submit();
		}
	}


 
</script>