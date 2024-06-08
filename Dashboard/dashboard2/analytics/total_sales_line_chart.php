<?php
if(!isset($_SESSION)){
    session_start();
}
$company = $_SESSION['companyid'];
include "../../../Connection/connection_string.php";


//begin::Total Sales line chart
// Calculate the start and end dates for the last 6 months
$start_date = date("Y-m-01", strtotime("-5 months"));
$end_date = date("Y-m-t");

// Query to get the total net profit for each month
$query = "SELECT YEAR(dcutdate) AS year, MONTH(dcutdate) AS month, SUM(ngross) AS total_net_profit
          FROM sales
          WHERE dcutdate >= '$start_date' AND dcutdate <= '$end_date'  AND compcode = '$company'
          GROUP BY YEAR(dcutdate), MONTH(dcutdate)
          ORDER BY YEAR(dcutdate), MONTH(dcutdate)";
$result = mysqli_query($con, $query);

// Initialize an array to store the data
$data = array();

// Loop through the results and store them in the data array
while ($row = mysqli_fetch_assoc($result)) {
    $date = date("M Y", strtotime($row['year'] . '-' . $row['month'] . '-01'));
    $data[$date] = $row['total_net_profit'];
}

// Initialize an array to store the categories (month-year values)
$categories = array();

// Initialize an array to store the net profits
$net_profits = array();

// Loop through the data array to populate the categories and net profits arrays
foreach ($data as $date => $net_profit) {
    $categories[] = $date;
    $net_profits[] = $net_profit;
}

// Update the series and xaxis categories in the options variable
$options['series'][0]['data'] = $net_profits;
$options['xaxis']['categories'] = $categories;

// Output the updated options variable
echo json_encode($options);
//end::Total Sales line chart