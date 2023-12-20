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
    $quartersAndMonths = getQuartersAndMonths($year);

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) {
        $compname = stringValidation($list['compname']);
        $comptin = TinValidation($list['comptin']);
        $tinHeader = onlyNumber($list['comptin']);
    }


    header("Content-type: text/plain");
    header("Content-Disposition: attachment; filename=\"".$tinHeader.$month.$year."1601EQ.dat\"");
    
    $data = "HQAP,H1601EQ,$comptin,0000,\"$compname\",\"\",\"\",\"\",$month/$year,$rdo\n";
    $TOTAL_CREDIT = 0;
    $TOTAL_GROSS = 0;
    $count = 1;

    foreach($quartersAndMonths as $quarter => $months) :
        $QUARTERDATA = dataquarterly($months);
        if($QUARTERDATA['valid']) {
            foreach($QUARTERDATA['quarter'] as $row): 
                $list = $row['data'];
                $Quarter_last = $row['last_month'];

                $credit = $list['ncredit'];
                $code = $list['cewtcode'];

                $ewt = getEWT($list['cewtcode']);

                $tins = TinValidation($list['ctin']);
                $ewtcode = $ewt['code'];
                $rate = number_format($ewt['rate'],2);
                $gross = round($list['ngross'],2);
                $credit = round($list['ncredit'],2);

                if (ValidateEWT($code) && $credit != 0 && $ewt['valid']) {
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
            endforeach;
        }
    endforeach;

        
    
    $data .= "C1,1601EQ,$comptin,0000,$month/$year,$TOTAL_GROSS,$TOTAL_CREDIT";
    echo $data;

    exit;