<?php


function totalSales($company)
{
    // Query to get the sum of all nnet values
    global $con;
    $query_total_nnet = "SELECT SUM(nnet) AS total_nnet FROM sales WHERE compcode = '$company' and lvoid=0 and lcancelled=0";
    $result_total_nnet = mysqli_query($con, $query_total_nnet);
    $row_total_nnet = mysqli_fetch_assoc($result_total_nnet);
    $total_nnet = $row_total_nnet["total_nnet"];

    // Output the total net sales
    $total_sales = '';
    if ($total_nnet !== null) {
        $total_sales = $total_nnet;
        $total_sales = formatCurrencyMillion($total_sales);
    } else {
        // Handle the case when $total_nnet is null
        $total_sales = '0.00';
    }

    // Total Sales percentage change
    //$start_of_last_week = date("Y-m-d", strtotime("last monday"));
   // $end_of_last_week = date("Y-m-d", strtotime("last sunday"));

    $previous_week = strtotime("-1 week +1 day");

    // Query to get the total nnet sales for last week 
    $query_last_week = "SELECT SUM(ngross) AS total_nnet_last_week FROM sales WHERE MONTH(dcutdate) = MONTH(DATE_SUB(NOW(),INTERVAL 30 DAY)) AND YEAR(dcutdate) = YEAR(DATE_SUB(NOW(),INTERVAL 30 DAY)) AND compcode = '$company' and lvoid=0 and lcancelled=0";
    $result_last_week = mysqli_query($con, $query_last_week);
    $row_last_week = mysqli_fetch_assoc($result_last_week);
    $total_nnet_last_week = $row_last_week["total_nnet_last_week"];

    // Query to get the total nnet sales for the current week
    $query_current_week = "SELECT SUM(ngross) AS total_nnet_current_week FROM sales WHERE MONTH(dcutdate) = MONTH(curdate()) AND YEAR(dcutdate) = YEAR(curdate()) AND compcode = '$company' and lvoid=0 and lcancelled=0";
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


    $total_sales = '';
    if ($total_nnet_current_week !== null) {
        $total_sales = $total_nnet_current_week;
        $total_sales = formatCurrencyMillion($total_sales);
    } else {
        // Handle the case when $total_nnet is null
        $total_sales = '0.00';
    }

    return array(
       // 'revenue' => $total_sales,
       'revenue' => $total_sales,
       'percentageChange' => $percentage_change
    );


}


function topSellingItem($company){
    global $con;

    $topSellingItem = 'No data';
    $totalSaleValue = '0.00';
    $percentage_change = 'No change';

    $sql = "
        SELECT s_t.citemno, i.citemdesc, SUM(s_t.nprice) AS total_price
        FROM sales_t s_t
        INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
        INNER JOIN items i ON s_t.citemno = i.cpartno
        WHERE s.lapproved = 1 AND s.lvoid = 0 AND s.compcode = '$company' and year(s.dcutdate) = year(curdate())
        GROUP BY s_t.citemno,s.dcutdate,i.citemdesc
        ORDER BY total_price DESC
        LIMIT 1
    ";
    
    $result = $con->query($sql);
    $topSellingItemDesc = "";
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $topSellingItemDesc = $row['citemdesc'];
        $topSellingItem = $row['citemno'];
        $totalSaleValue = $row['total_price'];
        $totalSaleValue = formatCurrencyMillion($totalSaleValue);
    }

    // Start percentage change of topselling item this week compared to last week
    $query_last_week = "
        SELECT
            SUM(s_t.nprice) AS total_revenue_last_week
        FROM
            sales_t s_t
            INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
        WHERE
            s.lapproved = 1 AND s.lvoid = 0 AND s_t.citemno = '$topSellingItem' AND WEEK(s.dcutdate) = WEEK(NOW()) - 1 AND s.compcode = '$company'
    ";

    $result_last_week = $con->query($query_last_week);
    if ($result_last_week && $result_last_week->num_rows > 0) {
        $row_last_week = $result_last_week->fetch_assoc();
        $total_revenue_last_week = $row_last_week["total_revenue_last_week"];

        // Check if total_revenue_last_week is not null or zero
        if ($total_revenue_last_week != null && $total_revenue_last_week != 0) {
            // Query to get the total revenue for the top selling item for this week
            $query_this_week = "
                SELECT
                    SUM(s_t.nprice) AS total_revenue_this_week
                FROM
                    sales_t s_t
                    INNER JOIN sales s ON s.compcode = s_t.compcode AND s.ctranno = s_t.ctranno
                WHERE
                    s.lapproved = 1 AND s.lvoid = 0 AND s_t.citemno = '$topSellingItem' AND WEEK(s.dcutdate) = WEEK(NOW()) AND s.compcode = '$company'
            ";

            $result_this_week = $con->query($query_this_week);
            if ($result_this_week && $result_this_week->num_rows > 0) {
                $row_this_week = $result_this_week->fetch_assoc();
                $total_revenue_this_week = $row_this_week["total_revenue_this_week"];

                // Calculate the percentage change in revenue
                $percentage_change = (($total_revenue_this_week - $total_revenue_last_week) / $total_revenue_last_week) * 100;

                // Format the percentage change
                $percentage_change = ($percentage_change > 0) ? "+" . number_format($percentage_change, 0) . "%" : number_format($percentage_change, 0) . "%";
            }
        }
    }

    return array(
        'name' => $topSellingItemDesc,
        'revenue' => $totalSaleValue,
        'percentageChange' => $percentage_change
    );
}





