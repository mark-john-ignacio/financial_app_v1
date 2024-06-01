<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $prepared = mysqli_real_escape_string($con, $_SESSION['employeename']);

    /**
     * Initiate Variables
     */
    $tranno = mysqli_real_escape_string($con, $_POST['tranno']);
    $item = mysqli_real_escape_string($con, $_POST['itm']);
    $unit = mysqli_real_escape_string($con, $_POST['unit']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
    $amount = mysqli_real_escape_string($con, $_POST['amount']);
    $flag_status = mysqli_real_escape_string($con, $_POST['flag_status']);
    $kitchen_receipt = mysqli_real_escape_string($con, $_POST['kitchen_receipt']);

    $discount = mysqli_real_escape_string($con, $_POST['discount']);
    $validID = mysqli_real_escape_string($con, $_POST['discountID']);
    $person = mysqli_real_escape_string($con, $_POST['discountName']);
    $waiting_time = mysqli_real_escape_string($con, $_POST['waiting_time']);

    $coupon = json_decode($_POST['coupon'], true);
    $specialdisc = json_decode($_POST['specialdisc'], true);
    $date = date("Y-m-d");

    $net =  floatval($amount) / floatval(1 + (12/100));
    $vat = $net * (12/100);
    $price = $amount / $quantity;

    $pendingorder_date = date('Y-m-d h:i');

    $sql = "INSERT INTO pos_t (`compcode`, `tranno`, `item`, `uom`, `quantity`, `amount`, `net`, `vat`, `gross`) 
        VALUES('$company', '$tranno', '$item', '$unit', '$quantity', '$price', '$net', '$vat', '$amount')";

    if($flag_status == "Insert"){
        $sql1 = "INSERT INTO pendingorder_status (`tranno`, `payment_transaction`, `items`, `quantity`, `waiting_time`, `transaction_type`, `pstatus`, `order_adding`, `receipt`) 
        VALUES('$tranno', '$tranno', '$item', '$quantity', '$waiting_time', 'Payment', 'Not Done', '$pendingorder_date', '$kitchen_receipt')";

        mysqli_query($con, $sql1);
    }
    else if($flag_status == "Update"){
        $sql_select_pending = "SELECT * FROM pendingorder_status WHERE tranno = '$tranno' AND items = '$item'";
        $result_pending = mysqli_query($con, $sql_select_pending);

        if(mysqli_num_rows($result_pending) > 0){
            $total_existing_quantity = 0;

            // Loop through existing rows to sum up the quantities
            while ($existing_row = mysqli_fetch_assoc($result_pending)) {
                $total_existing_quantity += $existing_row['quantity'];
            }
            $new_quantity = $quantity - $total_existing_quantity;

            if($new_quantity > 0){
                $sql4 = "INSERT INTO pendingorder_status 
                (`tranno`, `payment_transaction`, `items`, `quantity`, `waiting_time`, `transaction_type`, `pstatus`, `order_adding`, `receipt`) 
                VALUES ('$tranno', '$tranno', '$item', '$new_quantity', '$waiting_time', 'Payment', 'Not Done', '$pendingorder_date', '$kitchen_receipt')";
                $query4 = mysqli_query($con, $sql4);
            }
        }
        else{
            $sql4 = "INSERT INTO pendingorder_status 
                (`tranno`, `payment_transaction`, `items`, `quantity`, `waiting_time`, `transaction_type`, `pstatus`, `order_adding`, `receipt`) 
                VALUES ('$tranno', '$tranno', '$item', '$quantity', '$waiting_time', 'Payment', 'Not Done', '$pendingorder_date', '$kitchen_receipt')";
                $query4 = mysqli_query($con, $sql4);
        }
    }
    
    if(mysqli_query($con, $sql)){
        /**
         * @var {array} $coupon is a 1 dimensional array
         * @var {int} $i incremental count per index in an array
         */

        if(!empty($coupon)){
            for($i = 0; $i < sizeof($coupon); $i++){
                $sql = "INSERT INTO coupon_t (`compcode`, `tranno`, `CouponNo`, `ddate`, `preparedby`) 
                    VALUES ('$company', '$tranno', '{$coupon[$i]}', '$date', '$prepared')";
                mysqli_query($con, $sql);

                mysqli_query($con, "UPDATE coupon SET `status` = 'CLAIMED' WHERE compcode = '$company' AND CouponNo = '{$coupon[$i]}'");
            }
        }

        if(!empty($specialdisc)){
            foreach($specialdisc as $list){
                $items = mysqli_real_escape_string($con, $list['item']);
                $types = mysqli_real_escape_string($con, $list['type']);
                $persons = mysqli_real_escape_string($con, $list['person']);
                $ids = mysqli_real_escape_string($con, $list['id']);
                $amounts = number_format($list['amount'], 2);
                
                $sql = "INSERT INTO specialdiscount_t (`compcode`, `tranno`, `itemno`, `type`, `person`, `personID`, `amount`) 
                VALUES ('$company', '$tranno', '$items', '$types', '$persons', '$ids', '$amounts')";
                mysqli_query($con, $sql);
            }
            
        }

        echo json_encode([
            'valid' => true,
            'print' => $kitchen_receipt,
            'msg' => "Payment Successfully added"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Unsuccessfully Inserted"
        ]);
    }