<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$employeeid = $_SESSION['employeeid'];

	$date1 = $_POST["date1"];
	$salestat = $_POST["selstat"];

	$arrinvs = array();
	$arrsupplist = array();

		//select all Sales Invoice
		/*if($salestat=="Trade"){
			$tbl = "sales";
		}elseif($salestat=="Non-Trade"){
			$tbl = "ntsales";
		}*/
	
		//if($salestat!==""){
			$sqlsuppinv = "Select A.ctranno, A.ngross, IFNULL(C.ncm,0) as ncm, IFNULL(C.ndm,0) as ndm, IFNULL(C.napplied,0) as napplied, A.ccode, A.dcutdate
			from ".$tbl." A 
			left JOIN
				(
					Select Z.csalesno, Sum(Z.napplied) as napplied, Sum(Z.ncm) as ncm, Sum(Z.ndm) as ndm
					From receipt_sales_t Z
					left join receipt X on Z.compcode=X.compcode and Z.ctranno=X.ctranno
					Where Z.compcode='$company' and X.lapproved=1
					Group by Z.csalesno
				) C on A.ctranno=C.csalesno
			Where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$date1', '%m/%d/%Y') and A.lapproved=1";
		/*}else{
			$sqlsuppinv = "Select A.ctranno, A.ngross, IFNULL(C.ncm,0) as ncm, IFNULL(C.ndm,0) as ndm, IFNULL(C.napplied,0) as napplied, A.ccode, A.dcutdate
			from sales A 
			left JOIN
				(
					Select Z.csalesno, Sum(Z.napplied) as napplied, Sum(Z.ncm) as ncm, Sum(Z.ndm) as ndm
					From receipt_sales_t Z
					left join receipt X on Z.compcode=X.compcode and Z.ctranno=X.ctranno
					Where Z.compcode='$company' and X.lapproved=1
					Group by Z.csalesno
				) C on A.ctranno=C.csalesno
			Where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$date1', '%m/%d/%Y') and A.lapproved=1
			UNION ALL
			Select A.ctranno, A.ngross, IFNULL(C.ncm,0) as ncm, IFNULL(C.ndm,0) as ndm, IFNULL(C.napplied,0) as napplied, A.ccode, A.dcutdate
			from ntsales A 
			left JOIN
				(
					Select Z.csalesno, Sum(Z.napplied) as napplied, Sum(Z.ncm) as ncm, Sum(Z.ndm) as ndm
					From receipt_sales_t Z
					left join receipt X on Z.compcode=X.compcode and Z.ctranno=X.ctranno
					Where Z.compcode='$company' and X.lapproved=1
					Group by Z.csalesno
				) C on A.ctranno=C.csalesno
			Where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$date1', '%m/%d/%Y') and A.lapproved=1";
		}*/
		
		$result=mysqli_query($con,$sqlsuppinv);
	
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrinvs[] = $row;
			$sinet = (floatval($row['ngross']) + floatval($row['ndm'])) - floatval($row['ncm']);
	
			if($sinet > floatval($row['napplied'])){
				$arrsupplist[] = $row['ccode'];
			}
		}

	//customers
	$arrsuppx = array();
	$sqlpay = "Select A.cempid as ccode, COALESCE(A.ctradename,A.cname) as cname, A.cterms, IFNULL(B.nallow,0) as nintval from customers A left join groupings B on A.compcode=B.compcode and A.cterms=B.ccode and B.ctype='TERMS' Where A.compcode='$company' and A.cempid in ('".implode("','", $arrsupplist)."') Order by A.cname";
	$result=mysqli_query($con,$sqlpay);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrsuppx[] = array('ccode' => $row['ccode'], 'cname' => $row['cname'], 'terms' => $row['nintval']);
	}

	//use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use \PhpOffice\PhpSpreadsheet\Cell\DataType;

	// Create new Spreadsheet object
	$spreadsheet = new Spreadsheet();

	// Set document properties
	$spreadsheet->getProperties()->setCreator('Myx Financials')
			->setLastModifiedBy('Myx Financials')
			->setTitle('AR Ageing Report')
			->setSubject('AR Ageing Report')
			->setDescription('AR Ageing Report, generated using Myx Financials.')
			->setKeywords('myx_financials ar_ageing')
			->setCategory('Myx Financials Report');

	// Start Header
	$cols = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Suppliers');

	$arrdays = array();
	$arrtotperage = array();

	$sql = "select * from ageing_days where compcode='$company' and cagetype='AR' order by id";
	$result=mysqli_query($con,$sql);
	$cntr = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrdays[] = $row;

		$arrtotperage[$row['id']] = 0;

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $row['cdesc']);
	}

	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, 'Grand Total');
	// End Headers

	// Start Details

	$gtot = 0;
	$gtotftr = 0;
	$cols = 0;
	$rows = 1;

	foreach($arrsuppx as $rws0){
		$cols++;
		$rows++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rws0['cname']);

		foreach($arrdays as $row){

			$cols++;

			$nmtot = 0;
			foreach($arrinvs as $xr2){
				if($xr2['ccode']==$rws0['ccode']){

					$sinet = (floatval($xr2['ngross']) + floatval($xr2['ndm'])) - floatval($xr2['ncm']);
					$remain = $sinet - round(floatval($xr2['napplied']),2);

					if(round($sinet,2) > round(floatval($xr2['napplied']),2)){

						$dategvn = $xr2['dcutdate'];
						$cterms = $rws0['terms'];

						$your_date = date('Y-m-d', strtotime($dategvn. " + {$cterms} days"));


						$now = time(); // or your date as well $rws0['terms']
						$datediff = $now - strtotime($your_date);

						$datediff = round($datediff / (60 * 60 * 24));

						if($datediff < 0 && floatval($row['fromdays']) == 0  && floatval($row['todays']) == 0){
							$nmtot = $nmtot + $remain;
						}else{

							if(floatval($row['fromdays']) > 0  && floatval($row['todays']) == 0){

								if($datediff >= floatval($row['fromdays'])){
	
									$nmtot = $nmtot + $remain;
									
								}
	
							}else{
								if(($datediff >= floatval($row['fromdays'])) && ($datediff <= floatval($row['todays']))){
									//echo $datediff.": ".$xr2['ngross']."<br>";
									$nmtot = $nmtot + $remain;
		
								}
							}

						}
			
						

					//	if(($datediff >= floatval($row['fromdays'])) && ($datediff <= floatval($row['todays']))){
							//echo $datediff.": ".$xr2['ngross']."<br>";
					//		$nmtot = $nmtot + floatval($xr2['ngross']);
					//	}

						
					}
				}
			}

			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $nmtot);

			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			
			$arrtotperage[$row['id']] = $arrtotperage[$row['id']] + $nmtot;

			$gtot = $gtot + $nmtot;

		}

		$cols++;
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $gtot);

		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		
		$gtotftr = $gtotftr + $gtot; 

		$gtot = 0;

		$cols = 0;

	}
	// End Details


	//TOTALS
	$rows++;
	$cols = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "TOTAL");
	foreach($arrdays as $row){
		$cols++;

		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrtotperage[$row['id']]);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	}
	$cols++; 
	$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $gtotftr);
	$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");



// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('AP_Ageing_Report');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="AR_Ageing_Report.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

?>