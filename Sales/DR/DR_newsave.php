<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include('../../Connection/connection_string.php');
	include('../../include/denied.php');
    require_once('../../Model/helper.php');

    $dmonth = date('m');
    $dyear = date('y');

    $company = $_SESSION['companyid'];
    $cSINo = "DR".$dmonth.$dyear."00001";
    $directory = "../../Components/assets/DR/{$company}_{$cSINo}";


    if(upload_image($_FILES, $directory))
        return true;
    else 
        return false;

    
?>