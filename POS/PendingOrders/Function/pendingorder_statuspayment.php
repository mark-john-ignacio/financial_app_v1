<?php
include('../../../Connection/connection_string.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tranno'])) {
    $target_tranno = $_POST['tranno'];
    $target_order_date = $_POST['order_date'];

    $sql = "SELECT pt.tranno, pt.item
            FROM pos_t pt
            WHERE pt.tranno = ?";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $con->error);
    }

    $stmt->bind_param("s", $target_tranno);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $all_updates_successful = true;

        while ($row = $result->fetch_assoc()) {
            $tranno = $row['tranno'];
            $item = $row['item'];
            $transaction_type = "Payment";

            // Check if record exists
            $check_stmt = $con->prepare("SELECT COUNT(*) FROM pendingorder_status WHERE tranno = ? AND items = ? AND transaction_type = ?");
            if (!$check_stmt) {
                die("Error preparing check statement: " . $con->error);
            }

            $check_stmt->bind_param("sss", $tranno, $item, $transaction_type);
            $check_stmt->execute();
            $check_stmt->bind_result($exists);
            $check_stmt->fetch();
            $check_stmt->close();

            // Update the existing record
            $status = "Done";
            $update_stmt = $con->prepare("UPDATE pendingorder_status SET pstatus = ? WHERE tranno = ? AND items = ? AND transaction_type = ? AND order_adding = ?");
            if (!$update_stmt) {
                die("Error preparing update statement: " . $con->error);
            }

            $update_stmt->bind_param("sssss", $status, $tranno, $item, $transaction_type, $target_order_date);
            if (!$update_stmt->execute()) {
                $all_updates_successful = false;
                $update_stmt->close();
                break;
            }
            $update_stmt->close();
        }

        if ($all_updates_successful) {
            //header("Location:/myxfin_clone/POS/PendingOrders/pending_order.php");
            //exit;
            echo "All records updated successfully.";
        } else {
            echo "Error updating some records.";
        }
    } else {
        echo "No matching records found for tranno: " . $target_tranno;
    }

    $stmt->close();
} else {
    echo "No tranno provided in the POST data.";
}

$con->close();
?>
