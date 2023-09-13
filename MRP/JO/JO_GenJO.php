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
	$sqllabelnme = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and citemno = '".$dmainitms."' and ldefault = 1");
	while($row2 = mysqli_fetch_array($sqllabelnme, MYSQLI_ASSOC)){
		$totdcount[] = array('citemno' => $row2['citemno'], 'ldefault' => $row2['nversion']);
	}

	$getboms = array();
	$chbom = mysqli_query($con,"select * from mrp_bom where compcode='$company' and cmainitemno = '".$dmainitms."' Order By cmainitemno,nitemsort");
	while($row2 = mysqli_fetch_array($chbom, MYSQLI_ASSOC)){
		$getboms[] = $row2;
	}

	$getbomshrs = array();
	$chbom = mysqli_query($con,"select * from mrp_items_parameters where compcode='$company'");
	while($row2 = mysqli_fetch_array($chbom, MYSQLI_ASSOC)){
		$getbomshrs[$row2['citemno']] = $row2;
	}

	mysqli_query($con,"DELETE FROM mrp_jo_process where compcode='$company' and mrp_jo_ctranno ='$tranno'");

	function getsubs($itm,$maintran,$soref,$lvl,$qty){
		global $totdcount;
		global $getboms;
		global $getbomshrs;
		global $con;
		global $company;

		//get version
		$xcver = 1;
		$cnt = 0;
		foreach($totdcount as $rs1){
			if($itm==$rs1['citemno']){
				$xcver = $rs1['ldefault'];
			}
		}

		$nxtlvl = 0;
		foreach($getboms as $rs2){
			$nxtlvl = intval($lvl)+1;
			
			if($itm==$rs2['cmainitemno'] && intval($rs2['nlevel'])==$nxtlvl){

				//echo "<br><br>".$rs2['citemno']."-";
				$getwsort = chkwithsub($itm,$rs2['nitemsort'],$rs2['nlevel']);
				//echo $getwsort;
				if($getwsort=="True"){
					$cnt++;
					$SINo = $maintran."-L".$rs2['nlevel']."-".$cnt;

					//echo $rs2['citemno']."-".$SINo."<br>";

					$totqty = floatval($qty)*floatval($rs2['nqty'.$xcver]);

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

					mysqli_query($con, "INSERT INTO mrp_jo_process(`compcode`, `mrp_jo_ctranno`, `ctranno`, `nrefident`, `citemno`, `cunit`, `nqty`, `nworkhrs`, `nsetuptime`, `ncycletime`, `ntottime`) values('$company', '$maintran', '$SINo', '".$rs2['nid']."', '".$rs2['citemno']."', '".$rs2['cunit']."', '".$totqty."', $nwork, $nsetup, $ncycle, $nxtot)");

				}
			}
		}

		if($nxtlvl<=5){
			getsubs($itm,$maintran,$soref,$nxtlvl,$qty);
		}

	}

	function chkwithsub($citmno,$subsort,$sublvl){
		global $getboms;
		$cnt = 0;
		foreach($getboms as $rs2){

			//echo "<br>".$citmno."==".$rs2['cmainitemno']." && ".intval($subsort).">".intval($rs2['nitemsort'])." && ".intval($rs2['nlevel']).">".intval($sublvl);
			if($citmno==$rs2['cmainitemno'] && intval($rs2['nitemsort'])>intval($subsort) && intval($rs2['nlevel'])>intval($sublvl)){
				$cnt++;
				break;
			}else if($citmno==$rs2['cmainitemno'] && intval($rs2['nitemsort'])>intval($subsort) && intval($rs2['nlevel'])==intval($sublvl)){
				break;
			}
		}

		if($cnt>0){
			return "True";
		}else{
			return "False";
		}
	}

	
	getsubs($dmainitms,$dmainaryy['ctranno'],$dmainaryy['crefSO'],1,$dmainaryy['nqty']);

	//mysqli_query($con,"Update so set lsent=1 where compcode='$company' and ctranno='$tranno'");

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

	var count = 5;

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