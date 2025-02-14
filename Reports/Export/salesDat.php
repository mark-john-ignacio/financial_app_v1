<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "SalesDat";
    require_once("../../Connection/connection_string.php");
    include('../../include/denied.php');
	include('../../include/access2.php');
    require_once("../../Model/helper.php");

    $company_code = $_SESSION['companyid'];
    $yearcut = $_REQUEST['exportyear'];
    $monthcut = $_REQUEST['exportmonth'];
    $rdo = $_REQUEST['exportRDO'];
    $sales = [];
    $exempt = round(0,2);
    $zerorated= round(0,2);
    $net = round(0,2);
    $vat = round(0,2);

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
        $compute = ComputeRST($row);
        $exempt += round((float)$compute['exempt'],2);
        $zerorated += round((float)$compute['zero'],2);
        $net += round((float)$compute['net']);
        $vat += round((float)$compute['vat']);
    }

    if(count($sales) > 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$tin."S".$monthcut . $yearcut . ".dat\"");
        $company_name = stringValidation($company['compname']);
        $data = "H,S,\"$tin\",\"$company_name\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"$compaddress\",\"{$company['compzip']}\",$exempt,$zerorated,$net,$vat,$rdo,$lastDay,12\n";

        foreach($sales as $list){
            $compute = ComputeRST($list);
            $fullAddress = stringValidation($list['chouseno']);
            $state = stringValidation($list['cstate']);
            if(trim($list['ccity']) != ""){
                $state .= " " . stringValidation($list['ccity']);
            }

            $tinclient = TinValidation($list['ctin']);
            $client_name = stringValidation($list['cname']);
            $trade_name = stringValidation($list['ctradename']);
            $EXEMPT =   round((float)$compute['exempt'],2);
            $ZERO =     round((float)$compute['zero'],2);
            $NET =      round((float)$compute['net'],2);
            $VAT =      round((float)$compute['vat'],2);
            $data .= "D,S,\"$tinclient\",\"$client_name\",,,,\"$fullAddress\",\"$state\",$EXEMPT,$ZERO,$NET,$VAT,\"$tin\",$lastDay\n";
        }

        // Output the data
        echo $data;
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut . "/" . $yearcut?>")</script>
        <?php
    }
    exit;
    

    