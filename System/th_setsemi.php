<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$colnme = $_REQUEST['col'];
	$colval = $_REQUEST['val'];


	if(!mysqli_query ($con, "Update pos_cutoff set ".$colnme."='$colval', lastupdate=NOW() where compcode='$company'")){
		echo "False";
	}
	else{
		echo "True";
	}
		




?>
