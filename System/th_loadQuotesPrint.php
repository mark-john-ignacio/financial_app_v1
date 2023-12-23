<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


$company = $_SESSION['companyid'];

    $result = mysqli_query($con,"SELECT * FROM `parameters` where compcode='$company' and ccode in ('QUOTEHDR','QUOTEFTR','POEMAILBODY', 'QUOTE_RMKS', 'QUOTE_BILLING')"); 
            
    if (mysqli_num_rows($result)!=0) {
      while($comprow = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                 
           $json['ccode'] = $comprow['ccode']; 
           $json['cdesc'] = $comprow['cdesc']; 
		       $json2[] = $json;

      }
                    
     }
     else{
                     
           $json['ccode'] = ""; 
           $json['cdesc'] = ""; 
                     
     }

	echo json_encode($json2);




?>
