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
    ->setTitle('Job Order Summary')
    ->setSubject('Job Order Summary Report')
    ->setDescription('Job Order Report, generated using Myx Financials.')
    ->setKeywords('myx_financials job_order_report')
    ->setCategory('Myx Financials Report');


//BODY
			$company = $_SESSION['companyid'];
			$date1 = $_POST["date1"];
			$date2 = $_POST["date2"];

			$txtCustID = $_POST["txtCustID"];
			$itmtype = $_POST["seltype"];
			$itmclass = $_POST["seliclass"];
			$custype = $_POST["selcustype"];
			$trantype = $_POST["seltrantype"]; 
			$postedtran = $_POST["sleposted"];

			$mainqry = "";
			$finarray = array();

			$qryitm = "";
			if($txtCustID!=""){
				$qryitm = $qryitm." and b.ccode='".$txtCustID."'";
			}

			if($itmtype!=""){
				$qryitm = $qryitm." and c.ctype='".$itmtype."'";
			}

			if($itmclass!=""){
				$qryitm = $qryitm." and c.cclass='".$itmclass."'";
			}

			$qrycust = "";
			if($custype!==""){
				$qrycust = " and d.ccustomertype='".$custype."'";
			}

			$qryposted = "";
			if($postedtran!==""){
				$qryposted = " and b.lapproved=".$postedtran."";
			}

			$dsql = "";
			$dsql2 = "";
			if($trantype=="Trade"){

				$dsql = "select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From so_t a	
				left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc";

				$dsql2 = "select DISTINCT a.citemno, c.citemdesc, a.cunit, c.cclass, e.cdesc as typdesc
				From so_t a	
				left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Order By c.cclass, c.citemdesc";

			}elseif($trantype=="Non-Trade"){

				$dsql = "select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc";

				$dsql2 = "select DISTINCT a.citemno, c.citemdesc, a.cunit, c.cclass, e.cdesc as typdesc
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Order By c.cclass, c.citemdesc";

			}else{

				$dsql = "Select A.compcode, A.ccode, A.ctradename, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.cclass, A.typdesc, sum(A.nqty) as nqty
				From (
				select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From so_t a	
				left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
				UNION ALL
				select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc) A 
				Group By A.compcode, A.ccode, A.ctradename, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.cclass, A.typdesc";

				$dsql2 = "Select DISTINCT A.citemno, A.citemdesc, A.cunit, A.cclass, A.typdesc
				From (
				select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From so_t a	
				left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
				UNION ALL
				select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc) A 
				Order By A.cclass, A.citemdesc";
	
			}

			$result=mysqli_query($con,$dsql);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$finarray[] = $row;
			}

			$itmslist = array();
			$result=mysqli_query($con,$dsql2);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$itmslist[] = $row;
			}


			$ts1 = strtotime($date1);
			$ts2 = strtotime($date2);

			$year1 = date('Y', $ts1);
			$year2 = date('Y', $ts2);

			$month1 = date('m', $ts1);
			$month2 = date('m', $ts2);

			$mnths = (($year2 - $year1) * 12) + ($month2 - $month1);

			$mnths = $mnths + 1;

			//getcustomers
			$allcustomers = array();
			$allitems = array();
			foreach($finarray as $row)
			{
				if (!in_array($row['ccode'], $allcustomers)){
					$allcustomers[] = $row['ccode'];
				}

				if (!in_array($row['citemno'], $allitems)){
					$allitems[] = $row['citemno'];
				}
			}

			$custlist = array();
			$rescusto = mysqli_query($con,"Select cempid as ccode, COALESCE(cdelname,ctradename,cname) as cname From customers where compcode='$company' and cempid in ('".implode("','", $allcustomers)."') Order By ccustomertype, cdelname");
			while($row = mysqli_fetch_array($rescusto, MYSQLI_ASSOC)){
				$custlist[] = $row;
			}

			function getqty($ccode,$citmno,$cunit){
				global $finarray;
				$retqy = "";
				foreach($finarray as $rsx){
					if($rsx['ccode']==$ccode && $rsx['citemno']==$citmno && $rsx['cunit']==$cunit){
						$retqy = $rsx['nqty'];
						break;
					}
				}

				return $retqy;
			}

			$cols = 4;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Item Class');
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Product Code');
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'Product Description');
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 1, 'UOM');

			foreach($custlist as $rs){
				$cols++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $rs['ccode']." - ".$rs['cname']);
			}

			$class="";
			$classval="";
			$classcode="";
			$totPrice=0;	
			$totCost=0;

			$cols = 0;
			$rows = 1;
			foreach($itmslist as $rwx)
			{
				$cols = 4;
				$rows++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rows, $rwx['typdesc']);
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $rows, $rwx['citemno']);
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $rows, $rwx['citemdesc']);
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $rows, $rwx['cunit']);

				foreach($custlist as $rs){
					$cols++;
						$x = getqty($rs['ccode'],$rwx['citemno'],$rwx['cunit']);
						if($x!=""){
							$x = number_format($x);
						}	

					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $x);
				}

			}

//BODY



	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('JO_Per_CustItem');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="JO_Per_CustItem.xlsx"');
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