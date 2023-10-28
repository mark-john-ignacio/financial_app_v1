<?php 

    if(!isset($_SESSION)){
        session_start();
    }

    include ('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];
    $page = $_SESSION['pageid'] === "POS_void.php" ? $_SESSION['pageid'] : null;
    $user = mysqli_real_escape_string($con, $_REQUEST['user']);
    $password = mysqli_real_escape_string($con, $_REQUEST['password']);

    $sql = "SELECT a.*, b.pageid FROM users a LEFT JOIN users_access b on a.Userid = b.userid WHERE a.Userid = '$user' AND b.pageid = 'POS_Void.php' ORDER BY a.Userid ASC LIMIT 1";
    // $sql = "SELECT a.* FROM users a WHERE a.Userid = '$user' ORDER BY a.Userid ASC LIMIT 1";
    $query = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($query) != 0){
        $row = $query -> fetch_assoc();
        $hashpassword = $row['password'];

        if(password_verify($password, $hashpassword)){
            echo json_encode([
                'valid' => true,
                'msg' => "Successfully Logged in"
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'msg' => "Password Does not match"
            ]);
        }
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "User ID not found!"
        ]);
    }