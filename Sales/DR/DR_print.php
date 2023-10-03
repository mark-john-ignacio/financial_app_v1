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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin,c.cname as cdelname from dr a 
          left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join customers c on a.compcode=c.compcode and a.cdelcode=c.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cdelname'];
    $CustDelName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
    $Adds = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];
    $cTin = $row['ctin'];

		//$SalesType = $row['csalestype'];
		$Gross = $row['ngross'];
    $cTerms = $row['cterms'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
	}
}
?>

<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">

<head>
</head>

<body style="padding-top:0.82in" onLoad="window.print()">

<table width="100%" border="0" cellpadding="1" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td colspan="2" style="padding-right: 0.25in;" align="right"><font size="3"><b><?php echo $csalesno;?></b></font></td>
  </tr>

  <tr>
    <td VALIGN="TOP">

      <table width="100%" border="0" cellpadding="2" style=" margin-top: 0.14in !important">
        <tr><td style="padding-left: 1.2in;"> <?=$CustName?> </td></tr>
        <tr><td style="padding-left: 1.2in"><?=$cTin?></td></tr>
        <tr><td style="padding-left: 1.2in"><?=$Adds?> </td></tr>       
        </tr>
      </table>

    </td>
    <td style="width: 2.7in" VALIGN="TOP"> 
      <table width="100%" border="0" >
        <tr><td style="padding-right: 0.3in;" align="right"> <?=date_format(date_create($Date), "M d, Y")?> </td></tr>
        <tr><td style="padding-right: 0.3in;" align="right"> &nbsp; </td></tr>
        <tr><td style="padding-right: 0.3in;" align="right"> <?=$cTerms?> </td></tr>
        <tr><td style="padding-right: 0.3in;" align="right"> &nbsp; </td></tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" style="padding-left: 0.3in; padding-top: 30px;">
    
    <table width="100%" border="0" cellpadding="2">
      <?php 
		$sqlbody = mysqli_query($con,"select a.*,b.citemdesc from dr_t a left join items b on a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno'");

		if (mysqli_num_rows($sqlbody)!=0) { 
		$cntr = 0;
		$totnqty = 0;
		while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
		 $cntr = $cntr + 1;
						
	?>
      
      <tr>
        <td style="width: 0.7in"><?php echo number_format($rowbody['nqty']);?></td> 
        <td style="width: 0.8in"><?php echo $rowbody['cunit'];?></td> 
        <td><?php echo $rowbody['citemno'];?></td>
        <td style="text-overflow: ellipsis; width: 5in"><?php echo $rowbody['citemdesc'];?></td>
               
      </tr>
  <?php
    }
  }
  ?>
    </table></td>
  </tr>
</table>

</body>
</html>