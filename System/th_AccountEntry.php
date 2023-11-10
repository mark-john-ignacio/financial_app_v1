<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include ("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

    $account = $_REQUEST["account"];
    $sql = "SELECT * FROM accounts WHERE compcode = '$company' AND ccategory = 'Liabilities' AND ctype = 'Details'";
    $query = mysqli_query($con, $sql);
    $liabilities = [];
    if(mysqli_num_rows($query) != 0) {  
        while($row = $query -> fetch_assoc()){
            array_push($liabilities, $row);
        }

        echo json_encode([
            'valid' => true,
            'data' => $liabilities
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "No Account has been found"
        ]);
    }