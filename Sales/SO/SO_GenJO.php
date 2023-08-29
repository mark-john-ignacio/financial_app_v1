<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	if($_REQUEST['typ']=="POST"){
		$_SESSION['pageid'] = "SO_post";
	}

	require_once "../../include/denied.php";
	require_once "../../include/access.php";

	$tranno = $_REQUEST['x'];
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$dmonth = date("m");
	$dyear = date("y");


	$msgz = "";
	$status = "True";

	$sql = "select X.ctranno, B.ccode, X.citemno, A.citemdesc, X.cunit, X.nqty as totqty
	from so_t X
	left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
	left join so B on X.compcode=B.compcode and X.ctranno=B.ctranno
	where X.compcode='$company' and X.ctranno = '$tranno' and B.lapproved=1";

	$dmainaryy = array();
	$dmainitms = array();
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$dmainitms[] = $row2['citemno'];
		$dmainaryy[] = $row2;
	}

	$totdcount = array();
	$sqllabelnme = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and citemno in ('".implode("','", $dmainitms)."') and ldefault = 1");
	while($row2 = mysqli_fetch_array($sqllabelnme, MYSQLI_ASSOC)){
		$totdcount[] = array('citemno' => $row2['citemno'], 'ldefault' => $row2['nversion']);
	}


	$getboms = array();
	$chbom = mysqli_query($con,"select * from mrp_bom where compcode='$company' and cmainitemno in ('".implode("','", $dmainitms)."') Order By cmainitemno,nitemsort");
	while($row2 = mysqli_fetch_array($chbom, MYSQLI_ASSOC)){
		$getboms[] = $row2;
	}

	function getsubs($itm,$maintran,$soref,$lvl){
		global $totdcount;
		global $getboms;

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

					echo $rs2['citemno']."-".$SINo."<br>";

					//mysqli_query($con, "INSERT INTO mrp_jo(`compcode`, `ctranno`, `ctranno_main`, `ccode`, `crefSO`, `citemno`, `cunit`, `nqty`) values('$company', '$cSINo', '0', '".$row2['ccode']."', '".$row2['ctranno']."', '".$row2['citemno']."', '".$row2['cunit']."', '".$row2['totqty']."')")

				}
			}
		}

		if($nxtlvl<=5){
		getsubs($itm,$maintran,$soref,$nxtlvl);
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

	
		foreach($dmainaryy as $row2){

			$chkSales = mysqli_query($con,"select * from mrp_jo where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
			if (mysqli_num_rows($chkSales)==0) {
				$cSINo = "JOR-".$dmonth.$dyear."00000";
			}
			else {
				while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
					$lastSI = $row['ctranno'];
				}
				
				
				if(substr($lastSI,2,2) <> $dmonth){
					$cSINo = "JOR-".$dmonth.$dyear."00000";
				}
				else{
					$baseno = intval(substr($lastSI,8,5)) + 1;
					$zeros = 5 - strlen($baseno);
					$zeroadd = "";
					
					for($x = 1; $x <= $zeros; $x++){
						$zeroadd = $zeroadd."0";
					}
					
					$baseno = $zeroadd.$baseno;
					$cSINo = "JOR-".$dmonth.$dyear.$baseno;
				}
			}

			if (!mysqli_query($con, "INSERT INTO mrp_jo(`compcode`, `ctranno`, `ctranno_main`, `ccode`, `crefSO`, `citemno`, `cunit`, `nqty`) values('$company', '$cSINo', '0', '".$row2['ccode']."', '".$row2['ctranno']."', '".$row2['citemno']."', '".$row2['cunit']."', '".$row2['totqty']."')")) {
			
				$status = "False";
				$msgz = $msgz . "<b>ERROR: </b>There's a problem generating your JO!";

			}else{
				$msgz = $msgz . "<b>SUCCESS: </b>Your JO is successfully generated";

				echo $cSINo."<br>";
				getsubs($row2['citemno'],$cSINo,$row2['ctranno'],1);
			}

		}


	//mysqli_query($con,"Update so set lsent=1 where compcode='$company' and ctranno='$tranno'");

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	//echo json_encode($json2);


?>