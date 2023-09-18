<?php

if(!isset($_SESSION)){
    session_start();
}

include('../Connection/connection_string.php');

$userid = $_SESSION['employeeid'];
// $sql = "SELECT * FROM `users` WHERE Userid = '$userid'";

// if($result = mysqli_query($con, $sql)){
//     while($row = mysqli_fetch_array($result, $sql)){
//         $password = $row['password'];
//     };

//     if(password_verify('Password', $password)){
//         echo "You have a need to reset a password";
//         header('Location: ChangePass.php');
//     }
// }

$id = $_POST['id'];
$sql = "SELECT * FROM `users` WHERE `Userid` = '$id' ";
		$result = mysqli_query($con, $sql);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            @$modify = $row['modify'];
            @$endModify = date('Y-m-d', strtotime($row['modify'].'+30days'));
            $dateNow = date('Y-m-d');
            $password = $row['password'];
        } 
		
		
		

        if(password_verify('Password', $password) ){
                echo $dateNow < @$modify || $dateNow > @$endModify || $modify == null;
            // header("Location: MasterFiles/ChangePass.php");

            
        } else {
            echo false;
        }
		
?>