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

    $tin = TinValidation($company['comptin']);
    $compaddress = str_replace(",", "", $company['compadd']);
    $lastDay = date('m/t/Y', strtotime("$yearcut-$monthcut-01"));

    

    $sql = "SELECT a.*,b.cname, b.ctradename, b.czip, b.chouseno, b.ccity, b.ccountry, b.cstate, b.ctin, b.cvattype FROM sales a 
    LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
    WHERE a.compcode = '$company_code' 
    AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $monthcut 
    AND YEAR(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $yearcut  
    AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled = 0
    AND b.cvattype != 'NV'
    AND a.ctranno in (
        SELECT b.csalesno FROM receipt a 
        left join receipt_sales_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                    WHERE a.compcode = '$company_code' 
                    AND a.lapproved = 1 
                    AND a.lvoid = 0 
                    AND a.lcancelled = 0
    )";

    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($sales, $row);
        switch($row['cvattype']){
            case "VT":
                $net += round((float)$nnet,2);
                $vat += round((float)$nvat,2);
                break;
            case "NV":
                $net += round((float)$nnet,2);
                $vat += round((float)$nvat,2);
                break;
            case "VE":
                $exempt += round((float)$row['ngross'],2);

                break;
            case "ZR":
                $zerorated += round((float)$row['ngross'],2);
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
            $fullAddress = stringValidation($list['chouseno']);
            if(trim($list['ccity']) != ""){
                $fullAddress .= " " . stringValidation($list['ccity']);
            }
            if(trim($list['ccountry']) != ""){
                $fullAddress .= " " . stringValidation($list['ccountry']);
            }
            $FullZip = stringValidation($list['cstate']);
            
            if(trim($list['czip']) != ""){
                $FullZip = " ". stringValidation($list['czip']);
            }

            $tinclient = TinValidation($list['ctin']);
            $EXEMPT =   round((float)$compute['exempt'],2);
            $ZERO =     round((float)$compute['zero'],2);
            $NET =      round((float)$compute['net'],2);
            $VAT =      round((float)$compute['vat'],2);
            $data .= "D,S,\"$tinclient\",\"{$list['cname']}\",,,,\"{$list['ctradename']}\",\"$fullAddress\",\"$FullZip\",$EXEMPT,$ZERO,$NET,$VAT,\"$tin\",$lastDay\n";
        }

        // Output the data
        echo $data;
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    exit;
    

    