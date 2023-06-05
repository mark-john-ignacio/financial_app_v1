<?php
if(!isset($_SESSION)){
session_start();
}

require_once "../../Connection/connection_string.php";
$q = $_REQUEST["id"];
$company = $_SESSION['companyid'];


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

$sql = "SELECT A.cacctno, A.ctranno, DATE_FORMAT(B.dapvdate,'%m/%d/%Y') as dapvdate, sum(A.ncredit) as namount, IFNULL(sum(D.napplied),0) as napplied 
FROM `apv_t` A 
left join `apv` B on A.compcode=B.compcode and A.ctranno=B.ctranno
left join `accounts` C on A.compcode=C.compcode and A.cacctno=C.cacctno
left join 
	(	
		select a.napplied, a.capvno, a.ctranno
		from paybill_t a
		left join paybill b on a.ctranno=b.ctranno
		where b.lcancelled=0
	) D on A.ctranno=D.capvno
where A.compcode='$company' and B.lapproved=1 and A.ncredit <> 0 and C.ccategory='LIABILITIES' and B.ccode='$q'
group by A.cacctno,a.ctranno,b.dapvdate order by B.dapvdate";

//echo $sql;

$rsd = mysqli_query($con,$sql);

?>
<table width="100%" border="0" cellpadding="0" id="MyTable">
 <thead>
  <tr>
    <th scope="col">AP No</th>
    <th scope="col" width="150px">Date</th>
    <th scope="col" width="150px">Amount(PHP)</th>
    <th scope="col" width="150px">Payed(PHP)</th>
    <th scope="col" width="150px">Total Owed(PHP)</th>
    <th scope="col" width="150px">Amount Applied(PHP)</th>
  </tr>
 </thead>
 <tbody>
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
    <td><?php echo $rs['ctranno']?><input type="hidden" name="cTranNo<?php echo $cntr;?>" id="cTranNo<?php echo $cntr;?>" value="<?php echo $rs['ctranno']?>" /> <input type="hidden" name="cacctno<?php echo $cntr;?>" id="cacctno<?php echo $cntr;?>" value="<?php echo $rs['cacctno']?>" /></td>
    
   <!--<td>&nbsp;</td>-->
    
    <td><?php echo $rs['dapvdate']?><input type="hidden" name="dApvDate<?php echo $cntr;?>" id="dApvDate<?php echo $cntr;?>" value="<?php echo $rs['dapvdate']?>" /></td>
    
    <td align="right"><?php echo $amtapv; ?><input type="hidden" name="nAmount<?php echo $cntr;?>" id="nAmount<?php echo $cntr;?>" value="<?php echo $rs['namount']?>" />&nbsp;&nbsp;&nbsp;</td>

    <td align="right"><?php echo $amtapplied; ?><input type="hidden" name="cTotPayed<?php echo $cntr;?>" id="cTotPayed<?php echo $cntr;?>"  value="<?php echo $amtapplied; ?>" style="text-align:right" readonly="readonly">&nbsp;&nbsp;&nbsp;</td>
        
    <td style="padding:2px" align="center"><?php echo $totowed; ?><input type="hidden" name="cTotOwed<?php echo $cntr;?>" id="cTotOwed<?php echo $cntr;?>"  value="<?php echo $totowed; ?>">&nbsp;&nbsp;&nbsp;</td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nApplied<?php echo $cntr;?>" id="nApplied<?php echo $cntr;?>"  value="0.0000" style="text-align:right" /></td>
  </tr>	
<?php
}
}
?>

</tbody>
</table>

