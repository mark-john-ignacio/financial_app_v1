<?php 
    if(!isset($_SESION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $bank = [];

    $sql = "SELECT * FROM bank WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($bank, $row);
    }

    echo json_encode($bank);