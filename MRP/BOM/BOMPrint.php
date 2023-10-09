<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];
	$tranno = $_POST['hdntransid'];


	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
		}

	}

	function float2rat($n, $tolerance = 1.e-6) {
    $h1=1; $h2=0;
    $k1=0; $k2=1;
    $b = 1/$n;
    do {
        $b = 1/$b;
        $a = floor($b);
        $aux = $h1; $h1 = $a*$h1+$h2; $h2 = $aux;
        $aux = $k1; $k1 = $a*$k1+$k2; $k2 = $aux;
        $b = $b-$a;
    } while (abs($n-$h1/$k1) > $n*$tolerance);

    return "$h1/$k1";
}

	$totdcount = array();
	$sqllabelnme = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and ldefault = 1");
	while($row2 = mysqli_fetch_array($sqllabelnme, MYSQLI_ASSOC)){
		$totdcount[$row2['citemno']] = $row2['nversion'];
	}

	$mainitmdsc = array();
	$sql = "select * From items where compcode='$company' and cpartno='$tranno'";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$mainitmdsc[] = $row2;
	}
	
	$arrmrpjo = array();
	$arrmrpjoall = array();
	$sql = "select X.*, A.citemdesc from mrp_bom X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno where X.compcode='$company' Order by X.cmainitemno, X.nitemsort";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjoall[] = $row2;
		if($row2['cmainitemno']==$tranno){
			$arrmrpjo[] = $row2;
		}	
	}

	$levelcnt = 2;
	function getsublvlcnt($itmno,$lvl){
		global $con;
		global $company;
		global $arrmrpjo;
		global $arrmrpjoall;
		global $levelcnt;

		foreach($arrmrpjoall as $row3){
			if($row3['cmainitemno']==$itmno && $row3['ctype']=="MAKE"){
				if($lvl+1 > $levelcnt){
					$levelcnt = $lvl+1;
				}
				getsublvlcnt($row3['citemno'],$lvl+1);
			}
		}

		return $levelcnt;
	}

	$totlcnt = getsublvlcnt($arrmrpjo[0]['cmainitemno'],2);
	//$totlcnt = 6;
	function getsub($itmno,$nqty,$nlvl){
		global $con;
		global $company;
		global $totlcnt;
		global $arrmrpjoall;
		global $totdcount;

		$nlvl++;

		$isval = "";
		foreach($arrmrpjoall as $row3){
			if($row3['cmainitemno']==$itmno){

				for($i=1; $i<=$totlcnt; $i++){
					$xlvldsc = ($i==$nlvl) ? $nlvl : "";
					$isval = $isval . "<td width='20px' align='center'>". $xlvldsc ."</td>";
				}

				if(isset($totdcount[$row3['citemno']])){
					$lv = $totdcount[$row3['citemno']];
				}else{
					$lv = 1;
				}

				$ntotqty = $nqty * floatval($row3['nqty'.$lv]);
				$ntotqty = (floor($ntotqty) == $ntotqty) ? number_format($ntotqty) : number_format($ntotqty,4);
				//if(floatval($ntotqty) < 1 ){
					//$ntotqty = float2rat($ntotqty);
				//}
				$isval = $isval . "<td align='center'>". $row3['citemno'] . "</td>";
				$isval = $isval . "<td align='center'>&nbsp;</td>";
				$isval = $isval . "<td align='center'>". $row3['citemdesc'] ."</td>";
				$isval = $isval . "<td align='center'>". $ntotqty ."</td>";
				$isval = $isval . "<td align='center'>". $row3['cunit'] ."</td>";
				$isval = $isval . "<td align='center'>". $row3['ctype'] ."</td>";


				echo "<tr>".$isval."</tr>";
				$isval = "";
				if($row3['ctype']=="MAKE"){
					$ntotqty = $nqty * floatval($row3['nqty'.$lv]);
					getsub($row3['citemno'],$ntotqty,$nlvl);
				}
			}
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Verdana, sans-serif;
			font-size: 9pt;
		}
		.tdpadx{
			padding-top: 5px; 
			padding-bottom: 5px
		}
		.tddetz{
			border-left: 1px solid; 
			border-right: 1px solid;
		}
		.tdright{
			padding-right: 10px;
		}
		
	</style>
</head>

<body > <!-- onLoad="window.print()" -->

	<table border="0" width="100%" cellpadding="1px"  id="tblMain">
		<tr>		<td align="left" width="250px"> 
				<img src="<?php echo "../".$logosrc; ?>" height="68px">
			</td>
			<td align="center"> 

					<font size="+2"><b>BOM</b></font><br><font size="+1">(BOM List)</font>

			</td>
			<td align="center"> 
				&nbsp;
			</td>
		</tr>
		
	</table>
	<br><br>

	<table border="1" width="100%" cellpadding="3px"  id="tblMain" style="border-collapse: collapse">
		<tr>
			<th colspan="<?=$totlcnt?>">Level</th>
			<th>Item Code</th>
			<th>Raw Material Title</th>
			<th>Raw Material No.</th>
			<th>Qty</th>
			<th>Unit</th>
			<th>Type</th>
		</tr>

		<tr>
			<?php
				for($i=1; $i<=$totlcnt; $i++){
			?>
				<td width="20px" align="center"> <?=($i==1) ? "1" : ""?> </td>
			<?php
				}
			?>
			<td align="center"> <?=$mainitmdsc[0]['cpartno'];?> </td>
			<td> &nbsp; </td>
			<td align="center"> <?=$mainitmdsc[0]['citemdesc'];?> </td>
			<td align="center"> 1 </td>
			<td align="center"> <?=$mainitmdsc[0]['cunit'];?> </td>
			<td align="center"> MAKE </td>
		</tr>

		<?php
			foreach($arrmrpjo as $rs1){

				if(isset($totdcount[$rs1['citemno']])){
					$lv = $totdcount[$rs1['citemno']];
				}else{
					$lv = 1;
				}

				if(floatval($rs1['nqty'.$lv]) < 1 ){
					$ntotqty = float2rat($rs1['nqty'.$lv]);
				}
		?>
			<tr>
				<?php
					for($i=1; $i<=$totlcnt; $i++){
				?>
						<td width="20px" align="center"> <?=($i==2) ? 2 : ""?> </td>
				<?php
					}
				?>
				<td align="center"> <?=$rs1['citemno']?> </td>
				<td align="center"> &nbsp; </td>
				<td align="center"> <?=$rs1['citemdesc']?> </td>
				<td align="center"> <?=(floor($rs1['nqty'.$lv]) == $rs1['nqty'.$lv]) ? number_format($rs1['nqty'.$lv]) : number_format($rs1['nqty'.$lv],4);?> </td>
				<td align="center"> <?=$rs1['cunit']?> </td>
				<td align="center"> <?=$rs1['ctype']?> </td>
			</tr>
		<?php
				if($rs1['ctype']=="MAKE"){
					
					getsub($rs1['citemno'],$rs1['nqty'.$lv],2);
				}
			}
		?>
	</table>
</body>
</html>