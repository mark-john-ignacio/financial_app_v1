<?php 
     if(!isset($_SESSION)){
        session_start();
    }

    include('../Connection/connection_string.php');
    require_once('../Model/helper.php');


    $id = $_POST['id'];
    $password = $_POST['password'];
    $new = $_POST['newpassword'];
    $confirm = $_POST['confirmPassword'];

    $sql = "SELECT * FROM `users` WHERE Userid = '$id'";

    $result = mysqli_query($con, $sql);
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){;
        $current = $row['password'];
        $usertype = $row['usertype'];
    }

    if(match_password($new, $confirm)){
        if(password_verify($password, $current)){
            
            $hashpassword = better_crypt($new);
            $date = date('Y-m-d');
            $sql = "update `users` set `password`='$hashpassword', `modify`='$date', `cstatus` = 'Active' where Userid = '$id'";
    
            if(mysqli_query($con, $sql)){
                $_SESSION['login'] = true;
                echo json_encode([
                    'valid' => true,
                    'msg' => 'Update has been successful!',
                    'usertype' => $usertype
                ]);
            } else {
                echo json_encode([
                    'valid' => false,
                    'errCode' => 'ERR_MSG',
                    'errMsg' => $mysql_error($con) 
                ]);
            }
    
           
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