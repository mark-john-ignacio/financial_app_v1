<?php
if ($_SESSION['employeeid'] == "") {
	header('Location: ../../denied.php');
}
else{
	//check user access level sa page
	$employeeid = $_SESSION['employeeid'];
	$pageid = $_SESSION['pageid'];
	
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = '$pageid'");

	if(mysqli_num_rows($sql) == 0){
	
		header('Location: ../../include/deny.php');
	}
	
	
}
?>
