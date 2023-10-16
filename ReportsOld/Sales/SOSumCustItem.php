<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "SalesOrders.php";

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
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc";

				$dsql2 = "select DISTINCT a.citemno, c.citemdesc, a.cunit, c.cclass, e.cdesc as typdesc
				From so_t a	
				left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Order By c.cclass, c.citemdesc";

			}elseif($trantype=="Non-Trade"){

				$dsql = "select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc";

				$dsql2 = "select DISTINCT a.citemno, c.citemdesc, a.cunit, c.cclass, e.cdesc as typdesc
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
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
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
				UNION ALL
				select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
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
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
				".$qryitm.$qrycust.$qryposted."
				Group By a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
				UNION ALL
				select a.compcode, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty
				From ntso_t a	
				left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
				left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
				left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
				left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
				where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
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
			$rescusto = mysqli_query($con,"Select cempid as ccode, COALESCE(ctradename,cname) as cname From customers where compcode='$company' and cempid in ('".implode("','", $allcustomers)."') Order By ccustomertype, COALESCE(ctradename,cname)");
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
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>JO Summary</title>
</head>

<body style="padding:10px">
	<center>
	<h2><?php echo strtoupper($compname);  ?></h2>
	<h2>Job Order Summary: Per Customer and Item</h2>
	<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
	<br>
	</center>

	<br><br>
	<table width="100%" border="0" align="center" id="MyTable">
		
		<tr>
			<th>Item Class</th>
			<th colspan="2">Product</th>
			<th>UOM</th>
			<?php
				foreach($custlist as $rs){
			?>
				<td align="center" style="padding-left: 5px; padding-right: 5px"><b><?=$rs['ccode']."<br>".$rs['cname']?></b></td>
			<?php
				}
			?>
		</tr>


		<?php

			$class="";
			$classval="";
			$classcode="";
			$totPrice=0;	
			$totCost=0;
			foreach($itmslist as $row)
			{
				
				if($class!=$row['cclass']){
					$classval=$row['typdesc'];
					$classcode="class='rpthead'";
				}
		?>  
		<tr <?php echo $classcode;?> >
			<td style="padding-right: 10px"><b><?php echo $classval;?></b></td>
			<td nowrap style="padding-right: 10px"><?php echo $row['citemno'];?></td>
			<td nowrap style="padding-right: 10px"><?php echo strtoupper($row['citemdesc']);?></td>
			<td nowrap style="padding-right: 10px"><?php echo $row['cunit'];?></td>

			<?php
				foreach($custlist as $rs){
			?>
				<td align="center" nowrap>
					<?php
						$x = getqty($rs['ccode'],$row['citemno'],$row['cunit']);
						if($x!=""){
							echo number_format($x);
						}	
					?>
				</td>
			<?php
				}
			?>
		</tr>
		<?php 
				$class=$row['cclass'];
				$classval="";
				$classcode="";
			}
		?>
	</table>
</body>
</html>

<script type="text/javascript">
	$(document).ready(function() {

		//$('#MyTable tbody tr:last').clone().insertBefore('#MyTable tbody tr:first');
	});
</script>