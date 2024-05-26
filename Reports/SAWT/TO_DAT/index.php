<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "BIRSAWT";

    include("../../../Connection/connection_string.php");
    include('../../../include/denied.php');
    include('../../../include/access.php');
    include('../../../Model/helper.php');

    $company = $_SESSION['companyid'];
    //$month = date("m", strtotime($_POST['months']));
    $year = $_POST['years'];
    $rdo = $_POST['rdo'];
    $companies = [];

    switch($_POST['selqrtr']){
        case 1:
            $months = "1,2,3";
            $month = 3;
            $xendingmonth = "1st";
            break;
        case 2:
            $months = "4,5,6";
            $month = 6;
            $xendingmonth = "2nd";
            break;
        case 3:
            $months = "7,8,9";
            $month = 9;
            $xendingmonth = "3rd";
            break;
        case 4:
            $months = "10,11,12";
            $month = 12;
            $xendingmonth = "4th";
            break;
        default: 
            $months = "";
            break;
    }

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
    
    $sql = "SELECT b.cewtcode, b.ctranno, b.ngrossbefore as ngross, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc 
    FROM sales b
    LEFT JOIN customers c on b.compcode = c.compcode AND b.ccode = c.cempid
    LEFT JOIN groupings d on c.compcode = d.compcode AND c.ccustomertype = d.ccode AND d.ctype = 'CUSTYP'
    WHERE b.compcode = '$company' AND MONTH(b.dcutdate) in ($months) AND YEAR(b.dcutdate) = '$year' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 and IFNULL(b.cewtcode,'') <> '' Order By b.dcutdate, b.ctranno";
    $query = mysqli_query($con, $sql);

    if(mysqli_num_rows($query) != 0){
        $xapvs = array();
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
        {
            $xapvs[] = $row;
        }

        //header("Content-type: text/plain");
        //header("Content-Disposition: attachment; filename=\"".$tinHeader.$month.$year."1702Q.dat\"");
        

        $zip = new ZipArchive();
        $zipname=$tinHeader."Q".$_POST['selqrtr'].$year."1702Q";// give zip file name 
        $zip->open($zipname, ZipArchive::CREATE); //example_zip zip file created 

        $xarr = explode(",",$months);
        foreach($xarr as $r1){
            $number = str_pad($r1, 2, '0', STR_PAD_LEFT);
            // header("Content-type: text/plain");
            //  header("Content-Disposition: attachment; filename=\"".$tinHeader.$number.$year."1601EQ.dat\"");
        
            $txtfflnme = $tinHeader.$number.$year."1702Q.dat";
            $myfile = fopen($txtfflnme, "w") or die("Unable to open file!");


            // Changing Data Heading H1601EQ
            $data = "HSAWT,H1702Q,$comptin,0000,\"$compname\",\"\",\"\",\"\",$number/$year,$rdo\n";
            $TOTAL_CREDIT = 0;
            $TOTAL_GROSS = 0;
            $count = 1;

            foreach($xapvs as $list) {
                
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
                    $data .= "DSAWT,D1702Q,$count,$tins,0000,$company_name,$lname,$fname,$midname,$number/$year,,$ewtcode,$rate,$gross,$credit\n";
                    $count += 1;

                    $TOTAL_CREDIT += $credit;
                    $TOTAL_GROSS += $gross;
                    
                }
            }
            $data .= "CSAWT,C1702Q,$comptin,0000,$number/$year,$TOTAL_GROSS,$TOTAL_CREDIT";
            //echo $data;
            fwrite($myfile, $data);
            $zip->addFile($txtfflnme); //add each file into zip file
            $data = "";
            $TOTAL_CREDIT = 0;
            $TOTAL_GROSS  = 0;
            $count = 0;
        }

        $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);

    } else {
        ?>
            <script type="text/javascript">alert("No record has been found on month of <?= $monthcut . "/" . $yearcut?>")</script>
        <?php
    }
    exit;