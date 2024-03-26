<?php
if(!isset($_SESSION)){
    session_start();
}
$company = $_SESSION['companyid'];
include "../../../Connection/connection_string.php";

// Query to get the revenue data per month for the top selling item
$sql = "
    SELECT s_t.citemno, SUM(s_t.nprice) AS total_price
    FROM sales_t s_t
    INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
    WHERE s.lapproved = 1 AND s.lvoid = 0 AND s.compcode = '$company'
    GROUP BY s_t.citemno
    ORDER BY total_price DESC
    LIMIT 1
    ";

$result = $con->query($sql);

if ($result->num_rows > 0) {
// Output the widget HTML with the dynamic data
    while ($row = $result->fetch_assoc()) {
        $topSellingItem = $row['citemno'];
}
}
$query = "
    SELECT
        YEAR(s.dcutdate) AS year,
        MONTH(s.dcutdate) AS month,
        SUM(s_t.nprice) AS total_revenue
    FROM
        sales_t s_t
        INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
    WHERE
        s.lapproved = 1 AND s.lvoid = 0 AND s_t.citemno = '$topSellingItem'
    GROUP BY
        YEAR(s.dcutdate), MONTH(s.dcutdate)
    ORDER BY
        YEAR(s.dcutdate), MONTH(s.dcutdate)
";

$result = $con->query($query);

if ($result->num_rows > 0) {
    // Array to store revenue data per month
    $revenueData = array();

    while ($row = $result->fetch_assoc()) {
        $year = $row["year"];
        $month = $row["month"];
        $revenue = $row["total_revenue"];

        // Format month as "M" (Jan, Feb, etc.)
        $formattedMonth = date("M", mktime(0, 0, 0, $month, 1));

        // Add revenue data to array
        $revenueData[] = array("month" => $formattedMonth . " " . $year, "revenue" => $revenue);
    }

    // Convert revenue data to JSON and output
    echo json_encode($revenueData);

} else {
    echo "No data available";
}
?>