<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];

    // $now = date("Y-m-d", strtotime("08/12/2023"));
    $now = date("Y-m-d");

    function getWeek($date){
        $week = [];
        $ts = strtotime($date);
        $dow = date('w', $ts);
        $offset = $dow - 1;
        if ($offset < 0) {
            $offset = 6;
        }
        $ts = $ts - $offset*86400;
        for ($i = 0; $i < 7; $i++, $ts += 86400){
            // array_push($week, date("m/d/Y l", $ts));
            array_push($week, date("m/d/Y", $ts));
        }

        return $week;
    }

    function SalesWeekData($date){
        global $company, $con;
        $now = getWeek($date);
        $amounts = [];
        for($i = 0; $i < count($now); $i++){
            $today = date("Y-m-d", strtotime($now[$i]));
            $cost = 0;

            $sql = "SELECT a.napplied FROM receipt a
                WHERE a.compcode = '$company' AND a.lapproved = 1 AND a.lcancelled = 0 AND a.lvoid = 0 AND a.dcutdate = '$today'";
            $query = mysqli_query($con, $sql);
            
            while($row = $query -> fetch_assoc()){
                $cost += floatval($row['napplied']);
            }
            array_push($amounts, $cost);
        }

        return $amounts;
    }

    function PurchaseWeekData($date){
        global $company, $con;
        $now = getWeek($date);
        $amounts = [];

        for($i = 0; $i < count($now); $i++){
            $today = date("Y-m-d", strtotime($now[$i]));
            $cost = 0;

            $sql = "SELECT npaid as total FROM paybill WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0 AND dcheckdate = '$today'";
            $query = mysqli_query($con, $sql);

            while($row = $query -> fetch_assoc()){
                $cost += floatval($row['npaid']);
            }
            array_push($amounts, $cost);
        }
        return $amounts;
    }
    
    

    $sql = "SELECT * FROM users_access WHERE userid = '$employee' ";
    $user = '';
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        switch($row['pageid']){
             case "DashboardSales.php":
                echo json_encode([
                    'week' => getWeek($now),    
                    'values' => SalesWeekData($now)
                ]);
                break;
            case "DashboardPurchase.php":
                echo json_encode([
                    'week' => getWeek($now),    
                    'values' => PurchaseWeekData($now)
                ]);
                break;
        }
    }