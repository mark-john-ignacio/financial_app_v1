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

    $sql = "SELECT a.*, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry FROM apv a 
                LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
                WHERE a.compcode ='$company_code' 
                AND MONTH(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $monthcut 
                AND YEAR(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $yearcut
                AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled =0 
                -- AND c.cvatcode <> 'NT'
                AND a.ctranno in (
                        SELECT b.capvno FROM paybill a 
                        LEFT JOIN paybill_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                        LEFT JOIN suppinv c on a.compcode = c.compcode AND c.ctranno = b.capvno
                        WHERE a.compcode = '$company_code' AND c.npaidamount > 0
                )";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($sales, $row);
        switch($row['cvatcode']){
            case "VT":
                $net += floatval($row['nnet']);
                $vat += floatval($row['nvat']);
                break;
            default: 
            break;
        }

        switch($row['csalestype']){
            case "Goods":
                $goods += floatval($row['ngross']);
                break;
            case "Services":
                $service += floatval($row['ngross']);
                break;
            case "Capital":
                $capital += floatval($row['ngross']);
                break;
            default:
                break;
        }
    }
    
    $date = date("m/d/Y");

    if(count($sales) > 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"Sales-$date.dat\"");

        $data = "H,P,\"{$company['comptin']}\",\"{$company['compname']}\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"{$company['compadd']}\",\"{$company['compzip']}\",$exempt,$service,$capital,$goods,$vat,$date,12\n";

        foreach($sales as $list){
            $compute = ComputePaybills($list);
            $fullAddress = str_replace(",", "", $list['chouseno']);
            if(trim($list['ccity']) != ""){
                $fullAddress .= " ". str_replace(",", "", $list['ccity']);
            }
            if(trim($list['ccountry']) != ""){
                $fullAddress .= " ". str_replace(",", "", $list['ccountry']);
            }

            $zip = str_replace(",", "", $list['cstate']);
            if(trim($list['czip']) != ""){
                $zip .= " ". str_replace(",", "", $list['czip']);
            }
            $getDate = date("m/d/Y", strtotime($list['dcutdate']));
            $data .= "D,P,\"{$list['ctin']}\",\"{$list['cname']}\",,,,\"{$list['ctradename']}\",\"$fullAddress\",\"$zip\",{$compute['exempt']},{$compute['zero']},{$compute['service']},{$compute['capital']},{$compute['goods']},{$compute['vat']},\"{$company['comptin']}\",$getDate\n";
        }

        // Output the data
        echo $data;
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    exit;
    