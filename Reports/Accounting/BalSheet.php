<?php
	if(!isset($_SESSION)){
	session_start();
	}
	$_SESSION['pageid'] = "BalSheet.php";

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

	//getall accounts
	$allaccounts = array();
	$result=mysqli_query($con,"SELECT A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Balance Sheet' ORDER BY CASE WHEN A.ccategory='ASSETS' THEN 1 WHEN A.ccategory='LIABILITIES' THEN 2 WHEN A.ccategory='EQUITY' THEN 3 END, A.nlevel, A.cacctid");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$allaccounts[] = $row;
	}


	//glactivity
		$arrallwithbal = array();
		$sql = "Select A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
				From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
				where A.compcode='$company' and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
				and B.cFinGroup = 'Balance Sheet'
				Group By A.acctno, B.cacctdesc
				Having sum(A.ndebit)<>0 or sum(A.ncredit)<>0
				Order By A.acctno";
//echo $sql;

		$result=mysqli_query($con,$sql);

		$darray = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$darray[] = $row;
			$arrallwithbal[] = $row['acctno'];
			getparent($row['acctno']);

		}

		function getparent($cacctno){
			global $allaccounts;
			global $arrallwithbal;
			
			foreach($allaccounts as $zx0){

				if($zx0['cacctid']==$cacctno){
					$arrallwithbal[] = $zx0['mainacct'];
					getparent($zx0['mainacct']);
				}
				
			}
		}
	//end glactivity

	//print_r($arrallwithbal);


	//sort accounts .. tree mode
	$mainarray = array();

	$dacctarraylist = array();
	$result=mysqli_query($con,"SELECT A.ccategory, A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Balance Sheet' and A.cacctid in ('".implode("','", $arrallwithbal)."') ORDER BY CASE WHEN A.ccategory='ASSETS' THEN 1 WHEN A.ccategory='LIABILITIES' THEN 2 WHEN A.ccategory='EQUITY' THEN 3 END, A.nlevel, A.cacctid");

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$dacctarraylist[] = $row;
	}

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
					case "ASSETS":
						$xtot = floatval($rsp['ndebit']) - floatval($rsp['ncredit']);
						break;
					case "LIABILITIES":
						$xtot = floatval($rsp['ncredit']) - floatval($rsp['ndebit']);
						break;
					case "EQUITY":
						$xtot = floatval($rsp['ncredit']) - floatval($rsp['ndebit']);
						break;
					}

				break;

			}
		}

		return $xtot;
	}

	function getpads($xlevl){

		$GENxyz = intval($xlevl);

		$GENxyz0 = 0;	

		switch ($GENxyz) {
			case 1:
				$GENxyz0 = 0;
				break;
			case 2:
				$GENxyz0 = 10;
				break;
			case 3:
				$GENxyz0 = 25;
				break;
			case 4:
				$GENxyz0 = 40;
				break;
			case 5:
				$GENxyz0 = 65;
				break;
			default:
				$GENxyz0 = 0;
		}

		return $GENxyz0;
	}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css?x=<?=time()?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Balance Sheet</title>

	<style>
			tr:hover {
				background-color: gainsboro;
			}
			@media print {
				.my-table {
					width: 100% !important;
				}
			}
	</style>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Balance Sheet</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table width="50%" border="0" align="center" cellpadding="3" class="my-table">
  <tr>

    <th style="text-align:center" width="100px">Account No. </th>
    <th style="text-align:center">Account Name</th>
    <th style="text-align:center">&#8369;</th>
  </tr>
 
 <?php

			if(count($mainarray)>0) {


 //echo "<pre>";
 //print_r($mainarray);
 //echo "</pre>";

 $twinGross = 0;

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

			$GENxyz1 = getpads($row['nlevel']);

			echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($row['nlevel'])]."</b></td><td align='right' style='border-bottom: 1px solid; border-top: 1px solid;'><b>".number_format($arrlvlamt[intval($row['nlevel'])],2)."</b></td></tr>";

			$arrlvlamt[intval($row['nlevel'])] = 0;
		}

		if($row['ctype']=="General"){

			$arrlvl[$row['nlevel']] = $row['cacctid']; 
			$arrlvldsc[$row['nlevel']] = $row['cacctdesc']; 

		}
		
		if($ccate!==$row['ccategory']){
			echo "<tr><td colspan='2' style='border-bottom-style: double; border-top: 2px solid #000;'><b>TOTAL ".$ccate."</b></td><td align='right' style='border-bottom-style: double; border-top: 2px solid #000;'><b>".number_format($arrlvlamt[0],2)."</b></td></tr>";

			if($ccate=="LIABILITIES" || $ccate=="EQUITY"){
				$twinGross = $twinGross + floatval($arrlvlamt[0]);
			}
			$arrlvlamt[0] = 0;

			echo "<tr><td colspan='3' style='padding-top: 20px'><b>".$row['ccategory']."</b></td></tr>";
		}
	
		
		$GENxyz1 = getpads($row['nlevel']);
