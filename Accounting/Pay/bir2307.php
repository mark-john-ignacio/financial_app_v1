<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];
	
	//ewt and vat accts PURCH_VAT EWTPAY
	$disreg = array();
	$disregEWT = "";
	$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$disreg[] = $row['cacctno'];
		if($row['ccode']=="EWTPAY"){
      $disregEWT = $row['cacctno'];
    }
	}


	//PAYOR INFO
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
		$comptin =  str_replace("-",".",$row['comptin']);
		$compadd =  $row['compadd']; 
		$compzip =  $row['compzip'];
		//$comptin = $row['comptin'];
	}

	//PAYEE INFO
	$ccodesxz = "";
	$dwithnorefz = 0;
	$sqlrfp = "select * From paybill where compcode='$company' and ctranno='".$_POST["id"]."'";
	$result=mysqli_query($con,$sqlrfp);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$ccodesxz = $row['ccode'];
		$dpaydate = $row['ddate'];
		$dwithnorefz = $row['lnoapvref'];
	}

	//PAYEE INFO
	$sql = "select * From suppliers where compcode='$company' and ccode='".$ccodesxz."'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$payeename =  $row['cname'];
		$payeetin =  str_replace("-",".",$row['ctin']);
		$payeeadd =  $row['chouseno']; 
		if($row['ccity']!=""){
			$payeeadd = $payeeadd . ", ".$row['ccity'];
		}
		if($row['cstate']!=""){
			$payeeadd = $payeeadd . ", ".$row['cstate'];
		}
		if($row['ccountry']!=""){
			$payeeadd = $payeeadd . ", ".$row['ccountry'];
		}
		$payeezip =  $row['czip'];
	}

	$arrqone = array('01','02','03');
	$arrqtwo = array('04','05','06',);
	$arrqtri = array('07','08','09');
	$arrqfor = array('10','11','12');

	$dmonth = date("m", strtotime($dpaydate));
	$dyear = date("Y", strtotime($dpaydate));

	if(in_array($dmonth, $arrqone)){

		$date1 = "0131".$dyear;
		$date2 = "0331".$dyear;

	}elseif(in_array($dmonth, $arrqtwo)){

		$date1 = "0401".$dyear;
		$date2 = "0630".$dyear;

	}elseif(in_array($dmonth, $arrqtri)){

		$date1 = "0701".$dyear;
		$date2 = "0930".$dyear;

	}elseif(in_array($dmonth, $arrqfor)){
		$date1 = "1001".$dyear;
		$date2 = "1231".$dyear;
	}


	//get sign
	//PAYEE INFO
	$signimg = "";
	$sqlimg = "select * From parameters where compcode='$company' and ccode='BIR2307_sign'";
	$result=mysqli_query($con,$sqlimg);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$signimg = $row['cvalue'];
	}
	
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BIR 2307</title>
	<style type="text/css">
		.form-container{
				position: relative;
				text-align: center;
				color: #000;
				font-weight: bold;
				text-transform: uppercase;
				width: 8.5in;
				height: 13in;
		}

		.datefrom{
			position: absolute;
			top: 132px;
			left: 195px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
			/*border: 1px solid #000;*/
		}

		.dateto{
			position: absolute;
			top: 132px;
			left: 543px;
			width: 105px;
			height:  25px;    
			letter-spacing: 11px;
		}

		.payeetin{
			position: absolute;
			top: 175px;
			left: 271px;
			width: 278px;
			height:  25px;    
			letter-spacing: 12px;
			
		}

		.payeename{
			position: absolute;
			top: 213px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payeeadd{
			position: absolute;
			top: 253px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payeezip{
			position: absolute;
			top: 253px;
			left: 743px;
			width: 0.70in;
			height:  20px;    
			text-align: left;
			letter-spacing: 10px;
		}

		.payortin{
			position: absolute;
			top: 337px;
			left: 271px;
			width: 278px;
			height:  25px;    
			letter-spacing: 12px;
			
		}

		.payorname{
			position: absolute;
			top: 375px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payoradd{
			position: absolute;
			top: 415px;
			left: 28px;
			width: 8in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
		}

		.payorzip{
			position: absolute;
			top: 415px;
			left: 743px;
			width: 0.70in;
			height:  20px;    
			text-align: left;
			letter-spacing: 10px;
		}

		.detewtdesc{
			position: absolute;
			/*top: 496px;*/
			left: 5px;
			width: 2.25in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
			font-size: 9px;
			line-height: 9px;
			font-weight: lighter;
		} 
		
		.detewtcode{
			position: absolute;
			/*top: 496px;*/
			left: 226px;
			width: 0.581in;
			height:  20px;    
			/*border: 1px solid;*/
			text-align: left;
			font-size: 11px;
			text-align: center;
		} 
		
		.detewtmonth{
			position: absolute;
			/*top: 496px;*/
			width: 1in;
			height:  16px;    
			/*border: 1px solid;*/
			text-align: left;
			font-size: 11px;
			text-align: right;
		} 

		.dlone{
			left: 286px;
		}

		.dltwo{
			left: 390px;
		}

		.dltri{
			left: 492px;
		}
		
		.detewttotal{
			position: absolute;
			/*top: 496px;*/
			left: 592px;
			width: 1in;
			height:  16px;    
			/*border: 1px solid;*/
			text-align: left;
			font-size: 11px;
			text-align: right;
		} 
		
		.detewtamt{
			position: absolute;
			/*top: 496px;*/
			left: 695px;
			width: 1.2in;
			height:  16px;    
			/*border: 1px solid;*/
			text-align: left;
			font-size: 11px;
			text-align: right;
		}

		.allewtamt{
			position: absolute;
			top: 696px;
			left: 702px;
			width: 1.2in;
			height:  16px;    
			/*border: 1px solid;*/
			text-align: left;
			font-size: 11px;
			text-align: right;
			font-weight: bold;
		} 
		
		.alltotal{
			position: absolute;
			top: 696px;
			left: 601px;
			width: 1in;
			height:  16px;    
			/*border: 1px solid;*/
			text-align: left;
			font-size: 11px;
			text-align: right;
			font-weight: bold;
		} 
		 
		.signimg{
			position: absolute;
			top: 990px;
			left: 255px;
			width: 3.5in;
			height:  20px;    
			/*border: 1px solid;*/
		}
	</style>
</head>

<body>
	<div class="form-container" style="font-size: 0.9em;font-weight: bold;" >
		<img src="../../bir_forms/bir2307_page1.jpg" width="100%">
		<div class="datefrom"><?=$date1 ?></div>
		<div class="dateto"><?=$date2 ?></div> 

		<div class="payeetin"><?=$payeetin?></div> 
		<div class="payeename"><?=$payeename?></div> 
		<div class="payeeadd"><?=$payeeadd?></div> 
		<div class="payeezip"><?=$payeezip?></div>
		
		<div class="payortin"><?=$comptin?></div>
		<div class="payorname"><?=$compname?></div>
		<div class="payoradd"><?=$compadd?></div>
		<div class="payorzip"><?=$compzip?></div>


		<?php
		$NOREFGAmt = 0;
		if($dwithnorefz==1){

			
			$sqlrfp = "Select SUM(A.namount) as namount from paybill_t A where A.compcode='$company' and A.ctranno='".$_POST["id"]."' and A.cacctno not in ('".implode("','",$disreg)."') and A.entrytyp = 'Debit'";
			$result=mysqli_query($con,$sqlrfp);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
			{
				$NOREFGAmt = $row['namount'];
			}

			$sqlrfp = "Select A.cewtcode, A.namount, A.newtamt, B.cdesc as ewtdesc, B.nrate from paybill_t A left join wtaxcodes B on A.compcode=B.compcode and A.cewtcode=B.ctaxcode where A.compcode='$company' and A.ctranno='".$_POST["id"]."' and A.cacctno='".$disregEWT."'";

		}else{
			$sqlrfp = "select B.compcode, B.ctranno, GROUP_CONCAT(if (B.cewtcode ='', null, B.cewtcode)) as cewtcode, sum(B.namount) as namount, sum(B.ndue) as ndue, sum(B.newtamt) as newtamt, C.cdesc as ewtdesc, C.nrate
			From 
				(					
					Select G.compcode, G.ctranno, 
					CASE WHEN G.cacctno = '".$disregEWT."' THEN G.cewtcode ELSE '' END as cewtcode, 
					CASE WHEN G.cacctno not in ('".implode("','",$disreg)."') and G.ndebit <> 0 THEN G.ndebit ELSE 0 END as namount, 
					CASE WHEN G.cacctno not in ('".implode("','",$disreg)."') and G.ndebit <> 0 THEN G.ndebit ELSE 0 END as ndue,
					CASE WHEN G.cacctno = '".$disregEWT."' THEN G.ncredit ELSE 0 END as newtamt
					From apv_t G 
					left join apv H on G.compcode=H.compcode and G.ctranno=H.ctranno
					left join accounts I on G.compcode=I.compcode and G.cacctno=I.cacctid
					Where G.compcode='$company'
				) B
			left join wtaxcodes C on B.compcode=C.compcode and B.cewtcode=C.ctaxcode
			where B.compcode='$company' and B.ctranno in (Select capvno from paybill_t where compcode='$company' and ctranno='".$_POST["id"]."')
			Group By B.compcode, B.ctranno";
		}

			//echo $sqlrfp;

			$deftop = 496;
			$result=mysqli_query($con,$sqlrfp);
			$cnt = 0;

			$totdues = 0;
			$totewts = 0;
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
			{
				if($row['cewtcode']!=""){
				$cnt++;
					if($cnt > 1){
						$deftop = $deftop + 39;
					}
		?>
					<div class="detewtdesc" style="top: <?=$deftop?>px !important"><?=$row['ewtdesc']."(".$row['nrate'].")"?></div>   
					<div class="detewtcode" style="top: <?=$deftop?>px !important"><?=str_replace(",","",$row['cewtcode'])?></div>

					<?php

						$arrmonthone = array('01','04','07','10');
						$arrmonthtwo = array('02','05','08','11');
						$arrmonthtri = array('03','06','09','12');

						$dmonth = date("m", strtotime($dpaydate));
						$classcode = "";
						if(in_array($dmonth, $arrmonthone)){
							$classcode = "dlone";
						}elseif(in_array($dmonth, $arrmonthtwo)){
							$classcode = "dltwo";
						}elseif(in_array($dmonth, $arrmonthtri)){
							$classcode = "dltri";
						}
					?>
					<div class="detewtmonth <?=$classcode?>" style="top: <?=$deftop?>px !important"><?=($dwithnorefz==1) ? number_format($NOREFGAmt,2) : number_format($row['namount'],2)?></div>

					<div class="detewttotal" style="top: <?=$deftop?>px !important"><?=($dwithnorefz==1) ? number_format($NOREFGAmt,2) : number_format($row['namount'],2)?></div>
					<div class="detewtamt" style="top: <?=$deftop?>px !important"><?=number_format($row['newtamt'],2)?></div>
		<?php
			if($dwithnorefz==1) {
				$totdues = $totdues + floatval($NOREFGAmt);
			}else{
				$totdues = $totdues + floatval($row['namount']);
			}
				
				$totewts = $totewts + floatval($row['newtamt']);;
			}
			}
		?>
	</div>

	<div class="alltotal" ><?=number_format($totdues,2)?></div> 
	<div class="allewtamt"><?=number_format($totewts,2)?></div>  

	<div class="signimg"><img src = "../../bir_forms/sign/<?=$signimg?>" style="width: 3in"></div>

</body>
</html>