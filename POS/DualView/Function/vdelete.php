<?php
if(!isset($_SESSION)){
    session_start();
}
include('../../../Connection/connection_string.php');

$prepared = mysqli_real_escape_string($con, $_SESSION['employeeid']);

if (isset($_POST['name1'])) {
    $item_code = $_POST['name1'];

    $sql = "DELETE FROM pos_cart WHERE item = ? AND employee_name = ?";

    if ($stmt = $con->prepare($sql)) {

        $stmt->bind_param("ss", $item_code, $prepared);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "Record deleted successfully.";
            } else {
                echo "No record found with the specified name.";
            }
        } else {
            echo "Error executing statement: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }

    $con->close();
} else {
    echo "No name1 received.";
}
?>
