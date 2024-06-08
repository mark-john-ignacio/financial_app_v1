<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "BIRQAP";
    include("../../../Connection/connection_string.php");
    include('../../../include/denied.php');
    include('../../../include/access.php');
    require_once("../../../Model/helper.php");

    $company = $_SESSION['companyid'];
    $year = date("Y", strtotime($_POST['years']));
    $rdo = $_POST['rdo'];
    $companies = [];

    //get default EWT acct code
	@$ewtpaydef = "";
	@$ewtpaydefdsc = "";
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='EWTPAY'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$ewtpaydef = $row['cacctno'];
			@$ewtpaydefdsc = $row['cacctdesc']; 
		}
	}

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) {
        $compname = stringValidation($list['compname']);
        $comptin = TinValidation($list['comptin']);
        $tinHeader = onlyNumber($list['comptin']);
    }
    $TOTAL_CREDIT = 0;
    $TOTAL_GROSS = 0;
    $count = 1;

    $xendingmonth = "";
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

    $sql = "SELECT a.ncredit-a.ndebit as ncredit, a.cewtcode, a.newtrate, a.ctranno, b.ngross, b.dapvdate, c.cname, CONCAT_WS(', ', c.chouseno, c.ccity) as caddress, c.ctin, d.cdesc, MONTH(b.dapvdate) as dmox
    FROM apv_t a
    LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
    LEFT JOIN suppliers c ON b.compcode = c.compcode AND b.ccode = c.ccode 
    LEFT JOIN groupings d ON c.compcode = d.compcode AND c.csuppliertype = d.ccode AND d.ctype = 'SUPTYP'
    WHERE a.compcode = '$company' AND MONTH(b.dapvdate) in ($months) AND YEAR(b.dapvdate) = '$year' AND  b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 and a.cacctno='$ewtpaydef' and IFNULL(a.cewtcode,'') <> '' Order By b.dapvdate, a.ctranno";

    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $xapvs = array();
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
        {
            $xapvs[] = $row;
        }

        $zip = new ZipArchive();
        $zipname=$tinHeader."Q".$_POST['selqrtr'].$year."1601EQ";// give zip file name 
        $zip->open($zipname, ZipArchive::CREATE); //example_zip zip file created 

        $xarr = explode(",",$months);
        foreach($xarr as $r1){
            $number = str_pad($r1, 2, '0', STR_PAD_LEFT);
           // header("Content-type: text/plain");
          //  header("Content-Disposition: attachment; filename=\"".$tinHeader.$number.$year."1601EQ.dat\"");
        
          $txtfflnme = $tinHeader.$number.$year."1601EQ.dat";
          $myfile = fopen($txtfflnme, "w") or die("Unable to open file!");

            
            $data = "HQAP,H1601EQ,$comptin,0000,\"$compname\",$number/$year,$rdo\n";
            

            foreach($xapvs as $list) {

                if($list['dmox']==$r1){
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

                            $okgross = floatval($credit) / (floatval($rate)/100);
                            $data .= "D1,1601EQ,$count,$tins,0000,$company_name,$lname,$fname,$midname,$number/$year,$ewtcode,$rate,$okgross,$credit\n";
                            $count += 1;

                            $TOTAL_CREDIT += $credit;
                            $TOTAL_GROSS += $okgross;
                        }
                    }
                }
            }
            $data .= "C1,1601EQ,$comptin,0000,$number/$year,$TOTAL_GROSS,$TOTAL_CREDIT";
           // echo $data . "<br><br>";
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
            <script type="text/javascript">alert("No record has been found for <?=  $xendingmonth . " Quarter/" . $yearcut?>")</script>
        <?php
    }
    exit;