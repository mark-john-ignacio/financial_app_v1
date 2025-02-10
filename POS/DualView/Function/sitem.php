<?php
include('../../../Connection/connection_string.php');

session_start();

$itemId = $con->real_escape_string($_GET['itemId'] ?? "Admin");
$company = $_SESSION['companyid'];

function fetchCartItems($con, $itemId, $company) {
    $sql = "SELECT pcart.id, pcart.item, pcart.quantity, pcart.item_specialDisc, pcart.item_coupon, it.citemdesc, it.cunit, ipt.nprice
            FROM pos_cart pcart
            JOIN items it ON pcart.item = it.cpartno AND it.compcode = '$company'
            JOIN items_pm_t ipt ON pcart.item = ipt.citemno AND ipt.compcode = '$company'
            WHERE pcart.employee_name = '$itemId'
            ORDER BY pcart.id ASC";
    return mysqli_query($con, $sql);
}

function fetchAdditionalDiscounts($con, $itemCode, $company) {
    $sql = "SELECT dct.discount, dct.type, ip.nprice 
            FROM discountmatrix_t dct 
            JOIN items_pm_t ip ON dct.itemno = ip.citemno 
            WHERE dct.itemno = '$itemCode' AND dct.compcode = '$company'";
    return mysqli_query($con, $sql);
}

function calculateTotalDiscount($additionalResult) {
    $totalDiscount = 0;
    while ($additionalRow = $additionalResult->fetch_assoc()) {
        if ($additionalRow["type"] == "PERCENT") {
            $total_percent = floatval($additionalRow["nprice"] * ($additionalRow["discount"] / 100));
            $totalDiscount += floatval($total_percent);
        } elseif ($additionalRow["type"] == "PRICE") {
            $totalDiscount += floatval($additionalRow["discount"]);
        }
    }
    return $totalDiscount;
}

function prepareItemData($row, $totalDiscount) {
    return [
        "id" => $row["id"],
        "item" => $row["citemdesc"],
        "quantity" => $row["quantity"],
        "price" => $row["nprice"],
        "unit" => $row["cunit"],
        "specialDisc" => $row["item_specialDisc"],
        "coupon" => $row["item_coupon"],
        "discount" => floatval($totalDiscount)
    ];
}

function getCartItems($con, $itemId, $company) {
    $result = fetchCartItems($con, $itemId, $company);
    $itemArray = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $itemCode = $row["item"];
            $additionalResult = fetchAdditionalDiscounts($con, $itemCode, $company);
            $totalDiscount = calculateTotalDiscount($additionalResult);
            $itemData = prepareItemData($row, $totalDiscount);
            $itemArray[] = $itemData;
        }
        return $itemArray;
    } else {
        return null;
    }
}

$itemArray = getCartItems($con, $itemId, $company);

if ($itemArray) {
    echo json_encode($itemArray);
} else {
    echo json_encode(["error" => "Item not found"]);
}

$con->close();
?>