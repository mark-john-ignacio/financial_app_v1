<?php
include('../Connection/connection_string.php');
 
	$ctyp = $_REQUEST['xz']; 
	$cUserID = $_REQUEST['emp'];
	
		 
	$sql = "update users set `cstatus`= '$ctyp' where Userid='$cUserID'";

		if (!mysqli_query($con, $sql)) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

echo '<script language="javascript"> 
alert("User\'s Status changed to: '.$ctyp.'") 
location.href="users.php?f=" 
</script>';

?>
