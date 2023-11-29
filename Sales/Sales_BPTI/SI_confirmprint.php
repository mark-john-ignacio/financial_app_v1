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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.nlimit, b.ctradename,b.chouseno,b.ccity,b.cstate,b.ctin from sales a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

  if (mysqli_num_rows($sqlhead)!=0) {
    while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
      $CustCode = $row['ccode'];
      $CustName = $row['cname'];
      $CustNameBuss = $row['ctradename'];
      $Remarks = $row['cremarks'];
      $Date = $row['dcutdate'];
      $cRefDR = $row['crefmoduletran'];
      $Gross = $row['ngross'];

      $Adds = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];
      $cTin = $row['ctin'];
      $cTerms = $row['cterms'];

      $nLimit = $row['nlimit'];
      
      $cvatcode = $row['cvatcode'];
      
      $lCancelled = $row['lcancelled'];
      $lPosted = $row['lapproved'];
    }
  }

  //get DR details 
  $cRefDRCust = "";
  $sqlDR = mysqli_query($con,"select a.*, b.cname from dr a left join customers b on a.compcode=b.compcode and a.cdelcode=b.cempid where a.compcode='$company' and a.ctranno = '$cRefDR'");

  if (mysqli_num_rows($sqlDR)!=0) {
    while($row = mysqli_fetch_array($sqlDR, MYSQLI_ASSOC)){
      $cRefDRCust = $row['cname'];
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

    .soldTo{ 
			position: absolute;
			top: 170px;
			left: 145px;
			width: 400px;
			height:  15px;  
      text-align: left;  
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

		.shipTo{ 
			position: absolute;
			top: 190px;
			left: 145px;
			width: 400px;
			height:  15px;  
      text-align: left;  
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .tin{
			position: absolute;
			top: 215px;
			left: 145px;
			width: 400px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .busstyle{
			position: absolute;
			top: 238px;
			left: 145px;
			width: 400px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .address{
			position: absolute;
			top: 260px;
			left: 145px;
			width: 400px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .date{
			position: absolute;
			top: 170px;
			left: 680px;
			width: 135px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .terms{
			position: absolute;
			top: 190px;
			left: 680px;
			width: 135px;
			height:  15px;  
      text-align: left; 
      /*border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}

    .refdr{
			position: absolute;
			top: 215px;
			left: 680px;
			width: 135px;
			height:  15px;  
      text-align: left; 
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		} 

    .invno{
			position: absolute;
			top: 238px;
			left: 680px;
			width: 135px;
			height:  15px;  
      text-align: left; 
     /* border: 1px solid #000; 
			letter-spacing: 11px;
			border: 1px solid #000;*/
		}   

    .RowCont{
      position: absolute;
      top: 310px !important;
      display: table;
      left: 28px; /*Optional*/
      table-layout: fixed; /*Optional*/
      /* border: 1px solid #000; */
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
      location.href = "SI_print.php?x="+x;
    }
    
  }

</script>
</head>

<body>
<div class="form-container">
    <img src="siform_blank.jpg?x=<?=time()?>" width="100%">

    <div class="soldTo"><?=$CustName?></div> 
    <div class="shipTo"><?=$cRefDRCust?></div>
    <div class="date"><?=date_format(date_create($Date), "M d, Y")?></div>
    <div class="terms"><?=$cTerms?></div>
    <div class="tin"><?=$cTin?></div>
    <div class="refdr"><?=$cRefDR?></div>
    <div class="busstyle"><?=$CustNameBuss?></div>
    <div class="invno"><?=$csalesno?></div>

    <div class="address"><?=$Adds?></div>
   

  <div class="RowCont">
    <?php 
		$sqlbody = mysqli_query($con,"select a.*, b.citemdesc, c.nrate from sales_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno left join taxcode c on a.compcode=c.compcode and a.ctaxcode=c.ctaxcode where a.compcode='$company' and a.ctranno = '$csalesno' Order By a.nident");

		if (mysqli_num_rows($sqlbody)!=0) {
		$cntr = 0;
		$totnetvat = 0;
		$totlessvat = 0;
		$totvatxmpt = 0;
		$totvatable = 0;
		
    $deftop = 275;
		while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
		  $cntr = $cntr + 1;
      $deftop = $deftop + 39;
						
	?>
    <div class="Row">
      <div class="Column" style="width: 115px">Test</div>
      <div class="Column" style="width: 119px"><?=$rowbody['citemno'];?></div>
      <div class="Column" style="width: 216px; text-align: left; padding-left: 5px"><?=$rowbody['citemdesc'];?></div>
      <div class="Column" style="width: 88px"><?=number_format($rowbody['nqty']);?> <?=$rowbody['cunit'];?></div>
      <div class="Column" style="width: 100px; text-align: right"><?=number_format($rowbody['nprice'],4);?></div>
      <div class="Column" style="width: 119px; text-align: right"><?=number_format($rowbody['namount'],2);?></div>
    </div>

      <?php 
	  
	  		if(floatval($rowbody['nrate'])!=0){
				//echo "A";
				$totnetvat = floatval($totnetvat) + floatval($rowbody['nnetvat']);
				$totlessvat = floatval($totlessvat) + floatval($rowbody['nlessvat']);
				
				$totvatable = floatval($totvatable) + floatval($rowbody['namount']);

          }
          else{
            //echo "B";
            $totvatxmpt = floatVAL($totvatxmpt) + floatval($rowbody['namount']);
          }
    }
    
		}
		
		
		if($cvatcode=='VT' || $cvatcode=='NV'){
			$printVATGross = number_format($Gross,2);
			
				if((float)$totvatxmpt==0){
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

</div>
</div>
       
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