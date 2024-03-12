<?php
	if(!isset($_SESSION)){
	session_start();
	}
	include('../../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];
	$ctranno = $_REQUEST['id'];
	$crid = $_REQUEST['crid'];

	function getacctcode($xcode,$xid){
		global $company;
		global $con;
		$cdsec = array();
		if($xcode!=""){
			$sql = mysqli_query($con,"SELECT cacctid, cacctdesc FROM `accounts` where compcode='$company' and cacctid='$xcode'"); 
		}else{
			$sql = mysqli_query($con,"SELECT cacctid, cacctdesc FROM `accounts` where compcode='$company' and cacctno='$xid'"); 
		}
		
		if (mysqli_num_rows($sql)!=0) {
			while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
				$cdsec[] = $row;
			}
		}

		return $cdsec;
	}

	@$arrtaxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and ctype = 'Purchase' and cstatus='ACTIVE' order By cvatdesc"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[$row['cvatcode']] = number_format($row['nrate']); 
		}
	}

	$dmonth = date("m");
	$dyear = date("y");

	$chkSales = mysqli_query($con,"select * from apv where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "AP".$dmonth.$dyear."00000";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		//echo $lastSI."<br>";
		//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
		if(substr($lastSI,2,2) <> $dmonth){
			$cSINo = "AP".$dmonth.$dyear."00000";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "AP".$dmonth.$dyear.$baseno;
		}
	}

	echo "<center><font size='4'>Generating APV Others ". $_REQUEST['id'] . "<br>".$cSINo."</font></center>";

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	$nvaluecurrbase = "";	
	$nvaluecurrbasedesc = "";	
	$result = mysqli_query($con,"SELECT A.cvalue, CONCAT(B.symbol,\" - \",B.country,\" \",B.unit) as currencyName FROM parameters A left join currency_rate B on A.compcode=B.compcode and A.cvalue=B.symbol WHERE A.compcode='$company' and A.ccode='DEF_CURRENCY'"); 

	if (mysqli_num_rows($result)!=0) {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){																				
			$nvaluecurrbase = $row['cvalue'];
			$nvaluecurrbasedesc = $row['currencyName'];
		}
	}

	//get default Input tax acct code
	@$OTpaydef = "";
	@$OTpaydefdsc = ""; 
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='PURCH_VAT'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$OTpaydef = $row['cacctno']; 
			@$OTpaydefdsc = $row['cacctdesc']; 
		}
	}

	$sqlhead = mysqli_query($con,"Select A.*, IFNULL(B.cname,'') as cname, IFNULL(C.cacctdesc,'') as cacctdesc, B.cacctcode as suppacctcode From apv_temp A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctid Where A.compcode='$company' and A.nid='$ctranno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){	
		
			//header
			$xref = $row['cparticulars'];
			if($row['cref']!=""){
				$xref = $xref." Reference: ".$row['cref'];
			}

			$cname = mysqli_real_escape_string($con, $row['cname']);

			if (!mysqli_query($con, "INSERT INTO `apv`(`compcode`, `ctranno`, `ddate`, `dapvdate`, `ccode`, `cpayee`, `cpaymentfor`, `ngross`, `cpreparedby`, `captype`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`) values('$company', '$cSINo', NOW(), '".$row['ddate']."', '".$row['ccode']."', '".$cname."','".$xref."', '".$row['ngross']."', '$preparedby', 'Others', '$nvaluecurrbase', '$nvaluecurrbasedesc', '1')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} else{

				//details
				$z = 1;
				$crefrr = "";
				$refcidenttran = $cSINo."P".$z;

				$ndebit = floatval($row['nvatamt']) + floatval($row['nnonvat']);
				mysqli_query($con,"INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cewtcode`, `newtrate`) values('$company', '$refcidenttran', '$z', '$cSINo', '$crefrr', '".$row['cacctcode']."', '".$row['cacctdesc']."', '', $ndebit, 0, '', 0)");

				if(floatval($row['nvatamt']) > 0){
					$z++;
					$crefrr = "";
					$refcidenttran = $cSINo."P".$z;

					$ndebit = floatval($row['nvat']);

					$xrae = 0;
					if($row['cvatcode']!=""){
						$xrae = @$arrtaxlist[$row['cvatcode']];
					}
					mysqli_query($con,"INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cewtcode`, `newtrate`) values('$company', '$refcidenttran', '$z', '$cSINo', '$crefrr', '".@$OTpaydef."', '".@$OTpaydefdsc."', '', $ndebit, 0, '".$row['cvatcode']."', ".$xrae.")");
				}

				$z++;
				$crefrr = "";
				$refcidenttran = $cSINo."P".$z;

				if($crid!=0){
					$bngh = getacctcode($crid,"");
				}else{
					$bngh = getacctcode("",$row['suppacctcode']); 
				}
				
				$ncredit = floatval($row['nvatamt']) + floatval($row['nnonvat']) + floatval($row['nvat']);
				mysqli_query($con,"INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cewtcode`, `newtrate`) values('$company', '$refcidenttran', '$z', '$cSINo', '$crefrr', '".$bngh[0]['cacctid']."', '".$bngh[0]['cacctdesc']."', '', 0, $ncredit, '', 0)");
			}
			
		}
		
	}

	mysqli_query($con,"UPDATE apv_temp set crem='Y' where compcode='$company' and crem = 'N' and nid='$ctranno'");

?>
 <script>
 	window.location="uploadtran_Del.php?crid=<?=$crid?>";
 </script>
