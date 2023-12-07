<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];
    // $datefrom = date("Y-m-d",strtotime($_REQUEST['from']));
    // $dateto = date("Y-m-d", strtotime($_REQUEST['to']));

    
    function Sales(){
        global $company, $datefrom, $dateto, $con;
        $sales = [];
        // Needed a date Query
        // $sql = "SELECT a.*, b.cname FROM receipt a
        // LEFT JOIN customers b ON a.compcode = b.compcode AND a.ccode = b.cempid
        // WHERE a.compcode = '$company' AND a.lapproved = 1 AND a.lcancelled = 0 AND a.lvoid = 0 AND (a.dcutdate BETWEEN '$datefrom' AND '$dateto')";

        $sql = "SELECT a.*, b.cname FROM receipt a
        LEFT JOIN customers b ON a.compcode = b.compcode AND a.ccode = b.cempid
        WHERE a.compcode = '$company' AND a.lapproved = 1 AND a.lcancelled = 0 AND a.lvoid = 0 AND YEAR(a.ddate) = YEAR(CURDATE())";
        $query = mysqli_query($con, $sql);
        $receipt = mysqli_num_rows($query);
        
        $cost = 0;

        while($row = $query -> fetch_assoc()){
            array_push($sales, $row['cname']);
            $cost += $row['napplied'];
        }

        $payor = match(count($sales)){
            0 => "N/A",
            default => Ranking_System($sales)
        };

        return [
            'valid' => true,
            'label' => "Sales",
            'total' => $receipt,
            'cost' => number_format($cost, 2),
            'best_rank' => $payor,
        ];
    }

    function Purchase(){
        global $company, $datefrom, $dateto, $con;
        $purchase = [];
        // $sql = "SELECT * FROM paybill WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0 AND (dcheckdate BETWEEN '$datefrom' AND '$dateto')";
        $sql = "SELECT * FROM paybill WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0 AND YEAR(ddate) = YEAR(CURDATE())";
        $query = mysqli_query($con, $sql);
        $paybill = mysqli_num_rows($query);

        $cost = 0;

        while($row = $query -> fetch_assoc()){
            array_push($purchase, $row['cpayee']);
            $cost += $row['npaid'];
        }

        $payee = match(count($purchase)){
            0 => "N/A",
            default => Ranking_System($purchase)
        };

        return [
            'valid' => true,
            'label' => "Purchase",
            'total' => $paybill,
            'cost' => number_format($cost,2),
            'best_rank' => $payee, 
        ];
    }
    

    function Ranking_System($client){
        $counts = array_count_values($client);
        $maxCount = max($counts);
        $maxValues = array_keys($counts, $maxCount);
        return implode('', $maxValues);
    }

    // $sql = "SELECT SUM(nretailout) as cost FROM tblinventory WHERE compcode = '$company' AND (dcutdate BETWEEN '$datefrom' AND '$dateto') ";
    // $query = mysqli_query($con, $sql);
    // $row = $query -> fetch_assoc();
    // $cost += floatval($row['cost']);
    

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