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
        LEFT JOIN sales_t s_t ON s.compcode = s_t.compcode AND s.ctranno = s.ctranno 
        LEFT JOIN items i ON s_t.citemno = i.cpartno AND s.compcode = i.compcode
        
    WHERE
        s.lapproved = 1 AND s.lvoid = 0
    GROUP BY
        s_t.citemno, i.citemdesc
    ORDER BY
        total_sales DESC
";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    $salesData = array();
    while ($row = $result->fetch_assoc()) {
        $salesData[] = array(
            "item_number" => $row["item_number"],
            "item_description" => $row["item_description"],
            "total_sales" => $row["total_sales"]
        );
    }
    echo json_encode($salesData);
} else {
    echo "No sales data available";
}