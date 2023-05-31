<?php
  if(!isset($_SESSION)){
    session_start();
  }

  $_SESSION['pageid'] = "Monthly_IVAT.php";

  include('../../Connection/connection_string.php');
  include('../../include/denied.php');
  include('../../include/access2.php');

  $company = $_SESSION['companyid'];
  $sql = "select * From company where compcode='$company'";
  $result=mysqli_query($con,$sql);
          
  if (!mysqli_query($con, $sql)) {
    printf("Errormessage: %s\n", mysqli_error($con));
  } 
            
  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
  {
    $compname =  $row['compname'];
  }

  $dte = explode("/",$_POST["date1"]);
  $dtemo = $dte[0];
  $dteyr = $dte[1];

  $stat = $_POST["selstat"];

  $monthNum  = intval($dtemo);
  $dateObj   = DateTime::createFromFormat('!m', $monthNum);
  $monthName = $dateObj->format('F');

  $qry = "";
  $varmsg = "";

  //all ewt codes used in APV
  @$arrewtcodes = array();
  $sql = "Select DISTINCT CASE WHEN A.cewtcode='' THEN '' Else A.cewtcode End as cewtcode, A.newtrate
  from 
    (
    Select compcode, cewtcode, newtrate
    From apv_d 
    where IFNULL(cewtcode,'') <> ''
  
    UNION ALL
  
    Select compcode, IFNULL(cewtcode,'') as cewtcode, newtrate
    From apv_t
    where IFNULL(cewtcode,'') <> ''
    
  ) A
  where A.compcode='$company' order by A.newtrate";

  $result=mysqli_query($con,$sql);
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
    @$arrewtcodes[] = array('code' => $row['cewtcode'], 'rate' => $row['newtrate']);
  }

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Monthly Input VAT and W/Tax Report</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>BIR Monthly Input VAT and W/Tax Report</h2>
<h3>For <?=$monthName;?> <?=$dteyr;?></h3><br>
<h3><?php echo $varmsg;?></h3>
</center>

