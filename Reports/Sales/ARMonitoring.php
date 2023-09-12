<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
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
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>AR Monitoring</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>AR Monitoring</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table border="0" align="center" cellpadding="5px" id="MyTable">
  <tr>
    <th nowrap>Type</th>
    <th nowrap>Transaction No.</th>
    <th nowrap>Reference</th>
    <th nowrap>Date</th>
    <th nowrap colspan="2">Customer</th>
    <th nowrap align="right">Vatable Sales</th>
		<th nowrap align="right">VAT%</th>
		<th nowrap align="right">VAT Amount</th>
		<th nowrap align="right">Sales Amount</th>
		<th nowrap style="text-align: center">EWT</th>
		<th nowrap align="right">EWT Amount</th>
		<th nowrap align="right">AR Balance<br>Net of TAX</th>
		<th nowrap align="right">Amount Collected</th>
		<th nowrap align="right">Balance</th>
  </tr>
  
<?php

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$trantype = "";
	if(isset($_POST['seltrantype'])){
		$trantype=$_POST['seltrantype'];
	}else{
		$trantype="Trade";
	}

	$postedtran = $_POST["selrpt"];

	$mainqry = "";
	$finarray = array();

	$qryposted = "";
	$qryposted2 = "";
	if($postedtran!==""){
		$qryposted = " and B.lapproved=".$postedtran."";
		$qryposted2 = " and A.lapproved=".$postedtran."";
	}

	if($trantype=="Trade"){

		$transrefDR = array();
		$result=mysqli_query($con,"Select ctranno, GROUP_CONCAT(DISTINCT creference) as cref from sales_t where compcode='$company' group by ctranno");
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$transrefDR[$row['ctranno']] = $row['cref'];
		}

		@$arrpaymnts = array();
		$sqlpay = "select X.* from receipt_sales_t X left join receipt B on X.compcode=B.compcode and X.ctranno=B.ctranno where X.compcode='$company' and B.lcancelled = 0 order By X.csalesno, B.ddate";
		$respay = mysqli_query ($con, $sqlpay);
		while($rowardj = mysqli_fetch_array($respay, MYSQLI_ASSOC)){
			@$arrpaymnts[] = $rowardj;
		}

		$transctions = array();
		$sqlx = "Select A.type, A.ctranno, A.ccode, A.cname, A.cacctid, A.cacctdesc, IFNULL(A.ctaxcode,'') as ctaxcode, A.nrate, IFNULL(A.cewtcode,'') as cewtcode, A.newtrate, A.dcutdate, SUM(ROUND(A.namountfull,2)) as ngross, SUM(ROUND(A.namount,2)) as cm, SUM(nvatgross) as nvatgross, (SUM(ROUND(A.namountfull,2)) - SUM(ROUND(A.namount,2)) - SUM(nvatgross)) as vatamt
		From (
			Select 'SI' as type, A.ctranno, B.ccode, COALESCE(C.ctradename, C.cname) as cname, A.citemno, ((A.nqtyreturned) * (A.nprice-A.ndiscount)) as namount, (A.nqty * (A.nprice-A.ndiscount)) as namountfull, B.dcutdate, D.cacctid, D.cacctdesc, A.ctaxcode, A.nrate, A.cewtcode, A.newtrate, 
						CASE 
							WHEN IFNULL(A.nrate,0) <> 0 
							THEN 
								ROUND(((A.nqty-A.nqtyreturned)*(A.nprice-A.ndiscount))/(1 + (A.nrate/100)),2)
							ELSE 
								A.namount 
							END as nvatgross
		From sales_t A 
		left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
		left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
		left join accounts D on C.compcode=D.compcode and C.cacctcodesales=D.cacctno 
		left join wtaxcodes E on A.compcode=E.compcode and A.cewtcode=E.ctaxcode 
		where A.compcode='$company' and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.lcancelled=0
		".$qryposted."
		
		UNION ALL

		Select 'BS' as type, A.ctranno, B.ccode, COALESCE(C.ctradename, C.cname) as cname, '' as citemno, 0 as namount, A.namount as namountfull, B.dcutdate, '' as cacctid, '' as cacctdesc, CASE WHEN B.cvattype='VatIn' THEN F.ctaxcode ELSE '' END as ctaxcode, CASE WHEN B.cvattype='VatIn' THEN F.nrate ELSE '' END as nrate, '' as cewtcode, 0 as newtrate, 	
						CASE 
							WHEN B.cvattype='VatIn'
							THEN 
								ROUND((A.nqty*A.nprice)/(1 + (F.nrate/100)),2)
							ELSE 
								A.namount 
							END as nvatgross
		From quote_t A
		left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno
		left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
		left join items E on A.compcode=E.compcode and A.citemno=E.cpartno 
		left join taxcode F on E.compcode=F.compcode and E.ctaxcode=F.ctaxcode
		where A.compcode='$company' and B.quotetype='billing' and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.lcancelled=0 ".$qryposted." and A.ctranno not in (Select Y.creference From sales_t Y left join sales X on Y.compcode=X.compcode and Y.ctranno=X.ctranno where Y.compcode='$company' and X.lcancelled=0)

		) A
		Group By A.ctranno, A.ccode, A.cname, A.cacctid, A.cacctdesc, A.ctaxcode, A.nrate, A.cewtcode, A.newtrate, A.dcutdate
		order by A.dcutdate, A.ctranno";

		$result=mysqli_query($con,$sqlx);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$finarray[] = $row;
			$transctions[] = $row['ctranno'];
		}

	}elseif($trantype=="Non-Trade"){

		$result=mysqli_query($con,"select b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		order by a.ctranno, a.nident");
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$finarray[] = $row;
		}

	}else{
		$result=mysqli_query($con,"Select A.dcutdate, A.ctranno, A.ctype, A.typdesc, A.ccode, A.cname, A.lapproved, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount, A.namount
		From (
			select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
			From sales_t a	
			left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."

			UNION ALL

			select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
			From ntsales_t a	
			left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."
		) A 
		order by A.ctranno, A.nident");
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$finarray[] = $row;
		}
	}

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$ngross = 0;
	foreach($finarray as $row)
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
			$invval = 
			$remarks = 
			$ccode =
			$xtypx =  
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";

			$ewtcode = 0;
			$vx = explode(";",$row['newtrate']);
			foreach($vx as $vx2){
				if($vx2!=""){
					$ewtcode = $ewtcode + floatval($vx2);
				}
			}
		
