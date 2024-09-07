<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];
	
	// Initialize data array
	$data = array();

	// ew and vat accts PURCH_VAT EWTPAY
	$disreg = array();
	$disregEWT = "";
	$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
	$result = mysqli_query($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$disreg[] = $row['cacctno'];
		if($row['ccode']=="EWTPAY"){
			$disregEWT = $row['cacctno'];
		}
	}

	// PAYOR INFO
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$data['compname'] =  $row['compname'];
		$data['comptin'] =  str_replace("-",".",$row['comptin']);
		$data['compadd'] =  $row['compadd']; 
		$data['compzip'] =  $row['compzip'];
	}

	// PAYEE INFO
	$ccodesxz = "";
	$dwithnorefz = 0;
	$sqlrfp = "select * From paybill where compcode='$company' and ctranno='".$_POST["id"]."'";
	$result=mysqli_query($con,$sqlrfp);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$ccodesxz = $row['ccode'];
		$data['dpaydate'] = $row['ddate'];
		$dwithnorefz = $row['lnoapvref'];
	}

	// PAYEE INFO
	$sql = "select * From suppliers where compcode='$company' and ccode='".$ccodesxz."'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$data['payeename'] =  $row['cname'];
		$data['payeetin'] =  str_replace("-",".",$row['ctin']);
		$data['payeeadd'] =  $row['chouseno']; 
		if($row['ccity']!=""){
			$data['payeeadd'] .= ", ".$row['ccity'];
		}
		if($row['cstate']!=""){
			$data['payeeadd'] .= ", ".$row['cstate'];
		}
		if($row['ccountry']!=""){
			$data['payeeadd'] .= ", ".$row['ccountry'];
		}
		$data['payeezip'] =  $row['czip'];
	}

	$arrqone = array('01','02','03');
	$arrqtwo = array('04','05','06',);
	$arrqtri = array('07','08','09');
	$arrqfor = array('10','11','12');

	// Get the reporting_period_type from the company table
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$data['reporting_period_type'] = $row['reporting_period_type'];
	}
	// if reporting_period_type is fiscal, Get the fiscal_month_start_end from the company table
	if ($data['reporting_period_type'] == 'fiscal') {
		$sql = "select * From company where compcode='$company'";
		$result=mysqli_query($con,$sql);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$data['fiscal_month_start_end'] = $row['fiscal_month_start_end'];
		}
	}
	// create condition for the date1 and date2 based on the fiscal_month_start_end, fiscal_month_start_end is a two digit month value
	if ($data['reporting_period_type'] == 'fiscal') {
		$data = computeFiscalDates($data);
	// else if reporting_period_type is calendar, use the code below
	}else{
	
		$dmonth = date("m", strtotime($data['dpaydate']));
		$dyear = date("Y", strtotime($data['dpaydate']));

		if(in_array($dmonth, $arrqone)){
			$data['date1'] = "0131".$dyear;
			$data['date2'] = "0331".$dyear;
		}elseif(in_array($dmonth, $arrqtwo)){
			$data['date1'] = "0401".$dyear;
			$data['date2'] = "0630".$dyear;
		}elseif(in_array($dmonth, $arrqtri)){
			$data['date1'] = "0701".$dyear;
			$data['date2'] = "0930".$dyear;
		}elseif(in_array($dmonth, $arrqfor)){
			$data['date1'] = "1001".$dyear;
			$data['date2'] = "1231".$dyear;
		}
	}

	function computeFiscalDates($data) {
		$dmonth = date("m", strtotime($data['dpaydate']));
		$dyear = date("Y", strtotime($data['dpaydate']));
		$fiscal_month_start_end = $data['fiscal_month_start_end'];
	
		// Define fiscal quarters based on the start month
		$quarters = [
			'01' => [['01', '02', '03'], ['04', '05', '06'], ['07', '08', '09'], ['10', '11', '12']],
			'02' => [['02', '03', '04'], ['05', '06', '07'], ['08', '09', '10'], ['11', '12', '01']],
			'03' => [['03', '04', '05'], ['06', '07', '08'], ['09', '10', '11'], ['12', '01', '02']],
			'04' => [['04', '05', '06'], ['07', '08', '09'], ['10', '11', '12'], ['01', '02', '03']],
			'05' => [['05', '06', '07'], ['08', '09', '10'], ['11', '12', '01'], ['02', '03', '04']],
			'06' => [['06', '07', '08'], ['09', '10', '11'], ['12', '01', '02'], ['03', '04', '05']],
			'07' => [['07', '08', '09'], ['10', '11', '12'], ['01', '02', '03'], ['04', '05', '06']],
			'08' => [['08', '09', '10'], ['11', '12', '01'], ['02', '03', '04'], ['05', '06', '07']],
			'09' => [['09', '10', '11'], ['12', '01', '02'], ['03', '04', '05'], ['06', '07', '08']],
			'10' => [['10', '11', '12'], ['01', '02', '03'], ['04', '05', '06'], ['07', '08', '09']],
			'11' => [['11', '12', '01'], ['02', '03', '04'], ['05', '06', '07'], ['08', '09', '10']],
			'12' => [['12', '01', '02'], ['03', '04', '05'], ['06', '07', '08'], ['09', '10', '11']],
		];
	
		// Get the quarters for the given fiscal start month
		$fiscalQuarters = $quarters[$fiscal_month_start_end];
	
		// Determine the quarter based on the payment month
		foreach ($fiscalQuarters as $index => $quarter) {
			if (in_array($dmonth, $quarter)) {
				$data['date1'] = $quarter[0] . "01" . $dyear;
				$data['date2'] = $quarter[2] . "30" . $dyear;
				// Adjust the end date for February
				if ($quarter[2] == '02') {
					$data['date2'] = $quarter[2] . "28" . $dyear;
				}
				// Adjust the end date for months with 31 days
				if (in_array($quarter[2], ['01', '03', '05', '07', '08', '10', '12'])) {
					$data['date2'] = $quarter[2] . "31" . $dyear;
				}
				break;
			}
		}
	
		return $data;
	}

	// Get signature image
	$signimg = "";
	$sqlimg = "select * From parameters where compcode='$company' and ccode='BIR2307_sign'";
	$result=mysqli_query($con,$sqlimg);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$signimg = $row['cvalue'];
	}
	$data['signimg'] = $signimg;

	// Process and return amounts
	$NOREFGAmt = 0;
	$totdues = 0;
	$totewts = 0;
	$details = array();

	if($dwithnorefz==1){
		$sqlrfp = "Select SUM(A.namount) as namount from paybill_t A where A.compcode='$company' and A.ctranno='".$_POST["id"]."' and A.cacctno not in ('".implode("','",$disreg)."') and A.entrytyp = 'Debit'";
		$result=mysqli_query($con,$sqlrfp);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$NOREFGAmt = $row['namount'];
		}
		$sqlrfp = "Select A.cewtcode, A.namount, A.newtamt, B.cdesc as ewtdesc, B.nrate from paybill_t A left join wtaxcodes B on A.compcode=B.compcode and A.cewtcode=B.ctaxcode where A.compcode='$company' and A.ctranno='".$_POST["id"]."' and A.cacctno='".$disregEWT."'";
	}else{
		$sqlrfp = "select B.compcode, B.ctranno, GROUP_CONCAT(if (B.cewtcode ='', null, B.cewtcode)) as cewtcode, sum(B.namount) as namount, sum(B.ndue) as ndue, sum(B.newtamt) as newtamt, C.cdesc as ewtdesc, C.nrate
		From 
			(					
				Select G.compcode, G.ctranno, 
				CASE WHEN G.cacctno = '".$disregEWT."' THEN G.cewtcode ELSE '' END as cewtcode, 
				CASE WHEN G.cacctno not in ('".implode("','",$disreg)."') and G.ndebit <> 0 THEN G.ndebit ELSE 0 END as namount, 
				CASE WHEN G.cacctno not in ('".implode("','",$disreg)."') and G.ndebit <> 0 THEN G.ndebit ELSE 0 END as ndue,
				CASE WHEN G.cacctno = '".$disregEWT."' THEN G.ncredit ELSE 0 END as newtamt
				From apv_t G 
				left join apv H on G.compcode=H.compcode and G.ctranno=H.ctranno
				left join accounts I on G.compcode=I.compcode and G.cacctno=I.cacctid
				Where G.compcode='$company'
			) B
		left join wtaxcodes C on B.compcode=C.compcode and B.cewtcode=C.ctaxcode
		where B.compcode='$company' and B.ctranno in (Select capvno from paybill_t where compcode='$company' and ctranno='".$_POST["id"]."')
		Group By B.compcode, B.ctranno";
	}

	$result=mysqli_query($con,$sqlrfp);
	$cnt = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if($row['cewtcode']!=""){
			$cnt++;
			$details[] = array(
				'ewtdesc' => $row['ewtdesc']."(".$row['nrate'].")",
				'cewtcode' => str_replace(",","",$row['cewtcode']),
				'amount' => ($dwithnorefz==1) ? number_format($NOREFGAmt,2) : number_format($row['namount'],2),
				'newtamt' => number_format($row['newtamt'],2),
			);
			if($dwithnorefz==1) {
				$totdues += floatval($NOREFGAmt);
			}else{
				$totdues += floatval($row['namount']);
			}
			$totewts += floatval($row['newtamt']);
		}
	}

	$data['details'] = $details;
	$data['totdues'] = number_format($totdues, 2);
	$data['totewts'] = number_format($totewts, 2);

	// Set content type to JSON and output the data
	header('Content-Type: application/json');
	echo json_encode($data);
?>
