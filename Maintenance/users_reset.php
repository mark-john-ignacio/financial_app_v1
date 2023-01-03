<?php
include('../Connection/connection_string.php');
function better_crypt($input, $rounds = 10) { 

	$crypt_options = array( 'cost' => $rounds ); 
	return password_hash($input, PASSWORD_BCRYPT, $crypt_options); 

}

 
	$cUserID = $_REQUEST['empid'];
	$cPass = 'Password';
	
	$password_hash = better_crypt($cPass);
		 
	$sql = "update users set `password`= '$password_hash' where Userid='$cUserID'";

		if (!mysqli_query($con, $sql)) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

echo '<script language="javascript">
alert("User\'s Password is reset to: password")
location.href="users.php?f="
</script>';

?>
