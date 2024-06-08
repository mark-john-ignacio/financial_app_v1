<?php
echo "<center>";
if($_FILES["file"]["name"]!="")
{
	$validextensions = array("xlsx");
	$temporary = explode(".", $_FILES["file"]["name"]);
	$file_extension = end($temporary);

	$cFileID = date("YmdHis");


	if (in_array($file_extension, $validextensions)) {
		if ($_FILES["file"]["error"] > 0)
		{
			echo "<br>Return Code: " . $_FILES["file"]["error"];
		}
		else
		{
			if (file_exists("../bom_uploads/" . $_FILES["file"]["name"])) {
				unlink ("../bom_uploads/" . $_FILES["file"]["name"]);
			}
			
				$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				$targetPath = "../bom_uploads/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$newtarz = "\bom_uploads\\".$cFileID.".".$file_extension;

				$newtargetPath= "../bom_uploads/".$cFileID.".".$file_extension;

				move_uploaded_file($sourcePath,$newtargetPath) ; // Moving Uploaded file
				echo "<br>File Uploaded Successfully...!!";
				echo "<br>File Name: " . $newtargetPath;
				echo "<br>File Type: " . $_FILES["file"]["type"];
				echo "<br>File Size: " . ($_FILES["file"]["size"] / 1024) . " kB";
			
				header('Location: upload_check.php?id='.$newtargetPath.'&xcitm='.$_REQUEST['xcitemno'].'&xcvers='.$_REQUEST['selver']);
		}
	}
	else
	{
		echo "<br>***Invalid file Size or Type***";
	}
}
else {
	echo "<br>NO EXCEL file detected!";
}

echo "</center>";
?>