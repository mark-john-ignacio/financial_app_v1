<?php
ini_set('max_execution_time', 1800); // 300 = 5min

if(!isset($_SESSION)){
session_start();
}
include('Connection/connection_string.php');


$chkSales = mysqli_query($con2,"select * From logfile where year(ddate) = '2018' and month(ddate) = '01' and module in ('PURCHASE ORDER')");

	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		
		
			if (!mysqli_query($con, "INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `module`, `cevent`, `cmachine`, `cremarks`) values('".$row["compcode"]."', '".$row["ctranno"]."', '".$row["cuser"]."', '".$row["ddate"]."', '".$row["module"]."', '".$row["cevent"]."', '".$row["cmachine"]."', '".$row["cremarks"]."')")) {
				
				echo $row["ctranno"]."<br>";
				
				echo mysqli_error($con);
			} 		
		
	}
	
?>