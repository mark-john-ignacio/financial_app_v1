<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    include('Connection/connection_string.php');
    include('Model/helper.php');

    if(isset($_POST['email'])) {

        $useremail = $_POST['email'];

        $stmt = $con->prepare("SELECT * FROM users WHERE cemailadd = BINARY ?");
        $stmt->bind_param("s", $useremail);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $xp = ($row['Userid']) ?? "";

        if($xp != ""){
            echo "true";
        }else{
            echo "false";
        }
       

    }else{
        echo "false";
    }

?>