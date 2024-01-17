<?php 
    if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "CashBook.php";

	include('../../Connection/connection_string.php');

    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['x'];


	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);

    if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
						
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
		$compadd = $row['compadd'];
		$comptin = $row['comptin'];
        $compphone = $row['cpnum'];
        $ptucode = $row['ptucode'];
        $ptudate = $row['ptudate'];
        $compemail = $row['email'];
        
	}

    $sql = "select a.*,b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, b.ctin,b.cpricever,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit 
    from ntsales a 
    left join customers b on a.compcode=b.compcode and a.ccode=b.cempid
    where a.ctranno = '$tranno' and a.compcode='$company'";

    $data = [];
    $totGrossAmt = 0;
    $result = mysqli_query($con, $sql);
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $address = $row['chouseno'] . " " . $row['ccity'] . " " . $row['cstate'] . " " . $row['ccountry'];
        // array_push($data, $row);
        $data = $row;
        $totGrossAmt = $row['ngross'];
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print V1</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">	
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
    <style>
        @media print{
            body {
                margin: 5mm 5mm 5mm 5mm;
            }
        }
        </style>
</head>
<body style="position: relative; padding-top:0" id='body' onclick="window.print();">
    <div id='header' class='container' style='width: 100%;'>
        <div class='row' style='display: flex;'>

            <div class='col-sm' style='width: 100%; text-align: justify; text-justify: inter-word;'>
                    <h3 class='nopadding'><?= $compname ?></h3>
                    <h5 class='nopadding'><?= $compadd ?></h5>
                    <!-- <h5 class='nopadding'>Tel/Fax: </h5> -->
                    <h5 class='nopadding'>Mobile No.: <?= $compphone ?></h5>
                    <!-- <h5 class='nopadding'>Manila Line: </h5> -->
                    <h5 class='nopadding'>Email: <?= $compemail ?></h5>
                    <!-- <h5 class='nopadding'>Website: www.serttech.com</h5> -->
                   <!-- <h5 class='nopadding'>VAT Reg. TIN: <?//= $comptin ?></h5>-->
            </div>
            <div class='col-sm' style='width: 100%; text-align: center;'>
                <h3>Delivery Receipt</h3>
                <h4><?=$tranno?></h4>
            </div>
        </div>
    </div>
    <div id='body' class='container' style='width: 100%; margin-top: 15px'>
        <div class='row' style="display: flex;">
            <div class='col-sm ' style='width: 100%;'>
                <h4 class='nopadding'><span style="font-weight: bold;">Sold To: </span> <?= $data['cname'] ?> </h4>
            </div>
            <div class='col-sm text-right nopadding' style='width: 75%;'>
                <h4 class='nopadding'><span style="font-weight: bold;">Delivery Date: </span> <?= date_format(date_create($data['dcutdate']),"m/d/Y")  ?> </h4>
            </div>
        </div>
        <!--<div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5 class='nopadding'><span style="font-weight: bold;">TIN: </span> <?//= $data['ctin'] ?></h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5 class='nopadding'><span style="font-weight: bold;">P.O. Terms: </span><?//= $data['ctranno'] ?> </h5>
            </div>
        </div>-->
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h4 class='nopadding'><span style="font-weight: bold;">Address: </span> <?= $address ?> </h4>
            </div>
            <!--<div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;"> Business Style: </span> <?//= $data['cname'] ?></h5>
            </div>-->
        </div>
    </div>


    <div class='container' id='item' style='width: 100%; margin-top: 15px; height: 5.5in;'>
        <div class='row' >
            <table class='table' id='salestable' >
                <thead  style=' border: .5 solid black;border-radius: 20%;'>
                    <tr>
                        <th>No.</th>
                        <th width='50%'>ITEM DESCRIPTION</th>
                        <th style="text-align: right">QTY</th>
                        <th>UNIT</th>
                        <th style="text-align: right">UNIT PRICE</th>
                        <th style="text-align: right">AMOUNT</th>
                    </tr>
                </thead>
                <tbody >
                    <?php

                        $sqlbody = mysqli_query($con,"Select * from ntdr_t_serials a where a.compcode='$company'");
                        $dataserials = array();
                        while($row = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
                            $dataserials[] = $row;
                        }

                        $sqlbody = mysqli_query($con,"select a.*, d.ngross, b.citemdesc, c.nrate from ntsales_t a 
                        left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
                        left join taxcode c on a.compcode=c.compcode and a.ctaxcode=c.ctaxcode 
                        left join sales d on a.compcode = d.compcode and a.ctranno = d.ctranno
                        where a.compcode='$company' and a.ctranno = '$tranno'");
                        $data = array();
                        $cnt = 0;
                        while($row = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
                            $cnt++;
                    ?>
                        <tr>
                            <td><?=$cnt?></td>
                            <td><?=$row['citemdesc']?>
                                <?php
                                    $serials = array();
                                    foreach($dataserials as $rcx){
                                        if($row['creference']==$rcx['ctranno'] && $row['nrefident']==$rcx['nrefidentity']){
                                            $serials[] = $rcx['cserial'];
                                        }
                                    }

                                    if(count($serials)>1){
                                        echo implode("<br>",$serials);
                                    }
                                ?>
                            </td>
                            <td style="text-align: right"><?=number_format($row['nqty'])?></td>
                            <td><?=$row['cunit']?></td>
                            <td style="text-align: right"><?=number_format($row['nprice'],2)?></td>
                            <td style="text-align: right"><?=number_format($row['namount'],2)?></td>
                        </tr>
                    <?php
                        }                
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class='container' id='item' style='width: 100%; top: 0; '>
        <div class='row' style='display: flex;'>
            <table style='width: 100%; '>
                <thead style=' border: .5 solid black;border-radius: 20%;'>
                    <tr>
                        <th width='65%'>&nbsp;</th>
                        <th width='35%' colspan='4' align='right'>
                            <div style='display: flex;'>
                                <div style='width: 50%; font-weight: 700'>TOTAL AMOUNT DUE: </div>
                                <div id='totaldue' style='width: 50%; font-weight: 700; padding-right: 10px; text-align: right'><?=number_format($totGrossAmt,2)?></div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id='footer' class='container' style='width: 100%; margin-top: 2px;'>
        <div class='row' style='display: flex;'>
           <!-- <div class='col-sm' style='width: 20%; font-size: 9px; font-weight: bold;'>
                <h5>PTU No.: <?//= $ptucode ?></h5>
                <h5>Date Issued: <?//= $ptudate ?></h5>
                <h5>Inclusive Serial No.: <?= $tranno ?></h5>
                <h5>Timestamp: <?//= date('m-d-Y') ?></h5>
            </div>-->
            <div class='col-sm' style='width: 40%; '>
                <div style='font-size: 10px; margin-left: 15px; font-weight: bold; width: 100%;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prepared By:</div>
                <div style='width: 85%; margin-left:10%; margin-top: 20%; border: 1px solid black;'></div>
                <div style='font-size: 14px; width: 100%; text-align: center;'>Signature over printed name</div>
            </div>
            <div class='col-sm' style='width: 40%; '>
                <div style='font-size: 10px; margin-left: 15px; font-weight: bold; width: 100%;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Issued By:</div>
                <div style='width: 85%; margin-left:10%; margin-top: 20%; border: 1px solid black;'></div>
                <div style='font-size: 14px; width: 100%; text-align: center;'>Signature over printed name</div>
            </div>
            <div class='col-sm' style='width: 40%; border: 1 solid black '>
                <div style='font-size: 10px; margin-left: 5px; font-weight: bold; width: 100%; text-align: center;'>Received the merchandise in good order and condition:</div>
                <div style='width: 85%; margin-left:10%; margin-top: 20%; border: 1px solid black;'></div>
                <div style='font-size: 14px; width: 100%; text-align: center;'>Signature over printed name</div>
                
            </div>
        </div>
        <div style='font-size: 12px; font-weight: bold; width: 100%; text-align: right;'>THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES</div>
    </div>
    
</body>
</html>
