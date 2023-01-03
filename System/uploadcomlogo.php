<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	if(isset($_FILES['file']['name'])){
	  
	   /* Getting file name */
	   $filename = $_FILES['file']['name'];

	   /* Location */
	   $location = "../images/".$filename;
	   $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
	   $imageFileType = strtolower($imageFileType);

	   /* Valid extensions */
	   $valid_extensions = array("jpg","jpeg","png");

	   $response = 0;
	   /* Check file extension */
	   if(in_array(strtolower($imageFileType), $valid_extensions)) {
	      /* Upload file */
	      if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
	         $response = $location;
	      }

	      mysqli_query($con,"UPDATE company set `clogoname` = '$location'");
	   }

	   echo $response;
	   exit;
	}

echo 0;


?>
