<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../../Connection/connection_string.php");
    require_once("../../../Model/helper.php");

    $company = $_SESSION['companyid'];
    $month = date("m", strtotime($_POST['months']));
    $year = $_POST['years'];
    $rdo = $_POST['rdo'];
    $companies = [];

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) {
        $compname = stringValidation($list['compname']);
        $comptin = TinValidation($list['comptin']);
        $tinHeader = onlyNumber($list['comptin']);
    }

    // $sql = "SELECT a.cewtcode, a.newtamt, a.ctranno, b.ngross, b.dcheckdate, c.cname, c.chouseno, c.ccity, c.ctin FROM paybill_t a 
    //     LEFT JOIN paybill b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    //     LEFT JOIN suppliers c on a.compcode = c.compcode AND b.ccode = c.ccode
    //     WHERE a.compcode = '$company' AND MONTH(b.dcheckdate) = '$month' AND YEAR(b.dcheckdate) = '$year'";
    
    $sql = "SELECT a.cewtcode, a.ctranno, b.ngross, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc FROM sales_t a
        LEFT JOIN sales b on a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN customers c on a.compcode = c.compcode AND b.ccode = c.cempid
        LEFT JOIN groupings d on a.compcode = b.compcode AND c.ccustomertype = d.ccode
        WHERE a.compcode = '$company' AND MONTH(b.dcutdate) = '$month' AND YEAR(b.dcutdate) = '$year' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND d.ctype = 'CUSTYP'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"".$tinHeader.$month.$year."1702Q.dat\"");
        
        // Changing Data Heading H1601EQ
        $data = "HSAWT,H1702Q,$comptin,0000,\"$compname\",\"\",\"\",\"\",$month/$year,$rdo\n";
        $TOTAL_CREDIT = 0;
        $TOTAL_GROSS = 0;
        $count = 1;

        while($list = $query -> fetch_assoc()) {
            
            $code = $list['cewtcode'];
            $ewt = getEWT($code);

            $tins = TinValidation($list['ctin']);
            $name = stringValidation($list['cname']);
            $gross = round($list['ngross'], 2);

            if (ValidateEWT($code) && $ewt['valid']) {
                $rate = round($ewt['rate'], 2);
                $toEwtAmt = $gross * ($rate / 100);
                $credit = round($toEwtAmt, 2);
                $ewtcode = $ewt['code'];

                $company_name = "";
                $fname = "";
                $lname = "";
                $midname = "";

                switch($list['cdesc']) {
                    case "PERSON": 
                        $fullname = explode(" ", $list['cname']);
                        $fname = "\"" . $fullname[0] . "\"";
                        $lname = "\"" . $fullname[1] . "\"";
                        $midname = !empty($fullname[2]) ? "\"" . $fullname[2] . "\"" : ""; 
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

                // Changing Data D1702Q
                $data .= "DSAWT,D1702Q,$count,$tins,0000,$company_name,$lname,$fname,$midname,$month/$year,,$ewtcode,$rate,$gross,$credit\n";
                $count += 1;

                $TOTAL_CREDIT += $credit;
                $TOTAL_GROSS += $gross;
                
            }
        }
        $data .= "CSAWT,C1702Q,$comptin,0000,$month/$year,$TOTAL_GROSS,$TOTAL_CREDIT";
        echo $data;
    } else {
        ?>
            <script type="text/javascript">alert("No record has been found on month of <?= $monthcut . "/" . $yearcut?>")</script>
        <?php
    }
    exit;