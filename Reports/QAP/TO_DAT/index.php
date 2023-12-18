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
        $compname = stringValidation($list['compname']);
        $comptin = TinValidation($list['comptin']);
        $tinHeader = onlyNumber($list['comptin']);
    }

    $sql = "SELECT a.ncredit, a.cewtcode, a.ctranno, b.ngross, b.dapvdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON a.compcode = b.compcode AND b.ccode = c.ccode 
        LEFT JOIN groupings d ON a.compcode = b.compcode AND c.csuppliertype = d.ccode
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) = '$month' AND YEAR(b.dapvdate) = '$year' AND  b.lapproved = 1, AND b.lvoid = 0 AND b.lcancelled = 0 AND d.ctype = 'SUPTYP'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$tinHeader.$month.$year."1601EQ.dat\"");
        
        $data = "HQAP,H1601EQ,$comptin,0000,\"$compname\",\"\",\"\",\"\",$month/$year,$rdo\n";
        $TOTAL_CREDIT = 0;
        $TOTAL_GROSS = 0;
        $count = 1;

        while($list = $query -> fetch_assoc()) {

            $credit = $list['ncredit'];
            $code = $list['cewtcode'];

            if(strlen($code) != 0 && $credit != 0){
                $ewt = getEWT($list['cewtcode']);
                if($ewt['valid']) {
                    $tins = TinValidation($list['ctin']);
                    $ewtcode = $ewt['code'];
                    $rate = number_format($ewt['rate'],2);
                    $gross = round($list['ngross'],2);
                    $credit = round($list['ncredit'],2);

                    $company_name = "";
                    $fname = "";
                    $lname = "";
                    $midname = "";

                    switch($list['cdesc']) {
                        case "PERSON": 
                            $fullname = explode(" ", $list['cname']);
                            $fname = "\"" . $fullname[0] . "\"";
                            $lname = "\"" . $fullname[1] . "\"";
                            $midname = !empty($fullname[2])? "\"" . $fullname[2] . "\"" : ""; 
                            break;
                        case "COMPANY": 
                            $company_name = "\"" . stringValidation($list['cname']) . "\"";
                            break;
                        case "SCHOOL":
                            $company_name = "\"" . stringValidation($list['cname']) . "\"";
                            break;
                        case "OTHERS":
                            $company_name = "\"" . stringValidation($list['cname']) . "\"";
                            break;
                    }

                    $data .= "D1,1601EQ,$count,$tins,0000,$company_name,$lname,$fname,$midname,$month/$year,$ewtcode,$rate,$gross,$credit\n";
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