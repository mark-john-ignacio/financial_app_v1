<?php 
    if(!isset($_SESSION)) {
        session_start();
    }

	include("../../Connection/connection_string.php");

	$company = $_SESSION['companyid'];

    $sql = "select * From company where compcode='$company'";
    $result=mysqli_query($con,$sql);
    
    if (!mysqli_query($con, $sql)) {
        printf("Errormessage: %s\n", mysqli_error($con));
    } 
        
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $comp = $row;
    }

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BIR 1601-EQ</title>
	<style type="text/css">
		.form-container{
				font-family: monospace, monospace;
				font-size: 20px;
				position: relative;
				text-align: center;
				color: #000;
				font-weight: bold;
				text-transform: uppercase;
				width: 8.5in;
				height: 13in;
		}

		.dateyr{
			position: absolute;
			top: 142px;
			left: 21px;
			width: 80px;
			height:  20px;    
			letter-spacing: 13px;
			padding-left: 6px;
			/* border: 1px solid #000; */
		}

		.qrtr1{
			position: absolute;
			top: 142px;
			left: 105px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.qrtr2{
			position: absolute;
			top: 142px;
			left: 167px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.qrtr3{
			position: absolute;
			top: 142px;
			left: 228px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.qrtr4{
			position: absolute;
			top: 142px;
			left: 290px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.ammendY{
			position: absolute;
			top: 142px;
			left: 370px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.ammendN{
			position: absolute;
			top: 142px;
			left: 433px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.atwY{
			position: absolute;
			top: 142px;
			left: 513px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.atwN{
			position: absolute;
			top: 142px;
			left: 573px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.nsht{
			position: absolute;
			top: 142px;
			left: 687px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		} 

		.wtin1{
			position: absolute;
			top: 190px;
			left: 313px;
			width: 50px;
			height:  20px;    
			letter-spacing: 12px;
			/*border: 1px solid #000;*/
		} 

		.wtin2{
			position: absolute;
			top: 190px;
			left: 393px;
			width: 50px;
			height:  20px;    
			letter-spacing: 12px;
			/*border: 1px solid #000;*/
		} 

		.wtin3{
			position: absolute;
			top: 190px;
			left: 474px;
			width: 50px;
			height:  20px;    
			letter-spacing: 12px;
			/*border: 1px solid #000;*/
		} 

		.wtin4{
			position: absolute;
			top: 190px;
			left: 555px;
			width: 50px;
			height:  20px;    
			letter-spacing: 12px;
			/*border: 1px solid #000;*/
		} 

		.rdo{
			position: absolute;
			top: 190px;
			left: 759px;
			width: 50px;
			height:  20px;    
			letter-spacing: 12px;
			/*border: 1px solid #000;*/
		}

		.wname{
			position: absolute;
			top: 230px;
			left: 9px;
			width: 8.4in;
			height:  20px;    
			letter-spacing: 12px;
			text-align: left;
			/*border: 1px solid #000;*/
		}

		.wadd{
			position: absolute;
			top: 270px;
			left: 10px;
			width: 8.3in;
			height:  18px;    
			letter-spacing: 12px;
			text-align: left;
			/*border: 1px solid #000;*/
		}
		.wadd2{
			position: absolute;
			top: 293px;
			left: 10px;
			width: 8.4in;
			height:  20px;    
			letter-spacing: 12px;
			text-align: left;
			/*border: 1px solid #000;*/
		} 

		.wzip{
			position: absolute;
			top: 293px;
			left: 738px;
			width: 1in;
			height:  20px;    
			letter-spacing: 12px;
			text-align: left;
			/*border: 1px solid #000;*/
		} 

		.xphone{
			position: absolute;
			top: 320px;
			left: 130px;
			height:  20px;    
			letter-spacing: 12px;
			text-align: left;
			/*border: 1px solid #000;*/
		} 

		.xemail{
			position: absolute;
			top: 345px;
			left: 130px;
			height:  20px;    
			letter-spacing: 12px;
			text-align: left;
			text-transform: none !important;
			/*border: 1px solid #000;*/
		}

		.atcatsP{
			position: absolute;
			top: 317px;
			left: 611px;
			text-align: left;
			/*border: 1px solid #000;*/
		}

		.atcatsG{
			position: absolute;
			top: 317px;
			left: 713px;
			text-align: left;
			/*border: 1px solid #000;*/
		}

		.signimg{
			position: absolute;
			top: 930px;
			left: 470px;
			/*border: 1px solid #000;*/
		} 

	</style>
</head>

<body>
	<div class="form-container" style="font-size: 0.9em;font-weight: bold;" >
		<img src="../../bir_forms/bir1601eq_page1.jpg" width="100%">
		<div class="dateyr"><?=$_POST['txt1601eq_yr'] ?></div>
		<div class="qrtr<?=$_POST['txt1601eq_qrtr']?>">&#10004;</div> 
		<div class="ammend<?=$_POST['txt1601eq_amnd']?>">&#10004;</div> 
		<div class="atw<?=$_POST['txt1601eq_anytx']?>">&#10004;</div> 
		<div class="nsht"><?=$_POST['txt1601eq_nosheets']?></div> 
		
		<?php
			$tins = explode("-",$_POST['txt1601eq_tin']);
		?>
		<div class="wtin1"><?=$tins[0]?></div>
		<div class="wtin2"><?=$tins[1]?></div>
		<div class="wtin3"><?=$tins[2]?></div>
		<div class="wtin4"><?=$tins[3]?></div>
		
		<div class="rdo"><?=$_POST['txt1601eq_rdo']?></div> 
		<div class="wname"><?=str_replace(' ', '&nbsp;', substr($_POST['txt1601eq_nme'],0,40))?></div>
		<div class="wadd"><?=str_replace(' ', '&nbsp;', $_POST['txt1601eq_add'])?></div>
		<div class="wadd2"><?=str_replace(' ', '&nbsp;', substr($_POST['txt1601eq_add2'],0,31))?></div>
		<div class="wzip"><?=substr($_POST['txt1601eq_zip'],0,4)?></div>

		<div class="xphone"><?=substr($_POST['txt1601eq_signum'],0,12)?></div>
		<div class="xemail"><?=substr($_POST['txt1601eq_email'],0,34)?></div>
		<div class="atcats<?=$_POST['txt1601eq_cat']?>">&#10004;</div>
		
		<?php
			$xtop = 380;
			for($i=1; $i<=6; $i++){
				$xtop += 25;

				$gross = ($_POST['txt1601eq_gross'.$i]!="") ? explode(".",str_replace(",","",$_POST['txt1601eq_gross'.$i])) : 0; 
				$grossbase = ($_POST['txt1601eq_gross'.$i]!="") ? $gross[0] : 0; 
				$grossdec = ($_POST['txt1601eq_gross'.$i]!="") ? $gross[1] : 0; 

				$xtax = ($_POST['txt1601eq_tax'.$i]!="") ? explode(".",str_replace(",","",$_POST['txt1601eq_tax'.$i])) : 0; 
				$xtaxbase = ($_POST['txt1601eq_tax'.$i]!="") ? $xtax[0] : 0; 
				$xtaxdec = ($_POST['txt1601eq_tax'.$i]!="") ? $xtax[1] : 0;
		?>
			<div style="position: absolute; top: <?=$xtop?>px; left: 30px; text-align: left; letter-spacing: 12px;"><?=$_POST['txt1601eq_atc'.$i]?></div>

			<div style="position: absolute; top: <?=$xtop?>px; left: 171px; text-align: right; letter-spacing: 12px; width: 200px;"><?=$grossbase?></div>
			<div style="position: absolute; top: <?=$xtop?>px; left: 383px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$grossdec?></div>

			<div style="position: absolute; top: <?=$xtop?>px; left: 429px; text-align: right; width: 90px;"><?=$_POST['txt1601eq_rate'.$i]?></div>
			
			<div style="position: absolute; top: <?=$xtop?>px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$xtaxbase?></div>
			<div style="position: absolute; top: <?=$xtop?>px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$xtaxdec?></div>
		<?php
			}

			$otoewt = explode(".",str_replace(",","",$_POST['txt1601eq_totewt'])); 
			$otoewtbase = $otoewt[0]; 
			$otoewtdec = $otoewt[1];

			$less1 = explode(".",str_replace(",","",$_POST['txt1601eq_less1'])); 
			$less1base = $less1[0]; 
			$less1dec = $less1[1];

			$less2 = explode(".",str_replace(",","",$_POST['txt1601eq_less2'])); 
			$less2base = $less2[0]; 
			$less2dec = $less2[1];
		?>

		<div style="position: absolute; top: 555px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$otoewtbase?></div>
		<div style="position: absolute; top: 555px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$otoewtdec?></div>

		<div style="position: absolute; top: 580px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$less1base?></div>
		<div style="position: absolute; top: 580px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$less1dec?></div>

		<div style="position: absolute; top: 605px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$less2base?></div>
		<div style="position: absolute; top: 605px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$less2dec?></div>

		<?php
			$taxrem = explode(".",str_replace(",","",$_POST['txt1601eq_prev'])); 
			$taxrembase = $taxrem[0]; 
			$taxremdec = $taxrem[1];

			$taxovr = explode(".",str_replace(",","",$_POST['txt1601eq_overr'])); 
			$taxovrbase = $taxovr[0]; 
			$taxovrdec = $taxovr[1];

			$taxotpy = explode(".",str_replace(",","",$_POST['txt1601eq_otrpay'])); 
			$taxotpybase = $taxotpy[0]; 
			$taxotpydec = $taxotpy[1];

			$taxsum = explode(".",str_replace(",","",$_POST['txt1601eq_totrem'])); 
			$taxsumbase = $taxsum[0]; 
			$taxsumdec = $taxsum[1];

			$taxstldue = explode(".",str_replace(",","",$_POST['txt1601eq_taxdue'])); 
			$taxstlduebase = $taxstldue[0]; 
			$taxstlduedec = $taxstldue[1];
		?>

		<div style="position: absolute; top: 630px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$taxrembase?></div>
		<div style="position: absolute; top: 630px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$taxremdec?></div>

		<div style="position: absolute; top: 655px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$taxovrbase?></div>
		<div style="position: absolute; top: 655px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$taxovrdec?></div>

		<div style="position: absolute; top: 680px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$taxotpybase?></div>
		<div style="position: absolute; top: 680px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$taxotpydec?></div>

		<div style="position: absolute; top: 703px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$taxsumbase?></div>
		<div style="position: absolute; top: 703px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$taxsumdec?></div>

		<div style="position: absolute; top: 727px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$taxstlduebase?></div>
		<div style="position: absolute; top: 727px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$taxstlduedec?></div>


		<?php
			$surch = explode(".",str_replace(",","",$_POST['txt1601eq_pensur'])); 
			$surchbase = $surch[0]; 
			$surchdec = $surch[1];

			$pintr = explode(".",str_replace(",","",$_POST['txt1601eq_penint'])); 
			$pintrbase = $pintr[0]; 
			$pintrdec = $pintr[1];

			$compro = explode(".",str_replace(",","",$_POST['txt1601eq_pencom'])); 
			$comprobase = $compro[0]; 
			$comprodec = $compro[1];

			$totpens = explode(".",str_replace(",","",$_POST['txt1601eq_pentot'])); 
			$totpensbase = $totpens[0]; 
			$totpensdec = $totpens[1];

			$gtots = explode(".",str_replace(",","",$_POST['txt1601eq_gtot'])); 
			$gtotsbase = $gtots[0]; 
			$gtotsdec = $gtots[1];
		?>

		<div style="position: absolute; top: 753px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$surchbase?></div>
		<div style="position: absolute; top: 753px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$surchdec?></div>

		<div style="position: absolute; top: 778px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$pintrbase?></div>
		<div style="position: absolute; top: 778px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$pintrdec?></div>

		<div style="position: absolute; top: 802px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$comprobase?></div>
		<div style="position: absolute; top: 802px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$comprodec?></div>

		<div style="position: absolute; top: 826px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$totpensbase?></div>
		<div style="position: absolute; top: 826px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$totpensdec?></div>

		<div style="position: absolute; top: 851px; left: 530px; text-align: right; letter-spacing: 12px; width: 225px;"><?=$gtotsbase?></div>
		<div style="position: absolute; top: 851px; left: 770px; text-align: right; letter-spacing: 12px; width: 50px;"><?=$gtotsdec?></div>

		<?php
			if(isset($_POST['txt1601eq_ifovr1'])){
		?>
		<div style="position: absolute; top: 870px; left: 245px; width: 20px;">&#10004;</div>
		<?php
			}
			if(isset($_POST['txt1601eq_ifovr2'])){
		?>
		<div style="position: absolute; top: 870px; left: 353px; width: 20px;">&#10004;</div>
		<?php
			}
			if(isset($_POST['txt1601eq_ifovr3'])){
		?>
		<div style="position: absolute; top: 870px; left: 559px; width: 20px;">&#10004;</div>
		<?php
			}
		?>
	</div>


	<div class="signimg"><img src = "../<?=$comp['bir_sig_sign']?>" style="width: 3in"></div>


	<?php
		//print_r($_POST);
	?>
</body>
</html>