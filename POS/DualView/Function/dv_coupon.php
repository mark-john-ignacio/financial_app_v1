<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include('../../../Connection/connection_string.php');

    $prepared = mysqli_real_escape_string($con, $_SESSION['employeeid']);

    if(isset($_POST['coupon'])) {

        $coupon = $_POST['coupon'];
        $coupon = mysqli_real_escape_string($con, $coupon);

        $update_query = "UPDATE pos_cart SET item_coupon = '$coupon' WHERE employee_name = '$prepared'";
        $result = mysqli_query($con, $update_query);

        if($result) {
            echo "Discount updated successfully.";
        } else {
            echo "Error updating discount: " . mysqli_error($con);
        }
    } else {
        echo "Discount value is not set.";
    }

    mysqli_close($con);

?>
