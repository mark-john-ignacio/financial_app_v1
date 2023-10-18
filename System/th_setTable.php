<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");

    $company = $_SESSION['companyid'];

    $table = json_decode($_POST['tables'], true);
    $remarks = json_decode($_POST['remarks'], true);

    for($i = 0; $i < sizeof($table); $i++){
        if($table[$i] != ""){
            $sql = "SELECT * FROM pos_grouping WHERE compcode = '$company' and code = '".$table[$i]."'";
            $query = mysqli_query($con, $sql);
            if(mysqli_num_rows($query) != 0) {
                $sql = "UPDATE pos_grouping `code` = '".$table[$i]."', `remarks` ='".$remarks[$i]."' ";
                $query = mysqli_query($con, $sql);
                echo json_encode([
                    'valid' => true,
                    'msg' => "Success"
                ]);
    
            } else {
                $sql = "INSERT INTO pos_grouping (`compcode`, `code`, `remarks`, `status`) VALUES('$company', '".$table[$i]."', '".$remarks[$i]."', '0')";
                
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