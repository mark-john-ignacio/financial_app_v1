<?php
  if(!isset($_SESSION)){
    session_start();
  }

  $_SESSION['pageid'] = "MonthlyVAT.php";

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


$refSOPO = array();

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Monthly VAT Report</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>BIR Monthly VAT Report</h2>
<h3>For <?=$monthName;?> <?=$dteyr;?></h3><br>
<h3><?php echo $varmsg;?></h3>
</center>

<br><br>
<table border="0" align="center" cellpadding = "7px">
  <tr>
  	<th>Date</th>
    <th>Trans No.</th>
    <th>SI Series</th>
    <th>DR</th>
    <th>PO</th>
    <th>TIN</th>
    <th>Address</th>
    <th>Name</th>
    <th>Store Branch</th>
    <th>Total</th>
    <th>Gross</th>
    <th>12% VAT</th>
    <th>NET</th>
  </tr>
  
<?php

//select * DR in SI
@$arrsalest = array();
$residet = mysqli_query($con,"Select creference, ctranno from sales_t Where compcode = '$company'");
while($row = mysqli_fetch_array($residet, MYSQLI_ASSOC)){
  @$arrsalest[] = $row;
}

//select * DR in SI
@$arrdrt = array();
$redrs = mysqli_query($con,"Select creference, ctranno from dr_t Where compcode = '$company'");
while($row = mysqli_fetch_array($redrs, MYSQLI_ASSOC)){
  @$arrdrt[] = $row;
}

//select * SO
@$arrsopo = array();
$residet = mysqli_query($con,"Select ctranno, cpono from so Where compcode = '$company'");
while($row = mysqli_fetch_array($residet, MYSQLI_ASSOC)){
  @$arrsopo[] = $row;
}

$qrystat = "";
if($stat!==""){
  $qrystat = " and A.lapproved=".$stat;
}

$sql = "Select a.ctranno, a.csiprintno, a.dcutdate, b.ctin, b.cname, b.ctradename, a.ngross, a.nnet, a.nvat, b.chouseno, b.ccity, b.cstate, b.ccountry
From sales a left join customers b on a.compcode = b.compcode and a.ccode=b.cempid
Where a.compcode = '$company' and DATE_FORMAT(a.dcutdate, '%m/%Y') = '".$_POST["date1"]."'
and a.lcancelled=0 ". $qrystat . " Order By a.dcutdate, a.ctranno";

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
    <td nowrap><?=$row['dcutdate']?></td>
    <td nowrap><?=$row['ctranno']?></td>
    <td nowrap><?=$row['csiprintno']?></td>
    <td nowrap>
      <?php
        //find reference DRs
        $drlist = array();

        foreach(@$arrsalest as $rts){
          if($row['ctranno']==$rts['ctranno']){
            $drlist[] = $rts['creference'];
          }
        }
        if(count($drlist)>0){
          $x = array_unique($drlist);

          echo implode(", ", $x);

            //findreferenceSO
            $refSOss = array();
            foreach(@$arrdrt as $sord){
              if(in_array($sord['ctranno'], $x)){
                $refSOss[] = $sord['creference'];
              }
            }

            $y = array_unique($refSOss);

            //findreferencePO
            foreach(@$arrsopo as $sorefs){
              if(in_array($sorefs['ctranno'], $y)){
                $refSOPO[] = $sorefs['cpono'];
              }
            }
        }else{
          echo "";
        }
       
      ?>

    </td>
    <td nowrap>
      <?php
        echo implode(", ", $refSOPO);
        $refSOPO = array();  
      ?>
    </td>
    <td nowrap><?=$row['ctin']?></td>
    <td nowrap><?=$address?></td>
    <td nowrap><?=$row['cname']?></td>
    <td nowrap><?=($row['cname']!==$row['ctradename']) ? $row['ctradename'] : ""; ?></td>
    <td align="right" nowrap><?=number_format($row['ngross'],2)?></td>
    <td align="right" nowrap><?=number_format($row['ngross'],2)?></td>
    <td align="right" nowrap><?=number_format($row['nvat'],2)?></td>
    <td align="right" nowrap><?=number_format($row['nnet'],2)?></td>
  </tr>
<?php 
  }
?>

</table>

</body>
</html>