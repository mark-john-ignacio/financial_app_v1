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


	$arrcomps = array();
	$arrcompsname = array();
	$company = $_SESSION['companyid'];
	$sql = "select compcode, compname From company";
	$result=mysqli_query($con,$sql);
  	$rowcount=mysqli_num_rows($result);
	if($rowcount>0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrcomps[] = $row;
			$arrcompsname[] = $row['compname'];
		}
	}
	
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
					
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
						
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
		$compadd = $row['compadd'];
		$comptin = $row['comptin'];
	}

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Company: ".implode(", ",$arrcompsname));
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2, "Company Address: ".strtoupper($compadd));
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 3, "Vat Registered Tin: ".$comptin);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 4, "Profit & Lost Statement: ");

	if($_POST['seldte']==1){
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, "For the Period: ".date_format(date_create($_POST["date1"]),"F d, Y") . " to " . date_format(date_create($_POST["date2"]),"F d, Y"));
	}else{
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, "For the Year: ".$_POST["selyr"]);
	}

	

	$rowd = 7;

	// HEADER //
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, 'Account No.');
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $rowd, 'Account Name');
		$cold = 2;
		foreach($arrcomps as $row){
			$cold++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $row['compname']);
		}
		$cold++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, 'Total');

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle("A1:".$xcol2)->getFont()->setBold(true);

		$cold++;

	// END HEAD //

	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
					
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
						
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}

	//getall accounts
	$allaccounts = array();
	$result=mysqli_query($con,"SELECT DISTINCT A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.cFinGroup='Income Statement' ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$allaccounts[] = $row;
	}

	$qrydte = "";
	if($_POST['seldte']==1){
		$date1 = $_POST["date1"];
		$date2 = $_POST["date2"];

		$qrydte = "A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')";
	}else{
		$dteyr = $_POST["selyr"];
		$qrydte = "YEAR(A.ddate) = '$dteyr'";	

		$date1 = "01/01/".$dteyr;
		$date2 = "12/31/".$dteyr;
	}

	//glactivity
	$arrallwithbal = array();
	$sql = "Select A.compcode, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where ".$qrydte." and IFNULL(B.cacctdesc,'') <> ''
			and B.cFinGroup = 'Income Statement'
			Group By A.compcode, A.acctno, B.cacctdesc
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
	$result=mysqli_query($con,"SELECT DISTINCT A.ccategory, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.cFinGroup='Income Statement' and A.cacctid in ('".implode("','", $arrallwithbal)."') ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");

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

	function gettotal($acctid, $xctype, $ccode){
		global $darray;

		$xtot = 0;
		foreach($darray as $rsp){
			if($rsp['acctno']==$acctid && $rsp['compcode']==$ccode){

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


	if(count($mainarray)>0) {

		$profitRevn =  array();
		$profitCost=  array();
		$BPROFITzc0 =  array();
		$BPEXPzc0 =  array();


		$arrlvl = array();
		$arrlvldsc = array();

		foreach($arrcomps as $comprow){
			$arrlvlamt[$comprow['compcode']."0"] = 0;
			$arrlvlamt[$comprow['compcode']."1"] = 0;
			$arrlvlamt[$comprow['compcode']."2"] = 0;
			$arrlvlamt[$comprow['compcode']."3"] = 0;
			$arrlvlamt[$comprow['compcode']."4"] = 0;
			$arrlvlamt[$comprow['compcode']."5"] = 0;

			$donetwo[$comprow['compcode']] = 0;
			$profitRevn[$comprow['compcode']] = 0;
			$profitCost[$comprow['compcode']] = 0;
			$BPROFITzc0[$comprow['compcode']] = 0;
			$BPEXPzc0[$comprow['compcode']] = 0;
		}

		$arrlvlcnt = 0;

		$ccate = $mainarray[0]['ccategory'];

		//echo "<tr><td colspan='3'><b>".$ccate."</b></td></tr>";

		$rowd++;
		$cold=1;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, $ccate);
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		foreach($arrcomps as $comprow){
			$cold++;
		}
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);


		$csubcate = "";

		$nlevel = 0;
		
		foreach($mainarray as $row)
		{
	
			if(intval($row['nlevel']) < intval($arrlvlcnt)){ //cold

				$rowd++;
				$cold=2;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, '');
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $rowd, "Total ".$arrlvldsc[intval($row['nlevel'])]);
				$mygtooth = 0;
				foreach($arrcomps as $comprow){
					$cold++;
					$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $arrlvlamt[$comprow['compcode'].intval($row['nlevel'])]);
					$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

					$mygtooth = $mygtooth + floatval($arrlvlamt[$comprow['compcode'].intval($row['nlevel'])]);
				}
				$cold++;
				
				$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$spreadsheet->getActiveSheet()->getStyle("A".$rowd.":".$lastcol)->getFont()->setBold(true);

				$arrlvlamt[$comprow['compcode'].intval($row['nlevel'])] = 0;
			}

			if($row['ctype']=="General"){

				$arrlvl[$row['nlevel']] = $row['cacctid']; 
				$arrlvldsc[$row['nlevel']] = $row['cacctdesc']; 

			}
			
			if($ccate!==$row['ccategory']){

				$rowd++;
				$cold=1;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, "TOTAL ".$ccate);
				$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				$cold++;
				$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

				$mygtooth = 0;
				foreach($arrcomps as $comprow){
					$cold++;
					$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $arrlvlamt[$comprow['compcode']."0"]);
					$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
					$spreadsheet->getActiveSheet()->getStyle($lastcol)->getFont()->setBold(true);

					$mygtooth = $mygtooth + floatval($arrlvlamt[$comprow['compcode'].intval($row['nlevel'])]);
				

					if($ccate=="REVENUE"){
						$profitRevn[$comprow['compcode']] = floatval($arrlvlamt[$comprow['compcode']."0"]);
					}

					if($ccate=="COST OF SALES"){				
						$profitCost[$comprow['compcode']] = floatval($arrlvlamt[$comprow['compcode']."0"]);
					}

					$arrlvlamt[$comprow['compcode']."0"] = 0;
					
				}
				$cold++;
				
				$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);

				


				if($row['ccategory']=="EXPENSES"){

					$rowd++;
					$cold=1;
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, "GROSS PROFIT");
					$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
					$cold++;
					$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
					$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

					$mygtooth = 0;
					foreach($arrcomps as $comprow){

						$cold++;						
						$BPROFITzc0[$comprow['compcode']] = floatval($profitRevn[$comprow['compcode']]) - floatval($profitCost[$comprow['compcode']]);
						//$donetwo[$comprow['compcode']] = ($BPROFITzc0[$comprow['compcode']]<0) ? "(".number_format(abs($BPROFITzc0[$comprow['compcode']]),2).")" : number_format(($BPROFITzc0[$comprow['compcode']]),2);
						$mygtooth = $mygtooth + floatval($BPROFITzc0[$comprow['compcode']]);

						$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
						$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $BPROFITzc0[$comprow['compcode']]);
						$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
					}

					$cold++;				
					$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
					$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

					$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);

				}

				$rowd++;
				$cold=1;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, $row['ccategory']);
				$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				foreach($arrcomps as $comprow){
					$cold++;
				}
				$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
				$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);
			}



			$rowd++;
			$cold=1;
			$cstegdb = ($row['ctype']=="General") ? "" : $row['cacctid'] ; 
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $cstegdb);

			$cold++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $row['cacctdesc']);
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();

			if($row['ctype']=="General"){
				$spreadsheet->getActiveSheet()->getStyle($xcol1)->getFont()->setBold(true);
			}

			$mygtooth = 0;
			foreach($arrcomps as $comprow){
				$cold++;

				$xcvb = "";
				if($row['ctype']=="Details"){
					$xcvb = gettotal($row['cacctid'], $row['ccategory'], $comprow['compcode']);

					for ($x = 0; $x < intval($row['nlevel']); $x++) {
						$arrlvlamt[$comprow['compcode'].$x] = $arrlvlamt[$comprow['compcode'].$x] + $xcvb;
					}  

					if(floatval($xcvb) > 0){
						//echo number_format($xcvb,2);

						//$xmain = intval($row['nlevel']) - 1;
						$mygtooth = $mygtooth + floatval($xcvb);
					}elseif(floatval($xcvb) < 0){
						//echo "(".number_format(abs($xcvb),2).")";
						$mygtooth = $mygtooth + floatval($xcvb);
					}else{
						//echo "-";
					}
				}

				$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $xcvb);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				if($row['ctype']=="General"){
					//$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);
				}

			}

			$cold++;				
			$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$spreadsheet->getActiveSheet()->getStyle($lastcol)->getFont()->setBold(true);

			$ccate = $row['ccategory'];
			$arrlvlcnt = $row['nlevel'];

		}


		if(intval($arrlvlcnt) !== 1){

			//$GENxyz1 = getpads($arrlvlcnt-1);

			$rowd++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, "");
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $rowd, "Total ".$arrlvldsc[intval($arrlvlcnt)-1]);
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();

			$cold = 2;
			$mygtooth = 0;
			foreach($arrcomps as $comprow){
				$cold++;
				$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $arrlvlamt[$comprow['compcode'].intval($arrlvlcnt)-1]);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$mygtooth = $mygtooth + $arrlvlamt[$comprow['compcode'].(intval($arrlvlcnt)-1)];
			}
			$cold++;				
			$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);
			


		}
	

		$xctotsuperTotl = 0;
		$xctot = array();
		$xctotax = array();
		$xctotaxaftr = array();

		$rowd++;
		$cold=1;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, "TOTAL ".$ccate);
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$cold++;
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

		$mygtooth = 0;
		foreach($arrcomps as $comprow){
			//$donetwo[$comprow['compcode']] = ($arrlvlamt[$comprow['compcode']."0"]<0) ? "(".number_format(abs($arrlvlamt[$comprow['compcode']."0"]),2).")" : number_format(($arrlvlamt[$comprow['compcode']."0"]),2);

			$cold++;
			$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $arrlvlamt[$comprow['compcode']."0"]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$mygtooth = $mygtooth + $arrlvlamt[$comprow['compcode']."0"];

			if($ccate=="EXPENSES"){
				$BPEXPzc0[$comprow['compcode']] = floatval($arrlvlamt[$comprow['compcode']."0"]);
			}
	
			$xctot[$comprow['compcode']] = $BPROFITzc0[$comprow['compcode']] -$BPEXPzc0[$comprow['compcode']];
			$xctotax[$comprow['compcode']] = 0;
			$xctotaxaftr[$comprow['compcode']] = 0;
		}
		$cold++;				
		$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);


	
		$rowd++;
		$cold=1;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, "NET INCOME/(LOSS) BEFORE TAX");
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$cold++;
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
		$mygtooth = 0;
		foreach($arrcomps as $comprow){
			$mygtooth = $mygtooth + $xctot[$comprow['compcode']];

			$cold++;
			$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $xctot[$comprow['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		}
		$cold++;				
		$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);


		$isPrvo  = "False";
		$isProvmcit = "False";
		foreach($arrcomps as $comprow){
			if(($xctot[$comprow['compcode']]) > 0) {	
				$isPrvo  = "True";
			}

			if(($xctot[$comprow['compcode']]) < 0) {
				$isProvmcit = "True";
			}
		}



		if($isPrvo=="True") {	

			$rowd++;
			$cold=1;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, "PROVISION FOR INCOME TAX ".$_REQUEST['ITper']."%");
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$cold++;
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
			$mygtooth = 0;
			foreach($arrcomps as $comprow){
				$cold++;
				$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				if(($xctot[$comprow['compcode']]) > 0) {

					$xctotax = $xctot[$comprow['compcode']] * (intval($_REQUEST['ITper'])/100);
					$xctotaxaftr[$comprow['compcode']] = $xctot[$comprow['compcode']] - $xctotax;	
	
					$mygtooth = $mygtooth + floatval($xctotax);
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $xctotax);				
				}else{
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, 0);
				}
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			}
			$cold++;				
			$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	
			$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);

		}


		if($isProvmcit=="True") {

			$rowd++;
			$cold=1;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, "PROVISION FOR MCIT (".$_REQUEST['MCITper']."% OF GROSS INCOME)");
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$cold++;
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
			$mygtooth = 0;
			foreach($arrcomps as $comprow){
				$cold++;
				$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
				if(($xctot[$comprow['compcode']]) < 0) {

					$xctotax = $BPROFITzc0[$comprow['compcode']] * (intval($_REQUEST['MCITper'])/100);
					$xctotaxaftr[$comprow['compcode']] = $xctot[$comprow['compcode']] - $xctotax;

					$mygtooth = $mygtooth + floatval($xctotax);					
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $xctotax);					
				}else{
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, 0);
				}
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			}
			$cold++;				
			$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	
			$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);

		}

		$rowd++;
		$cold=1;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rowd, "NET INCOME AFTER TAX");
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$cold++;
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
		$mygtooth = 0;
		foreach($arrcomps as $comprow){
			$mygtooth = $mygtooth + floatval($xctotaxaftr[$comprow['compcode']]);
			$cold++;
			$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $xctotaxaftr[$comprow['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		}
		$cold++;				
		$lastcol = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cold, $rowd)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cold, $rowd, $mygtooth);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastcol)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$lastcol)->getFont()->setBold(true);
		

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