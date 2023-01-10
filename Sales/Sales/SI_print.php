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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin from sales a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
    $Adds = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];
    $cTin = $row['ctin'];
    $cTerms = $row['cterms'];

    $cvatcode = $row['cvatcode'];

		//$SalesType = $row['csalestype'];
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
	}
} // onLoad="window.print()"
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">

<head>
</head>

<body style="padding-top:0.75in" onLoad="window.print()">

<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td colspan="2" align="right" style="height: 0.43in"><font size="3"><b><?php echo $csalesno;?></b></font></td>
  </tr>

  <tr>
    <td VALIGN="TOP">
    
      <table width="100%" border="0" cellpadding="3" cellspacing="5">
        <tr><td style="height: 0.35in; padding-left: 0.8in"> <?=$CustName?> </td></tr>
        <tr><td style="height: 0.35in; padding-left: 0.5in"><?=$Adds?> </td></tr>
        <tr><td style="height: 0.2in"> &nbsp;&nbsp;&nbsp; <?=$cTin?></td></tr>
        <tr><td style="height: 0.2in; padding-left: 0.8in; font-size: 11px"></td></tr>
        </tr>
      </table>

    </td>
    <td style="width: 2in"> 
      <table width="100%" border="0" cellpadding="3" cellspacing="5">
        <tr><td style="height: 0.28in"  align="right"> <?=date_format(date_create($Date), "M d, Y")?> </td></tr>
        <tr><td style="height: 0.28in"  align="right"> <?=$cTerms?> </td></tr>
        <tr><td style="height: 0.28in"  align="right"> &nbsp; </td></tr>
        <tr><td style="height: 0.28in"  align="right"> &nbsp; </td></tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" style="height: 4.4in !important" valign="top">
    
    <table width="100%" border="0" cellpadding="3">
      <?php 
        $sqlbody = mysqli_query($con,"select a.*, b.citemdesc, c.nrate from sales_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno left join taxcode c on a.compcode=c.compcode and a.ctaxcode=c.ctaxcode where a.compcode='$company' and a.ctranno = '$csalesno'");

        if (mysqli_num_rows($sqlbody)!=0) {

          $cntr = 0;
          $totnetvat = 0;
          $totlessvat = 0;
          $totvatxmpt = 0;
          $totvatable = 0;

          while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
          $cntr = $cntr + 1;
                
      ?>
      
            <tr>
              <td style="width: 0.6in"><?php echo $rowbody['nqty'];?></td> 
              <td style="width: 0.5in"><?php echo $rowbody['cunit'];?></td> 
              <td><?php echo $rowbody['citemno'];?></td>
              <td style="text-overflow: ellipsis; width: 3in"><?php echo $rowbody['citemdesc'];?></td>
              <td style="text-overflow: ellipsis; width: 1in" align="right"><?php echo number_format($rowbody['nprice'],2);?></td>
              <td style="text-overflow: ellipsis; width: 1.25in" align="right"><?php echo number_format($rowbody['namount'],2);?></td>
                    
            </tr>
      <?php
            if((int)$rowbody['nrate']!=0){
              //echo "A";
              $totnetvat = floatval($totnetvat) + floatval($rowbody['nnetvat']);
              $totlessvat = floatval($totlessvat) + floatval($rowbody['nlessvat']);
              
              $totvatable = floatval($totvatable) + floatval($rowbody['namount']);
            }
            else{
              //echo "B";
              $totvatxmpt = floatval($totvatxmpt) + floatval($rowbody['namount']);
            }
          }
        }

          if($cvatcode=='VT' || $cvatcode=='NV'){
            $printVATGross = number_format($Gross,2);
            
              if(floatval($totvatxmpt)==0){
                //echo "A";
                $printVEGross = "";
              }else{
                //echo "AB";
                $printVEGross =  number_format($totvatxmpt,2);
              }

            $printZRGross = "";


              $totnetvat = number_format($totnetvat,2);
              $totlessvat = number_format($totlessvat,2);
              $totvatable = number_format($totvatable,2);
            
          }elseif($cvatcode=='VE'){
            $printVATGross = "";
            $printVEGross = number_format($Gross,2);
            $printZRGross = "";
            
              $totnetvat = "";
              $totlessvat = "";
              $totvatable = "";
            
          }elseif($cvatcode=='ZR'){
            $printVATGross = "";
            $printVEGross = "";
            $printZRGross = number_format($Gross,2);

              $totnetvat = "";
              $totlessvat = "";
              $totvatable = "";
            
          }

      ?>

    </table>
  </td>
  </tr>

  <tr>
    <td colspan="2" valign="top">
      <table width="100%" border="0" cellpadding="2">

        <tr>
          <td colspan="4" align="right"  valign="bottom"><!--<b>Total Sales (VAT INCLUSIVE) </b>--></td>
          <td  valign="top" align="right"><b><?=$totvatable?></b></td>
        </tr>
        <tr>
          <td colspan="2" valign="bottom">&nbsp;</td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>LESS: VAT</b>--></td>
          <td  valign="bottom" align="right"><b><?=$totlessvat?></b></td>
        </tr>
        <tr>
          <td align="right" valign="bottom" style="width: 1.24in"><!--<b><b>Vatable Sales</b>--></td>
          <td valign="bottom"><div style="text-align:right; width:50%"><b><?=$totvatable?></b></div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>Amt. Net of VAT</b>--></td>
          <td  valign="bottom" align="right"><b><?=$totnetvat?></b></td>
        </tr>
        <tr>
          <td align="right" valign="bottom"><!--<b><b>Vat-Exempt Sales</b>--></td>
          <td valign="bottom"><div style="text-align:right; width:50%"><b><?=$printVEGross?></b></div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>LESS: SC/PWD DISC.</b>--></td>
          <td  valign="bottom" align="right"><b>&nbsp;</b></td>
        </tr>
        <tr>
          <td align="right" valign="bottom"><!--<b><b>Zero-Rated Sales</b>--></td>
          <td valign="bottom"><div style="text-align:right; width:50%"><b><?=$printZRGross?></b></div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>Amt. Due</b>--></td>
          <td  valign="bottom" align="right"><b><?=$totnetvat?></b></td>
        </tr>
        <!--
        <tr>
          <td colspan="2" valign="bottom">&nbsp;</td>
          <td colspan="2" valign="bottom" align="right"><b><b>Less: Witholding Tax</b></td>
          <td  valign="bottom" align="right">&nbsp;</td>
        </tr>
        -->
        <tr>
          <td align="right" valign="bottom"><!--<b><b>Vat Amt</b>--></td>
          <td valign="bottom"><div style="text-align:right; width:50%">&nbsp;</div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>ADD VAT</b>--></td>
          <td  valign="bottom" align="right"><b><?=$totlessvat?></b></td>
        </tr>
        <tr>
          <td colspan="2" valign="bottom">&nbsp;</td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>TOTAL AMT. DUE</b>--></td>
          <td  valign="bottom" align="right"><b><?=number_format($Gross,2)?></b></td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>