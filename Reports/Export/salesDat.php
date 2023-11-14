<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../Connection/connection_string.php");

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

    $sql = "SELECT a.*, b.cname, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry, b.czip 
        FROM sales a 
        LEFT JOIN customers b 
        ON a.compcode = b.compcode AND a.ccode = b.cempid
        WHERE a.compcode = '$company_code' 
        AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = '$monthcut'
        AND YEAR(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = '$yearcut'
        AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled =0";

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
                $zero += floatval($row['ngross']);
                break;
            default: 
            break;
        }
        
    }
    

    function Computation($transaction){
        global $con;
        global $company_code;

        $exempt = 0; $zero = 0;  $gross = 0; $net = 0; $less = 0; $amount = 0;
        $sql = "SELECT b.*, c.nrate FROM sales b 
                LEFT JOIN taxcode c on b.compcode=c.compcode AND b.cvatcode=c.ctaxcode
                WHERE b.compcode = '$company_code' AND b.ctranno = '$transaction' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled =0";
        $query = mysqli_query($con, $sql);
        while($row = $query -> fetch_assoc()){
            $taxcode = $row['cvatcode'];
            $gross = floatval($row['ngross']);

            if(floatval($row['nrate']) != 0 ){
                $net += floatval($row['nnet']);
                $less += floatval($row['nvat']);
                $amount += floatval($row['ngross']); 
            } else {
                $exempt += floatval($row['ngross']);
            }

        }
        switch($taxcode){
            case "VT":
                $gross = floatval($gross);
                $exempt = 0;
                $zero = 0;

                break;
            case "VE":
                $exempt = floatval($gross);
                $zero = 0;
                $net = 0;
                $less = 0;
                $amount = 0;
                break;
            case "ZR":
                $zero = floatval($gross);
                $exempt = 0;
                $net = 0;
                $less = 0;
                $amount= 0;
                break;
            default: 
            break;
        }
        

        return [
            'gross' => $gross,
            'exempt' => $exempt,
            'zero' => $zero,
            'taxable' => $net,
            'output' => $less,
            'gross_vat' => $amount
        ];

    }
    $date = date("m/d/Y");

    if(count($sales) > 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"Sales-$date.dat\"");


        $data = "H,S,\"{$company['comptin']}\",\"{$company['compname']}\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"{$company['compadd']}\",\"{$company['compzip']}\",$exempt,$zerorated,$net,$vat,$date,12\n";

        foreach($sales as $list){
            $compute = Computation($list['ctranno']);
            $fullAddress = str_replace(",", "", $list['chouseno']);
            if(trim($list['ccity']) != ""){
                $fullAddress .= " ". str_replace(",", "", $list['ccity']);
            }
            if(trim($list['ccountry']) != ""){
                $fullAddress .= " ". str_replace(",", "", $list['ccountry']);
            }

            $zip = $fullAddress = str_replace(",", "", $list['cstate']);
            if(trim($list['czip']) != ""){
                $zip .= " ". str_replace(",", "", $list['czip']);
            }
            $getDate = date("m/d/Y", strtotime($list['dcutdate']));
            $data .= "D,S,\"{$list['ctin']}\",\"{$list['cname']}\",,,,\"{$list['ctradename']}\",\"$fullAddress\",\"$zip\",{$compute['exempt']},{$compute['zero']},{$compute['taxable']},{$compute['output']},\"{$company['comptin']}\",$getDate\n";
        }

        // Output the data
        echo $data;
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    