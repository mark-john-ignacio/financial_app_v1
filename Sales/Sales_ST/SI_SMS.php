<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];

	$sqlcp = "select * from company where compcode='$company'";
	$resultcp = mysqli_query ($con, $sqlcp); 
	if(mysqli_num_rows($resultcp)!=0){
		while($rowcp = mysqli_fetch_array($resultcp, MYSQLI_ASSOC)){
			
			$rowcpnum = $rowcp['cpnum'];
			
		}
	}

		
		//SEND TEXT SMS
		
		$sql = "select X.citemno as cpartno
		from sales_t X
		where X.compcode='$company' and X.ctranno = '$tranno' Order By X.nidentity";
		
		$resultmain = mysqli_query ($con, $sql); 
		
		while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
			
			$output = shell_exec('START C:\xampp\htdocs\MyxFin\SMSCaster\smscaster.exe -Compose '.$rowcpnum.' "'.$row2['cpartno'].'" -Queue');
			
		}
		
		echo "True";

?>