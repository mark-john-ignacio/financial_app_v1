<?php

    if(!isset($_SESSION)){
        session_start();
    }

    $_SESSION['pageid'] = "PurchaseDat";

    require_once ("../../Connection/connection_string.php");
    include('../../include/denied.php');
	include('../../include/access2.php');
    
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

    //getallapv with input tax
    $allapvno = array();
    $apventry = array();
    $sql = "SELECT A.cmodule, A.ctranno, A.ctaxcode, B.nrate, A.ndebit, A.ncredit FROM glactivity A left join vatcode B on A.compcode=B.compcode and A.ctaxcode=B.cvatcode WHERE A.compcode = '$company_code' AND A.acctno = '$vat_code' and MONTH(A.ddate)=$monthcut and YEAR(A.ddate)=$yearcut";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $allapvno[] = $row['ctranno'];
            $apventry[$row['ctranno']] = $row;
        }
    }

    //getall apv with payment
    /*$allapvpaid = array();
    $sql = "SELECT A.ctranno, A.capvno FROM paybill_t A left join paybill B on A.compcode=B.compcode and A.ctranno=B.ctranno WHERE A.compcode = '$company_code' AND A.capvno in ('".implode("','",$allapvno)."') AND (B.lapproved = 1 AND B.lvoid = 0)";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $allapvpaid[] = $row['capvno'];
        }
    }*/

    $sql = "SELECT A.ctranno, A.ngross, A.ccode, A.chouseno, A.ccity, A.cstate, A.ccountry, A.ctin, A.cname, A.dapvdate
    From
    (
    SELECT A.ctranno, A.ngross, A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.dapvdate as dapvdate
    FROM apv A 
    left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
    WHERE A.compcode = '$company_code' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
    
    UNION ALL
    
    SELECT A.ctranno, A.ntotdebit as ngross, A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.djdate as dapvdate 
    FROM journal A 
    left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
    WHERE A.compcode = '$company_code' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
    ) A
    Order by A.dapvdate, A.cname";

    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        
        while($row = $query -> fetch_assoc()){
        
            $xcnet = 0;
            $xcvat = 0;
            $xczerotot = 0;
            $xcexmpt = 0;
            $xservc = 0;
            $xsgoods = 0;
            $xsgoodsother = 0;
            $rowgros = 0;

            if($apventry[$row['ctranno']]['nrate']>0){

                if(floatval($apventry[$row['ctranno']]['ndebit'])>0) {
                    $xcvbam = floatval($apventry[$row['ctranno']]['ndebit']);
                    $rowgros = $row['ngross'];
                }else{
                    $xcvbam = 0-floatval($apventry[$row['ctranno']]['ncredit']);
                    $rowgros = 0-$row['ngross'];
                }

                $xcnet = $xcvbam / (floatval($apventry[$row['ctranno']]['nrate'])/100);
                $xcvat = $xcvbam;

                if($apventry[$row['ctranno']]['ctaxcode']=="VTSDOM" || $apventry[$row['ctranno']]['ctaxcode'] == "VTSNR"){
                    $xservc = $xcnet;
                }

                if($apventry[$row['ctranno']]['ctaxcode']=="VTGE1M" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGNE1M"){
                    $xsgoods = $xcnet;
                }

                if($apventry[$row['ctranno']]['ctaxcode']=="VTGIMOCG" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGOCG"){
                    $xsgoodsother = $xcnet;
                }
            }

            if($apventry[$row['ctranno']]['ctaxcode']=="VTZERO"){
                $xczerotot = floatval($row['ngross']);
            }

            if($apventry[$row['ctranno']]['ctaxcode']=="VTNOTQ"){
                $xcexmpt = floatval($row['ngross']);
            }

           // $TOTAL_GROSS += floatval($row['ngross']);
            $net += floatval($xcnet);
            $vat += floatval($xcvat);
            $exempt += floatval($xcexmpt);
            $zerorated += floatval($xczerotot);
            $goods += floatval($xsgoods);
            $service += floatval($xservc);
            $capital += floatval($xsgoodsother);
            $totaltax += floatval($xcnet) + floatval($xcvat);
            
        }


        $net = round((float)$net,2);
        $vat = round((float)$vat,2);
        $exempt = round((float)$exempt,2);
        $zerorated = round((float)$zerorated,2);
        $goods = round((float)$goods,2);
        $service = round((float)$service,2);
        $capital = round((float)$capital,2);
        $totaltax = round((float)$totaltax,2);

    }

    $sql = "SELECT A.ctranno, A.ngross, A.ccode, A.chouseno, A.ccity, A.cstate, A.ccountry, A.ctin, A.cname, A.dapvdate
    From
    (
    SELECT A.ctranno, A.ngross, A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.dapvdate as dapvdate
    FROM apv A 
    left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
    WHERE A.compcode = '$company_code' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
    
    UNION ALL
    
    SELECT A.ctranno, A.ntotdebit as ngross, A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.djdate as dapvdate 
    FROM journal A 
    left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
    WHERE A.compcode = '$company_code' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
    ) A
    Order by A.dapvdate, A.cname";

    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        //Generate DAT File
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$tin."P".$monthcut . $yearcut . ".dat\"");
        $company_name = stringValidation($company['compname']);
        $data = "H,P,\"$tin\",\"{$company['compname']}\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"$compaddress\",\"{$company['compzip']}\",$exempt,$zerorated,$service,$goods,$capital,$vat,$vat,0,$rdo,$lastDay,12\n";

        while($row = $query -> fetch_assoc()){

            $fullAddress = stringValidation($row['chouseno']);
            $state = stringValidation($row['cstate']);
            if(trim($row['ccity']) != ""){
                $state .= " " . stringValidation($row['ccity']);
            }

            $xcnet = 0;
            $xcvat = 0;
            $xczerotot = 0;
            $xcexmpt = 0;
            $xservc = 0;
            $xsgoods = 0;
            $xsgoodsother = 0;
            $xcvbnm = 0;
            $rowgros = 0;

            if($apventry[$row['ctranno']]['nrate']>0){
               if(floatval($apventry[$row['ctranno']]['ndebit'])>0) {
                    $xcvbam = floatval($apventry[$row['ctranno']]['ndebit']);
                    $rowgros = $row['ngross'];
                }else{
                    $xcvbam = 0-floatval($apventry[$row['ctranno']]['ncredit']);
                    $rowgros = 0-$row['ngross'];
                }

                $xcnet = $xcvbam / (floatval($apventry[$row['ctranno']]['nrate'])/100);
                $xcvat = $xcvbam;

                if($apventry[$row['ctranno']]['ctaxcode']=="VTSDOM" || $apventry[$row['ctranno']]['ctaxcode'] == "VTSNR"){
                    $xservc = $xcnet;
                }

                if($apventry[$row['ctranno']]['ctaxcode']=="VTGE1M" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGNE1M"){
                    $xsgoods = $xcnet;
                }

                if($apventry[$row['ctranno']]['ctaxcode']=="VTGIMOCG" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGOCG"){
                    $xsgoodsother = $xcnet;
                }
            }

            if($apventry[$row['ctranno']]['ctaxcode']=="VTZERO"){
                $xczerotot = floatval($row['ngross']);
            }

            if($apventry[$row['ctranno']]['ctaxcode']=="VTNOTQ"){
                $xcexmpt = floatval($row['ngross']);
            }

            $xcvbnm += floatval($xcnet) + floatval($xcvat);

            $tinclient = TinValidation($row['ctin']);
            $name = stringValidation($row['cname']);
            $EXEMPT =       round((float)$xcexmpt,2);
            $NET =          round((float)$xcnet,2);
            $ZERO =         round((float)$xczerotot,2);
            $SERVICE =      round((float)$xservc,2);
            $CAPITAL =      round((float)$xsgoodsother,2);
            $GOODS =        round((float)$xsgoods,2);
            $VAT =          round((float)$xcvat,2);
            $GROSS_TAX =    round((float)$xcvbnm,2);
            $data .= "D,P,\"$tinclient\",\"$name\",,,,\"$fullAddress\",\"$state\",$EXEMPT,$ZERO,$SERVICE,$GOODS,$CAPITAL,$VAT,$tin,$lastDay\n";
        }

        // Output the data
        echo trim($data);
    } else {
        ?>
        <script type="text/javascript">alert("No record has been found on month of <?= $monthcut ?>/<?= $yearcut?>")</script>
        <?php
    }
    exit;