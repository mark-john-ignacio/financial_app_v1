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
	
	$csalesno = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname from so a left join customers b on a.ccode=b.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
    $TranDate = $row['ddate'];
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

<body style="padding:5px" onLoad="window.print();">
<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;">
  <tr>
    <td colspan="2"><font size="2"><b>JOB ORDER SLIP - <?php echo $csalesno;?></b></font></td>
  </tr>
  <tr>
    <td width="100">Customer:</td>
    <td><?php echo $CustCode;?> - <?php echo $CustName;?></td>
  </tr>
  <tr>
    <td width="100">JO Date:</td>
    <td><?php echo date_format(date_create($TranDate),"M d, Y H:i:s");?></td>
  </tr>
  <tr>
    <td width="100">Delivery Date:</td>
    <td><?php echo date_format(date_create($Date),"M d, Y");?></td>
  </tr>

  <tr>
    <td colspan="3">
    
    <table width="100%" border="0" cellpadding="3" style="border-style:dashed;">
      <tr>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Item Description</th>
        <th scope="col" height="30" style="border-top: 1px dashed; border-bottom: 1px dashed;">Qty</th>
        <th scope="col" style="border-top: 1px dashed; border-bottom: 1px dashed;">Unit</th>
      </tr>
      <?php 
		$sqlbody = mysqli_query($con,"select a.*,b.citemdesc from so_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno'");

		if (mysqli_num_rows($sqlbody)!=0) {
		$cntr = 0;
		while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
		 $cntr = $cntr + 1;
						
	?>
      
      <tr>
        <td style="border-right:1px dashed;"><?php echo strtoupper($rowbody['citemdesc'])?></td>
        <td style="border-right:1px dashed;" align="right"><?php echo $rowbody['nqty'];?></td>
        <td style="border-right:1px dashed;" align="right"><?php echo $rowbody['cunit'];?></td>
        
      </tr>
      <?php 
		}
		}
	  ?>
        <tr>
        <td height="30" colspan="3" style="border-top:1px dashed;" valign="bottom">Prepared By: <?php echo $_SESSION['employeefull'];?></td>
        </tr>

    </table></td>
  </tr>
</table>
</body>
</html>