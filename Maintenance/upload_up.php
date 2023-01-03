<?php

if(!isset($_SESSION)){
session_start();
}

$target_dir = "../imgemps/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

//echo $target_fileNew.".".$imageFileType;
$target_fileNew = $target_dir . $_SESSION['picid'] . "." . $imageFileType;

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
	unlink ($target_file);
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if(strtolower($imageFileType) != "jpg") {
    echo $imageFileType." FILE: "."Sorry, only JPG files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_fileNew )) {
        echo "The file ". $_SESSION['picid'] . "." . $imageFileType . " has been uploaded.";
		
		echo "<script type='text/javascript'>window.opener.location.reload(); window.close(); e.preventDefault();</script>";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?> 