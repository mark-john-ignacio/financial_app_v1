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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,c.Fname,c.Lname,c.Minit from apv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join users c on a.cpreparedby=c.Userid where a.compcode='$company' and a.ctranno = '$cpono'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cpaymentfor'];
		$Date = $row['dapvdate'];
		//$ChkNo = $row['cchkno'];
		
		$PreparedBy = $row['Lname'].", ".$row['Fname']." ".$row['Minit'];

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
	window.location.href = "APV_print.php?x="+x;
}
</script>
</head>

<body>
<br><br>
<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td><font size="3"><b><?php echo $companyname;?></b></font></td>
    <td colspan="2" align="center"><font size="3"><b>APV</b></font></td>
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
    <td width="100">&nbsp;</td>
    <td width="150">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">
    
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
      <tr>
        <td>Account Code</td>
        <td>Account Description</td>
        <td>Debit</td>
        <td>Credit</td>
        <td>Sub</td>
        </tr>
      <tr>
        <td colspan="5"><div style="padding-left:100px">
        <br>
        <b>PARTICULARS:</b><br>
        <div style="padding-left:20px">
        <?php echo nl2br($Remarks);?>
        </div> </div>
        </td>
        </tr>
      <tr>
        <td colspan="5">&nbsp;</td>
      </tr>
      <?php
	  
	$sqlhead = mysqli_query($con,"select a.cacctno, a.ctitle, a.csubsidiary, sum(a.ndebit) as ndebit, sum(a.ncredit) as ncredit , b.cname from apv_t a left join customers b on a.compcode=b.compcode and a.csubsidiary=b.cempid where a.compcode='$company' and a.ctranno = '$cpono' group by a.cacctno, a.ctitle ,b.cname,  a.csubsidiary");
	
	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){     
	  ?>
      <tr>
        <td><?php echo $row['cacctno']?></td>
        <td><?php echo $row['ctitle']?></td>
        <td><?php 
		if($row['ndebit']!=0){ 
			echo $row['ndebit'];
		}
		?></td>
        <td><?php 
		if($row['ncredit']!=0){ 
			echo $row['ncredit'];
		}
		?></td>
        <td><?php echo $row['csubsidiary']?></td>
      </tr>
      <?php
		}
	}
	  ?>
 
       <tr>
        <td colspan="5" valign="bottom">
        <div style="padding:20px">
        	<div style="float:left; width:30%;"><b>Prepared By:<br><br><br>&nbsp;&nbsp;&nbsp;</b><?php echo $PreparedBy;?></div>
            <div style="float:right; width:60%;" align="left"><b>Approved By:</b></div>
        </div>
        </td>
        </tr>
       <tr>
         <td colspan="5"  valign="bottom">&nbsp;</td>
       </tr>

    </table>
    
    </td>
  </tr>
</table>

<div align="center" id="menu" class="noPrint">
<div style="float:left;">&nbsp;&nbsp;<strong><font size="-1">Receiving</font></strong></div>
<div style="float:right;">
<input type="button" value="PRINT VOUCHER" onClick="Print('<?php echo $cpono;?>');" class="noPrint"/>
</div>
</div>

</body>
</html>