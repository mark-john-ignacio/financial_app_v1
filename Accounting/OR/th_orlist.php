<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	$company = $_SESSION['companyid'];
	
	if ($_REQUEST['y'] <> "") {
		$salesno = str_replace(",","','",$_REQUEST['y']);
		
		$qry = " and A.ctranno not in ('".$salesno."') ";
	}
	else {
		$qry = " ";
	}

	$tbl = "sales";
	$tbl2 = "sales_t";

	$receipt = $_REQUEST['type'];

	$receipttype = '';
	switch($receipt){
		case 'OR':
			$receipttype = "and A.csalestype = 'Services'";
			break;
		case 'CR':
			$receipttype = "and A.csalestype = 'Goods'";
			break;
	}

	//alldebitlist
	@$arradjlist = array();
	$sqlardj = "Select * From aradjustment X Where X.compcode='$company' and X.ccode='".$_REQUEST['x']."' and X.lapproved=1 and X.lvoid=0 and X.ctranno not in (Select A.aradjustment_ctranno from receipt_deds A left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lcancelled=0 and B.lvoid=0)";

	$resardj = mysqli_query ($con, $sqlardj);
	while($rowardj = mysqli_fetch_array($resardj, MYSQLI_ASSOC)){
		@$arradjlist[] = $rowardj;		
	}

	//allinvoice to get siseries
	@$arrsiseriesz = array();
	$sqlpay = "select a.ctranno, a.csiprintno from sales a where a.compcode='$company' and a.lcancelled = 0 and a.lvoid=0 UNION ALL select a.ctranno, a.csiprintno from ntsales a where a.compcode='$company' and a.lcancelled = 0 and a.lvoid=0";
	$respay = mysqli_query ($con, $sqlpay);
	while($rowsi = mysqli_fetch_array($respay, MYSQLI_ASSOC)){
		@$arrsiseriesz[$rowsi['ctranno']] = $rowsi['csiprintno'];
	}

	//allpayemnts
	@$arrpaymnts = array();
	$sqlpay = "select X.* from receipt_sales_t X left join receipt B on X.compcode=B.compcode and X.ctranno=B.ctranno where X.compcode='$company' and B.lcancelled = 0 and B.lvoid=0 order By X.csalesno, B.ddate";
	$respay = mysqli_query ($con, $sqlpay);
	while($rowardj = mysqli_fetch_array($respay, MYSQLI_ASSOC)){
		@$arrpaymnts[] = $rowardj;
	}

	$sql = "Select A.ctranno, A.ccurrencycode, A.dcutdate, A.ngrossbefore, A.ngross, A.nvat, A.newt, A.nnet, A.nexempt, A.nzerorated, A.cacctcode, D.cacctid, D.cacctdesc
	From ".$tbl." A
	left join customers C on A.compcode=C.compcode and A.ccode=C.cempid 
	left join accounts D on C.compcode=D.compcode and C.cacctcodesales=D.cacctno 
	where A.compcode='$company' and A.lapproved=1 and A.lvoid=0 $receipttype and A.ccode='".$_REQUEST['x']."'";

	//echo $sql;

	$result = mysqli_query ($con, $sql);
	
	$json2 = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$ntotaldue = $row['ngross'];

		$cDMTot = 0;
		$cCMTot = 0;
		foreach(@$arradjlist as $rxdebit){
			if($rxdebit['crefsi']==$row['ctranno'] && $rxdebit['ctype']=="Credit"){
				$cCMTot = $cCMTot + $rxdebit['ngross'];
			}

			if($rxdebit['crefsi']==$row['ctranno'] && $rxdebit['ctype']=="Debit"){
				$cDMTot = $cDMTot + $rxdebit['ngross'];
			}
		}

		$npay = 0;
		foreach(@$arrpaymnts as $rxpymnts){
			if($row['ctranno']==$rxpymnts['csalesno']){
				$npay = $npay + floatval($rxpymnts['napplied']);
			}
		}
		

		$ntotaldue = floatval($ntotaldue) - floatval($npay);		
		
		//echo $ntotal." - ".$npay."<br><br>";
		if(floatval($ntotaldue) > 0)
		{
	
			$json['csalesno'] = $row['ctranno'];			
			$json['dcutdate'] = $row['dcutdate'];

			$json['ngross'] = $row['ngross'];
			$json['ngrossdisplay'] = number_format($ntotaldue,2);
			$json['npayment'] = $npay;

			$json['cdm'] = $cCMTot;
			$json['ccm'] = $cDMTot;

			$json['cacctno'] = $row['cacctid'];
			$json['ctitle'] = $row['cacctdesc'];
			$json['ccurrencycode'] = $row['ccurrencycode'];
			$json['csalesseries'] = @$arrsiseriesz[$row['ctranno']];

			$json2[] = $json;
		 
		}
	}

	echo json_encode($json2);
	//echo "<pre>";
	//print_r($json2);
	//echo "</pre>";
	/*

	if($_REQUEST['typ']=="Trade"){

				$ewtcodes = explode(";",$row['newtrate']);
				$rsewtrates = array();
				foreach($ewtcodes as $rsewt){
					if($rsewt!="0" && $rsewt!=""){
						$rsewtrates[] = $rsewt . "% - ". number_format(floatval($row['ngross']) * (floatval($rsewt)/100),2);
					}
				}

				$json['cewtdesc'] = implode(";",$rsewtrates);
			}else{
				$json['cewtdesc'] = "-";
			}

			

	//For PARENT CODE
	$sql0 = "select A.ctranno, A.dcutdate, A.ngross, IFNULL(B.namount,0) as nCredit, IFNULL(C.namount,0) as nDebit, IFNULL(D.namount,0) as nPayments  , E.acctno, E.ctitle 
	from sales A 
	left join 
		( 
			select X.creference, sum(X.namount) as namount from aradj_t X left join aradj Y on X.compcode=Y.compcode and X.ctranno=Y.ctranno 
			where X.compcode='$company' and Y.lapproved = 1 and Y.ctype='Credit' 
			GROUP BY X.creference 
		) B on A.ctranno=B.creference left join 
		( 
			select U.creference, sum(U.namount) as namount from aradj_t U left join aradj V on U.compcode=V.compcode and U.ctranno=V.ctranno 
			where U.compcode='$company' and V.lapproved = 1 and V.ctype='Debit' 
			GROUP BY U.creference 
		) C on A.ctranno=C.creference left join
		( 
			select S.csalesno, sum(S.napplied) as namount from receipt_sales_t S left join receipt T on S.compcode=T.compcode and S.ctranno=T.ctranno 
			where S.compcode='$company' and T.lapproved = 1
			GROUP BY S.csalesno 
		) D on A.ctranno=D.csalesno
		left join glactivity E on A.compcode=E.compcode and A.ctranno=E.ctranno and E.ndebit <> 0
		left join customers F on A.compcode=F.compcode and A.ccode=F.cempid
	where A.compcode='$company' and A.lapproved=1 and year(dcutdate)>='2019' and F.cparentcode='".$_REQUEST['x']."' ".$qry." order by A.dcutdate, A.ctranno";
	
	//echo $sql;
	
	$result0 = mysqli_query ($con, $sql0);
	

	while($row0 = mysqli_fetch_array($result0, MYSQLI_ASSOC)){

		$ngross0 = $row0['ngross'];
		$ndm0 = $row0['nDebit'];
		$ncm0 = $row0['nCredit'];
		$npay0 = $row0['nPayments'];
		
		$ntotal0 = (((float)$ngross0 + (float)$ndm0) - (float)$ncm0) - (float)$npay0;

		if((float)$ntotal0 > 0)
		{
			
			 $json['csalesno'] = $row['ctranno'];
			 $json['dcutdate'] = $row['dcutdate'];
			 $json['ngross'] = $row['ngross'];
			 $json['ndebit'] = $row['nDebit'];
			 $json['ncredit'] = $row['nCredit'];
			 $json['npayment'] = $row['nPayments'];
			 $json['cacctno'] = $row['acctno'];
			 $json['ctitle'] = $row['ctitle'];
			 $json2[] = $json;
		 
		}

	}

	if(isset($json2)){
		echo json_encode($json2);
	}
	else{
		echo "";
	}

	*/

?>
