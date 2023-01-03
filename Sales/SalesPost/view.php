<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

//	$sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_DR'");
//	if(mysqli_num_rows($sqlauto) != 0){
//		while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
//		{
//			$autopost = $rowauto['cvalue'];
//		}
//	}

	
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
	
	$csalesno = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname from sales a left join customers b on a.ccode=b.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		//$SalesType = $row['csalestype'];
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
	}
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">

<head>
</head>

<body style="padding:5px">

<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td><font size="3"><b><?php echo $companyname;?></b></font></td>
    <td colspan="2" align="center"><font size="3"><b>Sales Invoice</b></font></td>
  </tr>
  <tr>
    <!--<td><font size="2"><b><?php //echo $companydesc;?></b></font></td>-->
    <td><font size="2"><b><?php echo $companyadd;?></b></font></td>
    <td width="100">Number:</td>
    <td width="150"><?php echo $csalesno;?></td>
  </tr>
  <tr>
    <td><font size="2"><b>TIN #<?php echo $companytin;?></b></font></td>
    <td width="100">Delivery Date:</td>
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
        <td height="60" valign="top" style="border:1px solid; border-style:dashed;"><font size="2"><b>CUSTOMER:</b></font><br>&nbsp;&nbsp; &nbsp; <?php echo $CustCode;?> - <?php echo $CustName;?></td>
        <td width="40%" height="60" valign="top" style="border:1px solid; border-style:dashed;"><font size="2"><b>DELIVERY DETAILS:</b></font><br>&nbsp;&nbsp; &nbsp; <?php echo $Remarks;?><br>
        <?php
        	if($lPrintPosted==1){
				echo "<font color='#FF0000'><b><i>ORIGINAL SI ALREADY PRINTED<i></b></font>";
			}
		?>
        
        </td>
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
        <th scope="col" style="border-top: 1px dashed; border-bottom: 1px dashed;">Price</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Total Amount</th>
      </tr>
      <?php 
		$sqlbody = mysqli_query($con,"select a.*,b.citemdesc from sales_t a left join items b on a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno'");

		if (mysqli_num_rows($sqlbody)!=0) {
		$cntr = 0;
		$totnqty = 0;
		$totnetvat = 0;
		$totlessvat = 0;
		
		while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
		 $cntr = $cntr + 1;
						
	?>
      
      <tr>
        <td style="border-right:1px dashed;"><?php echo $rowbody['citemno'];?></td>
        <td style="border-right:1px dashed;"><?php echo $rowbody['citemdesc'];?></td>
        <td style="border-right:1px dashed;" align="right"><?php echo $rowbody['nqty'];?> <?php echo $rowbody['cunit'];?></td>
        <td style="border-right:1px dashed;" align="right"><?php echo $rowbody['nprice'];?></td>
        <td align="right"><?php echo $rowbody['namount'];?></td>
        
      </tr>
      <?php 
	  		//$totnqty = (float)$totnqty + (float)$rowbody['nqty'];
				$totnetvat = (float)$totnqty + (float)$rowbody['nnetvat'];;
				$totlessvat = (float)$totnqty + (float)$rowbody['nlessvat'];;
			
		}
		}
	  ?>
        <tr>
        <td colspan="4" style="border-top:1px dashed;" align="right"  valign="bottom"><b>Total Gross: </b></td>
        <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?php echo $Gross;?></b></td>
        </tr>
        <tr>
          <td height="30" colspan="2" style="border-top:1px dashed;" valign="bottom">Prepared By: <?php echo $_SESSION['employeefull'];?></td>
          <td height="30" colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>Total Qty Delivered:</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?php echo $totnqty;?></b></td>
        </tr>

    </table></td>
  </tr>
</table>
</body>
</html>