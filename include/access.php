<?php

if (isset($_SESSION['timestamp'])) {

	$autologout=3600; //after 15 minutes of inactivity the user gets logged out
	$lastactive = $_SESSION['timestamp']; // Use of 'Null Coalescing Operator' - pulls the timestamp or sets it to 0.


	//echo $autologout.":".time()-$lastactive;
	if (time()-$lastactive>$autologout){
			$_SESSION = array();                   // Clear the session data
				setcookie(session_name(), false, time()-3600);     // Clear the cookie
				session_destroy();                         // Destroy the session data
				
				echo "<script>top.location='http://".$_SERVER['HTTP_HOST']."/denied.php'</script>";
				
				// header('Location: http://'.$_SERVER['HTTP_HOST'].'/MyxFin/denied.php');
	}else { 
			$_SESSION['timestamp']=time();              //Or reset the timestamp
			
				//check user access level sa page
			$employeeid = $_SESSION['employeeid'];
			$pageid = $_SESSION['pageid'];
			
			$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = '$pageid'");
		
			if(mysqli_num_rows($sql) == 0){
			
				//echo "<script>top.location='http://".$_SERVER['HTTP_HOST']."/deny.php'<//script>";
				
				header('Location: http://'.$_SERVER['HTTP_HOST'].'/include/deny.php');
			}

	}
}
else{
	echo "<script>top.location='http://".$_SERVER['HTTP_HOST']."/denied.php'</script>";
}
	?>
