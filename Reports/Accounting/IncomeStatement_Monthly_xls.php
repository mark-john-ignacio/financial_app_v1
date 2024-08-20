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

	$dteyr = $_POST["selyr"];

	//getall accounts
	$allaccounts = array();
	$result=mysqli_query($con,"SELECT A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Income Statement' ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$allaccounts[] = $row;
	}

	//glactivity
		$arrallwithbal = array();
		$sql = "Select MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
				From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
				where A.compcode='$company' and YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
				and B.cFinGroup = 'Income Statement'
				Group By MONTH(ddate), A.acctno, B.cacctdesc
				Having sum(A.ndebit)<>0 or sum(A.ncredit)<>0
				Order By A.acctno, MONTH(ddate)";

		$result=mysqli_query($con,$sql);

		$darray = array();
		$months = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$darray[] = $row;
			$arrallwithbal[] = $row['acctno'];
			$months[] = $row['dmonth'];
			//echo $row['acctno']."<br>";
			getparent($row['acctno']);

		}

		$hdr_months = array_unique($months);
		asort($hdr_months);

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

	function gettotal($acctid, $xctype, $xmo){
		global $darray;

		$xtot = 0;
		foreach($darray as $rsp){
			if($rsp['acctno']==$acctid && $rsp['dmonth']==$xmo){

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


	$rows = 1;
	$cols = 0;

	if(count($mainarray)>0) {

		$arrlvl = array();
		$arrlvldsc = array();

		foreach($hdr_months as $rxzm){
			$profitRevn[$rxzm] = 0;
			$profitCost[$rxzm]= 0;
			$BPROFITzc0[$rxzm] = 0;
			$BPEXPzc0[$rxzm] = 0;

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
					
					if($ccate=="REVENUE"){
						$profitRevn[$rxzm] = floatval($arrlvlamt[0][$rxzm]);
					}
	
					if($ccate=="COST OF SALES"){				
						$profitCost[$rxzm] = floatval($arrlvlamt[0][$rxzm]);
					}
	
					$arrlvlamt[0][$rxzm] = 0;
	
				}

				$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);


				if($row['ccategory']=="EXPENSES"){

					$cols = 1;
					$rows++;
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "GROSS PROFIT");
					$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
					$cols++;
					$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

					$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
					$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

					foreach($hdr_months as $rxzm){

						$BPROFITzc0[$rxzm] = floatval($profitRevn[$rxzm]) - floatval($profitCost[$rxzm]);
						$donetwo = ($BPROFITzc0[$rxzm]<0) ? "(".number_format(abs($BPROFITzc0[$rxzm]),2).")" : number_format(($BPROFITzc0[$rxzm]),2);

						$cols++;
						$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
						$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $donetwo);					
						$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

					}

					$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
					$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);

				}

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
					$xcvb = gettotal($row['cacctid'], $row['ccategory'], $rxzm);

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
					
			if($ccate=="REVENUE"){
				$profitRevn[$rxzm] = floatval($arrlvlamt[0][$rxzm]);
			}
		
			if($ccate=="COST OF SALES"){				
				 $profitCost[$rxzm]= floatval($arrlvlamt[0][$rxzm]);
			}

			if($ccate=="EXPENSES"){
				$BPEXPzc0[$rxzm] = floatval($arrlvlamt[0][$rxzm]);
			}
		
			if(floatval($BPEXPzc0[$rxzm])==0){
				$xctot[$rxzm] = floatval($profitRevn[$rxzm]) - floatval($profitCost[$rxzm]);
				$donetwo = ($xctot[$rxzm]<0) ? "(".number_format(abs($xctot[$rxzm]),2).")" : number_format(($xctot[$rxzm]),2);

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $donetwo);					
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			}else{
				$xctot[$rxzm] = $BPROFITzc0[$rxzm]-$BPEXPzc0[$rxzm];
			}
			
			$xctotax[$rxzm] = 0;
			$xctotaxaftr[$rxzm] = 0;
		}

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);
		


		// FOOTERS //

		$cols = 1;
		$rows++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "NET INCOME/(LOSS) BEFORE TAX");
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$cols++;
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

		foreach($hdr_months as $rxzm){
			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xctot[$rxzm]);					
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		}

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);

		
		// IT //
			$cols = 1;
			$rows++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "PROVISION FOR INCOME TAX ".$_REQUEST['ITper']."%");
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$cols++;
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
			$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

			foreach($hdr_months as $rxzm){

				$xmyval = 0;
				if(($xctot[$rxzm]) > 0) {
					$xctotax[$rxzm] = $xctot[$rxzm] * (intval($_REQUEST['ITper'])/100);
					$xctotaxaftr[$rxzm] = $xctot[$rxzm] - $xctotax[$rxzm];

					$xmyval = $xctotax[$rxzm];
				}

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xmyval);					
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			}

			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);
		// END IT //

		// MCIT //
			$cols = 1;
			$rows++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "PROVISION FOR MCIT (".$_REQUEST['MCITper']."% OF GROSS INCOME)");
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$cols++;
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
			$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

			foreach($hdr_months as $rxzm){

				$xmyval = 0;
				if(($xctot[$rxzm]) < 0) {
					$xctotax[$rxzm] = $BPROFITzc0[$rxzm] * (intval($_REQUEST['MCITper'])/100);
					$xctotaxaftr[$rxzm] = $xctot[$rxzm] - $xctotax[$rxzm];

					$xmyval = $xctotax[$rxzm];
				}

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xmyval);					
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			}

			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);
		// END MCIT //

		// AFTER TAX //
			$cols = 1;
			$rows++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "NET INCOME AFTER TAX");
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$cols++;
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
			$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);

			foreach($hdr_months as $rxzm){

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xctotaxaftr[$rxzm]);					
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			}

			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->getStyle("A".$rows.":".$xcol2)->getFont()->setBold(true);
		// AFTER TAX END //

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