// Function to return total gross sales
function totalGrossSales($company) {
    global $con;

        $query = "SELECT SUM(ngrossbefore) AS total_gross_sales FROM sales WHERE compcode = '$company' and lvoid=0 and lcancelled=0";
        $result = $con->query($query);
        $row = $result->fetch_assoc();
        $gross = isset($row['total_gross_sales']) ? $row['total_gross_sales'] : 0;
        return formatCurrency($gross);
}

function totalNetSales($company) {
    global $con;

    $query = "SELECT SUM(nnet) AS total_net_sales FROM sales WHERE compcode = '$company' and lvoid=0 and lcancelled=0";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    $net = isset($row['total_net_sales']) ? $row['total_net_sales'] : 0;
    return formatCurrency($net);
}

function totalDiscount($company) {
    global $con;

    $query = "SELECT SUM(nexempt + nzerorated) AS total_discount FROM sales WHERE compcode = '$company' and lvoid=0 and lcancelled=0";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    $discount = isset($row['total_discount']) ? $row['total_discount'] : 0;
    return formatCurrency($discount);
}

function totalVat($company) {
    global $con;

    $query = "SELECT SUM(nvat) AS total_vat FROM sales WHERE compcode = '$company' and lvoid=0 and lcancelled=0";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    $vat = isset($row['total_vat']) ? $row['total_vat'] : 0;
    return formatCurrency($vat);
}

function formatCurrency($amount) {
    // Check if the amount is greater than or equal to 1 million
    if ($amount >= 1000000) {
        // Convert the amount to M format (e.g., 1000000 => 1M, 1500000 => 1.5M)
        $formattedAmount = number_format($amount / 1000000, 1) . 'M';
    } elseif ($amount >= 1000) {
        // Check if the amount is greater than or equal to 1000
        // Convert the amount to K format (e.g., 1000 => 1K, 1500 => 1.5K)
        $formattedAmount = number_format($amount / 1000, 1) . 'K';
    } else {
        // If the amount is less than 1000, simply format it
        $formattedAmount = number_format($amount, 0);
    }
    return $formattedAmount;
}

function formatCurrencyMillion($amount) {
    // Check if the amount is greater than or equal to 1 million
    if ($amount >= 1000000) {
        // Convert the amount to M format (e.g., 1000000 => 1M, 1500000 => 1.5M)
        $formattedAmount = number_format($amount / 1000000, 1) . 'M';
    } else {
        // If the amount is less than 1000, simply format it
        $formattedAmount = number_format($amount, 0);
    }
    return $formattedAmount;
}

function formatCurrencyWhole($amount){
    return number_format($amount, 0);
}

function averageSales() {
    global $con;

    $query = "SELECT AVG(ngross) AS average_sales FROM sales";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    $averageSales = isset($row['average_sales']) ? $row['average_sales'] : 0;
    return formatCurrencyWhole($averageSales);
}

function totalRevenue() {
    global $con;

    $query = "SELECT SUM(nnet) AS total_revenue FROM sales";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    $revenue = isset($row['total_revenue']) ? $row['total_revenue'] : 0;
    return formatCurrencyWhole($revenue);
}

function totalNumberOfSales() {
    global $con;

    $query = "SELECT COUNT(*) AS total_sales FROM sales";
    $result = $con->query($query);
    $row = $result->fetch_assoc();
    $totalSales = isset($row['total_sales']) ? $row['total_sales'] : 0;
    return $totalSales;
}