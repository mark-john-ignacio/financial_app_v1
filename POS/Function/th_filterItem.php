<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    // include('../../include/denied.php');
    // include('../../include/access2.php');

    $company = $_SESSION['companyid'];

    $itemcode = $_REQUEST['code'];