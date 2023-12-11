<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../Connection/connection_string.php";

    $company = $_SESSION['companyid'];

    $sql = "SELECT a.* FROM receipt_t a WHERE compcode = '$company' AND a.lapproved = 1 AND a.lcancelled = 0 AND a.lvoid = 0 ";
    $query = mysqli_query($con, $sql);


    $sql = "SELECT ccode FROM groupings WHERE compcode = '$company' AND ctype = 'ITEMCLS'";
    $query = mysqli_query($con, $sql);