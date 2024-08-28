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
$fromDate = "";
$toDate = "";

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
    
    

    $sql = "SELECT * from sales WHERE compcode = '$company' AND ddate BETWEEN '$formattedFromDate' AND '$formattedToDate'";

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
    $totalNvat = 0.00;  // Initialize total nvat to 0.0
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $totalNvat += (float)$row['nvat'];  // Add nvat to the total
    }
    // Return JSON response
    echo json_encode([
        'receivedFromDate' => $formattedFromDate,
        'receivedToDate' => $formattedToDate,
        'company' => $company,
        'message' => 'Data received successfully',
        'data' => $data,
        'totalNvat' => $totalNvat
    ]);
} else {
    // Return error message in JSON format
    echo json_encode([
        'error' => 'No fromDate or toDate received'
    ]);
}

?>
