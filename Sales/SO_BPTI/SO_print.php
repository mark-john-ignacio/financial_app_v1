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
      $companylogx = $rowcomp['clogoname'];
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
    $PODate = $row['dpodate'];
		$Date = $row['dcutdate'];
		//$SalesType = $row['csalestype'];
		$Gross = $row['ngross'];
		$PONos = $row['cpono'];

		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
	}
}
?>

<!DOCTYPE html>
<html>
<style>
  body{
			font-family: Arial, sans-serif;
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
    #imgcontent {
        position: relative;
    }
    #imgcontent img {
        position: absolute;
        top: 2px;
        left: 3px;
    }
</style>
<head>
</head>

<body style="padding:5px" onLoad="window.print();">
<div id="imgcontent">
    <img src="../<?=$companylogx?>" class="ribbon" alt="" width="150px"/>
</div>
<table width="100%" border="0" cellpadding="3" style="border-collapse: collapse;">
  <tr>
    <td colspan="4" align="center" height="50px"><font style="font-size: 24px;">SALES ORDER</font></td>
  </tr>
  <tr>
    <td colspan="4" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td width="100"><b>Customer: </b></td>
    <td><?php echo $CustName;?></td>
		<td width="100"><b>SO No.:</b></td>
    <td><?=$csalesno?></td>
  </tr>
  <tr>
    <td width="100"><b>Control No.: </b></td>
    <td><?=$PONos;?></td>
		<td width="100"><b>PO Date</b></td>
    <td><?php echo date_format(date_create($PODate),"M d, Y");?></td>
  </tr>

  <tr>
    <td colspan="4">
    
      <table width="100%" border="1" cellpadding="3" style="border-collapse: collapse;">
        <tr>
          <th scope="col" height="30" width="20px">No.</th>
          <th scope="col" height="30" width="150px">PO No.</th>
          <th scope="col" height="30" width="150px">Item Code</th>
          <th scope="col" height="30">Item Description</th>
          <th style="text-align: center" scope="col" height="30">Qty</th>
          <th style="text-align: center" scope="col">Unit</th>
          <th style="text-align: center" scope="col">Need Date</th>
					<th style="text-align: center" scope="col">Remarks</th>
        </tr>
        <?php 
          $sqlbody = mysqli_query($con,"select a.*,b.citemdesc from so_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno' Order By a.nident");

          if (mysqli_num_rows($sqlbody)!=0) {
          $cntr = 0;
          while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
          $cntr = $cntr + 1;
                  
        ?>
        
        <tr>
        <td><?=$cntr?></td>
          <td><?php echo strtoupper($rowbody['ditempono']);?></td>
          <td><?php echo strtoupper($rowbody['citemno']);?></td>
          <td><?php echo strtoupper($rowbody['citemdesc']);?></td>
          <td style="text-align: center"><?=number_format($rowbody['nqty']);?></td>
          <td style="text-align: center"><?php echo $rowbody['cunit'];?></td>
					<td style="text-align: center"><?php echo $rowbody['ditemneeded'];?></td>
					<td style="text-align: center"><?php echo $rowbody['citemremarks'];?></td>
          
        </tr>
        <?php 
          }
        }
        ?>

      </table>
  
    </td>
  </tr>

  <tr>
    <td colspan="4" align="center" style="padding-top: 20px; border-collapse: collapse;">
      <table width="100%" border="1" cellpadding="3">
        <tr>
          <td height="50px" valign="top">Prepared By: </td>
          <td height="50px" valign="top">Checked By: </td>
          <td height="50px" valign="top">Approved By: </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>