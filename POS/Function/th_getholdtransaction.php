<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];
    $item = implode("','",$_REQUEST['items']);

    $sql = "SELECT * FROM pos_hold_t WHERE `compcode` = '$company' AND `transaction` IN ('". $item ."')";
    $query = mysqli_query($con, $sql);
    $data = [];
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            array_push($data, $row);
        }
    }
    
    echo json_encode([
        'valid' => true,
        'data' => $data
    ]);