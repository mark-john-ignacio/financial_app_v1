<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";

	//use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use \PhpOffice\PhpSpreadsheet\Cell\DataType;

	// Create new Spreadsheet object
	$spreadsheet = new Spreadsheet();

	// Set document properties
	$spreadsheet->getProperties()->setCreator('Myx Financials')
		->setLastModifiedBy('Myx Financials')
		->setTitle('Balance Sheet Report')
		->setSubject('Balance Sheet Report')
		->setDescription('Balance Sheet Report, generated using Myx Financials.')
		->setKeywords('myx_financials balance_sheet')
		->setCategory('Myx Financials Report');


	$company = $_SESSION['companyid'];
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
					
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
						
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}


	//default acct for retained earnings
	$tagwithRetained = 0;

	$acct_retained = "";
	$title_retained = "";
	$result=mysqli_query($con,"Select * From accounts_default where compcode='$company' and ccode='EQ_RETAINED'");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$acct_retained = $row['cacctno'];
		$title_retained = $row['cdescription'];
	}


	$dteyr = $_POST["selyr"];

	//getall accounts
	$allaccounts = array();
	$result=mysqli_query($con,"SELECT A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Balance Sheet' ORDER BY CASE WHEN A.ccategory='ASSETS' THEN 1 WHEN A.ccategory='LIABILITIES' THEN 2 WHEN A.ccategory='EQUITY' THEN 3 END, A.nlevel, A.cacctid");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$allaccounts[] = $row;
	}


	//glactivity
	$arrallwithbal = array();

	$arrallwithbal[] = $acct_retained;
	getparent($acct_retained);

	$sql = "Select MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
		From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
		where A.compcode='$company' and YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
		and B.cFinGroup = 'Balance Sheet'
		Group By MONTH(ddate), A.acctno, B.cacctdesc
		Having sum(A.ndebit)<>0 or sum(A.ncredit)<>0
		Order By A.acctno, MONTH(ddate)";
	//echo $sql;

		$result=mysqli_query($con,$sql);

		$months = array();
		$darray = array();

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$darray[] = $row;
			$months[] = $row['dmonth'];
			$arrallwithbal[] = $row['acctno'];
			getparent($row['acctno']);

		}
		
		$hdr_months = array_unique($months);
		asort($hdr_months);

		function getparent($cacctno){
			global $allaccounts;
			global $arrallwithbal;
			
			foreach($allaccounts as $zx0){

				if($zx0['cacctid']==$cacctno){
					$arrallwithbal[] = $zx0['mainacct'];
					getparent($zx0['mainacct']);
				}
				
			}
		}
	//end glactivity

	//print_r($arrallwithbal);


	//RETAINED EARNING - FROM INCOME STATEMENT
		$RetainedGross = array();
		foreach($hdr_months as $rx){
			$RetainedGross[$rx] = 0;
			$RetainedREV[$rx] = 0;
			$RetainedCOS[$rx] = 0;
			$RetainedEXP[$rx] = 0;
		}
		
			$sql = "Select MONTH(ddate) as dmonth, B.ccategory, 
			CASE WHEN B.ccategory='COST OF SALES' THEN sum(A.ndebit) - sum(A.ncredit) 
					WHEN B.ccategory='EXPENSES' THEN sum(A.ndebit) - sum(A.ncredit)
					WHEN B.ccategory='REVENUE' THEN sum(A.ncredit) - sum(A.ndebit)
			END as nbalance
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where A.compcode='$company' and YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
			and B.cFinGroup = 'Income Statement'
			Group By MONTH(ddate), B.ccategory
			Having sum(A.ndebit)<>0 or sum(A.ncredit)<>0
			Order By B.ccategory, MONTH(ddate)";
		$result=mysqli_query($con,$sql);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if($row['ccategory']=="REVENUE"){
				$RetainedREV[$row['dmonth']] = $row['nbalance'];
			}

			if($row['ccategory']=="COST OF SALES"){
				$RetainedCOS[$row['dmonth']] = $row['nbalance'];
			}

			if($row['ccategory']=="EXPENSES"){
				$RetainedEXP[$row['dmonth']] = $row['nbalance'];
			}
		}

		foreach($hdr_months as $rx){

			$RetainedGross[$rx] = (floatval($RetainedREV[$rx]) - floatval($RetainedCOS[$rx])) - floatval($RetainedEXP[$rx]);

		}
	// END RETAINED //


	//sort accounts .. tree mode
	$mainarray = array();

	$dacctarraylist = array();
	$result=mysqli_query($con,"SELECT A.ccategory, A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Balance Sheet' and A.cacctid in ('".implode("','", $arrallwithbal)."') ORDER BY CASE WHEN A.ccategory='ASSETS' THEN 1 WHEN A.ccategory='LIABILITIES' THEN 2 WHEN A.ccategory='EQUITY' THEN 3 END, A.nlevel, A.cacctid");

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$dacctarraylist[] = $row;
	}

	foreach($dacctarraylist as $rs1){
		if(intval($rs1['nlevel'])==1){
			$mainarray[] = array('ccategory' => $rs1['ccategory'], 'cacctid' => $rs1['cacctid'], 'cacctdesc' => $rs1['cacctdesc'], 'ctype' => $rs1['ctype'], 'nlevel' => $rs1['nlevel'], 'mainacct' => $rs1['mainacct']);
			if($rs1['ctype']=="General"){
				getchild($rs1['cacctid'], $rs1['nlevel']);
			}
		}
	}

	function getchild($acctcode, $nlevel){
		global $dacctarraylist;
		global $mainarray;

		foreach($dacctarraylist as $rsz){
			if($rsz['mainacct']==$acctcode){
				$mainarray[] = array('ccategory' => $rsz['ccategory'], 'cacctid' => $rsz['cacctid'], 'cacctdesc' => $rsz['cacctdesc'], 'ctype' => $rsz['ctype'], 'nlevel' => $rsz['nlevel'], 'mainacct' => $rsz['mainacct']);

				if($rsz['ctype']=="General"){
					getchild($rsz['cacctid'], $rsz['nlevel']);
				}
			}
		}
	}

	function gettotal($acctid, $xctype, $xmo){
		global $darray;

		$xtot = 0;
		foreach($darray as $rsp){
			if($rsp['acctno']==$acctid && $rsp['dmonth']==$xmo){

				switch ($xctype) {
					case "ASSETS":
						$xtot = floatval($rsp['ndebit']) - floatval($rsp['ncredit']);
						break;
					case "LIABILITIES":
						$xtot = floatval($rsp['ncredit']) - floatval($rsp['ndebit']);
						break;
					case "EQUITY":
						$xtot = floatval($rsp['ncredit']) - floatval($rsp['ndebit']);
						break;
					}

				break;

			}
		}

		return $xtot;
	}


	// HEADER //
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Account No.');
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Account Name');

	$cols = 2;
	foreach($hdr_months as $rxzm){
		$monthNum  = $rxzm;
		$dateObj   = DateTime::createFromFormat('!m', $monthNum);
		$monthName = $dateObj->format('F');

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $monthName);

	}

	$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 1)->getCoordinate();
	$spreadsheet->getActiveSheet()->getStyle("A1:".$xcol2)->getFont()->setBold(true);
	//END HEADER//
	

	//BODY//

	$rows = 1;
	$cols = 0;
	if(count($mainarray)>0) {

		$arrlvl = array();
		$arrlvldsc = array();
		
		foreach($hdr_months as $rxzm){
			$twinGross[$rxzm] = 0;
	
			$arrlvlamt[0][$rxzm] = 0;
			$arrlvlamt[1][$rxzm] = 0;
			$arrlvlamt[2][$rxzm] = 0;
			$arrlvlamt[3][$rxzm] = 0;
			$arrlvlamt[4][$rxzm] = 0;
			$arrlvlamt[5][$rxzm] = 0;
		}

		$arrlvlcnt = 0;

		$ccate = $mainarray[0]['ccategory'];
		$cols++;
		$rows++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $ccate);
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$cols++;
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

		$csubcate = "";
		$nlevel = 0;
		
		foreach($mainarray as $row)
		{

			if(intval($row['nlevel']) < intval($arrlvlcnt)){

				$cols = 1;
				for($x=intval($row['nlevel']); $x<intval($arrlvlcnt); $x++){
				
					if($x!=intval($row['nlevel'])){
		
						$rows++;
						
						$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "");
						$cols++;
						$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "Total ".$arrlvldsc[$x]);

						foreach($hdr_months as $rxzm){
							$cols++;
							$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
							$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrlvlamt[intval($x)][$rxzm]);					
							$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
							$arrlvlamt[intval($x)][$rxzm] = 0;
						}

						$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
						$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);
	
					}
	
				}

				$cols = 1;
				$rows++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "");
				$cols++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "Total ".$arrlvldsc[intval($row['nlevel'])]);

				foreach($hdr_months as $rxzm){
					$cols++;
					$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrlvlamt[intval($row['nlevel'])][$rxzm]);					
					$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
					$arrlvlamt[intval($row['nlevel'])][$rxzm] = 0;
				}

				$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);
				
			}
	
			if($row['ctype']=="General"){
	
				$arrlvl[$row['nlevel']] = $row['cacctid']; 
				$arrlvldsc[$row['nlevel']] = $row['cacctdesc']; 
	
			}
			
			if($ccate!==$row['ccategory']){
				
				$cols = 1;
				$rows++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "TOTAL ".$ccate);
				$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$cols++;
				$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

				$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
				$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

				foreach($hdr_months as $rxzm){
					$cols++;
					$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrlvlamt[0][$rxzm]);					
					$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
					
					if($ccate=="LIABILITIES" || $ccate=="EQUITY"){
						$twinGross[$rxzm] = $twinGross[$rxzm] + floatval($arrlvlamt[0][$rxzm]);
					}
					$arrlvlamt[0][$rxzm] = 0;
				}

				$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);
		
				$cols = 1;
				$rows++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['ccategory']);
				$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$cols++;
				$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

				$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
				$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);
		
			}

			$cols = 1;
			$rows++;
			$xcvb = "";

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['cacctid']);
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['cacctdesc']);

			foreach($hdr_months as $rxzm){

				if($row['ctype']=="Details"){

					if($row['cacctid']==$acct_retained){
						$tagwithRetained = 1;
						$xcvb = $RetainedGross[$rxzm];
					}else{
						$xcvb = gettotal($row['cacctid'], $row['ccategory'], $rxzm);
					}

					for ($x = 0; $x < intval($row['nlevel']); $x++) {
						$arrlvlamt[$x][$rxzm] = $arrlvlamt[$x][$rxzm] + $xcvb;
					}  

					if(floatval($xcvb) > 0){
						//echo number_format($xcvb,2);

						$xmain = intval($row['nlevel']) - 1;
						
					}elseif(floatval($xcvb) < 0){
						//echo "(".number_format(abs($xcvb),2).")";
					}else{
						//echo "-";
					}
				}
				
				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xcvb);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				if($row['ctype']=="General"){
					$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$lastCellAddress)->getFont()->setBold(true);
				}

				$ccate = $row['ccategory'];
				$arrlvlcnt = $row['nlevel'];

			}

		}

		$cols = 0;
		if(intval($arrlvlcnt) !== 1){

			$rows++;
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "");
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "Total ".$arrlvldsc[intval($arrlvlcnt)-1]);

			foreach($hdr_months as $rxzm){
				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrlvlamt[intval($arrlvlcnt)-1][$rxzm]);					
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			}

			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);

		}
	

		$cols = 1;
		$rows++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "TOTAL ".$ccate);
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$cols++;
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

		foreach($hdr_months as $rxzm){
			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrlvlamt[0][$rxzm]);					
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
					
			if($ccate=="LIABILITIES" || $ccate=="EQUITY"){
				$twinGross[$rxzm] = $twinGross[$rxzm] + floatval($arrlvlamt[0][$rxzm]);
			}
			$arrlvlamt[0][$rxzm] = 0;
		}

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);


		//FOOTER//
		$cols = 1;
		$rows++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "TOTAL LIABILITIES & EQUITY");
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$cols++;
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

		foreach($hdr_months as $rxzm){
			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $twinGross[$rxzm]);					
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		}

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);

	}

	//END BODY//

	

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Balance_Sheet');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Balance_Sheet.xlsx"');
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