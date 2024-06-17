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
		->setTitle('Income Statement Report')
		->setSubject('Income Statement Report')
		->setDescription('Income Statement Report, generated using Myx Financials.')
		->setKeywords('myx_financials income_statement')
		->setCategory('Myx Financials Report');

	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Account No.')
    ->setCellValue('B1', 'Account Title')
		->setCellValue('C1', '₱');

	$spreadsheet->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);


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

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	//getall accounts
	$allaccounts = array();
	$result=mysqli_query($con,"SELECT A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Income Statement' ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$allaccounts[] = $row;
	}

	//glactivity
		$arrallwithbal = array();
		$sql = "Select A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
				From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
				where A.compcode='$company' and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
				and B.cFinGroup = 'Income Statement'
				Group By A.acctno, B.cacctdesc
				Having sum(A.ndebit)<>0 or sum(A.ncredit)<>0
				Order By A.acctno";

		$result=mysqli_query($con,$sql);

		$darray = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$darray[] = $row;
			$arrallwithbal[] = $row['acctno'];
			//echo $row['acctno']."<br>";
			getparent($row['acctno']);

		}

		function getparent($cacctno){
			global $allaccounts;
			global $arrallwithbal;
			
			foreach($allaccounts as $zx0){

				if($zx0['cacctid']==$cacctno){
					//echo $zx0['cacctid']."".$zx0['mainacct']."<br><br>";
					$arrallwithbal[] = $zx0['mainacct'];
					getparent($zx0['mainacct']);
				}
				
			}
		}
	//end glactivity

	//echo "<pre>";
	//print_r(print_r($arrallwithbal));
	//echo "</pre>";

	//sort accounts .. tree mode
	$mainarray = array();
	$dacctarraylist = array();
	$result=mysqli_query($con,"SELECT A.ccategory, A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Income Statement' and A.cacctid in ('".implode("','", $arrallwithbal)."') ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$dacctarraylist[] = $row;
	}

