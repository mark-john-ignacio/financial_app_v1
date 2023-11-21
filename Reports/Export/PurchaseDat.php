<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../Connection/connection_string.php");
    require_once("../../Model/helper.php");

    $company_code = $_SESSION['companyid'];
    $yearcut = $_REQUEST['exportyear'];
    $monthcut = $_REQUEST['exportmonth'];
    $code = $_REQUEST['exportVat'];
    $rdo = $_REQUEST['exportRDO'];
    $sales = [];
    $exempt = 0;
    $zerorated= 0;
    $net = 0;
    $vat = 0;
    $goods = 0;
    $service = 0;
    $capital = 0;
    $totaltax = 0;

    $lastDay = date('m/t/Y', strtotime("$yearcut-$monthcut-01"));

    $sql = "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);

    $tin = TinValidation($company['comptin']);
    $compaddress = stringValidation($company['compadd']);

    $sql = "SELECT a.cacctno FROM accounts_default a WHERE a.compcode = '$company_code' AND a.ccode = 'PURCH_VAT' ORDER BY a.cacctno DESC LIMIT 1";
    $query = mysqli_query($con, $sql);
    $account = $query -> fetch_array(MYSQLI_ASSOC);
    $vat_code = $account['cacctno'];

    if($code == 'VT'){
        $sql = "SELECT a.*, b.* FROM paybill a
        LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
        WHERE a.compcode = '$company_code'
        AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
        AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut
        AND b.cvattype = '$code'
        AND ctranno in (
            SELECT a.ctranno FROM paybill_t a 
            LEFT JOIN apv_t b on a.compcode = b.compcode AND a.capvno = b.ctranno
            WHERE a.compcode = '$company_code' AND b.cacctno = '$vat_code'
        )
        AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1)";
    } else {
        $sql = "SELECT a.*, b.* FROM paybill a
        LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
        WHERE a.compcode = '$company_code'
        AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
        AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut
        AND b.cvattype = '$code'
        AND ctranno in (
            SELECT a.ctranno FROM paybill_t a 
            WHERE a.compcode = '$company_code' 
        )
        AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1)";
    }
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($sales, $row);
        $compute = ComputePaybills($row);

        $exempt +=      round((float)$compute['exempt'],2);
        $zerorated +=   round((float)$compute['zero'],2);
        $net +=         round((float)$compute['net'],2);
        $vat +=         round((float)$compute['vat'],2);
        $goods +=       round((float)$compute['goods'],2);
        $service +=     round((float)$compute['service'],2);
        $capital +=     round((float)$compute['capital'],2);
        $totaltax +=    round((float)$compute['gross_vat'],2);
    }

    if(count($sales) > 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$tin."P".$monthcut . $yearcut . ".dat\"");
        echo "H,P,\"$tin\",\"{$company['compname']}\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"$compaddress\",\"{$company['compzip']}\",$exempt,$zerorated,$service,$capital,$goods,$vat,$vat,0,$rdo,$lastDay,12\n";

        foreach($sales as $list){
            $compute = ComputePaybills($list);
            $fullAddress = stringValidation($list['chouseno']);
            if(trim($list['ccity']) != ""){
                $fullAddress .= " " . stringValidation($list['ccity']);
            }


            $tinclient = TinValidation($list['ctin']);
            $name = $list['cname'];
            $trade_name = $list['ctradename'];
            $EXEMPT =       round((float)$compute['exempt'],2);
            $NET =          round((float)$compute['net'],2);
            $ZERO =         round((float)$compute['zero'],2);
            $SERVICE =      round((float)$compute['service'],2);
            $CAPITAL =      round((float)$compute['capital'],2);
            $GOODS =        round((float)$compute['goods'],2);
            $VAT =          round((float)$compute['vat'],2);
            $GROSS_TAX =    round((float)$compute['gross_vat'],2);
            $data = "D,P,\"$tinclient\",\"$name\",,,,\"$trade_name\",\"$fullAddress\",$EXEMPT,$ZERO,$SERVICE,$CAPITAL,$GOODS,$VAT,$tin,$lastDay\n";
        }

        // Output the data
        echo trim($data);
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    exit;