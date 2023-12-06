<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    require_once "../Model/helper.php";
    $company = $_SESSION['companyid'];

    $date_range = $_POST['range'];
    $bank = $_POST['bank'];
    
    $excel_data = ExcelRead($_FILES);
    
    $sql = "SELECT * FROM ";
    
    for($i = 0; $i < count($excel_data); $i++){
        $data = $excel_data[$i];
        
        if($i == 0){
            //Excel Checker Header
            for($j = 0; $j < count($data); $j++){
                $proceed = match(onlyString($data[$j])){
                    "DATE" => true,
                    "ReferenceNo" => true,
                    "DEBIT" => true,
                    "CREDIT" => true,
                    "BALANCE" => true,
                    "Name" => true,
                    default => false
                };
                
                if($proceed == false) break;
            }
        } else {
            if(!$proceed) break;

            $date = $data[0];
            $refno = $data[2];
            $debit = floatval($data[3]);
            $credit = floatval($data[4]);
            $balance = floatval($data[5]);

            $sql = "INSERT INTO paycheck (`compcode`, ``,`refno`, `credit`, `debit`, `balance`, `date`) VALUES ('$company', '$refno', '$credit', '$debit', '$balance', '$date')";
            mysqli_query($con, $sql);
        }
        
    }
    if(!$proceed){
        echo "This template wasn't compatible. ";
    } else {
        ?>
            <script>alert("successfully insert")</script>
        <?php
    }