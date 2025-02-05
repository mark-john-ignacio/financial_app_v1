<?php
include('../../../Connection/connection_string.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["employeeCashierName"])) {
        $employee_cashier_name = $_POST["employeeCashierName"];

        $sql = "DELETE FROM pos_cart WHERE employee_id = ?";
        
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            die("Error in preparing statement: " . $con->error);
        }
        
        $stmt->bind_param("s", $employee_cashier_name);
        
        if ($stmt->execute()) {
            echo "Data for employee cashier '$employee_cashier_name' deleted successfully.";
        } else {
            echo "Error deleting data: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Employee cashier name not provided.";
    }
} else {
    echo "Invalid request method.";
}

$con->close();
?>