?>
   <tr>

    <td onclick="funcset('<?=$row['cacctid']?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer">
			<?php
				if($row['ctype']=="General"){
					//echo "<b>".$row['cacctid']."</b>";
				}else{
					echo $row['cacctid'];
				}
			?>
		</td>
    <td onclick="funcset('<?=$row['cacctid']?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer; text-indent:<?=$GENxyz1?>px ">
			<?php
				if($row['ctype']=="General"){
					echo "<b>".$row['cacctdesc']."</b>";
				}else{
					echo $row['cacctdesc'];
				}
			?>
		</td>
  	<td style="text-align:right">
				<?php
					if($row['ctype']=="Details"){
						$xcvb = gettotal($row['cacctid'], $row['ccategory']);

						for ($x = 0; $x < intval($row['nlevel']); $x++) {
							$arrlvlamt[$x] = $arrlvlamt[$x] + $xcvb;
						}  

						if(floatval($xcvb) > 0){
							echo number_format($xcvb,2);

							$xmain = intval($row['nlevel']) - 1;
							
						}elseif(floatval($xcvb) < 0){
							echo "(".number_format(abs($xcvb),2).")";
						}else{
							echo "-";
						}
					}
				?>
		</td>
  </tr>
<?php

		$ccate = $row['ccategory'];
		$arrlvlcnt = $row['nlevel'];

	}

	if(intval($arrlvlcnt) !== 1){

		$GENxyz1 = getpads($arrlvlcnt-1);

		echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($arrlvlcnt)-1]."</b></td><td align='right' style='border-bottom: 1px solid; border-top: 1px solid;'><b>".number_format($arrlvlamt[intval($arrlvlcnt)-1],2)."</b></td></tr>";
	}

	echo "<tr><td colspan='2' style='border-bottom-style: double; border-top: 2px solid #000;'><b>TOTAL ".$ccate."</b></td><td align='right' style='border-bottom-style: double; border-top: 2px solid #000;'><b>".number_format($arrlvlamt[0],2)."</b></td></tr>";

	if($ccate=="LIABILITIES" || $ccate=="EQUITY"){
		$twinGross = $twinGross + floatval($arrlvlamt[0]);
	}

?>

<tr><td colspan='3' style='padding-top: 20px'>&nbsp;</td></tr>
<tr>
	<td colspan='2' style='border-bottom-style: double; border-top: 1px solid #000;'>
		<b>TOTAL LIABILITIES & EQUITY</b>
	</td>
	<td align='right' style='border-bottom-style: double; border-top: 1px solid #000;'>
		<b><?=number_format($twinGross,2)?></b>
	</td>
</tr>

<?php
			}
?>
 
</table>

<form action="TBal_Det.php" name="frmdet" id="frmdet" target="_blank" method="POST">
	<input type="hidden" name="ccode" id="ccode" value="">
	<input type="hidden" name="date1" id="date1" value="">
	<input type="hidden" name="date2" id="date2" value="">
</form>

</body>
</html>

<script>
	function funcset(xcode, xdte1, xdte2){

		document.getElementById("ccode").value = xcode;
		document.getElementById("date1").value = xdte1;
		document.getElementById("date2").value = xdte2;
	
		document.getElementById("frmdet").submit(); 
	}
</script>