<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../../Connection/connection_string.php");
    require_once("../../../Model/helper.php");

    $company = $_SESSION['companyid'];
    $month = date("m", strtotime($_POST['months']));
    $year = date("Y", strtotime($_POST['years']));
    $rdo = $_POST['rdo'];
    $companies = [];

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) {
        $companies[] = $list;
    }

    $sql = "SELECT a.ncredit, a.cewtcode, a.ctranno, b.ngross, b.dapvdate, c.cname, c.chouseno, c.ccity, c.ctin FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON a.compcode = b.compcode AND b.ccode = c.ccode 
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) = '$month' AND YEAR(b.dapvdate) = '$year'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$tin."QAP".$month . $year . ".dat\"");
        
        $company_name = stringValidation($company['compname']);
        // $data = "H,S,\"$tin\",\"$company_name\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"$compaddress\",\"{$company['compzip']}\",$exempt,$zerorated,$net,$vat,$rdo,$lastDay,12\n";
        $comptin = TinValidation($companies['comptin']);
        $compname = stringValidation($companies['compname']);
        
        $data = "HQAP,H1601EQ,$comptin,0000,\"$compname\",$month/$year,$rdo\n";
        $TOTAL_CREDIT = 0;
        $TOTAL_GROSS = 0;
        while($list = $query -> fetch_assoc()) {

            $credit = $list['ncredit'];

            if(strlen($code) != 0 && $credit != 0){
                $ewt = getEWT($list['cewtcode']);
                if($ewt['valid']) {
                    $count = 1;
                    $tin = TinValidation($list['ctin']);
                    $name = stringValidation($list['cname']);
                    $ewtcode = $ewt['code'];
                    $rate = round($ewt['rate'],2);
                    $gross = round($list['ngross'],2);
                    $credit = round($list['ncredit'],2);

                    $data .= "D1,1601EQ,$count,$tin,0000,\"$name\",,,,$month/$year,$ewtcode,$rate,$gross,$credit";
                    $count += 1;

                    $TOTAL_CREDIT += $credit;
                    $TOTAL_GROSS += $gross;
                }
            }
        }
        $data .= "C1,1601EQ,$comptin,0000,$month/$year,$TOTAL_GROSS,$TOTAL_CREDIT";
        echo $data;
    } else {
        ?>
            <script type="text/javascript">alert("No record has been found on month of <?= $monthcut . "/" . $yearcut?>")</script>
        <?php
    }
    exit;