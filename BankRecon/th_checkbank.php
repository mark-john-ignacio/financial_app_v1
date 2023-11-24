<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];

    $sql = "SELECT * FROM deposit WHERE compcode = '$company' AND ctranno='$tranno'";
    $query = mysqli_query($con, $sql);