<?php
include('../../Connection/connection_string.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $companycode = $_POST["companycode"];

    // SQL query to select item details
    $sql = "SELECT customerDetails.cempid, customerDetails.cname, customerDetails.ctradename, customerDetails.cpricever
            FROM customers customerDetails
            WHERE customerDetails.compcode = ? AND customerDetails.cpricever != 'NONE'";

    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("s", $companycode);

        if ($stmt->execute()) {
            // Get the result set
            $result = $stmt->get_result();

            // Check if any records were found
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo $row["cempid"] . "&*&" . $row["cname"] . "&*&" . $row["ctradename"] . "&*&" . $row["cpricever"] . "^*^";
                }
            }
        } else {
            echo "<b><color=#FC0324>Error!</color></b> executing SQL statement: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<b><color=#FC0324>Error!</color></b> preparing SQL statement: " . $con->error;
    }

    // Close connection
    $con->close();
}
?>
