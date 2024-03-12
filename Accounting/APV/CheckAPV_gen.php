<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../../Connection/connection_string.php";
    include "../../Model/helper.php";

    $company = $_SESSION['companyid'];

    $witherr = "";
    $query = mysqli_query($con, "Select A.*, IFNULL(B.cname,'') as cname, IFNULL(C.cacctdesc,'') as cacctdesc From apv_temp A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctid Where A.compcode='$company'");
    $bkrectemp = array();
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){

        if($row['ddate']=="" || $row['ddate']==null || $row['cname']=="" || $row['cacctdesc']==""){
            $witherr = "YES";
        }

        if($witherr==""){
            mysqli_query($con, "INSERT INTO `apv`(`compcode`, `ctranno`, `ddate`, `dapvdate`, `ccode`, `cpayee`, `cpaymentfor`, `ngross`, `cpreparedby`, `captype`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`) values('$company', '$cSINo', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cCustID', '$cPayee','$cRemarks', $nGross, '$preparedby', '$cAPtype', '$CurrCode', '$CurrDesc', '$CurrRate')");
        }

        $witherr = "";
    }

    
?>