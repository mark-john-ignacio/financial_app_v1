<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];
	
	$sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_POS'");
	if(mysqli_num_rows($sqlauto) != 0){
		while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
		{
			$autopost = $rowauto['cvalue'];
		}
	}

  $siformname = "";
  $sqlauto = mysqli_query($con,"select filename from nav_menu_prints where compcode='$company' and code='SI_PRINT'");
	if(mysqli_num_rows($sqlauto) != 0){
		while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
		{
			$siformname = $rowauto['filename'];
		}
	}

	
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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.nlimit from sales a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		//$SalesType = $row['csalestype'];
		$Gross = $row['ngross'];

    $TotVatable = $row['nnet'];
    $TotZero = $row['nzerorated'];
    $TotVEx = $row['nexempt'];
    $TotVat = $row['nvat'];
		
		$nLimit = $row['nlimit'];
		
		$cvatcode = $row['cvatcode'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
}

$result = mysqli_query($con, "SELECT * FROM `parameters` WHERE compcode='$company' and ccode = 'PRINT_VERSION_SI'");
if(mysqli_num_rows($result) != 0){
  $verrow = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $version = $verrow['cvalue'];
} else {
  $version =''; 
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">
<style type="text/css">
#tblMain {
/* the image you want to 'watermark' */
background-image: url(../../images/preview.png);
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
function Print(tranno,id,lmt){
	
	location.href = "SI_postprint.php?tranno="+tranno+"&id="+id+"&lmt="+lmt;

}

function PrintRed(x, version){
  

  if(version == 1){
    location.href = "SI_printv1.php?tranno=" +x;
  } else {
    location.href = "<?=$siformname?>?x="+x;
  }
  
}

</script>
</head>

<body>
<br><br>
<span style="border-top:1px dashed;"><?php //echo $_SESSION['employeefull'];?></span>
<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td><font size="3"><b><?=$companyname;?></b></font></td>
    <td colspan="2" align="center"><font size="3"><b>Sales Invoice</b></font></td>
  </tr>
  <tr>
    <!--<td><font size="2"><b><?php //echo $companydesc;?></b></font></td>-->
    <td><font size="2"><b><?=$companyadd;?></b></font></td>
    <td width="100">Number:</td>
    <td width="150"><?=$csalesno;?></td>
  </tr>
  <tr>
    <td><font size="2"><b>TIN #<?=$companytin;?></b></font></td>
    <td width="100">Delivery Date:</td>
    <td width="150"><?=$Date;?></td>
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
        <td height="60" valign="top" style="border:1px solid; border-style:dashed;"><font size="2"><b>CUSTOMER:</b></font><br>&nbsp;&nbsp; &nbsp; <?=$CustCode;?> - <?=$CustName;?></td>
        <td width="40%" height="60" valign="top" style="border:1px solid; border-style:dashed;"><font size="2"><b>REMARKS:</b></font><br>&nbsp;&nbsp; &nbsp; <?=$Remarks;?></td>
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
        <td style="border-right:1px dashed;"><?=$rowbody['citemno'];?></td>
        <td style="border-right:1px dashed;"><?=$rowbody['citemdesc'];?></td>
        <td style="border-right:1px dashed;" align="right"><?=$rowbody['nqty'];?> <?=$rowbody['cunit'];?></td>
        <td style="border-right:1px dashed;" align="right"><?=number_format($rowbody['nprice'],2);?></td>
        <td align="right"><?=number_format($rowbody['namount'],2);?></td>
        
      </tr>
      
      <tr>
        <td colspan="5" style="border-top:1px dashed;"><?php //echo $cvatcode.":".(float)$totvatxmpt." : ".$printVEGross;?></td>
      </tr>

        <tr>
          <?php
            $xtotvatincxzs = floatval($TotVatable) + floatval($TotVat) + floatval($TotZero) + floatval($TotVEx);
          ?>
        <td colspan="4" style="border-top:1px dashed;" align="right"  valign="bottom"><b>Total Sales (VAT INCLUSIVE) </b></td>
        <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?=number_format($xtotvatincxzs,2)?></b></td>
        </tr>
        <tr>
          <td style="border-top:1px dashed;" align="right" valign="bottom"><b>Vatable Sales</b></td>
          <td style="border-top:1px dashed;" valign="bottom"><div style="text-align:right; width:50%"><b><?=($TotVatable!=="") ? number_format((floatval($TotVatable) + floatval($TotVat)),2) : "";?></b></div></td>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>Amt. Net of VAT</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?=($TotVatable!=="") ? number_format($TotVatable,2) : "";?></b></td>
        </tr>
        <tr>
          <td style="border-top:1px dashed;" align="right" valign="bottom"><b>Vat-Exempt Sales</b></td>
          <td style="border-top:1px dashed;" valign="bottom"><div style="text-align:right; width:50%"><b><?=($TotVEx!=="") ? number_format($TotVEx,2) : "";?></b></div></td>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>LESS: VAT</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?=($TotVat!=="") ? number_format($TotVat,2) : "";?></b></td>
        </tr>
        <tr>
          <td style="border-top:1px dashed;" align="right" valign="bottom"><b>Zero-Rated Sales</b></td>
          <td style="border-top:1px dashed;" valign="bottom"><div style="text-align:right; width:50%"><b><?=($TotZero!=="") ? number_format($TotZero,2) : "";?></b></div></td>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>LESS: SC/PWD DISC.</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td style="border-top:1px dashed;" align="right" valign="bottom"><b>Vat Amt</b></td>
          <td style="border-top:1px dashed;" valign="bottom"><div style="text-align:right; width:50%">&nbsp;</div></td>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>Amt. Due</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?=($totnetvat!=="") ? $totnetvat: "";?></b></td>
        </tr>
        <tr>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom">&nbsp;</td>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>Less: Witholding Tax</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom">&nbsp;</td>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>ADD VAT</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?=($totlessvat!=="") ? $totlessvat : "";?></b></td>
        </tr>
        <tr>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom">&nbsp;</td>
          <td colspan="2" style="border-top:1px dashed;" valign="bottom" align="right"><b>TOTAL AMT. DUE</b></td>
          <td style="border-top:1px dashed;"  valign="bottom" align="right"><b><?=($Gross!=="") ? number_format($Gross,2) : "";?></b></td>
        </tr>
<?php

    }}
?>
    </table></td>
  </tr>
</table>

<div align="center" id="menu" class="noPrint">
  <div style="float:left;">&nbsp;&nbsp;<strong><font size="-1">Sales Invoice</font></strong></div>
  <div style="float:right;">

        <?php     
        $strqry = "";
        $valsub = "";

        if($lPosted==0 && $autopost==1){
          $strqry = "Print('".$csalesno."','".$CustCode."','".$nLimit."')";
          $valsub = "PRINT AND POST INVOICE";
        }
        else{
          $strqry = "PrintRed('$csalesno', '$version')";
          $valsub = "PRINT INVOICE";
        }

        //echo $lPosted."==0 && ".$autopost."==1";
        ?>

      <input type="button" value="<?=$valsub;?>" onClick="<?=$strqry;?>;" class="noPrint"/>


  </div>
</div>

</body>
</html>