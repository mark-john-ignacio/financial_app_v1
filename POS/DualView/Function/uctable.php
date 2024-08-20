<?php
    include('../../../Connection/connection_string.php');

    $partNo = $_POST['partNo'];
    $quantity = $_POST['quantity'];

    $stmt = $con->prepare("UPDATE pos_cart SET quantity = ? WHERE item = ?");
    $stmt->bind_param("is", $quantity, $partNo);

    if ($stmt->execute()) {
        echo "Quantity updated successfully.";
    } else {
        echo "Error updating quantity: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
?>
