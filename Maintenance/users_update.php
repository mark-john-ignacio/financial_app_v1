<?php
include('../Connection/connection_string.php');

	$cUserID = $_POST['userid'];
	$cFName = $_POST['Fname'];
	$cMI = $_POST['Mname'];
	$cLName = $_POST['Lname']; 
	$cPass = $_POST['passT']; 
	$cEmail = $_POST['emailadd'];
	
	
	$chkID = mysqli_query($con,"select * from users where UserID = '$cUserID'");
	 
	 
	if (!mysqli_query($con, "UPDATE users set Fname = '$cFName', LName = '$cLName', Minit = '$cMI', cemailadd = '$cEmail' where Userid = '$cUserID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}
	else{
		echo "USER DETAILS:\nUser's details successfully updated!\n\nUSER IMAGE:";
	}



//For Uploading photo
if($_FILES["file"]["name"]!="")
{
$validextensions = array("jpeg", "jpg", "png");
$temporary = explode(".", $_FILES["file"]["name"]);
$file_extension = end($temporary);

	if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
	) && ($_FILES["file"]["size"] < 100000)//Approx. 100kb files can be uploaded.
	&& in_array($file_extension, $validextensions)) {
		if ($_FILES["file"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
		}
		else
		{
			if (file_exists("../imgusers/" . $_FILES["file"]["name"])) {
				unlink ("../imgusers/" . $_FILES["file"]["name"]);
			}
			
				$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				$targetPath = "../imgusers/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$newtargetPath = "../imgusers/".$cUserID.".".$file_extension; // to rename the image to userid
				move_uploaded_file($sourcePath,$newtargetPath) ; // Moving Uploaded file
				echo "\nImage Uploaded Successfully...!!";
				echo "\nFile Name: " . $newtargetPath;
				echo "\nFile Type: " . $_FILES["file"]["type"];
				echo "\nFile Size: " . ($_FILES["file"]["size"] / 1024) . " kB";

				//update file name in users table
				if (!mysqli_query($con, "UPDATE users set cuserpic = '$newtargetPath' where Userid = '$cUserID'")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				}

			
		}
	}
	else
	{
		echo "\n***Invalid file Size or Type***";
	}
}
else {
	echo "\nNO image file detected!";
}
					
?>
