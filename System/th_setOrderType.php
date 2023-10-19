<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");

    $company = $_SESSION['companyid'];

    $orderType = json_decode($_POST['order'], true);
    $remarks = json_decode($_POST['remarks'], true);
    $id = json_decode($_POST['id'], true);

    for($i = 0; $i < sizeof($orderType); $i++){
        if($orderType[$i] != ""){
            $sql = "SELECT * FROM pos_grouping WHERE `compcode` = '$company' and `type` = 'ORDER' and `code` = '".$orderType[$i]."'";
            $query = mysqli_query($con, $sql);
            if(mysqli_num_rows($query) != 0) {
                $sql = "UPDATE pos_grouping SET `code` = '".$orderType[$i]."', `remarks` ='".$remarks[$i]."' WHERE `compcode` = '$company' and `id` = '".$id[$i]."'";
                $query = mysqli_query($con, $sql);
                echo json_encode([
                    'valid' => true,
                    'msg' => "Success"
                ]);
            }else {
                $sql = "INSERT INTO pos_grouping (`compcode`, `code`, `remarks`, `type`, `status`) VALUES('$company', '".$orderType[$i]."', '".$remarks[$i]."', 'ORDER', '0')";
                
                if(mysqli_query($con, $sql)){
                    echo json_encode([
                        'valid' => true,
                        'msg' => "Success"
                    ]);
                } else {
                    
                    echo json_encode([
                        'valid' => false,
                        'msg' => "Not Success"
                    ]);
                }
            }
        }
    }
