<?php
include('../Connection/connection_string.php');


$employeeid = $_REQUEST['id'];
//$password = $_REQUEST['xpass'];

$sql = mysqli_query($con,"select * from users where Userid = '$employeeid' and cstatus <> 'Inactive'");
//echo "select * from users where userid = '$employeeid' and password='$password'";
if(mysqli_num_rows($sql) == 0){
	
	echo "<strong>ERROR!</strong> USER ID ($employeeid) DID NOT EXIST!";
	
	//echo "select * from users where userid = '$employeeid' and password='$password'";
}else{
	
	echo true;	
}

?>