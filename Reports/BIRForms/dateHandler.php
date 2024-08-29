<?php
header('Content-Type: application/json');

// Start session if not already started
if(!isset($_SESSION)) {
    session_start();
}

// Include necessary files
include("../../Connection/connection_string.php");
include('../../include/denied.php');
include('../../include/access.php');

$company = $_SESSION['companyid'];

// Initialize variables
// $fromDate = "";
// $toDate = "";

// Check if 'fromDate' and 'toDate' are set in POST request
if (isset($_POST['fromDate']) && isset($_POST['toDate'])) {
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate']; // Corrected this line

    // Sanitize input
    $fromDate = htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8');
    $toDate = htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8');
    
    // Convert MM/DD/YYYY to YYYY-MM-DD
    function formatDate($date) {
        $dateParts = explode('/', $date);
        if (count($dateParts) == 3) {
            return "{$dateParts[2]}-{$dateParts[0]}-{$dateParts[1]}";
        }
        return $date; // Keep original format if conversion is not needed
    }

    $formattedFromDate = formatDate($fromDate);
    $formattedToDate = formatDate($toDate);
    
    

    // $sql = "SELECT * from sales WHERE compcode = '$company' AND ddate BETWEEN '$formattedFromDate' AND '$formattedToDate'";
    $sql = "SELECT * FROM sales WHERE compcode = '001' AND dcutdate BETWEEN '$formattedFromDate' AND '$formattedToDate'
    AND ctranno IN ( SELECT receipt_sales_t.csalesno FROM receipt LEFT JOIN receipt_sales_t ON receipt.compcode = receipt_sales_t.compcode AND receipt.ctranno = receipt_sales_t.ctranno
                WHERE receipt.compcode = '001' AND receipt.lapproved = 1 AND receipt.lvoid = 0 AND receipt.lcancelled = 0);";

    $result = $con->query($sql);
     // Check for query errors
     if (!$result) {
        echo json_encode([
            'error' => 'Query error: ' . $con->error
        ]);
        exit();
    }

    // Fetch results
    $data = [];
    
    //A. Sales for the Quarter (Exclusive of VAT)
    // $totalVATableSalesA = 0.00;
    // $totalZeroRatedSales = 0.00;  
    // $totalExemptSales = 0.00;

    // //B. Output Tax for the Quarter
    // $totalVATableSalesB = 0.00;

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;

        // $totalVATableSalesA += (float)$row['nnet'];
        // $totalZeroRatedSales += (float)$row['nzerorated']; 
        // $totalExemptSales += (float)$row['nexempt']; 

        // $totalVATableSalesB  += (float)$row['nvat'];
    }
    // Return JSON response
    echo json_encode([
        'receivedFromDate' => $formattedFromDate,
        'receivedToDate' => $formattedToDate,
        'company' => $company,
        'message' => 'Data received successfully',
        'data' => $data,
        
         //A. Sales for the Quarter (Exclusive of VAT)
        // 'totalVATableSalesA' => $totalVATableSalesA,
        // 'totalZeroRatedSales' => $totalZeroRatedSales,
        // 'totalExemptSales' => $totalExemptSales,

        //  //B. Output Tax for the Quarter
        // 'totalVATableSalesB' => $totalVATableSalesB,
    ]);
} else {
    // Return error message in JSON format
    echo json_encode([
        'error' => 'No fromDate or toDate received'
    ]);
}

?>
