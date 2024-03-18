<?php

global $con;
if (!isset($_SESSION)) {
    session_start();
}
include "../../../Connection/connection_string.php";

$sql = "
   SELECT
        YEAR(s.dcutdate) AS year,
        MONTH(s.dcutdate) AS month,
        SUM(s_t.nprice) AS total_revenue,
        SUM(s.nnet) AS net_profit
    FROM
        sales s
        INNER JOIN sales_t s_t ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
    WHERE
        s.lapproved = 1 AND s.lvoid = 0
    GROUP BY
        YEAR(s.dcutdate), MONTH(s.dcutdate)
    ORDER BY
        YEAR(s.dcutdate), MONTH(s.dcutdate)
";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    $chartData = array();
    while ($row = $result->fetch_assoc()) {
        $formattedMonth = date("M", mktime(0, 0, 0, $row["month"], 1));
        $chartData[] = array(
            "month" => $formattedMonth . " " . $row["year"],
            "revenue" => $row["total_revenue"],
            "net_profit" => $row["net_profit"]
        );
    }
    echo json_encode($chartData);
} else {
    echo "No data available";
}

