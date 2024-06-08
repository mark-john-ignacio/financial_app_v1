<?php
if (!isset($_SESSION)) {
    session_start();
}

include('../../Connection/connection_string.php');
// include('../../include/denied.php');
// include('../../include/access2.php');

$company = $_SESSION['companyid'];
$transaction = $_REQUEST['code'];
$partno = $_REQUEST['partno'];
$item = $_REQUEST['name'];
$unit = $_REQUEST['unit'];
$quantity = $_REQUEST['quantity'];
$cost = $_REQUEST['cost'];
$waiting_time = $_REQUEST['waiting_time'];
$kitchen_receipt = $_REQUEST['kitchen_receipt'];
$date = date('Y-m-d h:i');
// $table = $_REQUEST['table'];

$sql = "SELECT * FROM pos_hold WHERE `compcode` = '$company' and `transaction` = '$transaction'";
$query = mysqli_query($con, $sql);

if (mysqli_num_rows($query) !== 0) {
    $sql = "INSERT INTO pos_hold_t 
            (`compcode`, `transaction`, `partno`, `item`, `quantity`, `unit`, `discount`, `cost`) 
            VALUES ('$company', '$transaction', '$partno', '$item', '$quantity', '$unit', '0', '$cost')";
    
    if (mysqli_query($con, $sql)) {
        $pendingorder_check = "SELECT * FROM pendingorder_status WHERE `tranno` = '$transaction' AND `items` = '$partno'";
        $query_check = mysqli_query($con, $pendingorder_check);

        if(mysqli_num_rows($query_check) == 0) {
            // No existing record, insert a new one
            $sql2 = "INSERT INTO pendingorder_status 
            (`tranno`, `items`, `quantity`, `waiting_time`, `transaction_type`, `pstatus`, `order_adding`, `receipt`) 
            VALUES ('$transaction', '$partno', '$quantity', '$waiting_time', 'Hold', 'Not Done', '$date', '$kitchen_receipt')";
            $query2 = mysqli_query($con, $sql2);
            
            echo json_encode([
                'valid' => true,
                'receipt' => $kitchen_receipt,
                'date' => $date,
                'data' => 'success'
            ]);
        } else {
            // Update existing row
            $pendingorder_check1 = "SELECT * FROM pendingorder_status WHERE `tranno` = '$transaction' AND `items` = '$partno'";
            $query_check1 = mysqli_query($con, $pendingorder_check1);

            if(mysqli_num_rows($query_check1) > 0){ 
                // If there are existing rows with the same transaction number and item
                $total_existing_quantity = 0;

                // Loop through existing rows to sum up the quantities
                while ($existing_row = mysqli_fetch_assoc($query_check1)) {
                    $total_existing_quantity += $existing_row['quantity'];
                }
                $new_quantity = $quantity - $total_existing_quantity;

                if($new_quantity > 0){
                    $sql4 = "INSERT INTO pendingorder_status 
                    (`tranno`, `items`, `quantity`, `waiting_time`, `transaction_type`, `pstatus`, `order_adding`, `receipt`) 
                    VALUES ('$transaction', '$partno', '$new_quantity', '$waiting_time', 'Hold', 'Not Done', '$date', '$kitchen_receipt')";
                    $query4 = mysqli_query($con, $sql4);
                }
                echo json_encode([
                    'valid' => true,
                    'receipt' => $kitchen_receipt,
                    'date' => $date,
                    'data' => 'success'
                ]);
            }
        }
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => 'Failed to insert into pos_hold_t'
        ]);
    }
} else {
    $sql = "DELETE FROM pos_hold WHERE `transaction` = '$transaction'";
    mysqli_query($con, $sql);

    echo json_encode([
        'valid' => false,
        'msg' => "POS Transaction Cannot Hold!"
    ]);
}
?>
