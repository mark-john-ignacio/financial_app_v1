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

$sql = "Select a.ctranno,DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate,a.namount
From loans a 
where a.compcode='$company' and  a.ccode='$q' and a.lapproved=1 and a.crefAP IS NULL
order by a.ddate";

//echo $sql;

$rsd = mysqli_query($con,$sql);

?>
<table width="100%" border="0" cellpadding="0" id="MyTable">
	<thead>
      <tr>
        <th scope="col">Loan No</th>
        <th scope="col">Date</th>
        <th scope="col">Amount(PHP)</th>
        <th scope="col">Payed(PHP)</th>
        <th scope="col" width="150px">Discount(PHP)</th>
        <th scope="col" width="150px">Total Owed(PHP)</th>
        <th scope="col" width="150px">Amount Applied(PHP)</th>
      </tr>
   </thead>
 <tbody>

<?php
$cntr = 0;
$amt = 0;

while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
$cntr = $cntr + 1;

?>
  <tr>
    <td><?php echo $rs['ctranno']?><input type="hidden" name="cTranNo<?php echo $cntr;?>" id="cTranNo<?php echo $cntr;?>" value="<?php echo $rs['ctranno']?>" /></td>
    
   <!--<td>&nbsp;</td>-->
    
    <td><?php echo $rs['ddate']?><input type="hidden" name="dApvDate<?php echo $cntr;?>" id="dApvDate<?php echo $cntr;?>" value="<?php echo $rs['ddate']?>" /></td>
    
    <td align="right"><?php echo $rs['namount']?><input type="hidden" name="nAmount<?php echo $cntr;?>" id="nAmount<?php echo $cntr;?>" value="<?php echo $rs['namount']?>" />&nbsp;&nbsp;&nbsp;</td>

    <td align="right">&nbsp;&nbsp;&nbsp;</td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nDiscount<?php echo $cntr;?>" id="nDiscount<?php echo $cntr;?>" value="0.0000" style="text-align:right" readonly="readonly"/></td>
    
    <td style="padding:2px" align="center"><input type="text" class="form-control input-sm" name="cTotOwed<?php echo $cntr;?>" id="cTotOwed<?php echo $cntr;?>"  value="<?php echo $rs['namount']; ?>" style="text-align:right" readonly="readonly"></td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nApplied<?php echo $cntr;?>" id="nApplied<?php echo $cntr;?>"  value="<?php echo $rs['namount']?>" style="text-align:right" /></td>
  </tr>	
<?php
}
?>
</tbody>
</table>

