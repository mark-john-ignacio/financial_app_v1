<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "../../Connection/connection_string.php";

$company = $_SESSION['companyid'];
$item = $_REQUEST['item'];
$unit = $_REQUEST['unit'];
$date = date("m/d/Y", strtotime($_REQUEST['date']));

// Escape user inputs to prevent SQL injection
$item = mysqli_real_escape_string($con, $item);
$unit = mysqli_real_escape_string($con, $unit);
$date = mysqli_real_escape_string($con, $date);

// $sql = "SELECT A.discount, A.type
//         FROM discountmatrix_t A
//         LEFT JOIN discountmatrix B ON A.compcode = B.compcode AND A.tranno = B.tranno
//         WHERE A.compcode = '$company' AND A.itemno = '$item' AND A.unit = '$unit' AND B.approved = 1 
//         AND B.deffective <= STR_TO_DATE('$date', '%m/%d/%Y') AND B.ddue >= STR_TO_DATE('$date', '%m/%d/%Y')
//         ORDER BY B.deffective DESC LIMIT 1";

$sql = "SELECT A.discount, A.type
		FROM discountmatrix_t A
		LEFT JOIN discountmatrix B ON A.compcode = B.compcode AND A.tranno = B.tranno
		WHERE A.compcode = '$company' AND A.itemno = '$item' AND A.unit = '$unit' AND B.approved = 1 
		ORDER BY B.deffective DESC LIMIT 1";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) != 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        'valid' => true,
        'data' => $row['discount'],
        'type' => $row['type']
    ]);
} else {
    echo json_encode([
        'valid' => false,
        'data' => 0,
        // 'type' => "PRICE"
		'type' => $data
    ]);
}
?>