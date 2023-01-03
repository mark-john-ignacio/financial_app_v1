<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];

	$result = mysqli_query ($con, "Select * From pos_cutoff where compcode='$company'"); 
		
			while($rowgrp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					$json['dyfr1'] = $rowgrp['dayfrom1'];
					$json['dyto1'] = $rowgrp['dayto1'];
					$json['dyfr2'] = $rowgrp['dayfrom2'];
					$json['dyto2'] = $rowgrp['dayto2'];
					$json2[] = $json;

		
			}
			
				echo json_encode($json2);




?>
