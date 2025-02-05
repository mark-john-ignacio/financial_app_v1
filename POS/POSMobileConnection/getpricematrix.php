<?php
include('../../Connection/connection_string.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $companycode = $_POST["companycode"];
    $cversion = $_POST["cversion"];

    // SQL query to select item details
    $sql = "SELECT pricelist.citemno, pricelist.nprice
            FROM items_pm priceversion
            JOIN items_pm_t pricelist ON pricelist.ctranno = priceversion.ctranno
            WHERE priceversion.compcode = ? AND priceversion.cversion = ? AND priceversion.lapproved = '1'";

    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("ss", $companycode, $cversion);

        if ($stmt->execute()) {
            // Get the result set
            $result = $stmt->get_result();

            // Check if any records were found
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo $row["citemno"] . "&*&" . $row["nprice"] . "^*^";
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
