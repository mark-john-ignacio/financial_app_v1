<?php
    include('../../Connection/connection_string.php');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $companyname = $_POST["companyname"];

        if (empty($companyname)) {
            echo "<b><color=#FC0324>Error!</color></b> Company name is required.";
            exit();
        }

        // SQL query
        $sql = "SELECT companyDetails.compcode AS companysystemcode, companyDetails.cpoweredname, companyDetails.cpoweredadd, companyDetails.cpoweredtin, companyDetails.caccredno, companyDetails.ddateissued, companyDetails.deffectdate, companyDetails.cptunum, companyDetails.dptuissued, companyDetails.creceiptmsg, companyDetails.cserialno, companyDetails.cmachine, companyinfo.compname, companyinfo.compdesc, companyinfo.compadd, companyinfo.compzip, companyinfo.comptin, companyinfo.compvat, companyinfo.cpnum
                FROM pos_system companyDetails 
                JOIN company companyinfo ON companyDetails.compcode = companyinfo.compcode
                WHERE companyinfo.compname = ?";

        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("s", $companyname);
            if ($stmt->execute()) {
                // Get the result set
                $result = $stmt->get_result();

                // Check if any records were found
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    echo $row["companysystemcode"] . "*&" . $row["cpoweredname"] . "*&" . $row["cpoweredadd"] . "*&" . $row["cpoweredtin"] . "*&" . $row["caccredno"] . "*&" . $row["ddateissued"] . "*&" . $row["deffectdate"] . "*&" . $row["cptunum"] . "*&" . $row["dptuissued"] . "*&" . $row["creceiptmsg"] . "*&" . $row["cserialno"] . "*&" . $row["cmachine"] . "*&" . $row["compname"] . "*&" . $row["compdesc"] . "*&" . $row["compadd"] . "*&" . $row["compzip"] . "*&" . $row["comptin"] . "*&" . $row["compvat"] . "*&" . $row["cpnum"];
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