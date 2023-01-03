<?php

if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

			$sql = "Select * From customers";
				
				$sqlhead=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				if (mysqli_num_rows($sqlhead)!=0) {
					while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){




						if (file_exists("../imgcust/" . $row["cempid"] . ".jpg")) {
							
							mysqli_query($con,"UPDATE customers set cuserpic = '../imgcust/" . $row["cempid"] . ".jpg' where cempid='".$row["cempid"]."'");
							
						}
						
						
					}
				}

?>
