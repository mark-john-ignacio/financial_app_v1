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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin from sales a 
  left join customers b on a.compcode=b.compcode and a.ccode=b.cempid 
  where a.compcode='$company' and a.ctranno = '$csalesno'");

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

		$SalesType = $row['csalestype'];
    $PayType = $row['cpaytype'];
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
	}
} // onLoad="window.print()"
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssSM.css">

<head>
</head>

<body style="padding-top:.7in" onLoad="window.print()">

<table width="100%" border="0" cellpadding="1" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td colspan="2" style="padding-right: 0.25in;" align="right">&nbsp;<font size="3"><b><?//php echo $csalesno;?></b></font></td>
  </tr>

  <tr>
    <td VALIGN="TOP">

      <table width="100%" border="0" cellpadding="2" style=" margin-top: 0.18in !important">
        <tr><td style="padding-left: 1.0in;"> <?=$CustName?> </td></tr>
        <tr><td style="padding-left: 1.0in; padding-top: 5px"><?=$cTin?></td></tr>
        <tr><td style="padding-left: 1.0in; padding-top: 5px"><?=$Adds?> </td></tr>       
        </tr>
      </table>

    </td>
    <td style="width: 2.7in" VALIGN="TOP"> 
      <table width="100%" border="0" >
        <tr><td style="padding-right: 0.1in;" align="right"> <?=($PayType=="Credit") ? date_format(date_create($Date), "M d, Y") : "&nbsp;";?> </td></tr>
        <tr><td style="padding-right: 0.1in; padding-top: 5px" align="right"> <?=($PayType=="Cash") ? date_format(date_create($Date), "M d, Y") : "&nbsp;";?> </td></tr>
        <tr><td style="padding-right: 0.1in; padding-top: 10px" align="right"> <?=($PayType=="Credit") ? $cTerms : "&nbsp;";?> <??> </td></tr>
        <tr><td style="padding-right: 0.1in; padding-top: 5px" align="right"> &nbsp; </td></tr>
      </table>
    </td>
  </tr>


  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" style="height: 5.3in; padding-left: 0.25in; padding-top: 13px;" VALIGN="TOP">
    
    <table width="100%" border="0" cellpadding="3">
      <?php 
        $sqlbody = mysqli_query($con,"select a.*, b.citemdesc, c.nrate from sales_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno left join taxcode c on a.compcode=c.compcode and a.ctaxcode=c.ctaxcode where a.compcode='$company' and a.ctranno = '$csalesno'");

        if (mysqli_num_rows($sqlbody)!=0) {

          $cntr = 0;
          $totnetvat = 0;
          $totlessvat = 0;
          $totvatxmpt = 0;
          $totvatable = 0;

          $nnetprice = 0;

          while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
          $cntr = $cntr + 1;
          $nnetprice = floatval($rowbody['nprice']) - floatval($rowbody['ndiscount']);
                
      ?>
      
            <tr> 
              <td align="center"><?=$cntr;?></td>
              <td style="text-overflow: ellipsis; width: 0.10in">&nbsp;&nbsp;<?php echo $rowbody['citemno'];?></td>
              <td style="text-overflow: ellipsis; width: 9.5in"><?php echo $rowbody['citemdesc'];?></td>
              <td style="width: .7in" align="center"><?php echo number_format($rowbody['nqty']);?></td> 
              <td style="width: .7in" align="center"><?php echo $rowbody['cunit'];?></td>
              <td style="text-overflow: ellipsis; width: 1.3in" align="right"><?php echo number_format($nnetprice,2);?></td>
              <td style="padding-right: 0.3in; width: 1.3in" align="right"><?php echo number_format($rowbody['namount'],2);?></td>
                    
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
                $printVEGross = 0;
              }else{
                //echo "AB";
                $printVEGross =  $totvatxmpt;
              }

            $printZRGross = 0;


              $totnetvat = $totnetvat;
              $totlessvat = $totlessvat;
              $totvatable = $totvatable;
            
          }elseif($cvatcode=='VE'){
            $printVATGross = 0;
            $printVEGross = $Gross;
            $printZRGross = 0;
            
              $totnetvat = 0;
              $totlessvat = 0;
              $totvatable = 0;
            
          }elseif($cvatcode=='ZR'){
            $printVATGross = 0;
            $printVEGross = 0;
            $printZRGross = $Gross;

              $totnetvat = 0;
              $totlessvat = 0;
              $totvatable = 0;
            
          }

      ?>

    </table>
  </td>
  </tr>

  <?php
    if($SalesType=="Services"){
  ?>
  <tr>
    <td colspan="2" valign="top" style="padding-top: 5px !important">
      <table width="100%" border="0">

        <tr>
          <td colspan="4" align="right"  valign="bottom"><!--<b>Total Sales (VAT INCLUSIVE) </b>-->&nbsp;</td>
          <td  valign="top" align="right"><b><?//=$totvatable?>&nbsp;</b></td>
        </tr>
        <tr>
          <td colspan="2" valign="bottom">&nbsp;</td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>LESS: VAT</b>-->&nbsp;</td>
          <td  valign="bottom" align="right"><b><?//=$totlessvat?></b>&nbsp;</td>
        </tr>
        <tr>
          <td align="right" valign="bottom" style="width: 1.24in"><!--<b><b>Vatable Sales</b>-->&nbsp;</td>
          <td valign="bottom"><div style="text-align:right; width:50%"><b><?//=$totvatable?></b>&nbsp;</div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>Amt. Net of VAT</b>-->&nbsp;</td>
          <td  valign="bottom" align="right"><b><?//=$totnetvat?></b>&nbsp;</td>
        </tr>
        <tr>
          <td align="right" valign="bottom"><!--<b><b>Vat-Exempt Sales</b>-->&nbsp;</td>
          <td valign="bottom"><div style="text-align:right; width:50%"><b><?//=$printVEGross?></b>&nbsp;</div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>LESS: SC/PWD DISC.</b>-->&nbsp;</td>
          <td  valign="bottom" align="right" style="padding-right: 0.3in"><b><?//=number_format($Gross,2)?>&nbsp;</b></td>
        </tr>
        <tr>
          <td align="right" valign="bottom"><!--<b><b>Zero-Rated Sales</b>-->&nbsp;</td>
          <td valign="bottom"><div style="text-align:right; width:50%"><b><?//=$printZRGross?></b>&nbsp;</div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>Amt. Due</b>-->&nbsp;</td>
          <td  valign="bottom" align="right" style="padding-right: 0.3in"><b><?=number_format($totvatable,2)?></b></td>
        </tr>
        <tr>
          <td align="right" valign="bottom"><!--<b><b>Vat Amt</b>-->&nbsp;</td>
          <td valign="bottom"><div style="text-align:right; width:50%">&nbsp;</div></td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>ADD VAT</b>-->&nbsp;</td>
          <td  valign="bottom" align="right" style="padding-right: 0.3in"><b><?=number_format($totlessvat,2)?></b></td>
        </tr>
        <tr>
          <td colspan="2" valign="bottom">&nbsp;</td>
          <td colspan="2" valign="bottom" align="right"><!--<b><b>TOTAL AMT. DUE</b>-->&nbsp;</td>
          <td  valign="bottom" align="right" style="padding-right: 0.3in"><b><?=number_format($Gross,2)?></b></td>
        </tr>

      </table>
    </td>
  </tr>
  <?php
    }else{
?>
<tr>
    <td colspan="2" valign="top" style="padding-top: 0.43in !important">
      <table width="100%" border="0" cellpadding="1px">

        <tr>
          <td rowspan="7" valign="top" align="right" style="width: 4in; padding-top: 5px !important">

            <table width="100%" border="0" cellpadding="1px">
              <tr><td style="padding-right: 0.3in; padding-top: 3px !important" align="right"> &nbsp;<b><?=($totvatable!==0) ? number_format($totvatable,2) : ""?> </b></td></tr>
              <tr><td style="padding-right: 0.3in; padding-top: 3px !important" align="right"> &nbsp;<b><?=($printVEGross!==0) ? number_format($printVEGross,2) : ""?> </b> </td></tr>
              <tr><td style="padding-right: 0.3in; padding-top: 3px !important" align="right"> &nbsp;<b><?=($printZRGross!==0) ? number_format($printZRGross,2) : ""?> </b> </td></tr>
              <tr><td style="padding-right: 0.3in; padding-top: 3px !important" align="right"> &nbsp;<b><?=($totlessvat!==0) ? number_format($totlessvat,2) : ""?></b> </td></tr>
            </table>

          </td>
          <td  valign="bottom" align="right" style="padding-right: 0.3in; height: 0.15in"><b><?=($totvatable!==0) ? number_format($totvatable,2) : ""?>&nbsp;</b></td>
        </tr>
        <tr>
          <td  valign="bottom" align="right" style="padding-right: 0.3in"><b><?=($totlessvat!==0) ? number_format($totlessvat,2) : ""?></b>&nbsp;</td>
        </tr>
        <tr> 
          <td  valign="bottom" align="right" style="padding-right: 0.3in; padding-top: 3px !important"><b><?=($totnetvat!==0) ? number_format($totnetvat,2) : ""?></b>&nbsp;</td>
        </tr>
        <tr>

          <td  valign="bottom" align="right" style="padding-right: 0.3in"><b><?//=number_format($Gross,2)?>&nbsp;</b></td>
        </tr>
        <tr>

          <td  valign="bottom" align="right" style="padding-right: 0.3in"><b><?=($totvatable!==0) ? number_format($totvatable,2) : ""?></b></td>
        </tr>
        <tr>

          <td valign="bottom" align="right" style="padding-right: 0.3in"><b><?=($totlessvat!==0) ? number_format($totlessvat,2) : ""?></b></td>
        </tr>
        <tr>
         
          <td valign="bottom" align="right" style="padding-right: 0.3in"><b><?=number_format($Gross,2)?></b></td>
        </tr>

      </table>
    </td>
  </tr>
<?php
    }
  ?>
</table>
</body>
</html>