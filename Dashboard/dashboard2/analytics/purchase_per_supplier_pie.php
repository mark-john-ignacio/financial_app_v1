<?php

global $con;
if (!isset($_SESSION)) {
    session_start();
}
$company = $_SESSION['companyid'];
include "../../../Connection/connection_string.php";

$sql = "
   SELECT
        c.cname AS country,
        SUM(a.namount) AS value
    FROM
        suppinv_t a
        LEFT JOIN suppinv b ON a.ctranno = b.ctranno AND a.compcode = b.compcode
        LEFT JOIN suppliers c ON b.ccode = c.ccode AND a.compcode = c.compcode
        LEFT JOIN items d ON a.citemno = d.cpartno AND a.compcode = d.compcode
    WHERE
        a.compcode = '$company'
    GROUP BY
        b.ccode
    ORDER BY
        SUM(a.namount) DESC
    LIMIT 6
";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    $chartData = array();
    while ($row = $result->fetch_assoc()) {
        $chartData[] = array(
            "country" => $row["country"],
            "value" => $row["value"]
        );
    }
    echo json_encode($chartData);
} else {
    echo "No data available";
}
