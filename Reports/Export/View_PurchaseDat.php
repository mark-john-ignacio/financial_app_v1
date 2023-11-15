<?php

    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    $company_code = $_SESSION['companyid'];


    $sql = "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);