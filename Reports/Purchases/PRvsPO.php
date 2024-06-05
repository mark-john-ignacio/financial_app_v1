<?php
	if(!isset($_SESSION)){
	session_start();
	}
	$_SESSION['pageid'] = "PRvsPO";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
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
	$custid = $_POST["seltype"]; 

	//get ALL PO
	$xallPO = array();
	$sql = "select b.dpodate, a.cpono, b.ccode, c.cname, a.citemno, a.cpartno, a.citemdesc, a.cunit, a.nqty, f.cdesc as prepdept_desc, b.cremarks as hdr_remarks, b.lapproved, a.creference, a.nrefident
	From purchase_t a
	left join purchase b on a.cpono=b.cpono and a.compcode=b.compcode
	left join suppliers c on b.ccode=c.ccode and a.compcode=c.compcode
	left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	left join users e on b.cpreparedby=e.Userid
	left join locations f on e.cdepartment=f.nid and f.compcode='$company'
	where a.compcode='$company' and b.lvoid = 0 and b.dpodate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 order by b.dpodate, a.cpono";
	$result=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$xallPO[] = $row;
	}

	function getPO($cprno,$citemno,$citemident){
		global $xallPO;

		foreach($xallPO as $rs){
			if($cprno==$rs['creference'] && $citemno==$rs['citemno'] && intval($citemident)==intval($rs['nrefident'])){
				echo "<tr nowrap>";
				echo "<td nowrap>".$rs['dpodate']."</td>";
				echo "<td nowrap>".$rs['cname']."</td>";
				echo "<td nowrap>".$rs['cpono']."</td>";
				echo "<td nowrap>".$rs['prepdept_desc']."</td>";
				echo "<td nowrap>&nbsp;</td>";
				echo "<td nowrap>".$rs['citemno']."</td>";
				echo "<td nowrap>&nbsp;</td>";
				echo "<td nowrap>".$rs['cpartno']."</td>";
				echo "<td nowrap>".$rs['citemdesc']."</td>";
				echo "<td nowrap>&nbsp;</td>";
				echo "<td nowrap align=\"right\">".number_format($rs['nqty'],2)."</td>";
				echo "<td nowrap align=\"right\">0.00</td>";
				echo "<td nowrap>".$rs['hdr_remarks']."</td>";
				echo "</tr>";
			}
		}
	}

	function getBalance($cprno,$citemno,$citemident){
		global $xallPO;

		$xbal = 0;
		foreach($xallPO as $rs){
			if($cprno==$rs['creference'] && $citemno==$rs['citemno'] && intval($citemident)==intval($rs['nrefident'])){
				$xbal = $xbal + floatval($rs['nqty']);
			}
		}

		return $xbal;
	}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PRvsPO</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Documents Analysis Purchase Request Vs Purchases Orders <?php echo date_format(date_create($_POST["date1"]),"m-d-Y");?> to <?php echo date_format(date_create($_POST["date2"]),"m-d-Y");?></h2>
<h3><?=($_POST["seltype"]!="") ? $_POST["seltype"] : "";?></h3>
</center>

<br><br>
<table width="100%" border="0" align="center" id="MyTable" cellpadding="3px">
  <tr>
    <th>Date</th>
    <th nowrap>Supplier Name</th>
    <th nowrap>Voucher No.</th>
    <th>Department</th>
    <th nowrap>Cost Center</th>
    <th>Code</th>
	<th nowrap>DR No.</th>
	<th>Name</th>
	<th>Description</th>
	<th>Alias</th>
	<th>Quantity</th>
	<th>Balance</th>
	<th>Remarks</th>
  </tr>
  
<?php

	$xqry = "";
	if($custid!=""){
		$xqry = " and b.locations_id = ". $custid;
	}

	$sql = "select b.dneeded, a.ctranno, b.locations_id, c.cdesc as locations_desc, a.nident, a.citemno, a.cpartdesc, a.citemdesc, a.cremarks, a.cunit, a.nqty, e.cdesc as costcenter_desc, b.cremarks as hdr_remarks
	From purchrequest_t a
	left join purchrequest b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join locations c on b.locations_id=c.nid and a.compcode=c.compcode
	left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	left join locations e on a.location_id=e.nid and a.compcode=e.compcode
	where a.compcode='$company' and b.lvoid = 0 and b.lapproved = 1 and b.lcancelled = 0 and b.dneeded between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$xqry." order by b.dneeded, a.ctranno";

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$xnbalance = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$xnbalance = getBalance($row['ctranno'],$row['citemno'],$row['nident']);

		$xnbalance = floatval($row['nqty']) - floatval($xnbalance);
?>  
  <tr>
    <td nowrap><?=$row['dneeded']?></td>
	<td nowrap>&nbsp;</td>
	<td nowrap><?=$row['ctranno']?></td>
	<td nowrap><?=$row['locations_desc']?></td>
	<td nowrap><?=$row['costcenter_desc']?></td>
	<td nowrap><?=$row['citemno']?></td>
	<td nowrap>&nbsp;</td>
	<td nowrap><?=$row['cpartdesc']?></td>
	<td nowrap><?=$row['citemdesc']?></td>
	<td nowrap><?=$row['cremarks']?></td>
	<td nowrap align="right"><?=number_format($row['nqty'],2)?></td>
	<td nowrap align="right"><?=number_format($xnbalance,2)?></td>
	<td nowrap><?=$row['hdr_remarks']?></td>
  </tr>
<?php 
		getPO($row['ctranno'],$row['citemno'],$row['nident']);
	}
?>

  
</table>

</body>
</html>
