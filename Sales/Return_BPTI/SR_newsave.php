<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include('../../Connection/connection_string.php');
	include('../../include/denied.php');
    require('../../Model/helper.php');

    $dmonth = date('m');
    $dyear = date('y');

    $company = $_SESSION['companyid'];
    $cSINo = "RP".$dmonth.$dyear."00002";
    $directory = '../../Components/assets/'.$company.'_'.$cSINo.'/';
    
    /**
     * Uploading Image Function
     */
    if(upload_image($_FILES, $directory)){
        return true;
    } else {
        return false;
    }

?>
