<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../../Connection/connection_string.php");
    require_once("../../../Model/helper.php");

    $company = $_SESSION['companyid'];
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
        header("Content-Disposition: attachment; filename=\"".$tin."S".$monthcut . $yearcut . ".dat\"");
        $company_name = stringValidation($company['compname']);
        // $data = "H,S,\"$tin\",\"$company_name\",\"\",\"\",\"\",\"{$company['compdesc']}\",\"$compaddress\",\"{$company['compzip']}\",$exempt,$zerorated,$net,$vat,$rdo,$lastDay,12\n";
        
    } else {

    }