<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../Connection/connection_string.php");
    require_once("../../Model/helper.php");

    $company_code = $_SESSION['companyid'];
    $yearcut = $_REQUEST['exportyear'];
    $monthcut = $_REQUEST['exportmonth'];
    $sales = [];
    $exempt = 0;
    $zerorated= 0;
    $net = 0;
    $vat = 0;
    $goods = 0;
    $service = 0;
    $capital = 0;

    $sql = "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);

    $sql = "SELECT a.*, b.* FROM paybill a
            LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
            WHERE a.compcode = '$company_code'
            AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
            AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut
            AND ctranno in (
                SELECT a.ctranno FROM paybill_t a WHERE a.compcode = '$company_code'
            )
            AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1)";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($sales, $row);
        $compute = ComputePaybills($row);

        $exempt += floatval($compute['exempt']);
        $zerorated += floatval($compute['zero']);
        $net += floatval($compute['net']);
        $vat += floatval($compute['vat']);
        $goods += floatval($compute['goods']);
        $service += floatval($compute['service']);
        $capital += floatval($compute['capital']);
    }
    
    $date = date("m/d/Y");

    if(count($sales) > 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"Sales-$date.dat\"");

        $data = "H,P,\"{$company['comptin']}\",\"{$company['compname']}\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"{$company['compadd']}\",\"{$company['compzip']}\",$exempt,$zerorated,$service,$capital,$goods,$vat,$date,12\n";

        foreach($sales as $list){
            $compute = ComputePaybills($list);
            $fullAddress = str_replace(",", "", $list['chouseno']);
            if(trim($list['ccity']) != "" && $list['ccity'] != null){
                $fullAddress .= " ". str_replace(",", "", $list['ccity']);
            }
            if(trim($list['ccountry']) != "" && $list['ccountry'] != null){
                $fullAddress .= " ". str_replace(",", "", $list['ccountry']);
            }

            $zip = str_replace(",", "", $list['cstate']);
            if(trim($list['czip']) != "" && $list['czip'] != null){
                $zip .= " ". str_replace(",", "", $list['czip']);
            }
            $getDate = date("m/d/Y", strtotime($list['dcheckdate']));
            $tin = $list['ctin'];
            $name = $list['cname'];
            $trade_name = $list['ctradename'];
            $data .= "D,P,\"$tin\",\"$name\",,,,\"$trade_name\",\"$fullAddress\",\"$zip\",{$compute['exempt']},{$compute['zero']},{$compute['service']},{$compute['capital']},{$compute['goods']},{$compute['vat']},\"{$company['comptin']}\",$getDate\n";
        }

        // Output the data
        echo trim($data);
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    exit;