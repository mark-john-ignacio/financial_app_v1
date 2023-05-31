<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvTrans_Reg.php";


	ini_set('MAX_EXECUTION_TIME', 900);

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

		$company = $_SESSION['companyid'];
		$sql = "select * From company where compcode='$company'";
		$result=mysqli_query($con,$sql);					
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$compname =  $row['compname'];
		}

		//get FG whse
		$whseFG = "";
		$whseRR = "";
		$whsePRET = "";
		$whseSRET = "";

		$sql = "select * From locations where compcode='$company' and ccode in ('DEF_WHOUT','DEF_WHIN','DEF_PROUT','DEF_SRIN')";

		$result=mysqli_query($con,$sql);					
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{

		}

	$dtfrom = $_POST["date1"];
	$dtto = $_POST["date2"];

	//BEGINNING BALANCE
	$arravails = array();
	$arritmslist = array();

	$sql = "select a.citemno, b.citemdesc, b.cunit, COALESCE((Sum(a.nqtyin)-sum(a.nqtyout)),0) as nqty
	From tblinventory a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
	where a.compcode='$company' and a.nsect
	ion_id='".$whseFG."' and b.ctradetype='Trade' and b.linventoriable=0 and a.dcutdate < STR_TO_DATE('$dtfrom', '%m/%d/%Y')
	group by a.citemno, b.citemdesc, b.cunit Order By  b.citemdesc";

	$sqltblinv= mysqli_query($con,$sql);
	$rowTemplate = $sqltblinv->fetch_all(MYSQLI_ASSOC);
	foreach($rowTemplate as $row0){
		$arravails[] = array('citemno' => $row0['citemno'], 'citemdesc' => $row0['citemdesc'], 'cunit' => $row0['cunit'], 'nqty' => $row0['nqty']);
		$arritmslist[] = $row0['citemno'];
	}

	//OTHERS
	$arrothers = array();
	$sql = "select a.citemno, b.citemdesc, b.cunit, a.ctype, a.nsection_id, COALESCE(Sum(a.nqtyin),0) as nqtyin, COALESCE(Sum(a.nqtyout),0) as nqtyout
	From tblinventory a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
	where a.compcode='$company' and a.nsection_id='".$whseFG."' and b.ctradetype='Trade' and b.linventoriable=0 and a.dcutdate between STR_TO_DATE('$dtfrom', '%m/%d/%Y') and STR_TO_DATE('$dtto', '%m/%d/%Y') group by a.citemno, b.citemdesc, b.cunit, a.ctype, a.nsection_id Order By b.citemdesc";

	$invothers = mysqli_query($con,$sql);
	$rowOthers = $invothers->fetch_all(MYSQLI_ASSOC);
	foreach($rowOthers as $row0){
		$arrothers[] = array('citemno' => $row0['citemno'], 'citemdesc' => $row0['citemdesc'], 'cunit' => $row0['cunit'], 'nqtyin' => $row0['nqtyin'], 'nqtyout' => $row0['nqtyout'], 'ctype' => $row0['ctype']);
		$arritmslist[] = $row0['citemno'];
	}

	//Actual Count / Inventory Ending
	$arrending = array();
	$sql = "select a.citemno, sum(a.nqty) as nqty
	From invcount_t a left join invcount b on a.compcode=b.compcode and a.ctranno=b.ctranno 
	left join items c on a.compcode=c.compcode and a.citemno=c.cpartno 
	where a.compcode='$company' and b.section_nid='".$whseFG."' and b.ctype='ending' and c.linventoriable=0 and b.dcutdate between STR_TO_DATE('$dtfrom', '%m/%d/%Y') and STR_TO_DATE('$dtto', '%m/%d/%Y') group by a.citemno";

	$invending= mysqli_query($con,$sql);
	$rowEnd = $invending->fetch_all(MYSQLI_ASSOC);
	foreach($rowEnd as $row0){
		$arrending[] = array('citemno' => $row0['citemno'],'nqty' => $row0['nqty']);
		$arritmslist[] = $row0['citemno'];
	}


?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
  <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Inventory Transfer - Register</title>
</head>

<body style="padding:20px">
<center>
<h2 class="nopadding"><?php echo strtoupper($compname);  ?></h2>
<h3 class="nopadding">Inventory Transfer - Register</h3>
<h4 class="nopadding">For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h4>
</center>
<br>

<table width="100%" border="0" align="center" cellpadding="3">
  <tr>
    <th style="text-align:center;">Transaction No</th>
		<th style="text-align:center;">Type</th>
    <th style="text-align:center;">Date</th>
    <th style="text-align:center;">Whse IN</th>
    <th style="text-align:center;">Whse OUT</th>
		<th style="text-align:center;">Item Code</th>
    <th style="text-align:center;">Description</th>
    <th style="text-align:center;">UOM</th>
		<th style="text-align:center;">Qty</th>
  </tr>
  
</table>

</body>
</html>

