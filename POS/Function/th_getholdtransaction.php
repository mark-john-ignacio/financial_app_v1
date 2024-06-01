<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];
    $prepared = mysqli_real_escape_string($con, $_SESSION['employeeid']);
    // $item = implode("','",$_REQUEST['items']);
    $item = $_REQUEST['items'];

    $sql = "SELECT a.*, b.table, b.ordertype FROM pos_hold_t a
    LEFT JOIN pos_hold b ON a.compcode = b.compcode AND a.transaction = b.transaction
    WHERE a.compcode = '$company' AND a.transaction = '$item'" ;
    $query = mysqli_query($con, $sql);

    $data = [];
    if(mysqli_num_rows($query) != 0){ 
        // Delete existing entries for this employee
        mysqli_query($con, "DELETE FROM pos_cart WHERE employee_name = '$prepared'");
        
        while($row = $query -> fetch_assoc()){
            array_push($data, $row);
        }
        foreach($data as $itemorder){
            $item_id = $itemorder['partno'];
            $item_trannp = $itemorder['transaction'];
            $item_quantity = $itemorder['quantity'];
            $insert_sql = "INSERT INTO pos_cart (item, quantity, employee_name) VALUES ('$item_id', '$item_quantity', '$prepared')";
            mysqli_query($con, $insert_sql);
        }

        echo json_encode([
            'valid' => true,
            'data' => $data
        ]);
    } else {
        // No records found, delete existing entries for this employee
        mysqli_query($con, "DELETE FROM pos_cart WHERE employee_name = '$prepared'");
        
        echo json_encode([
            'valid' => false,
            'msg' => "No Record Found"
        ]);
    }
?>
