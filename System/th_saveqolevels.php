<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$isokall = "True";
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	$company = $_SESSION['companyid'];

	mysqli_query($con,"UPDATE quote_approvals_id set compcode='".date('m/d/Y_H:i:s')."' where compcode='$company'");


	$result = mysqli_query ($con, "Select * From quote_approvals where compcode='$company'"); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
		$lvlnum = $row['nlevel'];

		$x1 = intval($_POST['tbLQL'.$lvlnum.'count']);

		if($x1>0){
			for ($x = 1; $x <= $x1; $x++) {
				$userdid1 = $_POST['selqosuser'.$lvlnum.$x];

				if(isset($_POST['selqoitmtyp'.$lvlnum.$x])){
					$itmtypd1 =  ($_POST['selqoitmtyp'.$lvlnum.$x]!=="") ? implode(",",$_POST['selqoitmtyp'.$lvlnum.$x]) : "";
				}else{
					$itmtypd1 = "";
				}

				if(isset($_POST['selqosutyp'.$lvlnum.$x])){
					$supptyp1 =  ($_POST['selqosutyp'.$lvlnum.$x]!=="") ? implode(",",$_POST['selqosutyp'.$lvlnum.$x]) : "";
				}else{
					$supptyp1 = "";
				}
				
				$signtyp1 =  (isset($_POST['qoqofilsign'.$lvlnum.$x]) ? $_POST['qoqofilsign'.$lvlnum.$x] : "");

				$sql = "INSERT INTO quote_approvals_id (`compcode`,`qo_approval_id`,`userid`,`items`,`suppliers`) values ('$company','$lvlnum','$userdid1','$itmtypd1','$supptyp1')";

				if ($con->query($sql) === TRUE) {
					$last_id = $con->insert_id;

					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$userdid1','$preparedby',NOW(),'UPDATED','QUOTE APPROVALS $lvlnum','$compname','Update Record')");
				
					if($_FILES["qofilsign".$lvlnum.$x]["name"]!="")
					{
						$validextensions = array("jpeg", "jpg", "png");
						$temporary = explode(".", $_FILES["qoqofilsign".$lvlnum.$x]["name"]);
						$file_extension = end($temporary);
					
						if ((($_FILES["qofilsign".$lvlnum.$x]["type"] == "image/png") || ($_FILES["qoqofilsign".$lvlnum.$x]["type"] == "image/jpg") || ($_FILES["qofilsign".$lvlnum.$x]["type"] == "image/jpeg")
						) && ($_FILES["qofilsign".$lvlnum.$x]["size"] < 100000)//Approx. 100kb files can be uploaded.
						&& in_array($file_extension, $validextensions)) {
							if ($_FILES["qofilsign".$lvlnum.$x]["error"] > 0)
							{
								echo "\nReturn Code: " . $_FILES["qofilsign".$lvlnum.$x]["error"];
							}
							else
							{
								if (file_exists("../imgsigns/" . $_FILES["qofilsign".$lvlnum.$x]["name"])) {
									unlink ("../imgsigns/" . $_FILES["qofilsign".$lvlnum.$x]["name"]);
								}
								
									$sourcePath = $_FILES['qofilsign'.$lvlnum.$x]['tmp_name']; // Storing source path of the file in a variable
									$targetPath = "../imgsigns/".$_FILES['qofilsign'.$lvlnum.$x]['name']; // Target path where file is to be stored
									
									$newtargetPath = "../imgsigns/".$last_id.".".$file_extension;
									move_uploaded_file($sourcePath,$newtargetPath) ; // Moving Uploaded file
									//echo "\nImage Uploaded Successfully...!!";
									//echo "\nFile Name: " . $newtargetPath;
									//echo "\nFile Type: " . $_FILES["qofilsign".$lvlnum.$x]["type"];
									//echo "\nFile Size: " . ($_FILES["qofilsign".$lvlnum.$x]["size"] / 1024) . " kB";
					
					
									//update file name in users table
									if (!mysqli_query($con, "UPDATE quote_approvals_id set sign = '$newtargetPath' where id = '$last_id'")) {
										printf("Errormessage: %s\n", mysqli_error($con));
									}
								
							}
						}
						else
						{
							echo "\n***Invalid file Size or Type***";
						}
					}

				} 
				else{

					//printf("Errormessage: %s\n", mysqli_error($con));

					$isokall = "False";
											
				}

			}
		}

	}

if($isokall=="True"){
	?>
	<script>
		alert("Quote Approvals Successfully Saved!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}else{
	?>
	<script>
		alert("Quote Approvals has error saving!");
		window.location.replace("https://<?=$_SERVER['HTTP_HOST']?>/System");
	</script>
	<?php
}

?>
