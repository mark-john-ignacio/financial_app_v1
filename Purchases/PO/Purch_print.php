<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.cphone,b.cmobile from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.cpono = '$cpono'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$CustAdd = $row['chouseno']." ".$row['ccity']." ".$row['cstate'];
		$CustContactNo = $row['cphone'];
			if($CustContactNo<>""){
				$CustContactNo = $CustContactNo." / ".$row['cmobile'];
			}else{
				$CustContactNo = $row['cmobile'];
			}
		$Remarks = $row['cremarks'];
		$Date = $row['ddate'];
		$DateNeeded = $row['dneeded'];
		$Gross = $row['ngrossbefore'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">

<head>
</head>

<body style="padding:5px" onLoad="window.print();">

<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td><font size="3"><b><?php echo $companyname;?></b></font></td>
    <td colspan="2" align="center"><font size="3"><b>Purchase Order</b></font></td>
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
    <td width="150"><?php echo date_format(date_create($Date), "m/d/Y"); ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="100">Date Needed:</td>
    <td width="150"><?php echo date_format(date_create($DateNeeded), "m/d/Y"); ?></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">
    
    <table width="100%" border="0" cellpadding="3" cellspacing="5">
      <tr>
        <td height="60" valign="top" style="border:1px solid; border-style:dashed;"><font size="2"><b>PURCHASE TO:</b></font>&nbsp;&nbsp; &nbsp; <?php echo $CustCode;?> - <?php echo $CustName;?><br>&nbsp;<?php echo $CustAdd;?><br>&nbsp;<?php echo $CustContactNo;?>
        
        </td>
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
		$sqlbody = mysqli_query($con,"select a.*,b.citemdesc, a.citemdesc as newdesc from purchase_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode = '$company' and a.cpono = '$cpono' Order by a.nident");

		if (mysqli_num_rows($sqlbody)!=0) {
		$cntr = 0;
		$itemdesc = "";
		while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
		 $cntr = $cntr + 1;
		 
		 if($rowbody['citemno']=='NEW_ITEM'){
			 $itemdesc = $rowbody['newdesc'];
		 }
		 else{
			 $itemdesc = $rowbody['citemdesc'];
		 }
						
	?>
      
      <tr>
        <td style="border-right:1px dashed;"><?php echo $rowbody['citemno'];?></td>
        <td style="border-right:1px dashed;"><?php echo $itemdesc;?></td>
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
</body>
</html>