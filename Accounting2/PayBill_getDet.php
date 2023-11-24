<?php
require_once "../Connection/connection_string.php";
$q = $_REQUEST["id"];
if (!$q) return;

//get CREDIT ACCOUNT CODE
    $sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVCREDIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nCredit = $row['cvalue'];
		}
	}else{
		$nCredit = "";
	}

$sql = "Select a.ctranno,DATE_FORMAT(b.dapvdate,'%m/%d/%Y') as dapvdate,a.namount, sum(c.napplied) as napplied
From apv_d a 
left join apv b on a.ctranno=b.ctranno
left join 
	(	
		select a.napplied, a.capvno, a.ctranno
		from paybill_t a
		left join paybill b on a.ctranno=b.ctranno
		where b.lcancelled=0
	) c on a.ctranno=c.capvno
where b.ccode='$q'
group by a.ctranno,b.dapvdate,a.namount";


$rsd = mysqli_query($con,$sql);

?>
<table width="100%" border="0" cellpadding="0" id="MyTable">
  <tr>
    <th scope="col">APV No</th>
    <!--<th scope="col">Status</th>-->
    <th scope="col">Date</th>
    <th scope="col">Amount(PHP)</th>
    <th scope="col">Payed(PHP)</th>
    <th scope="col" width="150px">Discount(PHP)</th>
    <th scope="col" width="150px">Total Owed(PHP)</th>
    <th scope="col" width="150px">Amount Applied(PHP)</th>
  </tr>
<?php
$cntr = 0;
$amtapv = 0;
$amtapplied = 0;

while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
$cntr = $cntr + 1;

$amtapv = $rs['namount'];
$amtapplied = $rs['napplied'];

$totowed = $amtapv - $amtapplied;
//echo $totowed . "<br>";
if ($totowed > 0) {
	
	$amtapv = number_format($amtapv,4,".",""); 
	$amtapplied = number_format($amtapplied,4,".",""); 
	$totowed = number_format($totowed,4,".","");
?>
  <tr>
    <td><?php echo $rs['ctranno']?><input type="hidden" name="cTranNo<?php echo $cntr;?>" id="cTranNo<?php echo $cntr;?>" value="<?php echo $rs['ctranno']?>" /></td>
    
   <!--<td>&nbsp;</td>-->
    
    <td><?php echo $rs['dapvdate']?><input type="hidden" name="dApvDate<?php echo $cntr;?>" id="dApvDate<?php echo $cntr;?>" value="<?php echo $rs['dapvdate']?>" /></td>
    
    <td align="right"><?php echo $amtapv; ?><input type="hidden" name="nAmount<?php echo $cntr;?>" id="nAmount<?php echo $cntr;?>" value="<?php echo $rs['namount']?>" />&nbsp;&nbsp;&nbsp;</td>

    <td align="right"><?php echo $amtapplied; ?>&nbsp;&nbsp;&nbsp;</td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nDiscount<?php echo $cntr;?>" id="nDiscount<?php echo $cntr;?>" value="0.0000" style="text-align:right" /></td>
    
    <td style="padding:2px" align="center"><input type="text" class="form-control input-sm" name="cTotOwed<?php echo $cntr;?>" id="cTotOwed<?php echo $cntr;?>"  value="<?php echo $totowed; ?>" style="text-align:right" readonly="readonly"></td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nApplied<?php echo $cntr;?>" id="nApplied<?php echo $cntr;?>"  value="0.0000" style="text-align:right" /></td>
  </tr>	
<?php
}
}
?>
</table>

