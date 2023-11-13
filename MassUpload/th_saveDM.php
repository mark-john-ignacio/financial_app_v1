<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");
    require_once("../Model/helper.php");
    $company = $_SESSION['companyid'];
    $duplicate = false;
    $isFinished = false;


    /**
     * Initiate Variable
     */
    $label = $_POST['label'];
    $description = $_POST['description'];
    $effect = date("Y-m-d", strtotime($_POST['effectdate']));
    $due = date("Y-m-d", strtotime($_POST['duedate']));

    $month = date('m');
    $year = date('y');

    $sql = "SELECT * FROM discountmatrix where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By `tranno` desc LIMIT 1";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query)==0) {
        $code = "DM".$month.$year."00000";
    } else {
        while($row = $query -> fetch_assoc()){
            $last = $row['tranno'];
        }
        
        
        if(substr($last,2,2) <> $month){
            $code = "DM".$month.$year."00000";
        }
        else{
            $baseno = intval(substr($last,6,5)) + 1;
            $zeros = 5 - strlen($baseno);
            $zeroadd = "";
            
            for($x = 1; $x <= $zeros; $x++){
                $zeroadd = $zeroadd."0";
            }
            
            $baseno = $zeroadd.$baseno;
            $code = "DM".$month.$year.$baseno;
        }
    }

    $excel_data = ExcelRead($_FILES);

    if(!empty($_FILES)){
        for($i = 1; $i < sizeof($excel_data); $i++){
            $data = $excel_data[$i];

            if($i === 1){
                $sql = "INSERT INTO discountmatrix (`compcode`, `tranno`, `remarks`, `label`, `deffective`, `ddue`, `status`, `ddate`, `approved`, `cancelled`) 
                                        VALUES('$company', '$code', '$description', '$label', '$effect', '$due', 'ACTIVE', NOW(), 0, 0)";
                $query = mysqli_query($con, $sql);
            }
            
            $sql = "INSERT INTO discountmatrix_t (`compcode`, `tranno`, `itemno`, `unit`, `discount`, `type`)
                                        VALUES('$company', '$code', '{$data[0]}', '{$data[1]}', '{$data[2]}', '{$data[3]}')";
            if(mysqli_query($con, $sql)){
                $isFinished = true;
            } else {
                $isFinished = false;
            }
                
            
        }

        if($isFinished) { 
            echo json_encode([
                "valid" => true,
                "msg" => "Successfully inserted"
            ]);
        } else { 
            deleteInserted();
            echo json_encode([
                "valid" => false,
                "msg" => "Inserting Transaction Failed"
            ]);
        }
    } else { 
        echo json_encode([
            "valid" => false,
            "msg" => "File not found! or File did not match the recommended File Template"
        ]);
    } 

    function deleteInserted(){
        global $con;
        global $company;
        global $code;
        $month = date('m');
        
        $sql = "DELETE FROM discountmatrix WHERE compcode = '$company' AND tranno = '$code' AND MONTH(ddate) = '$month'";
        mysqli_query($con, $sql);
        $sql = "DELETE FROM discountmatrix_t WHERE compcode = '$company' AND tranno = '$code'";
        mysqli_query($con, $sql);
    }