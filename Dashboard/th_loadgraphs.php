<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];

    // $now = date("Y-m-d", strtotime("08/12/2023"));
    $year = $_POST['year'];
    $Periodicals = $_POST['Periodicals'];
    $today = $_POST['days'];
    // $today = date("m-d");
    $now = date("Y-m-d", strtotime("$year-$today"));

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

    function Months(){
        for ($i = 1; $i <= 12; $i++) {
            $month = DateTime::createFromFormat('!m', $i);
            $months[] = $month->format('F');
        }
        return $months;
    }

    function SalesWeekData(){
        global $company, $con, $Periodicals, $now, $year;
        $Period = $Periodicals != "weekly" ? Months() : getWeek($now);
        $amounts = [];
        for($i = 0; $i < count($Period); $i++){
            $today = date("Y-m-d", strtotime($Period[$i]));
            $cost = 0;

            $sql = match($Periodicals) {
                "weekly" => "SELECT a.napplied FROM receipt a WHERE a.compcode = '$company' AND a.lapproved = 1 AND a.lcancelled = 0 AND a.lvoid = 0 AND STR_TO_DATE(a.dcutdate, '%Y-%m-%d') = '$today' AND YEAR(a.dcutdate) = $year",
                "monthly" => "SELECT a.napplied FROM receipt a WHERE a.compcode = '$company' AND a.lapproved = 1 AND a.lcancelled = 0 AND a.lvoid = 0 AND MONTH(a.dcutdate) = MONTH('$today') AND YEAR(a.dcutdate) = $year"
            };
            
            $query = mysqli_query($con, $sql);
            
            while($row = $query -> fetch_assoc()){
                $cost += round($row['napplied'], 2);
            }
            array_push($amounts, $cost);
        }

        return $amounts;
    }

    function PurchaseWeekData($date){
        global $company, $con, $Periodicals, $now, $year;
        // $now = getWeek($date);
        $Period = $Periodicals != "weekly" ? Months() : getWeek($now);
        $amounts = [];

        for($i = 0; $i < count($Period); $i++){
            $today = date("Y-m-d", strtotime($Period[$i]));
            $cost = 0;
            
            $sql = match($Periodicals) {
                "weekly" => "SELECT npaid as total FROM paybill WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0 AND STR_TO_DATE(dcheckdate, '%Y-%m-%d')= '$today' AND YEAR(dcheckdate) = $year",
                "monthly" => "SELECT npaid as total FROM paybill WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0 AND MONTH(dcheckdate) = MONTH('$today') AND YEAR(dcheckdate) = $year"
            };

            $query = mysqli_query($con, $sql);
            while($row = $query -> fetch_assoc()){
                $cost += round($row['total'], 2);
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
                    // 'week' => getWeek($now),  
                    'Periodicals' => $Periodicals != "weekly" ? Months() : getWeek($now),  
                    'values' => SalesWeekData($now)
                ]);
                break;
            case "DashboardPurchase.php":
                echo json_encode([
                    // 'week' => getWeek($now),
                    'Periodicals' => $Periodicals != "weekly" ? Months() : getWeek($now),  
                    'values' => PurchaseWeekData($now)
                ]);
                break;
        }
    }