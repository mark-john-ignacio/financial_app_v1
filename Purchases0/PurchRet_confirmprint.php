<?php
if(!isset($_SESSION)){
session_start();
}


include('../Connection/connection_string.php');
include('../include/denied.php');

	$company = $_SESSION['companyid'];

	
	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$companyid = $rowcomp['compcode'];
			$companyname = $rowcomp['compname'];
			$companydesc = $rowcomp['compdesc'];
			$companyadd = $rowcomp['compadd'];
			$companytin = $rowcomp['comptin'];
		}

	}
	
	$cpono = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname from purchreturn a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno = '$cpono'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$DateNeeded = $row['dreturned'];
		$PurchType = $row['creturntype'];
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../css/cssmed.css">
<style type="text/css">
#tblMain {
/* the image you want to 'watermark' */
background-image: url(../images/preview.png);
background-position: center;
background-size: contain;
background-repeat: no-repeat;
}

@media print {
.noPrint {
    display:none;
}
}
#menu{
	position: fixed;
	padding-top:0px 0px 0px 0px;
	top: 0px;
	height:30px;
	width:98%;
	border-style:solid;
	background-color:#9FF;
  border:1px solid black;
  opacity:1.0;
}
html, body {
	top:0px;
} 


</style>
<head>
<script type="text/javascript">
function Print(x){
	window.location.href = "PurchRet_print.php?x="+x;
}
</script>
</head>

<body>
<br><br>
<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td><font size="3"><b><?php echo $companyname;?></b></font></td>
    <td colspan="2" align="center"><font size="3"><b>Purchase Return</b> </font></td>
  </tr>
  <tr>
    <!--<td><font size="2"><b><?php //echo $companydesc;?></b></font></td>-->
    <td><font size="2"><b><?php echo $companyadd;?></b></font></td>
    <td width="100">Number:</td>
    <td width="150"><?php echo $cpono;?></td>
  </tr>
  <tr>
    <td><font size="2"><b>TIN #<?php echo $companytin;?></b></font></td>
    <td width="100">Date:</td>
    <td width="150"><?php echo $Date;?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="100">Page:</td>
    <td width="150">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">
    
    <table width="100%" border="0" cellpadding="3" cellspacing="5">
      <tr>
        <td height="60" valign="top" style="border:1px solid; border-style:dashed;"><font size="2"><b>SUPPLIER:</b></font><br>&nbsp;&nbsp; &nbsp; <?php echo $CustCode;?> - <?php echo $CustName;?></td>
        <td width="40%" height="60" valign="top" style="border:1px solid; border-style:dashed;"><font size="2"><b>DELIVERY DETAILS:</b></font><br>&nbsp;&nbsp; &nbsp; <?php echo $Remarks;?></td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">
    
    <table width="100%" border="0" cellpadding="3" style="border-style:dashed;">
      <tr>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Part No.</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Item Details</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Qty/UOM</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Price</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">%</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Total Php.</th>
      </tr>
      <?php 
		$sqlbody = mysqli_query($con,"select a.*,b.citemdesc from purchreturn_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$cpono'");

		if (mysqli_num_rows($sqlbody)!=0) {
		$cntr = 0;
		while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
		 $cntr = $cntr + 1;
						
	?>
      
      <tr>
        <td style="border-right:1px dashed;"><?php echo $rowbody['citemno'];?></td>
        <td style="border-right:1px dashed;"><?php echo $rowbody['citemdesc'];?></td>
        <td style="border-right:1px dashed;" align="right"><?php echo $rowbody['nqty'];?> <?php echo $rowbody['cunit'];?></td>
        <td style="border-right:1px dashed;" align="right"><?php echo $rowbody['nprice'];?></td>
        <td style="border-right:1px dashed;">&nbsp;</td>
        <td align="right"><?php echo $rowbody['namount'];?></td>
        
      </tr>
      <?php 
		}
		}
	  ?>
        <tr>
        <td height="30" colspan="2" style="border-top:1px dashed;" valign="bottom">Prepared By: <?php echo $_SESSION['employeefull'];?></td>
        <td colspan="3" style="border-top:1px dashed;" align="right"  valign="bottom"><b>Total Php: </b></td>
        <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?php echo $Gross;?></b></td>
        </tr>

    </table></td>
  </tr>
</table>

<div align="center" id="menu" class="noPrint">
<div style="float:left;">&nbsp;&nbsp;<strong><font size="-1">PURCHASE RETURN</font></strong></div>
<div style="float:right;">
<input type="button" value="PRINT RETURN" onClick="Print('<?php echo $cpono;?>');" class="noPrint"/>
</div>
</div>

</body>
</html>