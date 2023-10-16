<?php
require_once "../Connection/connection_string.php";


	$sql = "DELETE from ageing_days WHERE id = ".$_POST['n_id']; 

	if ($con->query($sql) === TRUE) {
		echo "True";
	} else {
		echo "Error deleting record: " . $con->error;
	}

?>
