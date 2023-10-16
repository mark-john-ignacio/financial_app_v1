<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php'); 

	$company = $_SESSION['companyid'];
	$ctranno = explode(",", implode("','", $_REQUEST['txtctranno']));

	$dateto = $_REQUEST['txtdateto'];
	$datefrom = $_REQUEST['txtdatefrom'];	
	
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
	$detail =array();	

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
	// $ccodesxz = "";
	// $sqlrfp = "select * From paybill where compcode='$company' and ctranno in ('".implode("','",$ctranno)."')";
	// $result=mysqli_query($con,$sqlrfp);
	// while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	// {
	// 	$detail['pays'] = array(
	// 		'code' =>$row['ccode']
	// 	);
	// 	$ccodesxz = $row['ccode'];
	//  $dpaydate = $row['ddate'];
	// }
	$month = array();;
	$sql = "select * From paybill where compcode='$company' and ctranno in ('".implode("','", $ctranno)."')";
	$result=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$detail['paybill'] = array(
            'code' => $row['ccode'],
            'date' => $row['ddate'],
			
        );
		array_push($month,  date("m", strtotime($row['ddate'])));
	}
	//PAYEE INFO
	$sql = "select * From suppliers where compcode='$company' and ccode in ('".$detail['paybill']['code']."')";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$payeeadd = $row['chouseno'];
		if($row['ccity']!=""){
			$payeeadd .= ", ".$row['ccity'];
		}
		if($row['cstate']!=""){
			$payeeadd .= ", ".$row['cstate'];
		}
		if($row['ccountry']!=""){
			$payeeadd .= ", ".$row['ccountry'];
		}

		$detail['payee'] = array(
			'name' => $row['cname'],
			'tin' => str_replace("-",".",$row['ctin']),
			'address' => $payeeadd ,
			'zip' => $row['czip']
		);
		// $payeename =  $row['cname'];
		// $payeetin =  str_replace("-",".",$row['ctin']);
		// $payeeadd =  $row['chouseno']; 
		// $payeezip =  $row['czip'];
	}

	$arrqone = array('01','02','03');
	$arrqtwo = array('04','05','06',);
	$arrqtri = array('07','08','09');
	$arrqfor = array('10','11','12');

	$dmonth = date("m", strtotime($detail['paybill']['date']));
	$dyear = date("Y", strtotime($detail['paybill']['date']));

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
			font-size: 11px;
			text-align: right;
		}

		.allewtamt{
			position: absolute;
			left: 695px;
			width: 1.2in;
			height:  16px;    
			/*border: 1px solid;*/
			font-size: 11px;
			text-align: right;
			font-weight: bold;
		} 
		
		.alltotal{
			position: absolute;
			left: 595px;
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
    <?php 
        $topAlign = 0;
        for($i = 0; $i < count($ctranno); $i++){
            ?>
                <div class="form-container" style="font-size: 0.9em;font-weight: bold; top:<?= $topAlign ?>" >
                    <img src="../../bir_forms/bir2307_page1.jpg" width="100%">
                    <div class="datefrom"><?=$date1 ?></div>
                    <div class="dateto"><?=$date2 ?></div> 

                    <div class="payeetin"><?= $detail['payee']['tin'] ?></div> 
                    <div class="payeename"><?= $detail['payee']['name'] ?></div> 
                    <div class="payeeadd"><?= $detail['payee']['address'] ?></div> 
                    <div class="payeezip"><?= $detail['payee']['zip'] ?></div>
                    
                    <div class="payortin"><?=$comptin?></div>
                    <div class="payorname"><?=$compname?></div>
                    <div class="payoradd"><?=$compadd?></div>
                    <div class="payorzip"><?=$compzip?></div>


                    <?php
                        $sqlrfp = "select B.compcode, B.ctranno, GROUP_CONCAT(B.cewtcode,'') as cewtcode, sum(B.namount) as namount, sum(B.ndue) as ndue, sum(B.newtamt) as newtamt, C.cdesc as ewtdesc, C.nrate, A.dapvdate
                        From paybill_t A 
                        left join
                            (
                                Select compcode, ctranno, cewtcode, sum(nnet) as namount, sum(ndue) as ndue, sum(newtamt) as newtamt
                                From apv_d
                                Group by compcode, ctranno, cewtcode
                                
                                UNION ALL 
                                
                                Select G.compcode, G.ctranno, G.cewtcode, sum(G.ncredit) as namount, 
                                CASE WHEN G.cacctno not in ('".implode("','",$disreg)."') THEN SUM(G.ndebit) ELSE 0 END as ndue,
                                CASE WHEN G.cacctno = '".$disregEWT."' THEN SUM(G.ncredit) ELSE 0 END as newtamt
                                From apv_t G 
                                left join apv H on G.compcode=H.compcode and G.ctranno=H.ctranno
                                left join accounts I on G.compcode=I.compcode and G.cacctno=I.cacctid
                                Where G.compcode='$company' and H.captype='Others' and G.ncredit <> 0
                                Group by G.compcode, G.ctranno, G.cewtcode
                            ) B on A.compcode=B.compcode and A.capvno=B.ctranno	
                        left join wtaxcodes C on B.compcode=C.compcode and B.cewtcode=C.ctaxcode
                        left join paybill D on C.compcode and A.ctranno and D.ctranno
                        where A.compcode='$company' and A.ctranno = '".$ctranno[$i]."' and A.dapvdate BETWEEN '$datefrom' and '$dateto'
                        Group By B.compcode, B.ctranno";

                        //echo $sqlrfp;

                        $deftop = 496;
                        $result=mysqli_query($con,$sqlrfp);
                        $cnt = 0;

                        $totdues = 0;
                        $totewts = 0;
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
                        {
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

                                    $classcode = "";
                                    if(in_array($month[$cnt - 1], $arrmonthone)){
                                        $classcode = "dlone";
                                    }elseif(in_array($month[$cnt - 1], $arrmonthtwo)){
                                        $classcode = "dltwo";
                                    }elseif(in_array($month[$cnt - 1], $arrmonthtri)){
                                        $classcode = "dltri";
                                    }
                                ?>
                                <div class="detewtmonth <?=$classcode?>" style="top: <?=$deftop?>px !important"><?=number_format($row['namount'],2)?></div>

                                <div class="detewttotal" style="top: <?=$deftop?>px !important"><?=number_format($row['namount'],2)?></div>
                                <div class="detewtamt" style="top: <?=$deftop?>px !important"><?=number_format($row['newtamt'],2)?></div>
                    <?php
                            $totdues = $totdues + floatval($row['namount']);
                            $totewts = $totewts + floatval($row['newtamt']);;
                        }
                    
                    ?>
                    <div class="alltotal" style="top: <?= $deftop+194 ?>px !important"><?=number_format($totdues,2)?></div> 
                    <div class="allewtamt" style="top: <?= $deftop+194 ?>px !important"><?=number_format($totewts,2)?></div>  

                    <div class="signimg" style="top: <?= $deftop+488 ?>px !important"><img src = "../../bir_forms/sign/<?=$signimg?>" style="width: 3in"></div>
                </div>

                

            <?php
        }
    ?>
	
</body>
</html>