?>  
  <tr>
    <td nowrap><?=$row['type'];?></td>
    <td nowrap><a href="javascript:;" onClick="printchk('<?=$row['ctranno'];?>','<?=$row['type'];?>');"><?=$row['ctranno'];?></a></td>
		<td nowrap><?=($row['type']=="SI") ? $transrefDR[$row['ctranno']] : "";?></td>
		<td nowrap><?=$dateval;?></td>
    <td nowrap><?= $row['ccode'];?></td>
    <td nowrap><?=$row['cname'];?></td>   
		<td nowrap style="text-align: right"><?=(floatval($row['nvatgross'])!=0) ? number_format($row['nvatgross'],2) : ""?></td> 
    <td nowrap style="text-align: center"><?=(intval($row['nrate'])!=0 && intval($row['nrate'])!="") ? number_format($row['nrate'])."%" : ""?></td>
    <td nowrap style="text-align: right">
			<?php
				if(intval($row['nrate'])!=0 && intval($row['nrate'])!=""){
					if(floatval($row['vatamt'])!=0) {
						echo number_format($row['vatamt'],2);
					}
				}
			?>
		</td>
		<td nowrap style="text-align: right"><?=(floatval($row['ngross'])!=0) ? number_format($row['ngross'],2) : ""?></td>		
		<td nowrap style="text-align: center"><?=(intval($ewtcode)!=0 && intval($ewtcode)!="") ? number_format($ewtcode)."%" : ""?></td>
		<td nowrap style="text-align: right">
			<?php
				$phpewtamt = 0;

					if(intval($ewtcode)!=0 && intval($ewtcode)!=""){
						$phpewtamt = floatval($row['nvatgross']) * (floatval($ewtcode)/100);
					}

					echo (floatval($phpewtamt)!=0) ? number_format($phpewtamt,2) : "";
			?>
		</td>
		<td nowrap style="text-align: right">
				<?php
					$netvatamt = floatval($row['ngross']) - floatval($phpewtamt);
					echo number_format($netvatamt,2);
				?>
		</td>
		<td nowrap style="text-align: right">
			<?php
				$npay = 0;
				$cntofist = 0;
				foreach(@$arrpaymnts as $rxpymnts){
				 if($row['ctranno']==$rxpymnts['csalesno'] && $row['ctaxcode']==$rxpymnts['ctaxcodeorig'] && $row['cewtcode']==$rxpymnts['cewtcodeorig']){
					 $cntofist++;
					 
					 if($cntofist==1){
						 $ntotal = floatval($rxpymnts['ndue']) - floatval($rxpymnts['napplied']);
					 }
 
					 $npay = $npay + floatval($rxpymnts['napplied']);
				 }
				}

				echo (floatval($npay)!=0) ? number_format($npay,2) : "";
			?>
		</td>
		<td nowrap style="text-align: right">
				<?php
					$nbalace = floatval($netvatamt) - floatval($npay);

					echo (floatval($nbalace)!=0) ? number_format($nbalace,2) : "";
				?>
		</td>
  </tr>
<?php 
	}
?>

    <!--<tr class='rptGrand'>
    	<td colspan="12" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?//php echo number_format($totAmount,2);?></b></td>
    </tr>-->
</table>


<form action="PrintQuote_PDF.php" method="post" name="frmQPrint" id="frmQprint" target="_blank">
	<input type="hidden" name="hdntransid" id="hdntransid" value="<?php echo $txtctranno; ?>">
</form>

</body>
</html>

<script type="text/javascript">
/*$( document ).ready(function() {

	$('#MyTable tbody tr:last').clone().insertBefore('#MyTable tbody tr:first');
});*/

	function printchk(x,$xtyp){
		if($xtyp=="BS"){
			$("#frmQprint").attr("action","../../Sales/Quote/PrintBilling_PDF.php");
		}else{
			$("#frmQprint").attr("action","PrintBilling_PDF.php");
		}

		$("#frmQprint").submit();
	}
</script>