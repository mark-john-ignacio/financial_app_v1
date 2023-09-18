<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../Connection/connection_string.php');
    require_once('../Model/helper.php');

    $id = $_SESSION['employeeid'];

    $password = mysqli_real_escape_string($con, $_POST['password']);
    $new = mysqli_real_escape_string($con, $_POST['newpassword']);


    $sql = "SELECT  * FROM `users` WHERE Userid = '$id' ";
    $result = mysqli_query($con, $sql);
    if(!mysqli_query($con, $sql)){
        printf("Errormessage: %s\n", mysqli_error($con));
    }
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $current = $row['password'];
        $modifyDate = $row['modify'];
    }
    
    if(password_verify($password, $current)){
        $hashpassword = better_crypt($new);
        $date30 = date('Y-m-d', strtotime($modifyDate.'+30days'));
        $dateNow = date('Y-m-d');
            
        //$sql = "UPDATE `users` SET `password` = '$hashpassword', `modify` = '$dateNow' WHERE `Userid` = '$id' AND  `modify` NOT BETWEEN '$dateNow' AND '$date30'";

        if( $dateNow > @$modifyDate || $dateNow < @$date30 || @$modifyDate == null){
            $sqls = "update `users` set `password`= '$hashpassword', `modify` = '$dateNow' where Userid='$id' ";
            $result = mysqli_query($con, $sqls);
            if(mysqli_query($con, $sqls)){
                echo json_encode([ 
                    'valid' => true,
                    'msg' => "Update has been Success!"
                ]);
            }   

        } else {
            echo json_encode([
                'valid' => false,
                'errCode' => 'INV_DATE',
                'errMsg' => "<strong>Error!</strong> Changing password should be at $date30"
            ]);
        }  
                
    } else {
        echo json_encode([
            'valid' => false,
            'errCode' => 'INV_PASS',
            'errMsg' => "<strong>ERROR!</strong> INVALID PASSWORD"
        ]);
    }
?>