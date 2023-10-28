<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];
    // $item = implode("','",$_REQUEST['items']);
    $item = $_REQUEST['items'];

    $sql = "SELECT a.*, b.table, b.ordertype FROM pos_hold_t a
    LEFT JOIN pos_hold b ON a.compcode = b.compcode AND a.transaction = b.transaction
    WHERE a.compcode = '$company' AND a.transaction = '$item'" ;
    $query = mysqli_query($con, $sql);

    $data = [];
    if(mysqli_num_rows($query) != 0){ 
        while($row = $query -> fetch_assoc()){
            array_push($data, $row);
        }
        echo json_encode([
            'valid' => true,
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "No Record Found"
        ]);
    }
    
    