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

  //APCDR
	$sqlapcdr = mysqli_query($con,"select * from dr_apc_t where compcode='$company' and ctranno = '$csalesno'");
	$rowapc = mysqli_fetch_row($sqlapcdr);
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

  body{
    font-family: Arial;
  }

  .form-container{
    position: relative;
    text-align: center;
    color: #000;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    width: 8.5in;
    height: 5.7in;
  }

  .delTo{
    position: absolute;
    top: 63px;
    left: 25px;
    width: 260px;
    height:  15px;  
    text-align: left; 
    /*border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  }

  .address{
    position: absolute;
    top: 97px;
    left: 25px;
    width: 260px;
    height:  15px;  
    text-align: left; 
    /* border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  }

  .pullrqs{
    position: absolute;
    top: 35px;
    left: 300px;
    width: 400px;
    height: 15px;  
    text-align: left; 
    /*border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  }

  .pullrmks{
    position: absolute;
    top: 63px;
    left: 290px;
    width: 162px;
    height:  55px;  
    overflow: hidden;
    font-size: 11px;
    font-weight: bold;
    text-align: left; 
    /*border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  }

  .revno{
    position: absolute;
    top: 65px;
    left: 458px;
    width: 150px;
    height:  20px;  
    text-align: center; 
    font-size: 18px;
    /*border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  }

  .delsched{
    position: absolute;
    top: 107px;
    left: 458px;
    width: 150px;
    height: 15px;  
    font-size: 18px;
    text-align: center; 
    /*border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  } 

  .dothers{
    position: absolute;
    top: 127px;
    left: 380px;
    width: 73px;
    height:  15px;  
    text-align: center; 
    /*border: 1px solid #000; 
    /*letter-spacing: 11px;
    border: 1px solid #000;*/
  }   

  .deldate{
    position: absolute;
    top: 92px;
    left: 615px;
    width: 175px;
    height: 20px;  
    font-size: 18px;
    text-align: center;  
    overflow: hidden;
    /*border: 1px solid #000;
    letter-spacing: 11px;
    border: 1px solid #000;*/
  }

  .logosmall{
    position: absolute;
    top: 64px;
    left: 615px;
    width: 175px;
    /*border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  }  

  .salesrep{
    position: absolute;
    top: 143px;
    left: 20px;
    width: 127px;
    height:  15px;  
    text-align: center; 
    /*border: 1px solid #000; 
    letter-spacing: 11px;
    border: 1px solid #000;*/
  } 

  .truckno{
    position: absolute;
    top: 143px;
    left: 155px;
    width: 127px;
    height:  15px;  
    text-align: center; 
    /*border: 1px solid #000; 
    letter-spacing: 11px; 
    border: 1px solid #000; */
  }

  .certby{
    position: absolute;
    top: 480px;
    left: 22px;
    width: 145px;
    height:  15px;  
    text-align: left; 
    overflow: hidden;
    /*border: 1px solid #000; 
    letter-spacing: 11px; 
    border: 1px solid #000;*/
  }

  .issuby{
    position: absolute;
    top: 480px;
    left: 188px;
    width: 125px;
    height:  15px;  
    text-align: left; 
    overflow: hidden;
    /*border: 1px solid #000; 
    letter-spacing: 11px; 
    border: 1px solid #000;*/
  }

  .chekby{
    position: absolute;
    top: 480px;
    left: 323px;
    width: 125px;
    height:  15px;  
    text-align: left; 
    overflow: hidden;
    /*border: 1px solid #000; 
    letter-spacing: 11px; 
    border: 1px solid #000;*/
  }

  .apprby{
    position: absolute;
    top: 480px;
    left: 465px;
    width: 125px;
    height:  15px;  
    text-align: left; 
    overflow: hidden;
    /*border: 1px solid #000; 
    letter-spacing: 11px; 
    border: 1px solid #000;*/
  } 
  
  .detremk{
    position: absolute;
    top: 196px;
    left: 465px;
    width: 320px;
    height:  242px;  
    line-height: 16px;
    text-align: right; 
    overflow: hidden;
    /*border: 1px solid #000; 
    letter-spacing: 11px; 
    border: 1px solid #000;*/
  }


  .RowCont{
    position: absolute;
    top: 197px !important;
    display: table;
    left: 20px; /*Optional*/
    table-layout: fixed; /*Optional*/
    height:  242px;
    overflow: hidden;
    /*border: 1px solid #000; */
  }

  .Row{    
    display: block;
    left: 28px; /*Optional*/  
    height:  16px;  
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

<head>
<script type="text/javascript">

function Print(tranno,id,lmt){
	
	location.href = "DR_postprint.php?tranno="+tranno+"&id="+id+"&lmt="+lmt;

}

function PrintRed(x, version){
	// 
  location.href = "DR_printAPC.php?x="+x;

}

</script>
</head>

<body>

  <div class="form-container">
    <img src="apc_dr.jpg?x=<?=time()?>" width="100%">

    <div class="delTo"><?=$companyname?></div>
    <div class="pullrqs"><?=$rowapc[2]?></div>
    <div class="address">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$companyadd?></div>
    <div class="pullrmks"><?=$rowapc[3]?></div>
    <div class="revno"><?=$rowapc[4]?></div>
    <div class="delsched"><?=$rowapc[7]?></div>
    <div class="dothers"><?=$rowapc[8]?></div>
		<div class="deldate"><?=date_format(date_create($Date), "Y-M-d")?></div>
    <div class="logosmall"><img src="../../images/logosmall.jpg?x=<?=time()?>" width="100%" height="30px"></div>

    <div class="salesrep"><?=$rowapc[5]?></div> 
    <div class="truckno"><?=$rowapc[6]?></div>

    <div class="certby"><?=$rowapc[9]?></div> 
    <div class="issuby"><?=$rowapc[10]?></div>
    <div class="chekby"><?=$rowapc[11]?></div> 
    <div class="apprby"><?=$rowapc[12]?></div>

    <div class="RowCont">

      <?php 
        $sqlbody = mysqli_query($con,"select a.*,b.citemdesc,c.cdesc from dr_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno left join groupings c on b.compcode=c.compcode and b.cclass=c.ccode and c.ctype='ITEMCLS' where a.compcode='$company' and a.ctranno = '$csalesno' order by a.nident");

        $firstclass = "";
        if (mysqli_num_rows($sqlbody)!=0) { 
          $cntr=0;
          while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
            $cntr++;
            if($cntr==1){
              $firstclass = $rowbody['cdesc'];
            }
               
      ?>
        <div class="Row">
          <div class="Column" style="width: 87px"><?=$rowbody['citemsysno'];?></div>
          <div class="Column" style="width: 93px">&nbsp;</div>
          <div class="Column" style="width: 130px; text-align: left !important"><?=$rowbody['citempono']?> </div>
          <div class="Column" style="width: 43px">&nbsp;</div>
          <div class="Column" style="width: 73px"><?=number_format($rowbody['nqty'])?> </div>
        </div>
      <?php
          }
        }
      ?>
    </div>

    <div class="detremk"><?=nl2br($Remarks)?><br><?=$firstclass?></div>
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