<?php
date_default_timezone_set('Asia/Manila');

include('../../Connection/connection_string.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $companycode = $_POST["companycode"];
    $allpaymentDetails = $_POST["allpaymentDetails"];
    $allpaymentItemList = $_POST["allpaymentItemList"];

    // Split allpaymentDetails by '*'
    $paymentDetailsArray = explode('*', $allpaymentDetails);

    // Split allpaymentItemList by '*'
    $paymentItemListArray = explode('*', $allpaymentItemList);

    // Iterate through each payment detail
    foreach ($paymentDetailsArray as $paymentDetail) {
        if (!empty($paymentDetail)) {
            // Generate new transaction code for each payment detail
            $posholdtransaction = "POS";
            
            // Get the current month and year
            $currentMonth = date('m');  // Two-digit month
            $currentYear = date('y');   // Two-digit year

            // Append the current month and year to the transaction code
            $posholdtransaction .= $currentMonth . $currentYear;

            // Initialize the numeric part
            $new_numeric_part = "";

            // Check if the transaction code already exists in the database
            if ($get_max_code_stmt = $con->prepare("SELECT MAX(CAST(SUBSTRING(tranno, 12) AS UNSIGNED)) FROM pos WHERE tranno LIKE ? AND compcode = ?")) {
                $like_pattern = "POS" . $currentMonth . $currentYear . '%';
                $get_max_code_stmt->bind_param("ss", $like_pattern, $companycode);
                
                // Execute the statement
                $get_max_code_stmt->execute();
                
                // Bind the result variable
                $get_max_code_stmt->bind_result($max_numeric_part);
                
                // Fetch the result
                $get_max_code_stmt->fetch();
                
                // Increment the numeric part if a record exists
                if ($max_numeric_part !== null) {
                    $new_numeric_part = str_pad((int)$max_numeric_part + 1, 5, '0', STR_PAD_LEFT);
                } else {
                    // If no records found, use "00001" as the initial numeric part
                    $new_numeric_part = "00001";
                }

                // Concatenate the numeric part to the transaction code
                $posholdtransaction .= $new_numeric_part;

                // Close the get_max_code statement
                $get_max_code_stmt->close();
            } else {
                echo "Error: " . $con->error;
                exit;
            }

            // Split each payment detail by ','
            $paymentFields = explode(',', $paymentDetail);

            // Ensure there are 19 fields
            if (count($paymentFields) > 17) {
                list($id, $firstname, $transactionno, $currentdate, $amount, $net, $vat, $gross, $customer, $ordertype, $tabletype, $discount, $tendered, $exchange, $coupon, $servicefee, $subtotal, $paymentmethod, $payment_customername, $payment_reference) = $paymentFields;

                // Format current date as 'd-m-Y' to 'Y-m-d H:i:s'
                $currentdate_formatted = DateTime::createFromFormat('d/m/Y h:i:s A', $currentdate)->format('Y-m-d H:i:s');

                // Prepare the SQL query for insertion into 'pos' table
                $sql = "INSERT INTO pos (compcode, preparedby, tranno, ddate, amount, net, vat, gross, customer, orderType, `table`, discount, tendered, exchange, coupon, serviceFee, subtotal, payment_method, payment_reference)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = $con->prepare($sql)) {
                    $stmt->bind_param("sssssssssssssssssss", $companycode, $firstname, $posholdtransaction, $currentdate_formatted, $amount, $net, $vat, $gross, $customer, $ordertype, $tabletype, $discount, $tendered, $exchange, $coupon, $servicefee, $subtotal, $paymentmethod, $payment_reference);

                    if (!$stmt->execute()) {
                        echo "<b><color=#FC0324>Error!</color></b> executing SQL statement: " . $stmt->error . "<br>";
                    }
                    $stmt->close();
                } else {
                    echo "<b><color=#FC0324>Error!</color></b> preparing SQL statement: " . $con->error . "<br>";
                }

                // Now iterate through each item detail and insert into 'pos_items' table
                foreach ($paymentItemListArray as $itemDetail) {
                    if (!empty($itemDetail)) {
                        // Split each item detail by ','
                        $itemFields = explode(',', $itemDetail);

                        // Ensure there are 9 fields
                        if (count($itemFields) === 9) {
                            list($item_id, $item_transactionno, $item_name, $item_uom, $item_quantity, $item_amount, $item_net, $item_vat, $item_gross) = $itemFields;

                            // Check if the item belongs to the current transaction
                            if ($item_transactionno == $transactionno) {
                                // Prepare the SQL query for insertion into 'pos_items' table
                                $sql = "INSERT INTO pos_t (compcode, tranno, item, uom, quantity, amount, net, vat, gross)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                                if ($stmt = $con->prepare($sql)) {
                                    $stmt->bind_param("sssssssss", $companycode, $posholdtransaction, $item_name, $item_uom, $item_quantity, $item_amount, $item_net, $item_vat, $item_gross);

                                    if (!$stmt->execute()) {
                                        echo "<b><color=#FC0324>Error!</color></b> executing SQL statement: " . $stmt->error . "<br>";
                                    }
                                    $stmt->close();
                                } else {
                                    echo "<b><color=#FC0324>Error!</color></b> preparing SQL statement: " . $con->error . "<br>";
                                }
                            }
                        } else {
                            echo "<b><color=#FC0324>Error!</color></b> Invalid item data format.<br>";
                        }
                    }
                }
            } else {
                echo "<b><color=#FC0324>Error!</color></b> Invalid data format.<br>";
            }
        }
    }

    echo "Success";

    // Close connection
    $con->close();
}
?>
