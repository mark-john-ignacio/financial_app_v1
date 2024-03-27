<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);


// Start session if not started
if (!isset($_SESSION)) {
    session_start();
}

include('../Connection/connection_string.php');
require_once('../Model/helper.php');

//creating an array for response
$response = array();

//check if the request is from on load for auto login after closing the browser
if (isset($_REQUEST['from_window_load'])) {
    $employeeid = mysqli_real_escape_string($con, $_REQUEST['employeeid']);
    $session_id = mysqli_real_escape_string($con, $_REQUEST['session_id']);
    $companyid = mysqli_real_escape_string($con, $_REQUEST['companyid']);

    // Check if employee ID exists, session ID matches
    $sql = mysqli_query($con, "select * FROM users where userid = '$employeeid' AND session_ID = '$session_id' LIMIT 1");
	
    // Fetch SQL results
    $sql_results = mysqli_fetch_assoc($sql);

    if (mysqli_num_rows($sql) > 0) {
        $_SESSION['companyid'] = $companyid;
        $_SESSION['employeeid'] = $employeeid;
        $_SESSION['session_id'] = $session_id;
		

		//response for the json
        $response = array(
            'success' => true,
            'redirect_url' => 'main.php',
            'sql_results' => $sql_results
        );

    } else {
        // If no match, include SQL results and error message
        $message = "Invalid credentials for Employee ID: $employeeid, Session ID: $session_id, Company ID: $companyid";
        $response = array(
            'success' => false,
            'message' => $message,
            'sql_results' => $sql_results // Include SQL results in the response
        );
    }
    echo json_encode($response);
}
//if the login button is clicked
 else {

    // Proceed with regular login process
    $employeeid = mysqli_real_escape_string($con, $_REQUEST['employeeid']);
    $password = mysqli_real_escape_string($con, $_REQUEST['password']);
    $selcompany = mysqli_real_escape_string($con, $_REQUEST['selcompany']);
    $attempts = mysqli_real_escape_string($con, $_REQUEST['attempts']);




$sql = mysqli_query($con,"select * from users where userid = '$employeeid' LIMIT 1");
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
				'modify' => $row['modify'],
				'usertype' => $row['usertype'],
				'session_ID' => $row['session_ID']
				
			);

			$_SESSION['currapikey'] = '4c151e86299e4588939cdbb45a606021'; 
			//$_SESSION['currapikey2'] = '755e85fe16cf42a08c2c59c1ec5bd626'; 
		}

	$id = mysqli_real_escape_string($con, $employee['id']);

		//86400 one day
	
				

	if(statusAccount($employee['status'])){
		if(password_verify($password, $employee['password'])){
			
			//CHECK IF THE SESSION ID IS NOT EQUAL TO 0
			if ($employee['session_ID'] == 0) {
                // UPDATE THE SESSION ID TO DATABASE 
                mysqli_query($con, "UPDATE users SET session_ID = '".session_id()."' WHERE userid = '$employeeid'");
			
				$_SESSION['employeeid'] = $employeeid;
				$_SESSION['session_id'] = session_id();
				
				//set the cookies it has 30 days expiration
				setcookie('employeeid', $employeeid, time() + (86400 * 30), "/myxfin_st"); // 30 days expiration
				setcookie('session_id', session_id(), time() + (86400 * 30), "/myxfin_st"); // 30 days expiration
				setcookie('companyid', $selcompany, time() + (86400 * 30), "/myxfin_st"); // 30 days expiration

				$_SESSION['employeeid'] = $employee['id'];

				$_SESSION['employeename'] = $employee['name'];
				$_SESSION['employeefull'] = $employee['fullname'];
				$_SESSION['status'] = $employee['status'];
				$_SESSION['companyid'] = $selcompany;
				$_SESSION['timestamp']=time();
				
				$dateNow = date('Y-m-d h:i:s');
				$ipaddress = getHostByName(getHostName());
				//$hashedIP = better_crypt($ipaddress);
				$hashedIP = getMyIP();

				// $ipaddress = gethostbyaddr($_SERVER['REMOTE_ADDR']);

				// $sql = "SELECT b.logid, b.status, b.machine FROM `users_log`
				// WHERE Userid = '".$employee['id']."'
				// ORDER BY logid DESC LIMIT 1";
				
				$sql = "SELECT * FROM users_log WHERE Userid = '{$employee['id']}' ORDER BY logid DESC LIMIT 1";

				$result = mysqli_query($con, $sql);
				$status = true;
				$machine = $hashedIP;
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$status = $row['status'];
					$machine = $row['machine'];
					$_SESSION['loggedid'] = $row['logid'] ;
				}


				if(validStatus($status) || empty($status)){	
					//make the logged date to now for military time to avoid confusion
					$sql = "INSERT INTO `users_log` (`Userid`, `status`, `machine`, `logged_date`) VALUES ('".$employee['id']."', 'Online', '$hashedIP', NOW())";
					$result = mysqli_query($con, $sql);
					echo json_encode(valid30Days($employee['modify'], $employee['usertype']));

				} else {
					if(validIP($machine)){
						echo json_encode(valid30Days($employee['modify'], $employee['usertype']));
					} else {

						echo json_encode([
							'valid' => false,
							'errCode' => 'ERR_LOG',
							'errMsg' => "Your account was still " . $status
						]);
					}
				}

			//IF THE USER ALREADY LOG IN TO ANOTHER BROWSER
			} else {
				echo json_encode([
					'valid' => false,
					'errMsg' => "<strong>{$employeeid}</strong> is already logged in to another browser"
				]);
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
			'errMsg' => "Your account has been blocked! Contact your organization to reactivate your account"
		]);
	}
}}

?>