//echo "<pre>";
//print_r($dacctarraylist);
//echo "</pre>";

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

	function gettotal($acctid, $xctype){
		global $darray;

		$xtot = 0;
		foreach($darray as $rsp){
			if($rsp['acctno']==$acctid){

				switch ($xctype) {
					case "REVENUE":
						$xtot = floatval($rsp['ncredit']) - floatval($rsp['ndebit']);
						break;
					case "COST OF SALES":
						$xtot = floatval($rsp['ndebit']) - floatval($rsp['ncredit']);
						break;
					case "EXPENSES":
						$xtot = floatval($rsp['ndebit']) - floatval($rsp['ncredit']);
						break;
					}

				break;

			}
		}

		return $xtot;
	}


	$cnt = 1;

	if(count($mainarray)>0) {

		$profitRevn = 0;
		$profitCost= 0;
		$BPROFITzc0 = 0;
		$BPEXPzc0 = 0;


		$arrlvl = array();
		$arrlvldsc = array();
		$arrlvlamt[0] = 0;
		$arrlvlamt[1] = 0;
		$arrlvlamt[2] = 0;
		$arrlvlamt[3] = 0;
		$arrlvlamt[4] = 0;
		$arrlvlamt[5] = 0;

		$arrlvlcnt = 0;

		$ccate = $mainarray[0]['ccategory'];

		echo "<tr><td colspan='3'><b>".$ccate."</b></td></tr>";

		$csubcate = "";

		$nlevel = 0;
		
		foreach($mainarray as $row)
		{
	
			if(intval($row['nlevel']) < intval($arrlvlcnt)){

				$cnt++;
				$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$cnt, "")
					->setCellValue('B'.$cnt, "Total ".$arrlvldsc[intval($row['nlevel'])]) 
					->setCellValue('C'.$cnt, $arrlvlamt[intval($row['nlevel'])]);

				$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
				$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);

				$arrlvlamt[intval($row['nlevel'])] = 0;
			}

			if($row['ctype']=="General"){

				$arrlvl[$row['nlevel']] = $row['cacctid']; 
				$arrlvldsc[$row['nlevel']] = $row['cacctdesc']; 

			}
			
			if($ccate!==$row['ccategory']){

				$cnt++;

				$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$cnt, "TOTAL ".$ccate)
					->setCellValue('C'.$cnt, $arrlvlamt[0]);
				$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
				$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);


				if($ccate=="REVENUE"){
					$profitRevn = floatval($arrlvlamt[0]);
				}

				if($ccate=="COST OF SALES"){				
					$profitCost= floatval($arrlvlamt[0]);
				}

				$arrlvlamt[0] = 0;


				if($row['ccategory']=="EXPENSES"){
					$BPROFITzc0 = floatval($profitRevn) - floatval($profitCost);
					$donetwo = ($BPROFITzc0<0) ? "(".number_format(abs($BPROFITzc0),2).")" : number_format(($BPROFITzc0),2);

					$cnt++;
					$spreadsheet->setActiveSheetIndex(0)
						->setCellValue('A'.$cnt, "GROSS PROFIT")
						->setCellValue('C'.$cnt, $donetwo);
					$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
					$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);

				}

				$cnt++;
				$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$cnt, $row['ccategory']);
				$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":C".$cnt);
				$spreadsheet->getActiveSheet()->getStyle("A".$cnt)->getFont()->setBold(true);
			}


			$cnt++;
			$xcvb = "";
			if($row['ctype']=="Details"){
				$xcvb = gettotal($row['cacctid'], $row['ccategory']);

				for ($x = 0; $x < intval($row['nlevel']); $x++) {
					$arrlvlamt[$x] = $arrlvlamt[$x] + $xcvb;
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

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['cacctid'])
			->setCellValue('B'.$cnt, $row['cacctdesc']) 
			->setCellValue('C'.$cnt, $xcvb);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			if($row['ctype']=="General"){
				$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);
			}

			$ccate = $row['ccategory'];
			$arrlvlcnt = $row['nlevel'];

		}


		if(intval($arrlvlcnt) !== 1){

			//$GENxyz1 = getpads($arrlvlcnt-1);

			$cnt++;
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A'.$cnt, "")
				->setCellValue('B'.$cnt, "Total ".$arrlvldsc[intval($arrlvlcnt)-1]) 
				->setCellValue('C'.$cnt, $arrlvlamt[intval($arrlvlcnt)-1]);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
			$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);


		}
	
		$donetwo = ($arrlvlamt[0]<0) ? "(".number_format(abs($arrlvlamt[0]),2).")" : number_format(($arrlvlamt[0]),2);

		$cnt++;
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, "TOTAL ".$ccate)
			->setCellValue('C'.$cnt, $donetwo);
		$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
		$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
		$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);
	
		if($ccate=="REVENUE"){
			$profitRevn = floatval($arrlvlamt[0]);
		}
	
		if($ccate=="COST OF SALES"){				
			 $profitCost= floatval($arrlvlamt[0]);
		}
		
		if($ccate=="EXPENSES"){
			$BPEXPzc0 = floatval($arrlvlamt[0]);
		}
	
		
		if(floatval($BPEXPzc0)==0){
			$xctot = floatval($profitRevn) - floatval($profitCost);
		}else{
			$xctot = $BPROFITzc0-$BPEXPzc0;
		}

		$xctotax = 0;
		$xctotaxaftr = 0;


		$cnt++;
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, "NET INCOME/(LOSS) BEFORE TAX")
			->setCellValue('C'.$cnt, $xctot);
		$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
		$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
		$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);


		if(($xctot) > 0) {
			$xctotax = $xctot * (intval($_REQUEST['ITper'])/100);
			$xctotaxaftr = $xctot - $xctotax;

			$cnt++;
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A'.$cnt, "PROVISION FOR INCOME TAX ".$_REQUEST['ITper']."%")
				->setCellValue('C'.$cnt, $xctotax);
			$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
			$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);
		}
		if(($xctot) < 0) {
			$xctotax = $BPROFITzc0 * (intval($_REQUEST['MCITper'])/100);
			$xctotaxaftr = $xctot - $xctotax;

			$cnt++;
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A'.$cnt, "PROVISION FOR MCIT (".$_REQUEST['MCITper']."% OF GROSS INCOME)")
				->setCellValue('C'.$cnt, $xctotax);
			$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
			$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);
		}

			$cnt++;
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A'.$cnt, "NET INCOME AFTER TAX")
				->setCellValue('C'.$cnt, $xctotaxaftr);
			$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
			$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);


	}

	

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('IncomeStatement');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="IncomeStatement.xlsx"');
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