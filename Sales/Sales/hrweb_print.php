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

    $TotSales = $row['ngrossbefore'];
    $TotZero = $row['nzerorated'];
    $TotVEx = $row['nexempt'];
    $TotVat = $row['nvat'];
    $TotNets = $row['nnet'];

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
      top: 125px;
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
      top: 145px;
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
      top: 160px;
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
      top: 125px;
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
      top: 145px;
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
      top: 325px !important;
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
    </div>

    <div class="TotSales"><?=$TotSales?></div>
    <div class="LessVat"><?=$TotVat?></div>
    <div class="AmtNetVat"><?=$TotNets?></div>
    <div class="LessDisc"> </div>
    <div class="AmtDue"><?=$CustName?></div>
    <div class="AdddVat"><?=$TotVat?></div>
    <div class="TotAmtDue"><?=$TotSales?></div>

    <div class="LVatSales"><?=$TotSales?></div>
    <div class="LVatExempt"><?=$TotVEx?></div>
    <div class="LZeroRated"><?=$TotZero?></div>
    <div class="LVatAmt"><?=$TotVat?></div>
  </div>
  


</body>
</html>