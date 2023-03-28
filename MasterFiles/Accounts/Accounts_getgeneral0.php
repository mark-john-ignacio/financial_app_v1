<?php
include('../Connection/connection_string.php');


	$result = mysqli_query ($con, "select * from accounts WHERE ccategory = '".$_POST['Id']."' and ctype='General' order by cacctid"); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		

	     $json['id'] = $row['cacctno'];
		 $json['value'] = $row['cacctdesc'];
		 $json['maina'] = $row['mainacct'];
		 $json2[] = $json;

	}


	echo json_encode($json2);
					
?>
