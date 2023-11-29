<?php
  if(!isset($_SESSION)){
  session_start();
  }


  include('../../Connection/connection_string.php');
  include('../../include/denied.php');

    $company = $_SESSION['companyid'];

    $sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_DR'");
    if(mysqli_num_rows($sqlauto) != 0){
      while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
      {
        $autopost = $rowauto['cvalue'];
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
  
        $cAPCDR = $row['crefapcdr'];
        $cAPCORD = $row['crefapcord'];
  
        $Gross = $row['ngross'];
        $cTerms = $row['cterms'];
        
        $lCancelled = $row['lcancelled'];
        $lPosted = $row['lapproved'];
        $lPrintPosted = $row['lprintposted'];
      }
    }

  $result = mysqli_query($con, "SELECT * FROM `parameters` WHERE compcode='$company' and ccode = 'PRINT_VERSION_DR'");
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

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>

<style type="text/css">

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

  <style type="text/css">
    body{
      font-family: Arial;
      font-size: 14px;
    }

		.form-container{
				position: relative;
				text-align: center;
				color: #000;
				font-weight: bold;
				text-transform: uppercase;
				width: 8.5in;
				height: 7in;
		}

		.delTo{
			position: absolute;
			top: 158px;
			left: 125px;
			width: 400px;
			height:  15px;  
      text-align: left; 
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .address{
			position: absolute;
			top: 178px;
			left: 125px;
			width: 400px;
			height:  15px;  
      text-align: left; 
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .tin{
			position: absolute;
			top: 198px;
			left: 125px;
			width: 400px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .date{
			position: absolute;
			top: 160px;
			left: 650px;
			width: 135px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .terms{
			position: absolute;
			top: 180px;
			left: 650px;
			width: 135px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .refdr{
			position: absolute;
			top: 200px;
			left: 650px;
			width: 135px;
			height:  15px;  
      text-align: left; 
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		} 

    .reforder{
			position: absolute;
			top: 260px;
			left: 50px;
			width: 135px;
			height:  15px;  
      text-align: left; 
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}   

    .partDesc{
			position: absolute;
			left: 195px;
			width: 195px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .partNo{
			position: absolute;
			left: 396px;
			width: 155px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .partQty{
			position: absolute;
			left: 560px;
			width: 80px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		} 

		.xremarks{
			position: absolute;
			left: 645px;
			top: 260px;
			width: 140px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

  </style>

</style>
<head>
<script type="text/javascript">

function Print(tranno,id,lmt){
	
	location.href = "DR_postprint.php?tranno="+tranno+"&id="+id+"&lmt="+lmt;

}

function PrintRed(x, version){
	// 
  if(version == 1){
  location.href = "DR_printv1.php?tranno=" +x;
  } else {
    location.href = "DR_print.php?x="+x;
  }
}

</script>
</head>

<body>
<br><br>

  <div class="form-container">
    <img src="drform_blank.png?x=<?=time()?>" width="100%">

    <div class="delTo"><?=$CustName?></div>
    <div class="date"><?=date_format(date_create($Date), "M d, Y")?></div>
    <div class="address"><?=$Adds?></div>
    <div class="terms"><?=$cTerms?></div>
    <div class="tin"><?=$cTin?></div>
    <div class="refdr"><?=$cAPCDR?></div>

    <div class="reforder"><?=$cAPCORD?></div>


		<div class="xremarks"><?=$Remarks?></div>

    <?php 
      $sqlbody = mysqli_query($con,"select a.*,b.citemdesc from dr_t a left join items b on a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno'");

      if (mysqli_num_rows($sqlbody)!=0) { 
        $cntr = 0;
        $totnqty = 0;

        $deftop = 221;
        while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
          $cntr = $cntr + 1;
          $deftop = $deftop + 39;
              
	  ?>
          <div class="partDesc" style="top: <?=$deftop?>px !important"><?php echo $rowbody['citemdesc'];?></div> 
          <div class="partNo" style="top: <?=$deftop?>px !important"><?php echo $rowbody['citemno'];?></div>
          <div class="partQty" style="top: <?=$deftop?>px !important"><?php echo number_format($rowbody['nqty']);?>&nbsp;<?php echo $rowbody['cunit'];?></div>
    <?php
        }
      }
    ?>
  </div>

<div align="center" id="menu" class="noPrint">
<div style="float:left;">&nbsp;&nbsp;<strong><font size="-1">Delivery Receipt</font></strong></div>
<div style="float:right;">
<?php 
$strqry = "";
$valsub = "";

if($lPosted==0 && $autopost==1){
	$strqry = "Print('".$csalesno."','".$CustCode."','".$nLimit."')";
	$valsub = "PRINT AND POST DR";
}
else{
	$strqry = "PrintRed('$csalesno', '$version')";
	$valsub = "PRINT DR";
}


?>

<input type="button" value="<?php echo $valsub;?>" onClick="<?php echo $strqry;?>;" class="noPrint"/>


</div>
</div>

</body>
</html>