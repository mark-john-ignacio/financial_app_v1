<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];
    $items = json_decode($_REQUEST['item']);
    $unit = json_decode($_REQUEST['unit']);
    $discount = json_decode($_REQUEST['discount']);
    $type = json_decode($_REQUEST['types']);

    $flag = false;

    for($i = 0; $i < sizeof($discount); $i++){
        $sql = "INSERT INTO discountmatrix_t (`compcode`, `tranno`, `itemno`, `unit`, `discount`, `type`) VALUES ('$company', '$tranno', '{$items[$i]}', '{$unit[$i]}', '{$discount[$i]}', '{$type[$i]}')";
       if (mysqli_query($con, $sql)) {
            $flag = true;
        } else {
            $flag = false;
            printf("Errormessage: %s\n", mysqli_error($con));
        }

    }
    
    if($flag){
        echo json_encode([
            'valid' => true,
            'msg' => "Discount has been succesfully save!"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Discount has been Unsuccesfully save!"
        ]);
    }
   