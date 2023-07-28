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
  ->setTitle('Purchase Detailed')
  ->setSubject('Purchase Detailed Report')
  ->setDescription('Purchase Report, generated using Myx Financials.')
  ->setKeywords('myx_financials purchase_report')
  ->setCategory('Myx Financials Report');


$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Classification');
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Product Code');
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'Description');
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 1, 'UOM');

$cols = 4;
for ($x=1; $x<=12; $x++){
	$xmname = date("F", mktime(0, 0, 0, $x, 10));

	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $xmname.' Qty');

	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $xmname.' Amt');
}

$company = $_SESSION['companyid'];
$selyr = $_POST["selmonth"];
$postz = $_POST["sleposted"];

if($postz!==""){
	$qry = "and b.lapproved=".$postz;
}
else{
	$qry = "";
}

$sql = "select A.dmonth, A.cclass, A.cdesc, A.citemno, A.citemdesc, A.cunit, sum(A.nqty) as nqty, sum(A.nprice*A.nqty) as nprice, sum(A.ncost*A.nqty) as ncost
FROM
(
select MONTH(b.dreceived) as dmonth, d.cclass, c.cdesc, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, 0 as ncost
From suppinv_t a
left join suppinv b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
left join groupings c on d.cclass=c.ccode and c.ctype='ITEMCLS'
where a.compcode='$company' and YEAR(b.dreceived) = '$selyr' and b.lcancelled=0 ".$qry.") A
group by A.dmonth, A.cclass, A.cdesc,A.citemno, A.citemdesc, A.cunit
order by A.cclass, A.citemdesc, A.dmonth ";

echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	
	$row1 = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$citemno = $row1["citemno"];
	$citemdesc = $row1["citemdesc"];
	$cunit = $row1["cunit"];
	$classval = $row1['cdesc'];

	  for ($x=1; $x<=12; $x++){
		  
		  $myarrqty[$x] = 0;
		  $myarrret[$x] = 0;
		  $myarrcost[$x] = 0;
		
	  }

				  $myarrqty[$row1['dmonth']] = $row1['nqty'];
				  $myarrret[$row1['dmonth']] = $row1['nprice'];
				  $myarrcost[$row1['dmonth']] = $row1['ncost'];

	$rows = 1;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$rows++;
		
		if($citemno==$row['citemno']){
						  
			$myarrqty[$row['dmonth']] = $row['nqty'];
			$myarrret[$row['dmonth']] = $row['nprice'];
			$myarrcost[$row['dmonth']] = $row['ncost'];				
			
		}
		
		else{
			$cols = 0;


			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $classval);

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $citemno);

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $citemdesc);

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $cunit);

			for ($x=1; $x<=12; $x++){

				$cols++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $myarrqty[$x]);

				$cols++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $myarrret[$x]);

			}


			for ($x=1; $x<=12; $x++){
		  
				$myarrqty[$x] = 0;
				$myarrret[$x] = 0;
				$myarrcost[$x] = 0;
	
			}

			$myarrqty[$row['dmonth']] = $row['nqty'];
			$myarrret[$row['dmonth']] = $row['nprice'];
			$myarrcost[$row['dmonth']] = $row['ncost'];
				
			$citemno = $row["citemno"];
			$citemdesc = $row["citemdesc"];
			$cunit = $row["cunit"];
			$classval = $row['cdesc'];

		}

	}

	$totCost = $totCost + $row['ncost'];
	$totPrice = $totPrice + $row['nprice'];


	$cols = 0;


	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $classval);

	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $citemno);

	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $citemdesc);

	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $cunit);

	for ($x=1; $x<=12; $x++){

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $myarrqty[$x]);

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $myarrret[$x]);

	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Purchases_Sum_Monthly');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Purchases_Sum_Monthly.xlsx"');
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