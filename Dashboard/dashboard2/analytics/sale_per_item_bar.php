<?php

// Start session if not already started
global $con;
if (!isset($_SESSION)) {
    session_start();
}
// Include database connection
include "../../../Connection/connection_string.php";

// Get company ID from session
$company = $_SESSION['companyid'];


$sql = "
    SELECT
    s_t.citemno AS item_number,
    i.citemdesc AS item_description,
    SUM(s.ngross) AS total_sales
FROM
    sales s
    INNER JOIN sales_t s_t ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno 
    LEFT JOIN items i ON s_t.citemno = i.cpartno
WHERE
    s.lapproved = 1 AND s.lvoid = 0
    AND s.compcode = '$company'
GROUP BY
    s_t.citemno, i.citemdesc
ORDER BY
    total_sales DESC
LIMIT 5

";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    $salesData = array();
    while ($row = $result->fetch_assoc()) {
        $salesData[] = array(
            "item_number" => $row["item_number"],
            "item_description" => $row["item_description"],
            "total_sales" => number_format(floatval($row["total_sales"]),2,'.','')
        );
    }
    echo json_encode($salesData);
} else {
    echo "No sales data available";
}