<?php
include('../../../Connection/connection_string.php');

// Retrieve data from POST request
if(isset($_POST['tranno']) && isset($_POST['payment_transaction']) && isset($_POST['transaction_type']) && isset($_POST['ddate']) && isset($_POST['itemid'])) {
    $tranno = $_POST['tranno'];
    $payment_transaction = $_POST['payment_transaction'];
    $transaction_type = $_POST['transaction_type'];
    $ddate = $_POST['ddate'];
    $itemid = $_POST['itemid'];
    
    if($transaction_type == "Hold"){
        // Check if tranno and itemid are already declared
        $check_sql = "SELECT * FROM pendingorder_status WHERE tranno = '$tranno' AND items = '$itemid' AND order_adding = '$ddate'";
        $result = $con->query($check_sql);
        
        if($result->num_rows > 0) {
            $transaction_type = "Payment";
            $pstatus = "Done";

            $update_sql = "UPDATE pendingorder_status 
                           SET pstatus = '$pstatus' 
                           WHERE tranno = '$tranno' AND items = '$itemid' AND order_adding = '$ddate'";
            
            if ($con->query($update_sql) === TRUE) {
                header("Location:/myxfin_clone/POS/PendingOrders/pending_order.php");
            } else {
                echo "Error: " . $update_sql . "<br>" . $con->error;
            }
        } else {
            
        }
    }
    if($transaction_type == "Payment"){
        // Check if tranno and itemid are already declared
        $check_sql = "SELECT * FROM pendingorder_status WHERE payment_transaction = '$payment_transaction' AND items = '$itemid' AND order_adding = '$ddate'";
        $result = $con->query($check_sql);
        
        if($result->num_rows > 0) {
            $transaction_type = "Payment";
            $pstatus = "Done";

            $update_sql = "UPDATE pendingorder_status 
                           SET pstatus = '$pstatus' 
                           WHERE tranno = '$tranno' AND items = '$itemid' AND order_adding = '$ddate'";
            
            if ($con->query($update_sql) === TRUE) {
                header("Location:/myxfin_clone/POS/PendingOrders/pending_order.php");
            } else {
                echo "Error: " . $update_sql . "<br>" . $con->error;
            }
        } else {
            
        }
    }
} else {
    // tranno or itemid is not set in POST request
    echo "Error: tranno or itemid is not set in POST request";
}

$con->close();
?>
