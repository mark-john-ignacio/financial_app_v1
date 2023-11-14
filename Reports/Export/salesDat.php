<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../Connection/connection_string.php");

    $company_code = $_SESSION['companyid'];
    $yearcut = $_REQUEST['exportyear'];
    $monthcut = $_REQUEST['exportmonth'];
    $sales = [];

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
    }
    
    

    function Computation($transaction){
        global $con;
        global $company_code;

        $exempt = 0; $zero = 0;  $gross = 0; $net = 0; $less = 0; $amount = 0;
        $sql = "SELECT a.*, b.cvatcode, b.ngross, c.nrate FROM sales_t a
                LEFT JOIN sales b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                LEFT JOIN taxcode c on a.compcode=c.compcode AND a.ctaxcode=c.ctaxcode
                WHERE a.compcode = '$company_code' AND a.ctranno = '$transaction' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled =0";
        $query = mysqli_query($con, $sql);
        while($row = $query -> fetch_assoc()){
            $taxcode = $row['cvatcode'];
            $gross = floatval($row['ngross']);

            if(floatval($row['nrate']) != 0 ){
                $net += floatval($row['nnetvat']);
                $less += floatval($row['nlessvat']);
                $amount += floatval($row['namount']); 
            } else {
                $exempt += floatval($row['namount']);
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
                $gross = 0;
                $net = 0;
                $less = 0;
                break;
            case "ZR":
                $zero = floatval($gross);
                $exempt = 0;
                $gross = 0;
                $net = 0;
                $less = 0;
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
    $date = date("Y-m-d");

    if(count($sales) > 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"Sales-$date.dat\"");
        
        $data = "H,S,\"{$company['comptin']}\",\"{$company['compname']}\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"{$company['compadd']}\",\"{$company['compzip']}\"\n";

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
            $data .= "D,S,\"{$list['ctin']}\",\"{$list['cname']}\",,,,\"{$list['ctradename']}\",\"$fullAddress\",\"$zip\",{$compute['gross']},{$compute['exempt']},{$compute['zero']},{$compute['taxable']},{$compute['output']},{$list['ngross']},$getDate\n";
        }

        // Output the data
        echo $data;
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    