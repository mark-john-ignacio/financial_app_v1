<?php
include('../../../Connection/connection_string.php');

$itemId = $con->real_escape_string($_GET['itemId']);

// Modify the SQL query to include a JOIN with the pos_items table
$sql = "SELECT pcart.id, pcart.item, pcart.quantity, pcart.item_specialDisc, pcart.item_coupon, it.citemdesc, it.cunit, ipt.nprice
        FROM pos_cart pcart
        JOIN items it ON pcart.item = it.cpartno
        JOIN items_pm_t ipt ON pcart.item = ipt.citemno
        WHERE pcart.employee_name = '$itemId'
        ORDER BY pcart.id ASC";

$result = $con->query($sql);

$itemArray = array();
$totalDiscount = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $itemCode = $row["item"];  // The item number to be used for fetching discounts

        // Perform another SELECT query to fetch additional details
        $additionalSql = "SELECT dct.discount, dct.type, ip.nprice FROM discountmatrix_t dct JOIN items_pm_t ip ON dct.itemno = ip.citemno WHERE itemno = '$itemCode'";
        $additionalResult = $con->query($additionalSql);

        if ($additionalResult->num_rows > 0) {
            while ($additionalRow = $additionalResult->fetch_assoc()) {
                if($additionalRow["type"] == "PERCENT"){
                    $item_percenDisc = $additionalRow["discount"];
                    $total_percent = floatval($additionalRow["nprice"] * ($additionalRow["discount"]/100));
                    $totalDiscount += floatval($total_percent);
                }
                elseif($additionalRow["type"] == "PRICE"){
                    $totalDiscount += floatval($additionalRow["discount"]);
                }
            }
        }

        $itemData = array(
            "id" => $row["id"],
            "item" => $row["citemdesc"],
            "quantity" => $row["quantity"],
            "price" => $row["nprice"],
            "unit" => $row["cunit"],
            "specialDisc" => $row["item_specialDisc"],
            "coupon" => $row["item_coupon"],
            "discount" => floatval($totalDiscount)
        );

        $itemArray[] = $itemData;
    }

    $jsonData = json_encode($itemArray);
    echo $jsonData;
} else {
    echo json_encode(array("error" => "Item not found"));
}

$con->close();
?>