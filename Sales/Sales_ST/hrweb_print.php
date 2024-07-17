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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin, c.cdesc as ctermdesc from sales a 
  left join customers b on a.compcode=b.compcode and a.ccode=b.cempid 
  left join groupings c on a.compcode=c.compcode and a.cterms=c.ccode 
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
    $cTermsDesc = $row['ctermdesc'];

    $cvatcode = $row['cvatcode'];

		$SalesType = $row['csalestype'];
    $PayType = $row['cpaytype'];

		$Gross = $row['ngross'];

    $TotSales = (floatval($row['ngrossbefore']) > 0) ? number_format(floatval($row['ngrossbefore']),2): "";
    $TotZero = (floatval($row['nzerorated']) > 0) ? number_format(floatval($row['nzerorated']),2): "";
    $TotVEx = (floatval($row['nexempt']) > 0) ? number_format(floatval($row['nexempt']),2): "";
    $TotVat = (floatval($row['nvat']) > 0) ? number_format(floatval($row['nvat']),2): "";
    $TotNets = (floatval($row['nnet']) > 0) ? number_format(floatval($row['nnet']),2): "";

    $xcTot = floatval($row['nnet']) + floatval($row['nzerorated']) + floatval($row['nexempt']);
    $xcTotA = $xcTot + floatval($row['nvat']);

    $xcTot = (floatval($xcTot) > 0) ? number_format(floatval($xcTot),2): "";
    $xcTotA = (floatval($xcTotA) > 0) ? number_format(floatval($xcTotA),2): "";

		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
	}
} // onLoad="window.print()"
?>

<!DOCTYPE html>
<html>
<head>

  <style type="text/css">

    body{
      font-family: Arial;
    }

    .form-container{
      position: static;
      text-align: left;
      color: #000;
      font-size: 12px;
      text-transform: uppercase;
      width: 8.5in;
      height: 10in;
    }

    .delTo{
      position: absolute;
      top: 120px;
      left: 140px;
      width: 400px;
      height:  15px;  
      text-align: left; 
      /* border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }

    .tin{
      position: absolute;
      top: 140px;
      left: 140px;
      width: 400px;
      height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }

    .address{
      position: absolute;
      top: 155px;
      left: 140px;
      width: 400px;
      height:  15px;  
      text-align: left; 
      /* border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }

    .date{
      position: absolute;
      top: 120px;
      left: 600px;
      width: 135px;
      height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }

    .terms{
      position: absolute;
      top: 140px;
      left: 600px;
      width: 145px;
      height:  15px;  
      text-align: left; 
      overflow: hidden;
      /*border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }
 

    .RowCont{
      position: absolute;
      top: 328px !important;
      display: table;
      left: 85px; /*Optional*/
      table-layout: fixed; /*Optional*/
      height:  3.6in;
      overflow: hidden;
      /*border: 1px solid #000; */
    }

    .Row{    
      display: block;
      left: 28px; /*Optional*/  
      /*border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }

    .Column{
      display: table-cell; 
      /*border: 1px solid #000;
      letter-spacing: 11px;*/
    }

    .TotSales{
      position: absolute;
      top: 678px;
      left: 650px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }

    .LessVat{
      position: absolute;
      top: 689px;
      left: 650px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }

    .AmtNetVat{
      position: absolute;
      top: 710px;
      left: 650px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }

    .LessDisc{
      position: absolute;
      top: 710px;
      left: 650px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }

    .AmtDue{
      position: absolute;
      top: 751px;
      left: 650px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000; 
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }

    .AdddVat{
      position: absolute;
      top: 761px;
      left: 650px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000;   
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    } 

    .TotAmtDue{
      position: absolute;
      top: 782px;
      left: 650px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 12px;
      /* border: 1px solid #000;   
      letter-spacing: 11px;
      border: 1px solid #000;*/
    }

    .LVatSales{
      position: absolute;
      top: 710px;
      left: 325px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000;   
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }

    .LVatExempt{
      position: absolute;
      top: 727px;
      left: 325px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000;   
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }

    .LZeroRated{
      position: absolute;
      top: 737px;
      left: 325px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000;   
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }

    .LVatAmt{
      position: absolute;
      top: 756px;
      left: 325px;
      width: 1in;
      height:  10px;  
      text-align: right; 
      font-size: 11px;
      /* border: 1px solid #000;   
      letter-spacing: 11px;
      border: 1px solid #000;*/ 
    }
  </style>

</head>

<body onLoad="window.print()">

  <div class="form-container">

    <div class="delTo"><?=$CustName?></div>
    <div class="tin"><?=$cTin?></div>
    <div class="address"><?=$Adds?></div>

    <div class="date"><?=date_format(date_create($Date), "M d, Y")?></div>   
    <div class="terms"><?=$cTermsDesc?></div>    

    <div class="RowCont">
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
      <div class="Row">
        <div class="Column" style="width: 1in"><?php echo number_format($rowbody['nqty']);?></div>
        <div class="Column" style="width: 0.7in"><?php echo $rowbody['cunit'];?></div>
        <div class="Column" style="width: 3.15in;"><?php echo $rowbody['citemdesc'];?></div>
        <div class="Column" style="width: 1.1in; text-align: right"><?php echo number_format($nnetprice,2);?></div>
        <div class="Column" style="width: 1in; text-align: right"><?php echo number_format($rowbody['namount'],2);?></div>
      </div>
      <?php
            
          }
        }

      ?>

      <div class="Row">
        <div class="Column" style="width: 1in">&nbsp;</div>
        <div class="Column" style="width: 0.7in">&nbsp;</div>
        <div class="Column" style="width: 3.15in;"><?=$Remarks;?></div>
        <div class="Column" style="width: 1.1in; text-align: right">&nbsp;</div>
        <div class="Column" style="width: 1in; text-align: right">&nbsp;</div>
      </div>

    </div>

    <div class="TotSales"><?=$xcTotA?></div>
    <div class="LessVat"><?=$TotVat?></div>
    <div class="AmtNetVat"><?=$TotNets?></div>
    <div class="LessDisc"> &nbsp; </div>
    <div class="AmtDue"><?=$xcTot?></div>
    <div class="AdddVat"><?=$TotVat?></div> 
    <div class="TotAmtDue"><?=$TotSales?></div>


    <div class="LVatSales"><?=$TotNets?></div> 
    <div class="LVatExempt"><?=$TotVEx?></div> 
    <div class="LZeroRated"><?=$TotZero?></div> 
    <div class="LVatAmt"><?=$TotVat?></div> 

  </div>
  


</body>
</html>