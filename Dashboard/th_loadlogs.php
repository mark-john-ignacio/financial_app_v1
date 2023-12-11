<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";

    $company = $_SESSION['companyid'];

    $sql = "SELECT * FROM logfile WHERE compcode = '$company' ORDER BY ddate DESC LIMIT 10";
    $query = mysqli_query($con, $sql);
    $logs = [];
    while($row = $query -> fetch_assoc()) {
        array_push($logs, $row);
    }

    if(!empty($logs)){
        echo json_encode([
            "valid" => true,
            "data" => $logs,
        ]);
    } else {
        echo json_encode([
            "valid" => false,
            "msg" => "No Record Found!"
        ]);
    }