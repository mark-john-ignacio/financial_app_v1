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
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin,c.cname as cdelname from dr a 
          left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join customers c on a.compcode=c.compcode and a.cdelcode=c.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$CustCode = $row['ccode'];
			$CustName = $row['cdelname'];
			$CustDelName = $row['cname'];
			$Remarks = ($row['cremarks']==null || $row['cremarks']=="null") ? "" : $row['cremarks'];
			$RemarksLow = ($row['clowremarks']==null || $row['clowremarks']=="null") ? "" : $row['clowremarks'];
			$Date = $row['dcutdate'];
			$Adds = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];
			$cTin = $row['ctin'];

			$cAPCDR = $row['crefapcdr'];
			$cAPCORD = $row['crefapcord'];

			$Sign1 = $row['csign1'];
			$Sign2 = $row['csign2'];

			$Gross = $row['ngross'];
			$cTerms = $row['cterms'];
			
			$lCancelled = $row['lcancelled'];
			$lPosted = $row['lapproved'];
			$lPrintPosted = $row['lprintposted'];
		}
	}

	//get terms desc
	$cTermsDesc = "";
	$result = mysqli_query($con, "SELECT * FROM `groupings` WHERE compcode='$company' and ctype = 'TERMS' and ccode='$cTerms'");
	if(mysqli_num_rows($result) != 0){
	  $verrow = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  $cTermsDesc = $verrow['cdesc'];
	}

	@$xSign1 = "";
	@$xNameS1 = "";
	@$xSign2 = "";
	@$xNameS2 = "";
  
	if($Sign1!=""){
	  $getempz = mysqli_query($con,"Select cdesc, IFNULL(csign,'') as csign from mrp_operators where compcode='$company' and nid=".$Sign1); 
	  if (mysqli_num_rows($getempz)!=0) {
		while($row = mysqli_fetch_array($getempz, MYSQLI_ASSOC)){
		  @$xSign1 = $row['csign'];
		  @$xNameS1 = $row['cdesc'];
		}
	  }
	}
  
	if($Sign2!=""){
	  $getempz = mysqli_query($con,"Select cdesc, IFNULL(csign,'') as csign from mrp_operators where compcode='$company' and nid=".$Sign2); 
	  if (mysqli_num_rows($getempz)!=0) {
		while($row = mysqli_fetch_array($getempz, MYSQLI_ASSOC)){
		  @$xSign2 = $row['csign'];
		  @$xNameS2 = $row['cdesc'];
		}
	  }
	}
?>

<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>

<style type="text/css">

    body{
      font-family: Arial;
    }

	.form-container{
		position: relative;
		text-align: left;
		color: #000;
		font-size: 15px;
		font-weight: bold;
		text-transform: uppercase;
		width: 8.5in;
		height: 7in;
	}

	.delTo{
		position: absolute;
		top: 145px;
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
		top: 173px;
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
		left: 660px;
		width: 135px;
		height:  15px;  
		text-align: left; 
		/*border: 1px solid #000; 
		letter-spacing: 11px;
		border: 1px solid #000;*/
	}

	.terms{
		position: absolute;
		top: 183px;
		left: 660px;
		width: 145px;
		height:  15px;  
		text-align: left; 
		overflow: hidden;
		/*border: 1px solid #000; 
		letter-spacing: 11px;
		border: 1px solid #000;*/
	}

    .refdr{
		position: absolute;
		top: 200px;
		left: 660px;
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

	.csign1{
		position: absolute;
		top: 490px;
		left: 35px;
		width: 135px;
		height:  15px;  
		text-align: left; 
		/*border: 1px solid #000; 
		letter-spacing: 11px;
		border: 1px solid #000;*/
    }  

    .csign2{
		position: absolute;
		top: 490px;
		left: 220px;
		width: 135px;
		height:  15px;  
		text-align: left; 
		/* border: 1px solid #000; 
		letter-spacing: 11px;
		border: 1px solid #000;*/
    } 

  	.RowCont{
		position: absolute;
		top: 260px !important;
		display: table;
		left: 200px; /*Optional*/
		table-layout: fixed; /*Optional*/
		height:  242px;
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

	.xremarks{
		position: absolute;
		left: 645px;
		top: 260px;
		width: 140px;
		height:  15px;  
		text-align: right; 
		/*border: 1px solid #000; 
		letter-spacing: 11px;
		border: 1px solid #000;*/
	}

	.lowremarks{
		position: absolute;
		left: 190px;
		top: 390px;
		width: 200px;
		height:  75px;  
		text-align: center; 
		/*border: 1px solid #000; 
		letter-spacing: 11px;*/
	}

</style>

</head>

<body onLoad="window.print()">

  <div class="form-container">
    <!--<img src="drform.png" width="100%">-->

    <div class="delTo"><?=$CustName?></div>
    <div class="date"><?=date_format(date_create($Date), "M d, Y")?></div>
    <div class="address"><?=$Adds?></div>
    <div class="terms"><?=$cTermsDesc?></div>
    <div class="tin"><?=$cTin?></div>
    <div class="refdr"><?=$cAPCDR?></div>

    <div class="reforder"><?=$cAPCORD?></div>

	<div class="csign1">
      <?php
        if($xSign1!=""){
          echo "<img src = '".$xSign1."?x=".time()."' width='135px' >";
        }else{
          echo "<div style='padding-top: 28px !important'>". @$xNameS1 . "</div>";
        }
      ?>
    </div>
	  <div class="csign2">
      <?php
        if($xSign2!=""){
          echo "<img src = '".$xSign2."?x=".time()."' width='135px'>";
        }else{
          echo "<div style='padding-top: 28px !important'>". @$xNameS2 . "</div>";
        }
      ?>
    </div>

    <div class="RowCont">
    <?php 
	$firstclass = "";

      $sqlbody = mysqli_query($con,"select a.*, a.cpartno as citemdesc, c.cdesc  from dr_t a left join items b on a.citemno=b.cpartno join groupings c on b.compcode=c.compcode and b.cclass=c.ccode and c.ctype='ITEMCLS' where a.compcode='$company' and a.ctranno = '$csalesno'");

      if (mysqli_num_rows($sqlbody)!=0) { 
		
		$cntr=0;
        while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
			$cntr++;
            if($cntr==1){
              $firstclass = $rowbody['cdesc'];
            }
             
	  ?>
        <div class="Row">
          <div class="Column" style="width: 250px"><?php echo $rowbody['citemdesc']." ".$rowbody['citempartname'];?></div> 
          <div class="Column" style="width: 167px"><?php echo $rowbody['citemno'];?></div>
          <div class="Column" style="width: 87px"><?php echo number_format($rowbody['nqty']);?>&nbsp;<?php echo $rowbody['cunit'];?></div>
        </div>
   <?php
        }
      }
    ?>
     </div>

	 <div class="xremarks"><?=nl2br($Remarks)?><?//=$firstclass?></div>
	 <div class="lowremarks"><?=nl2br($RemarksLow)?></div>

  </div>


</body>
</html>