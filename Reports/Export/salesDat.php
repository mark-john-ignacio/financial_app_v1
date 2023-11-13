<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../Connection/connection_string.php");

    $company_code = $_SESSION['companyid'];
    $datecut = date("m", strtotime($_REQUEST["exportrange"]));
    $sales = [];

    $sql = "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);

    $sql = "SELECT a.*, b.cname, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry, b.czip FROM sales a 
    LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
    WHERE a.compcode = '$company_code' AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $datecut";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            array_push($sales, $row);
        }
    }
    

    function Computation($transaction){
        global $con;
        global $company_code;

        $exempt = 0; $zero = 0;  $gross = 0; $net = 0; $less = 0; $amount = 0;
        $sql = "SELECT a.*, b.ngross, c.nrate FROM sales_t a
                LEFT JOIN sales b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                LEFT JOIN taxcode c on a.compcode=c.compcode AND a.ctaxcode=c.ctaxcode
                WHERE a.compcode = '$company_code' AND a.ctranno = '$transaction'";
        $query = mysqli_query($con, $sql);
        while($row = $query -> fetch_assoc()){
            $taxcode = $row['ctaxcode'];
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
                $gross += floatval($gross);
                
                break;
            case "VE":
                $gross = 0;
                $net = 0;
                $less = 0;
                break;
            case "ZR":
                $zero += floatval($gross);
                $gross = 0;
                $net = 0;
                $less = 0;
                break;
            default: 
            break;
        }
        

        return [
            'gross' => $amount,
            'exempt' => $exempt,
            'zero' => $zero,
            'taxable' => $net,
            'output' => $less,
            'gross_vat' => $gross
        ];

    }
    $date = date("Y-m-d");

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