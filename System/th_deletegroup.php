<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");

	$company = $_SESSION['companyid'];
    $id = mysqli_real_escape_string($con, $_REQUEST['id']);
    
    $sql = "SELECT * FROM pos_grouping WHERE `compcode` = '$company' and `id` = '$id'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $sql = "DELETE FROM pos_grouping WHERE `compcode` = '$company' and `id` = '$id'";
        if(mysqli_query($con, $sql)){
            echo json_encode([
                'valid' => true,
                'msg' => "Successfully Deleted"
            ]);
        } else {
            echo json_encode([
                'valid' => true,
                'msg' => "Unsuccessfully Deleted"
            ]);
        }

    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Does not have a record!"
        ]);
    }