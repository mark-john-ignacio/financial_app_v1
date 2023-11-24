<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
		
		$sql = "select cpartno from items WHERE compcode='$company' and cscancode = '".$_POST['q']."'";

//	echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	if(mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){


			 echo $row['cpartno'];
		}

	}

?>
