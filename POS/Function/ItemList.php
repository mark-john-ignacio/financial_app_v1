<?php
if (!isset($_SESSION)) {
    session_start();
}

include('../../Connection/connection_string.php');

$company = $_SESSION['companyid'];
$prepared = mysqli_real_escape_string($con, $_SESSION['employeeid']);
$itemcode = $_REQUEST['code'];
$date = date("Y-m-d");
$data = [];

function fetchItemDetails($con, $company, $itemcode, $date) {
    $sql = "SELECT a.cpartno, a.cpartno AS cscancode, a.citemdesc, a.cunit, a.cstatus, a.linventoriable AS isInventory, IFNULL(c.nqty, 0) AS nqty
            FROM items a 
            LEFT JOIN (
                SELECT a.citemno, COALESCE((SUM(nqtyin) - SUM(nqtyout)), 0) AS nqty
                FROM tblinventory a
                RIGHT JOIN items d ON a.citemno = d.cpartno AND a.compcode = d.compcode
                WHERE a.compcode = '$company' AND a.dcutdate <= '$date' AND d.cpartno = '$itemcode'
                GROUP BY a.citemno
            ) c ON a.cpartno = c.citemno
            WHERE a.compcode = '$company' AND a.cpartno = '$itemcode'";

    return mysqli_query($con, $sql);
}

function checkItemInCart($con, $itemcode, $prepared) {
    $sql_check = "SELECT * FROM pos_cart WHERE item = '$itemcode' AND employee_name = '$prepared'";
    return mysqli_query($con, $sql_check);
}

function addItemToCart($con, $itemcode, $prepared) {
    $sql_insert = "INSERT INTO pos_cart (`item_id`, `quantity`, `employee_name`, `item`) VALUES ('$itemcode', '1', '$prepared', '$itemcode')";
    return mysqli_query($con, $sql_insert);
}

function updateItemInCart($con, $itemcode, $prepared) {
    $sql_update = "UPDATE pos_cart SET quantity = quantity + 1 WHERE item = '$itemcode' AND employee_name = '$prepared'";
    return mysqli_query($con, $sql_update);
}

function handleItemDetails($row, &$data) {
    if ($row['isInventory'] == 0 && (float)$row['nqty'] <= 0) {
        return [
            'valid' => false,
            'msg' => "No more stock available!"
        ];
    }

    $json = [
        'partno' => $row['cpartno'],
        'name' => $row['citemdesc'],
        'unit' => $row['cunit'],
        'quantity' => $row['nqty'],
        'isInventory' => $row['isInventory']
    ];

    if ($row['cstatus'] == "INACTIVE") {
        return [
            'valid' => false,
            'msg' => "Item is currently inactive!"
        ];
    }

    $data[] = $json;
    return [
        'valid' => true,
        'data' => $data
    ];
}

$query = fetchItemDetails($con, $company, $itemcode, $date);
$result = checkItemInCart($con, $itemcode, $prepared);

if (mysqli_num_rows($result) == 0) {
    addItemToCart($con, $itemcode, $prepared);
} else {
    updateItemInCart($con, $itemcode, $prepared);
}

if (mysqli_num_rows($query) != 0) {
    while ($row = $query->fetch_assoc()) {
        $response = handleItemDetails($row, $data);
        echo json_encode($response);
        exit;
    }
} else {
    echo json_encode([
        'valid' => false,
        'errorMsg' => 'No Data Has Found'
    ]);
}
?>