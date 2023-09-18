<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

//ob_start();
if(!isset($_SESSION)){
session_start();
}
	
include('../Connection/connection_string.php');

$employeeid = mysqli_real_escape_string($con,$_REQUEST['employeeid']);
$password = mysqli_real_escape_string($con,$_REQUEST['password']); 
$selcompany = mysqli_real_escape_string($con,$_REQUEST['selcompany']); 

$sql = mysqli_query($con,"select * from users where userid = '$employeeid'");
//echo "select * from users where userid = '$employeeid' and password='$password'";
if(mysqli_num_rows($sql) == 0){
	
	echo "<strong>ERROR!</strong> INVALID USER ID";
	//echo "select * from users where userid = '$employeeid' and password='$password'";
}else{
	
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
		{
			$_SESSION['employeeid'] = $employeeid;
			$_SESSION['employeename'] = strtoupper($row['Fname']);
			$_SESSION['employeefull'] = strtoupper($row['Fname']." ".$row['Lname']);
			
			$_SESSION['companyid'] = $selcompany;

			$_SESSION['timestamp']=time();
			
			$password_hash = $row['password'];

			$_SESSION['currapikey'] = '4c151e86299e4588939cdbb45a606021'; 
			//$_SESSION['currapikey2'] = '755e85fe16cf42a08c2c59c1ec5bd626'; 

		}

	if(password_verify($password, $password_hash)) { // password is correct
			
			
			echo true;
			
	
	}
	else{
		echo "<strong>ERROR!</strong> INVALID PASSWORD";
	}



}

?>