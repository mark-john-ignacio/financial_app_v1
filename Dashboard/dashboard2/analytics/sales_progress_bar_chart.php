<?php

global $con;
if (!isset($_SESSION)) {
    session_start();
}
include "../../../Connection/connection_string.php";

$currYear = date("Y");
$currMonth = date("m");
$currentQuarter = ceil($currMonth / 3);

// Calculate start and end dates of the current quarter for both this year and last year
$currentQuarterStart = $currYear . '-0' . (($currentQuarter - 1) * 3 + 1) . '-01';
$currentQuarterEnd = date('Y-m-t', strtotime($currYear . '-0' . ($currentQuarter * 3)));

$sql = "
    SELECT
    MONTH(s.dcutdate) AS month,
    SUM(CASE WHEN YEAR(s.dcutdate) = $currYear THEN s.ngross ELSE 0 END) AS this_year_gross,
    SUM(CASE WHEN YEAR(s.dcutdate) = ($currYear - 1) THEN s.ngross ELSE 0 END) AS last_year_gross
FROM
    sales s
WHERE
    s.lapproved = 1 AND s.lvoid = 0
    AND (
        (s.dcutdate >= '$currentQuarterStart' AND s.dcutdate < '$currentQuarterEnd') -- Current year's quarter
        OR
        (YEAR(s.dcutdate) = ($currYear - 1) AND MONTH(s.dcutdate) >= MONTH('$currentQuarterStart')) -- Same quarter of the previous year
    )
GROUP BY
    MONTH(s.dcutdate)
ORDER BY
    MONTH(s.dcutdate)
";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    $chartData = array();
    while ($row = $result->fetch_assoc()) {
        $formattedMonth = date("M", mktime(0, 0, 0, $row["month"], 1));
        $thisYearGross = $row["this_year_gross"];
        $lastYearGross = $row["last_year_gross"];


        $chartData[] = array(
            "month" => $formattedMonth,
            "this_year_gross" => $thisYearGross,
            "last_year_gross" => $lastYearGross,
        );
    }
    echo json_encode($chartData);
} else {
    echo "No data available";
}


