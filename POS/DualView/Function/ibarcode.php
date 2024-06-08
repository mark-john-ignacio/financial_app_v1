<?php
if(!isset($_SESSION)){
    session_start();
}

include('../../../Connection/connection_string.php');

$company = $_SESSION['companyid'];
$prepared = mysqli_real_escape_string($con, $_SESSION['employeeid']);

$itemcode = $_REQUEST['selected_item'];

// Prepare the SQL check statement
$sql_check = "SELECT * FROM pos_cart WHERE item = ? AND employee_name = '$prepared'";
$stmt_check = mysqli_prepare($con, $sql_check);
mysqli_stmt_bind_param($stmt_check, 's', $itemcode);
mysqli_stmt_execute($stmt_check);
$result = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result) == 0) {
    // Item does not exist, insert a new record with quantity 1
    $sql_insert = "INSERT INTO pos_cart (`item`, `quantity`, `employee_name`) VALUES (?, '1', ?)";
    $stmt_insert = mysqli_prepare($con, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, 'ss', $itemcode, $prepared);
    mysqli_stmt_execute($stmt_insert);
} else {
    // Item exists, update the quantity by adding 1
    $sql_update = "UPDATE pos_cart SET quantity = quantity + 1 WHERE item = ? AND employee_name = '$prepared'";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, 's', $itemcode);
    mysqli_stmt_execute($stmt_update);
}

// Close the prepared statements
mysqli_stmt_close($stmt_check);
if (isset($stmt_insert)) {
    mysqli_stmt_close($stmt_insert);
}
if (isset($stmt_update)) {
    mysqli_stmt_close($stmt_update);
}

?>