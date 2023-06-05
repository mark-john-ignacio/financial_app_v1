<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');


	if($_REQUEST['xval']==$_SESSION['myxtoken']){

		$company = $_SESSION['companyid'];
		$id = mysqli_real_escape_string($con,$_REQUEST['id']);
		$pass = mysqli_real_escape_string($con,$_REQUEST['pass']);
		
		//chk kung meron sa may access ung username na enter
		$sql = mysqli_query($con,"Select * from users_access where pageid='check_override' and userid='$id'"); 
		if(mysqli_num_rows($sql) == 0){
			echo "No Access!";
		}else{

			$sql = mysqli_query($con,"select * from users where userid = '$id'");
			if(mysqli_num_rows($sql) == 0){
				
				echo "Username not existing!";

			}else{

				while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
				{
					$password_hash = $row['password'];
				}
				
				if(password_verify($pass, $password_hash)) { // password is correct
												
					echo "True";
									
				}
				else{
					echo "Password Error!";
				}

			}

		}

	}else{

		echo "Token Error!";
			
	}
	
?>
