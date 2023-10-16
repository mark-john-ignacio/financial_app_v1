<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');

    $company = $_SESSION['companyid'];
	$ctranno = explode(",", implode("','", $_REQUEST['txtctranno']));
    $dateYear = $_REQUEST['year'];

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

    $sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	$detail = array();			
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
        $detail['company'] = array(
            'name' => $row['compname'],
            'tin' => str_replace("-"," ",$row['comptin']),
            'address' => $row['compadd'],
            'zip' => $row['compzip']
        );
	}

    $sql = "select * From paybill where compcode='$company' and ctranno in ('". implode("','", $ctranno) ."')";
	$result=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$detail['paybill'] = array(
            'code' => $row['ccode'],
            'date' => $row['ddate']
        );
	}

    $sql = "select * From suppliers where compcode='$company' and ccode in ('". $detail['paybill']['code']."')";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
        $address =  $row['chouseno']; 
		if($row['ccity']!=""){
			$address .=  ", ".$row['ccity'];
		}
		if($row['cstate']!=""){
			$address .=  ", ".$row['cstate'];
		}
		if($row['ccountry']!=""){
			$address .=  ", ".$row['ccountry'];
		}
        $detail['payee'] = array(
            'name' => $row['cname'],
            'tin' => str_replace("-"," ",$row['ctin']),
            'address' => $address,
            'zip' => $row['czip']
        );
	}

    $detail['quarter'] = array(
        'first' => array('01','02','03'),
        'second' => array('04','05','06',),
        'third' => array('07','08','09'),
        'fourth' => array('10','11','12')
    );

	$year = date("Y", strtotime($dateYear));

    $detail['date'] = array(
        'from' => "0131".$dateYear,
        'to' => "1231".$dateYear
    );

	
?>

<html>
    <head>
        <style>
            .form-container{
				position: relative;
				text-align: center;
				color: #000;
				font-weight: bold;
				text-transform: uppercase;
				width: 8.5in;
				height: 13in;
		    }
            /**
            * Text with letter spacing
            */
            .payee-tin {
                position: absolute;
                font-size: 15;
                top: 185px;
                left: 270px;
                letter-spacing: 11px;
            }

            .payee-zip {
                position: absolute;
                top: 260px;
                left: 89%;
                letter-spacing: 11px;
            }

            .payor-tin{
                position: absolute;
                font-size: 15;
                top: 340px;
                left: 270px;
                letter-spacing: 11px;
            }
            
            .payor-zip {
                position: absolute;
                top: 415px;
                left: 89%;
                letter-spacing: 11px;
            }

            .date-from {
                position: absolute;
                top: 145px;
                left: 200px;
                letter-spacing: 11px;
            }

            .date-to {
                position: absolute;
                top: 145px;
                left: 533px;
                letter-spacing: 11px;
            }

            /**
            * Text without letter spacing
            */
            .payee-name {
                position: absolute;
                top: 220px;
                left: 50px;
                text-align: left;
            }

            .payee-address{
                position: absolute;
                top: 260px;
                left: 50px;
                text-align: left;
            }

            .payee-foreign-address {
                position: absolute;
                top: 295px;
                left: 50px;
                text-align: left;
            }
            
            .payee-icr {
                position: absolute;
                top: 295px;
                left: 77%;
                text-align: left;
            }

            .payor-name {
                position: absolute;
                top: 375px;
                left: 50px;
                text-align: left;
            }
            
            .payor-address {
                position: absolute;
                top: 415px;
                left: 50px;
                text-align: left;
            }
            .detewtdesc{
                position: absolute;
                font-size: 7px;
                line-height: 7px;
                left: 28px;
                width: 300px;
                text-align: justify;
            }

            .detewtcode {
                position: absolute;
                font-size: 10;
                left: 385px;
                text-align: left;
            }

            .detewtmonth {
                position: absolute;
                font-size: 10;
                left: 63%;
                text-align: left;
            }

            .detewtamt {
                position: absolute;
                font-size: 10;
                left: 85%;
                text-align: left;
            }

            .alltotal {
                position: absolute;
                font-size: 10;
                top: 643px;
                left: 63%;
                text-align: left;
            }

            .allewtamt {
                position: absolute;
                font-size: 10;
                top: 643px;
                left: 85%;
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div class="form-container" style="font-size: 0.9em;font-weight: bold;" >
            <img src="../../bir_forms/BIR_2306_Form.svg" width="100%" >

            <div class="date-from"> <?= $detail['date']['from'] ?></div>
            <div class="date-to"> <?= $detail['date']['to'] ?> </div>

            <!-- Payee Details -->
            <div class="payee-tin"> <?= $detail['payee']['tin'] ?> </div>
            <div class="payee-name"> <?= $detail['payee']['name'] ?> </div>
            <div class="payee-address"> <?= $detail['payee']['address'] ?> </div>
            <div class="payee-zip"><?= $detail['payee']['zip'] ?></div>
            <div class="payee-foreign-address"> Sample Foreign Address</div>
            <div class="payee-icr">Sample ICR</div>

            <!-- Payor Details -->
            <div class="payor-tin"> <?= $detail['company']['tin'] ?></div>
            <div class="payor-name"> <?= $detail['company']['name'] ?></div>
            <div class="payor-address"> <?= $detail['company']['address'] ?> </div>
            <div class="payor-zip"> <?= $detail['company']['zip'] ?> </div>

            <?php 
                $sqlrfp = "select YEAR(A.dapvdate) as apvdate, B.compcode, B.ctranno, GROUP_CONCAT(B.cewtcode,'') as cewtcode, sum(B.namount) as namount, sum(B.ndue) as ndue, sum(B.newtamt) as newtamt, C.cdesc as ewtdesc, C.nrate
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
                where A.compcode='$company' and A.ctranno in ('".implode("','",$ctranno)."') and YEAR(A.dapvdate) = '$dateYear'
                Group By B.compcode, B.ctranno";

                $deftop = 466;
                $result=mysqli_query($con,$sqlrfp);
                $cnt = 0;

                $totdues = 0;
                $totewts = 0;

                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
			    {
				$cnt++;
					if($cnt > 1){
						$deftop = $deftop + 20;
					}

                    echo "";
            ?>
                    <div class="detewtdesc" style="top: <?=$deftop?>px !important"><?=$row['ewtdesc']."(".$row['nrate'].")"?></div>   
					<div class="detewtcode" style="top: <?=$deftop?>px !important"><?=str_replace(",","",$row['cewtcode'])?></div>

					<div class="detewtmonth " style="top: <?=$deftop?>px !important"><?=number_format($row['namount'],2)?></div>

					<div class="detewtamt" style="top: <?=$deftop?>px !important"><?=number_format($row['newtamt'],2)?></div>
		<?php
				$totdues = $totdues + floatval($row['namount']);
				$totewts = $totewts + floatval($row['newtamt']);;
			}
		
		?>
            <div class="alltotal" ><?=number_format($totdues,2)?></div> 
	        <div class="allewtamt"><?=number_format($totewts,2)?></div>  
        </div>
    </body>
</html>