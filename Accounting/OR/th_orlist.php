<?php
session_start();
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	if ($_REQUEST['y'] <> "") {
		$salesno = str_replace(",","','",$_REQUEST['y']);
		
		$qry = " and A.ctranno not in ('".$salesno."') ";
	}
	else {
		$qry = " ";
	}

	$tbl = "";
	if($_REQUEST['typ']=="Trade"){
		$tbl = "sales";
		$tbl2 = "sales_t";
	}elseif($_REQUEST['typ']=="Non-Trade"){
		$tbl = "ntsales";
		$tbl2 = "ntsales_t";
	}
	$receipt = $_REQUEST['type'];

	if($receipt === 'OR'){
		$receipttype = "Goods";
	} else{
		$receipttype = 'Services';
	}

	//alldebitlist
	@$arradjlist = array();
	$sqlardj = "select X.ctranno,X.crefsi, X.ngross, X.ctype from aradjustment X where X.compcode='$company' and X.ccode='".$_REQUEST['x']."' and IFNULL(crefsi,'') <> '' and isreturn = 0 and X.lapproved = 1 and X.ctranno not in (Select A.aradjustment_ctranno from receipt_deds A left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lcancelled=0 and B.lvoid=0)";
	$resardj = mysqli_query ($con, $sqlardj);
	while($rowardj = mysqli_fetch_array($resardj, MYSQLI_ASSOC)){
		@$arradjlist[] = $rowardj;		
	}

	//allpayemnts
	@$arrpaymnts = array();
	$sqlpay = "select X.* from receipt_sales_t X left join receipt B on X.compcode=B.compcode and X.ctranno=B.ctranno where X.compcode='$company' and B.lcancelled = 0 and B.lvoid=0 order By X.csalesno, B.ddate";
	$respay = mysqli_query ($con, $sqlpay);
	while($rowardj = mysqli_fetch_array($respay, MYSQLI_ASSOC)){
		@$arrpaymnts[] = $rowardj;
	}

	$sql = "Select A.ctranno, A.cacctid, A.cacctdesc, IFNULL(A.ctaxcode,'') as ctaxcode, A.nrate, A.ccurrencycode, IFNULL(A.cewtcode,'') as cewtcode, A.newtrate, A.dcutdate, SUM(ROUND(A.namountfull,2)) as ngross, SUM(ROUND(A.namount,2)) as cm, SUM(nvatgross) as nvatgross, (SUM(ROUND(A.namountfull,2)) - SUM(ROUND(A.namount,2)) - SUM(nvatgross)) as vatamt
	From (
		Select A.ctranno, A.citemno, ((A.nqtyreturned) * (A.nprice-A.ndiscount)) as namount, (A.nqty * (A.nprice-A.ndiscount)) as namountfull, B.dcutdate, D.cacctid, D.cacctdesc, A.ctaxcode, A.nrate, A.cewtcode, A.newtrate, B.ccurrencycode, 
					CASE 
						WHEN IFNULL(A.nrate,0) <> 0 
						THEN 
							ROUND(((A.nqty-A.nqtyreturned)*(A.nprice-A.ndiscount))/(1 + (A.nrate/100)),2)
						ELSE 
							A.namount 
						END as nvatgross
	From ".$tbl2." A 
	left join ".$tbl." B on A.compcode=B.compcode and A.ctranno=B.ctranno 
	left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
	left join accounts D on C.compcode=D.compcode and C.cacctcodesales=D.cacctno 
	left join wtaxcodes E on A.compcode=E.compcode and A.cewtcode=E.ctaxcode 
	where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and B.csalestype = '$receipttype' and B.ccode='".$_REQUEST['x']."') A
	Group By A.ctranno, A.cacctid, A.cacctdesc, A.ctaxcode, A.nrate, A.cewtcode, A.newtrate, A.dcutdate, A.ccurrencycode
	order by A.dcutdate, A.ctranno";

	//echo $sql;

	$result = mysqli_query ($con, $sql);
	
	$json2 = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$ntotal = $row['ngross'];
		$ngross = $row['ngross'];

			 $nwithadjcm = 0;
			 $nwithadjdm = 0;
			 foreach(@$arradjlist as $rxdebit){
				if($rxdebit['crefsi']==$row['ctranno'] && $rxdebit['ctype']=="Credit"){
					$nwithadjcm = $nwithadjcm + $rxdebit['ngross'];
				}

				if($rxdebit['crefsi']==$row['ctranno'] && $rxdebit['ctype']=="Debit"){
					$nwithadjdm = $nwithadjdm + $rxdebit['ngross'];
				}
			 }

			 $npay = 0;
			 $cntofist = 0;
			 foreach(@$arrpaymnts as $rxpymnts){
				if($row['ctranno']==$rxpymnts['csalesno'] && $row['ctaxcode']==$rxpymnts['ctaxcodeorig'] && $row['cewtcode']==$rxpymnts['cewtcodeorig']){
					$cntofist++;
					
					if($cntofist==1){
						$ntotal = floatval($rxpymnts['ndue']) - floatval($rxpymnts['napplied']);
					}

					$npay = $npay + floatval($rxpymnts['napplied']);
				}
			 }
		
		
		//echo $ntotal." - ".$npay."<br><br>";
		if(floatval($ntotal) > 0)
		{
			
			$json['csalesno'] = $row['ctranno'];
			$json['cewtcode'] = $row['cewtcode'];
			$json['newtrate'] = $row['newtrate'];

			$json['vatrate'] = $row['nrate'];
			$json['ctaxcode'] = $row['ctaxcode'];

			if($npay==0){
							
				$json['cvatamt'] = $row['vatamt'];
				$json['cnetamt'] = $row['nvatgross'];
				
			}else{
					//get VATABLE AMOUNT OF PAID - ibawas ang ewtrate

					$grossamt = floatval($npay) / ((100-floatval($row['newtrate']))/100);
					$dewt = round($grossamt,2) * (floatval($row['newtrate'])/100);
					$vatableamt = round($grossamt,2) / (1+(floatval($row['nrate'])/100));
					$dvatamt = round($vatableamt,2) * (floatval($row['nrate'])/100);
							
					$json['cvatamt'] = floatval($row['vatamt']) - round($dvatamt,2);
					$json['cnetamt'] = floatval($row['nvatgross']) - round($vatableamt,2);
			}	
					 		
			$json['cdm'] = $nwithadjdm;
			$json['ccm'] = floatval($row['cm']) + $nwithadjcm;
			$json['dcutdate'] = $row['dcutdate'];
			$json['ngross'] = $row['ngross'];			 
			//$json['withadj'] = $nwithadj;
			$json['npayment'] = $npay;
			$json['cacctno'] = $row['cacctid'];
			$json['ctitle'] = $row['cacctdesc'];
			$json['ccurrencycode'] = $row['ccurrencycode'];

			$json2[] = $json;
		 
		}

	}

	echo json_encode($json2);

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
