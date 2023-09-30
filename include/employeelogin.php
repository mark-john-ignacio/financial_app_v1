<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

//ob_start();
if(!isset($_SESSION)){
session_start();
}
	
include('../Connection/connection_string.php');
require_once('../Model/helper.php');

$employeeid = mysqli_real_escape_string($con,$_REQUEST['employeeid']);
$password = mysqli_real_escape_string($con,$_REQUEST['password']); 
$selcompany = mysqli_real_escape_string($con,$_REQUEST['selcompany']); 
$attempts = mysqli_real_escape_string($con, $_REQUEST['attempts']);

$sql = mysqli_query($con,"select * from users where userid = '$employeeid'");
//echo "select * from users where userid = '$employeeid' and password='$password'";
if(mysqli_num_rows($sql) == 0){
	
	echo "<strong>ERROR!</strong> INVALID USER ID";
	//echo "select * from users where userid = '$employeeid' and password='$password'";
}else{
	
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
		{
			$employee = array(
				'id' => $employeeid,
				'name' => strtoupper($row['Fname']),
				'fullname' => strtoupper($row['Fname']." ".$row['Lname']),
				'password' => $row['password'],
				'status' => $row['cstatus'],
				'modify' => $row['modify']
			);

			$_SESSION['currapikey'] = '4c151e86299e4588939cdbb45a606021'; 
			//$_SESSION['currapikey2'] = '755e85fe16cf42a08c2c59c1ec5bd626'; 

		}

	$id = mysqli_real_escape_string($con, $employee['id']);

		//86400 one day
		

	if(statusAccount($employee['status'])){
		if(password_verify($password, $employee['password'])){
			$_SESSION['employeeid'] = $employee['id'];
			$_SESSION['employeename'] = $employee['name'];
			$_SESSION['employeefull'] = $employee['fullname'];
			$_SESSION['status'] = $employee['status'];

			$_SESSION['companyid'] = $selcompany;
			$_SESSION['timestamp']=time();
			$dateNow = date('Y-m-d h:i:s');
			$ipaddress = getHostByName(getHostName());

			$sql = "SELECT b.logid, b.status, b.machine FROM `users` a
			left join `users_log` b on a.Userid = b.Userid
			WHERE a.Userid = '".$employee['id']."'
			ORDER BY b.logid ASC	";


			$result = mysqli_query($con, $sql);

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$status = $row['status'];
				$machine = $row['machine'];
				$_SESSION['loggedid'] = $row['logid'] ;
			}


			if(validStatus($status)){

				$sql = "INSERT INTO `users_log` (`Userid`, `status`, `machine`, `logged_date`) VALUES ('".$employee['id']."', 'Online', '$ipaddress', '$dateNow')";
				$result = mysqli_query($con, $sql);
				echo json_encode(valid30Days($employee['modify']));

			} else {

				if(validIP($machine)){
					echo json_encode(valid30Days($employee['modify']));
				} else {

					echo json_encode([
						'valid' => false,
						'errCode' => 'ERR_LOG',
						'errMsg' => "Your account was still logged in"
					]);
				}
			}


		} else {
			if(failedAttempt($attempts)){
				
				$sql = "UPDATE `users` SET `cstatus` = 'Deactivate' WHERE `Userid` = '$id'";
				if (!mysqli_query($con, $sql)) {
					echo json_encode([
						'valid' => false,
						'errCode' => 'ERR_MSG',
						'errMsg' => mysqli_error($con)
					]);
				} 
				$result = mysqli_query($con, $sql);
			}
			
			echo json_encode([
				'valid' => false,
				'errCode' => "INV_PASS",
				'errMsg' => "INVALID PASSWORD"
			]);
		}
	} else {
		echo json_encode([
				'valid' => false,
				'errCode' => 'ACC_DIS',
				'errMsg' => "Your account has been disabled!"
			]);
	}
}

?>