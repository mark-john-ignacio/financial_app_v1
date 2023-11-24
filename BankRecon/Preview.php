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
                    default => false
                };
                
                if($proceed == false) break;
            }
        } else {
            if(!$proceed) break;

            $date = $data[0];
            $refno = $data[1];
            $debit = $data[2];
            $credit = $data[3];
            $balance = $data[4];

            $sql = "INSERT INTO (`compcode`, `refno`, `credit`, `debit`, `balance`, `date`) VALUES ('$company', '$refno', '$credit', '$debit', '$balance', '$date')";
            mysqli_query($con, $sql);

        }
        
    }
    if(!$proceed){
        echo "No Record Found";
    } else {
        ?>
            <script>alert("successfully insert")</script>
        <?php
    }