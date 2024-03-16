<?php
function totalSales()
{
    // Query to get the sum of all nnet values
    global $con;
    $query_total_nnet = "SELECT SUM(nnet) AS total_nnet FROM sales";
    $result_total_nnet = mysqli_query($con, $query_total_nnet);
    $row_total_nnet = mysqli_fetch_assoc($result_total_nnet);
    $total_nnet = $row_total_nnet["total_nnet"];

    // Output the total net sales
    $total_sales = '';
    if ($total_nnet !== null) {
        $total_sales = number_format($total_nnet, 0, '.', ',');
    } else {
        // Handle the case when $total_nnet is null
        $total_sales = '0.00';
    }

    // Total Sales percentage change
    $start_of_last_week = date("Y-m-d", strtotime("last monday -1 week"));
    $end_of_last_week = date("Y-m-d", strtotime("last sunday"));

    // Get the date range for the current week (Monday to today)
    $start_of_current_week = date("Y-m-d", strtotime("monday this week"));
    $current_date = date("Y-m-d");

    // Query to get the total nnet sales for last week
    $query_last_week = "SELECT SUM(nnet) AS total_nnet_last_week FROM sales WHERE dcutdate >= '$start_of_last_week' AND dcutdate <= '$end_of_last_week'";
    $result_last_week = mysqli_query($con, $query_last_week);
    $row_last_week = mysqli_fetch_assoc($result_last_week);
    $total_nnet_last_week = $row_last_week["total_nnet_last_week"];

    // Query to get the total nnet sales for the current week
    $query_current_week = "SELECT SUM(nnet) AS total_nnet_current_week FROM sales WHERE dcutdate >= '$start_of_current_week' AND dcutdate <= '$current_date'";
    $result_current_week = mysqli_query($con, $query_current_week);
    $row_current_week = mysqli_fetch_assoc($result_current_week);
    $total_nnet_current_week = $row_current_week["total_nnet_current_week"];

    // Calculate the percentage increase or decrease
    $percentage_change = 0;
    if ($total_nnet_last_week != 0) {
        $percentage_change = (($total_nnet_current_week - $total_nnet_last_week) / $total_nnet_last_week) * 100;
    }

    // Output the percentage change
    if ($percentage_change > 0) {
        $percentage_change = "+" . round($percentage_change, 0) . "%";
    } elseif ($percentage_change < 0) {
        $percentage_change = round($percentage_change, 0) . "%";
    } else {
        $percentage_change = "No change";
    }

    return array(
        'revenue' => $total_sales,
        'percentageChange' => $percentage_change
    );


}

function totalSalesPercentageChange(){
    // Get the date range for last week (Monday to Saturday)
    global $con;

    return $percentage_change;
}


function topSellingItem(){
    // SQL query to get the top-selling item
    global $con;
    $sql = "
                                        SELECT s_t.citemno, SUM(s_t.nprice) AS total_price
                                        FROM sales_t s_t
                                        INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
                                        WHERE s.lapproved = 1 AND s.lvoid = 0
                                        GROUP BY s_t.citemno
                                        ORDER BY total_price DESC
                                        LIMIT 1
                                        ";

    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // Output the widget HTML with the dynamic data
        while ($row = $result->fetch_assoc()) {
            $topSellingItem = $row['citemno'];
            $totalSaleValue = $row['total_price'];
        }
    }

    if ($totalSaleValue !== null) {
        $totalSaleValue = number_format($totalSaleValue, 0, '.', ',');
    } else {
        // Handle the case when $total_nnet is null
        $totalSaleValue = '0.00';
    }

    // Start percentage change of topselling item this week compared to last week
    // Query to get the total revenue for the top selling item for last week
    $query_last_week = "
    SELECT
        SUM(s_t.nprice) AS total_revenue_last_week
    FROM
        sales_t s_t
        INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
    WHERE
        s.lapproved = 1 AND s.lvoid = 0 AND s_t.citemno = '$topSellingItem' AND WEEK(s.dcutdate) = WEEK(NOW()) - 1
";

    $result_last_week = $con->query($query_last_week);
    $total_revenue_last_week = 0; // Default value if result is 0
    if ($result_last_week && $result_last_week->num_rows > 0) {
        $row_last_week = $result_last_week->fetch_assoc();
        $total_revenue_last_week = $row_last_week["total_revenue_last_week"];
    }

    // Query to get the total revenue for the top selling item for this week
    $query_this_week = "
    SELECT
        SUM(s_t.nprice) AS total_revenue_this_week
    FROM
        sales_t s_t
        INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
    WHERE
        s.lapproved = 1 AND s.lvoid = 0 AND s_t.citemno = '$topSellingItem' AND WEEK(s.dcutdate) = WEEK(NOW())
";

    $result_this_week = $con->query($query_this_week);
    $total_revenue_this_week = 0; // Default value if result is 0
    if ($result_this_week && $result_this_week->num_rows > 0) {
        $row_this_week = $result_this_week->fetch_assoc();
        $total_revenue_this_week = $row_this_week["total_revenue_this_week"];
    }

    // Calculate the percentage change in revenue
    $percentage_change = ($total_revenue_last_week != 0) ? (($total_revenue_this_week - $total_revenue_last_week) / $total_revenue_last_week) * 100 : 0;

    // Format the percentage change
    if ($percentage_change > 0) {
        $percentage_change = "+" . number_format($percentage_change, 0) . "%";
    } elseif ($percentage_change < 0) {
        $percentage_change = number_format($percentage_change, 0) . "%";
    } else {
        $percentage_change = "No change";
    }




    return array(
        'name' => $topSellingItem,
        'revenue' => $totalSaleValue,
        'percentageChange' => $percentage_change
    );
}


