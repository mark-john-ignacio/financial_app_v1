<?php
if(!isset($_SESSION)){
session_start();
}
include('Connection/connection_string.php');

$id = mysqli_real_escape_string($con, $_SESSION['employeeid']);
$dateNow = date('Y-m-d h:i:s'); 
$ipaddress = getHostByName(getHostName());

//$sql = "UPDATE `users_log` SET `status` = 'Offline', logout_date = '$dateNow' WHERE logid = '".$loggedid."' and `Userid` = '$id' ";
$sql = "INSERT INTO `users_log` (`Userid`, `status`, `machine`, `logged_date`) VALUES ('$id', 'Offline', '$ipaddress', '$dateNow')";

$result = mysqli_query($con, $sql);

session_unset();
session_destroy();



header('Location: index.php');  
?>