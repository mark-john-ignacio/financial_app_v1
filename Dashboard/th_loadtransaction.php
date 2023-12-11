<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];

    function Sales(){
        global $con, $company;
        $sales = [];
        $sql = "SELECT a.*, b.cname FROM receipt a
        LEFT JOIN customers b ON a.compcode = b.compcode AND a.ccode = b.cempid
        WHERE a.compcode = '$company' AND a.lapproved = 1 AND a.lcancelled = 0 AND a.lvoid = 0 ORDER BY a.dcutdate DESC LIMIT 10";

        if($query = mysqli_query($con, $sql)){
            while($row = $query -> fetch_assoc()){
                $json = [
                    'valid' => true,
                    'name' => $row['cname'],
                    'transaction' => $row['ctranno'],
                    'remarks' => $row['cparticulars'],
                    'date' => $row['dcutdate']
                ];
                array_push($sales, $json);
            }
        } else {
            return ;
        }
        
        return $sales;
    }

    function Purchase(){
        global $con, $company;
        $purchase = [];
        $sql = "SELECT * FROM paybill WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0 ORDER BY dcheckdate DESC LIMIT 10";

        if($query = mysqli_query($con, $sql)){
            while($row = $query -> fetch_assoc()){
                $json = [
                    'valid' => true,
                    'name' => $row['cpayee'],
                    'transaction' => $row['ctranno'],
                    'remarks' => $row['cremarks'],
                    'date' => $row['dcheckdate']
                ];
                array_push($purchase, $json);
            }
        } else {
            return ;
        }
        
        return $purchase;
    }

    $sql = "SELECT * FROM users_access WHERE userid = '$employee' ";
    $user = '';
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        switch($row['pageid']){
             case "DashboardSales.php":
                echo json_encode(Sales());
                break;
            case "DashboardPurchase.php":
                echo json_encode(Purchase());
                break;
        }
    }