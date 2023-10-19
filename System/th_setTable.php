<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");

    $company = $_SESSION['companyid'];

    $table = json_decode($_POST['tables'], true);
    $remarks = json_decode($_POST['remarks'], true);
    $id = json_decode($_POST['id'], true);

    for($i = 0; $i < sizeof($table); $i++){
        if($table[$i] != ""){
            $sql = "SELECT * FROM pos_grouping WHERE `compcode` = '$company' and `type` = 'TABLE' and `code` = '".$table[$i]."'";
            $query = mysqli_query($con, $sql);
            if(mysqli_num_rows($query) != 0) {
                $sql = "UPDATE pos_grouping SET `code` = '".$table[$i]."', `remarks` ='".$remarks[$i]."' WHERE `compcode` = '$company' and `id` = '".$id[$i]."'";
                $query = mysqli_query($con, $sql);
                echo json_encode([
                    'valid' => true,
                    'msg' => "Update Success"
                ]);
    
            } else {
                $sql = "INSERT INTO pos_grouping (`compcode`, `code`, `remarks`, `type`, `status`) VALUES('$company', '".$table[$i]."', '".$remarks[$i]."', 'TABLE', '0')";
                
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