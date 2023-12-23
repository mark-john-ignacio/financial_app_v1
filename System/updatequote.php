<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
	$compname = php_uname('n');
    $employee = $_SESSION['employeeid'];
    $code = $_POST['code'];
    $description = $_POST['description'];
    
    $sql = "SELECT * FROM parameters WHERE compcode = '$company' AND ccode = '$code'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) > 0) {
        $sql = "UPDATE parameters SET `cdesc` = '$description' WHERE `compcode` = '$company' and `ccode` = '$code'";
        if(mysqli_query($con, $sql)) {
            $sql = "INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
            values('$company','$code','$employee',NOW(),'INSERTED','".$description."','$compname','Inserted New Record')";
            mysqli_query($con, $sql);
            
            echo json_encode([
                'valid' => true,
                'msg' => "Update Remarks Successful"
            ]);
        }
    } else {
        $sql = "INSERT INTO parameters (`compcode`,`ccode`,`cdesc`,`norder`) VALUES ('$company', '$code', '$description', 1)";
        
        if(mysqli_query($con, $sql)) {
            $sql = "INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
            values('$company','$code','$employee',NOW(),'INSERTED','".$description."','$compname','Inserted New Record')";
            mysqli_query($con, $sql);
            
            echo json_encode([
                'valid' => true,
                'msg' => "Update Remarks Successful"
            ]);
        }
    }