<br><br>
<table border="0" align="center" cellpadding = "7px">
  <tr>
  	<th>Date</th>
    <th>Check No/Ref No.</th>
    <th>PARTICULARS</th>
    <th>TIN</th>
    <th>Address</th>
    <th>Check Amount</th>
    <th>Gross Amount</th>
    <th>Vatable Amount</th>
    <th>Input VAT</th>
    <?php
      $arrewtamt = array();
      foreach(@$arrewtcodes as $rsx){
    ?>
    <th nowrap align="center"><?=$rsx['code']."<br>".$rsx['rate']."% WTAX"?></th>
    <?php
      }
    ?>
  </tr>
  
  <?php

    $qrystat = "";
    if($stat!==""){
      $qrystat = " and A.lapproved=".$stat;
    }

    //all apv reference per check
    $arrcheckapvs = array();
    $sql = "Select CASE WHEN cpaymethod='cheque' THEN ccheckno ELSE cpayrefno End as cref, B.capvno
    From paybill A left join paybill_t B on A.compcode=B.compcode and A.ctranno=B.ctranno
    where A.compcode='$company' ". $qrystat . " and DATE_FORMAT(A.dcheckdate, '%m/%Y') = '".$_POST["date1"]."' and A.lcancelled=0";
    $resapvs=mysqli_query($con,$sql);
	
    while($row = mysqli_fetch_array($resapvs, MYSQLI_ASSOC))
    {
      $arrcheckapvs[] = array('cchkno' => $row['cref'], 'capv' => $row['capvno']);
    }


     //all apv with amt and ewt
     $arrapvdets = array();
     $sql = "Select A.ctranno, SUM(A.namount) as namount, A.cewtcode, SUM(A.nvatamt) as nvatamt,  SUM(A.nnet) as nnet, SUM(A.newtamt) as newtamt
      From apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
      where A.compcode='$company' and B.lcancelled=0 Group By A.ctranno, A.cewtcode
     
      UNION ALL

      Select A.ctranno, B.ngross as namount, group_concat(A.cewtcode SEPARATOR '') as cewtcode, SUM(A.nvatamt) as nvatamt,  (B.ngross - SUM(A.nvatamt)) as nnet, SUM(A.newtamt) as newtamt
      From (
      Select A.compcode, A.ctranno, A.cacctno, IFNULL(cewtcode,'') as cewtcode,
      CASE WHEN A.cacctno = 'LIAB04005' THEN SUM(A.ndebit) Else 0 END as nvatamt,    
      CASE WHEN A.cacctno = 'LIAB04004' THEN SUM(A.ncredit) Else 0 END as newtamt
      From apv_t A
      left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
      where A.compcode='$company' and B.captype in ('Others','PettyCash')
      Group By A.compcode, A.ctranno, A.cacctno, A.cewtcode
      ) A
      left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
      Where B.lcancelled=0 Group by A.ctranno, B.ngross";
     $resapvs=mysqli_query($con,$sql);
   
     while($row = mysqli_fetch_array($resapvs, MYSQLI_ASSOC))
     {
       $arrapvdets[] = $row;
     }

    $sql = "Select CASE WHEN cpaymethod='cheque' THEN ccheckno ELSE cpayrefno End as cref, A.dcheckdate, A.ctranno, C.ctin, C.cname, C.ctradename, IFNULL(C.chouseno,'') as chouseno, IFNULL(C.ccity,'') as ccity, IFNULL(C.cstate,'') as cstate, IFNULL(C.ccountry,'') as ccountry, A.npaid
    From paybill A 
    left join suppliers C on A.compcode = C.compcode and A.ccode=C.ccode
    where A.compcode='$company' ". $qrystat . " and DATE_FORMAT(A.dcheckdate, '%m/%Y') = '".$_POST["date1"]."' and A.lcancelled=0 Order By A.dcheckdate, CASE WHEN cpaytype='cheque' THEN ccheckno ELSE cpayrefno End";

    $result=mysqli_query($con,$sql);
    
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
      $address = $row['chouseno'];
      if($row['ccity']!==""){
        $address  = $address. ", ". $row['ccity'];
      }
      if($row['cstate']!==""){
        $address  = $address. ", ". $row['cstate'];
      }
      if($row['ccountry']!==""){
        $address  = $address. ", ". $row['ccountry'];
      }
		
  ?>  
  <tr>
    <td nowrap><?=$row['dcheckdate']?></td>
    <td nowrap><?=$row['cref']?></td>
    <td nowrap><?=$row['cname']?></td>
    <td nowrap><?=$row['ctin']?></td>
    <td nowrap><?=$address?></td>

    <?php
      //get apv refs
      @$apvref = array();
      foreach($arrcheckapvs as $rs2){
        if($rs2['cchkno'] == $row['cref']){
          @$apvref[] = $rs2['capv'];
        }
      }

     // print_r(@$apvref);
     // echo "<br>";

      //get values sa apv array
      $nvattot = 0;
      $nvatabletot = 0;
      $napvgross = 0;
      foreach(@$arrewtcodes as $rsx){
        $arrewtamt[$rsx['code']] = 0;
      }
      foreach($apvref as $rs0){

        foreach($arrapvdets as $rs3){
         // echo $rs3['ctranno']."==".$rs0."<br><br>";
          if($rs3['ctranno']==$rs0){
            $nvattot = $nvattot + $rs3['nvatamt'];
            $nvatabletot = $nvatabletot + $rs3['nnet'];
            $napvgross =  $rs3['namount'];
            if($rs3['cewtcode']!==""){
              $arrewtamt[$rs3['cewtcode']] = $arrewtamt[$rs3['cewtcode']] + $rs3['newtamt'];
            }
          }
        }
      }
    ?>
    <td align="right" nowrap><?=number_format($row['npaid'],2);?></td>
    <td align="right" nowrap><?=number_format($napvgross,2);?></td>
    <td align="right" nowrap><?=number_format($nvatabletot,2);?></td>
    <td align="right" nowrap><?=number_format($nvattot,2);?></td>
    <?php
      foreach(@$arrewtcodes as $rsx){
    ?>
      <td align="right" nowrap><?=number_format($arrewtamt[$rsx['code']],2);?></td>
    <?php
      }
    ?>
  </tr>
  <?php 
    }
  ?>

</table>

</body>
</html>