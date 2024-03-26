<?php
if(!isset($_SESSION)){
session_start();
}
include('Connection/connection_string.php');
include('Model/helper.php');

$id = mysqli_real_escape_string($con, $_SESSION['employeeid']);
$ipaddress = getHostByName(getHostName());
$hashedIP = better_crypt($ipaddress);

// $ipaddress = gethostbyaddr($_SERVER['REMOTE_ADDR']);

//getting the reason from autologout and compare
$logoutReason = isset($_GET['logout_reason']) ? $_GET['logout_reason'] : '';

if ($logoutReason === 'inactivity') {
    $status = 'Auto-Logout';
} else{
    $status = 'Offline';
}

//$sql = "UPDATE `users_log` SET `status` = 'Offline', logout_date = '$dateNow' WHERE logid = '".$loggedid."' and `Userid` = '$id' ";
//make the logged_date to now for military time avoid confusion
$sql = "INSERT INTO `users_log` (`Userid`, `status`, `machine`, `logged_date`) 
        VALUES ('$id', '$status', '$hashedIP', NOW())";
$result = mysqli_query($con, $sql);

//set the sessionID to 0 to enable to log again
$sqlSession = "UPDATE users SET session_ID = 0 WHERE Userid = '$id'";
$result2 = mysqli_query($con, $sqlSession);



session_unset();
session_destroy();

header('Location: index.php');  
?>