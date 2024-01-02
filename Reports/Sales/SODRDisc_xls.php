<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$custype = $_POST["selcustype"];
	$trantype = $_POST["seltrantype"]; 
	$postedtran = $_POST["sleposted"];

	$mainqry = "";
	$finarray = array();

	$qrycust = "";
	if($custype!==""){
		$qrycust = " and d.ccustomertype='".$custype."'";
	}

	$qryposted = "";
	if($postedtran!==""){
		$qryposted = " and b.lapproved=".$postedtran."";
	}

	if($trantype=="Trade"){
		$tblhdr = "so";
		$tbldtl = "so_t";
	}elseif($trantype=="Non-Trade"){
		$tblhdr = "ntso";
		$tbldtl = "ntso_t";
	}

	if($trantype!==""){
		$xsql = "select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
		From ".$tbldtl." a	
		left join ".$tblhdr." b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
		".$qryposted.$qrycust."
		order by a.ctranno, a.nident";


	}else{
		$xsql = "Select A.nident, A.dcutdate, A.ctranno, A.ctype, A.typdesc, A.ccode, A.cname, A.lapproved, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.namount
		From (
			select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
			From so_t a	
			left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
			".$qryposted.$qrycust."

			UNION ALL

			select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
			From ntso_t a	
			left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
			".$qryposted.$qrycust."
		) A 
		order by A.ctranno, A.nident";
		
	}
	
	$finarray =  array();
	$itmslist = array();
	$custslist = array();
	$result=mysqli_query($con,$xsql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;

		if (!in_array($row['citemno'], array_column($itmslist, 'citemno'))) {
			$itmslist[] = array('citemno' => $row['citemno'], 'citemdesc' => $row['citemdesc'], 'cunit' => $row['cunit']);
		}

		if (!in_array($row['ccode'], array_column($custslist, 'ccode'))) {
			$custslist[] = array('ccode' => $row['ccode'], 'cname' => $row['cname']);
		}
	}


	$resDR=mysqli_query($con,"Select A.ctranno, A.nident, B.ccode, A.creference, A.crefident, A.citemno, A.nqty from dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') UNION ALL Select A.ctranno, A.nident, B.ccode, A.creference, A.crefident, A.citemno, A.nqty from ntdr_t A left join ntdr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')");
	$findr = array();
	while($row = mysqli_fetch_array($resDR, MYSQLI_ASSOC)){
		$findr[] = $row;
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
    ->setTitle('SO DR Discrepancy')
    ->setSubject('SO DR Discrepancy Report')
    ->setDescription('Sales Report, generated using Myx Financials.')
    ->setKeywords('myx_financials sales_report')
    ->setCategory('Myx Financials Report');


$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Item Code');
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Item Desc');
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'UOM');

$spreadsheet->getActiveSheet()->mergeCells("A1:A2");
$spreadsheet->getActiveSheet()->mergeCells("B1:B2");
$spreadsheet->getActiveSheet()->mergeCells("C1:C2");


$colno = 4;
$colrow = 4;
	foreach($custslist as $rocut){

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrow, 2, "SO");
		$colrow++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrow, 2, "DR");
		$colrow++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrow, 2, "VARIANCE");
		$colrow++;


		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colno, 1, $rocut['cname']);
		$col1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colno);

		$colno = $colno + 3;

		$col2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colno-1);

		$spreadsheet->getActiveSheet()->mergeCells($col1."1:".$col2."1");

		//columns


	}

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colno, 1, 'Total Order');
$col1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colno);
$spreadsheet->getActiveSheet()->mergeCells($col1."1:".$col1."2");

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colno+1, 1, 'Total Dispatch');
$col1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colno+1);
$spreadsheet->getActiveSheet()->mergeCells($col1."1:".$col1."2");

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colno+2, 1, 'Total Discrepancy');
$col1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colno+2);
$spreadsheet->getActiveSheet()->mergeCells($col1."1:".$col1."2");


//START DETAILS
	$totGrossSO = 0;
	$totGrossDR = 0;
	$cntrow = 2;
	foreach($itmslist as $row)
	{
		$cntrow++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $cntrow, $row['citemno']);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $cntrow, $row['citemdesc']);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $cntrow, $row['cunit']);

		$colrowx = 4;
		foreach($custslist as $rocut){

						$totSO = 0;
						foreach($finarray as $roworder){
							if($roworder['citemno']==$row['citemno'] && $roworder['ccode']==$rocut['ccode']){
								$totSO = $totSO + floatval($roworder['nqty']);
								$totGrossSO = $totGrossSO + floatval($roworder['nqty']);
							}
						}

						$xcsoecho = ($totSO==0) ? "" : $totSO;
						$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrowx, $cntrow, $xcsoecho);

						$colrowx++;

						$totDR = 0;
						foreach($findr as $rowdrs){
							if($rowdrs['citemno']==$row['citemno'] && $rowdrs['ccode']==$rocut['ccode']){
								$totDR = $totDR + floatval($rowdrs['nqty']);
								$totGrossDR = $totGrossDR + floatval($rowdrs['nqty']);
							}
						}

						$xcdrecho = ($totDR==0) ? "" : $totDR;
						$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrowx, $cntrow, $xcdrecho);

						$colrowx++;

						$xdvar = floatval($totDR) - floatval($totSO);
						$xctotecho = ($xdvar==0) ? "" : $xdvar;
						$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrowx, $cntrow, $xctotecho);

						$colrowx++;
		}

		$xdvartot = floatval($totGrossDR) - floatval($totGrossSO);			

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrowx, $cntrow, ($totGrossSO==0) ? "" : $totGrossSO);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrowx+1, $cntrow, ($totGrossDR==0) ? "" : $totGrossDR);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colrowx+2, $cntrow, ($xdvartot==0) ? "" : $xdvartot);

		$totGrossSO = 0;
		$totGrossDR = 0;

	}



// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('SODRSI');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SODRDiscrepancy.xlsx"');
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