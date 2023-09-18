<?php 
     if(!isset($_SESSION)){
        session_start();
    }

    include('../Connection/connection_string.php');
    require_once('../Model/helper.php');


    $id = mysqli_real_escape_string($con, $_POST['id']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $new = mysqli_real_escape_string($con, $_POST['newpassword']);
    $confirm = mysqli_real_escape_string($con, $_POST['confirmPassword']);

    $sql = "SELECT * FROM `users` WHERE Userid = '$id'";

    $result = mysqli_query($con, $sql);
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){;
        $current = $row['password'];
    }

    if(match_password($new, $confirm)){
        if(password_verify($password, $current)){
            $haspassword = better_crypt($password);
            $date = date('Y-m-d');
            $sql = "UPDATE `users` SET `password` = '$haspassword', `modify` = '$date'  WHERE Userid = '$id'";
            if(!mysqli_query($con, $sql)){
                echo json_encode([
                    'valid' => false,
                    'errCode' => 'ERR_MSG',
                    'errMsg' => $mysql_error($con) 
                ]);
            }
    
            echo json_encode([
                'valid' => true,
                'msg' => 'Update has been successful!'
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'errCode' => 'NOT_MATCH!',
                'errMsg' => 'Incorrect Password!'
            ]);
        }
    } else {
        echo json_encode([
            'valid' => false,
            'errCode' => 'NOT_MATCH',
            'errMsg' => 'Password Not Match'
        ]);
    }
?>