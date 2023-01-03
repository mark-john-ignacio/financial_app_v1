<?php

if (isset($_SESSION['timestamp'])) {

	$autologout=3600; //after 15 minutes of inactivity the user gets logged out
	$lastactive = $_SESSION['timestamp']; // Use of 'Null Coalescing Operator' - pulls the timestamp or sets it to 0.

	if (time()-$lastactive>$autologout){
				$_SESSION = array();                   // Clear the session data
				setcookie(session_name(), false, time()-3600);     // Clear the cookie
				session_destroy();                         // Destroy the session data
				
				//header('Location: http://'.$_SERVER['HTTP_HOST'].'/denied.php');
				//echo "https://".$_SERVER['HTTP_HOST']."/MyxFin/include/denied.php";
				echo "<script>top.location='https://".$_SERVER['HTTP_HOST']."/denied.php'</script>";
	}else { 
				$_SESSION['timestamp']=time();              //Or reset the timestamp
	}
}else{
	echo "<script>top.location='https://".$_SERVER['HTTP_HOST']."/denied.php'</script>";
}



//if ($_SESSION['employeeid'] == "") {
//	 header('Location: http://'.$_SERVER['HTTP_HOST'].'/CoopFin/denied.php');
//}
?>
