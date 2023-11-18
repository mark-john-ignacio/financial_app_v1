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

    $sql = "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);

    $tin = str_replace("-", "", $company['comptin']);
    $compaddress = str_replace(",", "", $company['compadd']);
    $lastDay = date('m/t/Y', strtotime("$yearcut-$monthcut-01"));

    $sql = "SELECT a.*,b.cname, b.ctradename, b.czip, b.chouseno, b.ccity, b.ccountry, b.cstate, b.ctin FROM sales a 
    LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
    WHERE a.compcode = '$company_code' 
    AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $monthcut 
    AND YEAR(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $yearcut  
    AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled = 0
    AND a.ctranno in (
        SELECT b.csalesno FROM receipt a 
        left join receipt_sales_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                    WHERE a.compcode = '$company_code' 
                    AND a.lapproved = 1 
                    AND b.ctaxcode <> 'NT'
                    AND a.lvoid = 0 
                    AND a.lcancelled = 0
    )";

    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($sales, $row);
        switch($row['cvatcode']){
            case "VT":
                $net += floatval($row['nnet']);
                $vat += floatval($row['nvat']);
                break;
            case "NV":
                $net += floatval($row['nnet']);
                $vat += floatval($row['nvat']);
                break;
            case "VE":
                $exempt += floatval($row['ngross']);

                break;
            case "ZR":
                $zerorated += floatval($row['ngross']);
                break;
            default: 
            break;
        }
        
    }

    if(count($sales) > 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$tin."S".$monthcut . $yearcut . ".dat\"");

        $data = "H,S,\"$tin\",\"{$company['compname']}\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"$compaddress\",\"{$company['compzip']}\",$exempt,$zerorated,$net,$vat,$lastDay,12\n";

        foreach($sales as $list){
            $compute = ComputeRST($list['ctranno']);
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

            $tinclient = str_replace(",", "", $list['ctin']);
            $data .= "D,S,\"$tinclient\",\"{$list['cname']}\",,,,\"{$list['ctradename']}\",\"$fullAddress\",\"$zip\",{$compute['exempt']},{$compute['zero']},{$compute['net']},{$compute['vat']},\"{$company['comptin']}\",$lastDay\n";
        }

        // Output the data
        echo $data;
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    exit;
    