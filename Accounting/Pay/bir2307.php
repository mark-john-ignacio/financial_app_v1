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

	function getReportingPeriod($company, $paymentDate) {
		$companyData = getCompanyData($company);
		$reportingPeriodType = $companyData['reporting_period_type'];
	
		if ($reportingPeriodType === 'fiscal') {
			return computeFiscalDates($paymentDate, $companyData['fiscal_month_start_end']);
		} else {
			return computeCalendarDates($paymentDate);
		}
	}
	
	function getCompanyData($company) {
		global $con;
		$company = mysqli_real_escape_string($con, $company);
		$sql = "SELECT reporting_period_type, fiscal_month_start_end FROM company WHERE compcode = ?";
		$stmt = mysqli_prepare($con, $sql);
		mysqli_stmt_bind_param($stmt, "s", $company);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		return mysqli_fetch_assoc($result);
	}
	
	function computeFiscalDates($paymentDate, $fiscalMonthStartEnd) {
		$fiscalMonthStartEnd = incrementFiscalMonth($fiscalMonthStartEnd);
		$paymentMonth = date("m", strtotime($paymentDate));
		$paymentYear = date("Y", strtotime($paymentDate));
		$quarters = generateFiscalQuarters();
		$fiscalQuarters = $quarters[$fiscalMonthStartEnd];
	
		foreach ($fiscalQuarters as $index => $quarter) {
			if (in_array($paymentMonth, $quarter)) {
				$startMonth = $quarter[0];
				$endMonth = $quarter[2];
				
				// Determine the correct year for start and end dates
				$startYear = $paymentYear;
				$endYear = $paymentYear;
				
				// If the fiscal year starts after the payment month, adjust the years
				if ($index == 0 && $paymentMonth < $fiscalMonthStartEnd) {
					$startYear--;
					$endYear--;
				}
				
				// If the end month is less than the start month, it means we've crossed into the next year
				if ($endMonth < $startMonth) {
					$endYear++;
				}
	
				$startDate = sprintf("%s-01-%s", $startMonth, $startYear);
				$endDate = sprintf("%s-%s-%s", $endMonth, getLastDayOfMonth($endMonth), $endYear);
				
				return ['date1' => $startDate, 'date2' => $endDate];
			}
		}
	}
	
	function computeCalendarDates($paymentDate) {
		$paymentMonth = date("m", strtotime($paymentDate));
		$paymentYear = date("Y", strtotime($paymentDate));
		$quarter = ceil($paymentMonth / 3);
		$startMonth = ($quarter - 1) * 3 + 1;
		$endMonth = $quarter * 3;
		
		$startDate = sprintf("%02d-01-%s", $startMonth, $paymentYear);
		$endDate = sprintf("%02d-%s-%s", $endMonth, getLastDayOfMonth($endMonth), $paymentYear);
		
		return ['date1' => $startDate, 'date2' => $endDate];
	}
	
	function generateFiscalQuarters() {
		$quarters = [];
		for ($startMonth = 1; $startMonth <= 12; $startMonth++) {
			$quarters[sprintf('%02d', $startMonth)] = [];
			for ($i = 0; $i < 4; $i++) {
				$quarter = [];
				for ($j = 0; $j < 3; $j++) {
					$month = ($startMonth + $i * 3 + $j - 1) % 12 + 1;
					$quarter[] = sprintf('%02d', $month);
				}
				$quarters[sprintf('%02d', $startMonth)][] = $quarter;
			}
		}
		return $quarters;
	}
	
	function getLastDayOfMonth($month) {
		return $month == '02' ? '28' : (in_array($month, ['04', '06', '09', '11']) ? '30' : '31');
	}

	function incrementFiscalMonth($fiscalMonthStartEnd) {
		$nextMonth = ((int)$fiscalMonthStartEnd % 12) + 1;
		return sprintf('%02d', $nextMonth);
	}
	
	// Usage
	$reportingPeriod = getReportingPeriod($company, $data['dpaydate']);
	$data['date1'] = $reportingPeriod['date1'];
	$data['date2'] = $reportingPeriod['date2'];

	// Set content type to JSON and output the data
	header('Content-Type: application/json');
	echo json_encode($data);
?>
