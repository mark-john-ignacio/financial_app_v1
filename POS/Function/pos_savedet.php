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

    $discount = mysqli_real_escape_string($con, $_POST['discount']);
    $validID = mysqli_real_escape_string($con, $_POST['discountID']);
    $person = mysqli_real_escape_string($con, $_POST['discountName']);

    $coupon = json_decode($_POST['coupon'], true);
    $specialdisc = json_decode($_POST['specialdisc'], true);
    $date = date("Y-m-d");

    $net =  floatval($amount) / floatval(1 + (12/100));
    $vat = $net * (12/100);
    $price = $amount / $quantity;

    $sql = "INSERT INTO pos_t (`compcode`, `tranno`, `item`, `uom`, `quantity`, `amount`, `net`, `vat`, `gross`) 
        VALUES('$company', '$tranno', '$item', '$unit', '$quantity', '$price', '$net', '$vat', '$amount')";
    
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
            'msg' => "Payment Successfully added"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Unsuccessfully Inserted"
        ]);
    }