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

// Query to get total purchase per item
$sql = "SELECT
    a.citemno AS item_code,
    d.citemdesc AS item_description,
    SUM(a.nqty) AS total_quantity,
    SUM(a.namount) AS total_amount
FROM
    suppinv_t a
    LEFT JOIN suppinv b ON a.ctranno = b.ctranno AND a.compcode = b.compcode
    LEFT JOIN items d ON a.citemno = d.cpartno AND a.compcode = d.compcode
WHERE
    a.compcode = '$company' AND b.lvoid = 0
GROUP BY
    a.citemno
";

$result = mysqli_query($con, $sql);

if (!$result) {
    printf("Errormessage: %s\n", mysqli_error($con));
    exit();
}

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}


// Return data as JSON
echo json_encode($data);
