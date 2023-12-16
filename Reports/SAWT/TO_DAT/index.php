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
    }

    $sql = "SELECT a.cewtcode, a.newtamt, a.ctranno, a.namount, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin FROM receipt_sales_t a
        LEFT JOIN receipt b on a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN customers c on a.compcode = c.compcode AND b.ccode = c.cempid
        WHERE a.compcode = '$company' AND MONTH(b.dcutdate) = '$month' AND YEAR(b.dcutdate) = '$year'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$comptin."SAWT".$month . $year . ".dat\"");
        
        $data = "HSAWT,H1601EQ,$comptin,0000,\"$compname\",$month/$year,$rdo\n";
        $TOTAL_CREDIT = 0;
        $TOTAL_GROSS = 0;
        while($list = $query -> fetch_assoc()) {

            $credit = $list['newtamt'];
            $code = $list['cewtcode'];

            if(strlen($code) != 0 && $credit != 0){
                $ewt = getEWT($list['cewtcode']);
                if($ewt['valid']) {
                    $count = 1;
                    $tins = TinValidation($list['ctin']);
                    $name = stringValidation($list['cname']);
                    $ewtcode = $ewt['code'];
                    $rate = round($ewt['rate'], 2);
                    $gross = round($list['namount'], 2);
                    $credit = round($credit, 2);

                    $data .= "D1,1601EQ,$count,$tins,0000,\"$name\",,,,$month/$year,$ewtcode,$rate,$gross,$credit";
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