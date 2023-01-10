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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate from ntsales a left join customers b on a.ccode=b.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
    $Adds = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];

		//$SalesType = $row['csalestype'];
		//$Gross = $row['ngross'];
		
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

<body style="padding:5px" >

<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td><h1>SALES ORDER SLIP<h1></td>
    <td colspan="2" align="center"><font size="3"><b><?php echo $csalesno;?></b></font></td>
  </tr>
  <tr>
    <td>To:&nbsp;&nbsp;&nbsp;<?=$CustName?></td>
    <td>Date: &nbsp;&nbsp;&nbsp; <?php echo $Date;?> </td>
  </tr>
  <tr>
    <td colspan="2">Address:&nbsp;&nbsp;&nbsp;<?=$Adds?></td>
  </tr>
  
  <tr>
    <td colspan="3">
    
    <table width="100%" border="0" cellpadding="3" style="border-style:dashed;">
      <tr>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Qty</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Unit</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Description</th>
        <th scope="col" style="border-top: 1px dashed; border-bottom: 1px dashed;">Unit Price</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Total Amount</th>
      </tr>
      <?php 
		$sqlbody = mysqli_query($con,"select a.*,b.citemdesc from ntsales_t a left join items b on a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno' Order By a.cidentity");

		if (mysqli_num_rows($sqlbody)!=0) {
		$cntr = 0;
		$totnqty = 0;
		$totnetvat = 0;
		$totlessvat = 0;
		$Gross = 0;
		while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
		 $cntr = $cntr + 1;
						
	?>
      
      <tr>
        <td style="border-right:1px dashed;"><?php echo number_format($rowbody['nqty'],2);?></td>
        <td style="border-right:1px dashed;"><?php echo $rowbody['cunit'];?></td>
        <td style="border-right:1px dashed;"><?php echo $rowbody['citemdesc'];?></td>
        <td style="border-right:1px dashed;" align="right"><?php echo number_format($rowbody['nprice'],2);?></td>
        <td align="right"><?php echo number_format($rowbody['namount'],2);?></td>
        
      </tr>
      <?php 
	  			//$totnqty = (float)$totnqty + (float)$rowbody['nqty'];
				$totnetvat = (float)$totnqty + (float)$rowbody['nnetvat'];
				$totlessvat = (float)$totnqty + (float)$rowbody['nlessvat'];
				
				$Gross = $Gross + $rowbody['namount'];
			
		}
		}
	  ?>
        <tr>
        <td colspan="4" style="border-top:1px dashed;" align="right"  valign="bottom"><b>Grand Total: </b></td>
        <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?php echo $Gross;?></b></td>
        </tr>
        <tr>
          <td height="30" colspan="2" style="border-top:1px dashed;" valign="bottom">Prepared By: <?php echo $_SESSION['employeefull'];?></td>
          <td height="30" colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><!--<b>Total Qty Delivered:</b>--></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?php echo $totnqty;?></b></td>
        </tr>

    </table></td>
  </tr>
</table>
</body>
</html>