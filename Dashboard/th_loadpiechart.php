<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];
    
    function Sales() {
        global $company, $con;
    
        $groupings = groupings();
        $groups = $groupings['groups'];
        $label = $groupings['label'];
        $list = [];
    
        $sql = "SELECT a.*, b.cclass FROM sales_t a 
                LEFT JOIN items b ON a.compcode = b.compcode AND a.citemno = b.cpartno
                WHERE a.compcode = '$company' AND a.ctranno IN (
                    SELECT ctranno FROM sales 
                    WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0
                )";
    
        $query = mysqli_query($con, $sql);
    
        for ($i = 0; $i < count($groups); $i++) {
            $total = 0;
            $class = $groups[$i];
            $query->data_seek(0); // Reset the result pointer before each iteration
            while ($row = $query->fetch_assoc()) {
                $classificationn = $row['cclass'];
                if ($classificationn == $class) {
                    $total += 1;
                }
            }
    
            array_push($list, $total); // Push the total, not the list itself
        }

        
    
        return [
            "valid" => true,
            "label" => $label, // Use the $label variable
            "data" => ToPercentage($list) // Use the $list variable containing totals
        ];
    }

    function groupings(){
        global $company, $con;
        $groups = [];
        $label = [];

        $sql = "SELECT ccode, cdesc FROM groupings WHERE compcode = '$company' AND ctype = 'ITEMCLS'";
        $query = mysqli_query($con, $sql);
        while($list = $query -> fetch_assoc()) {
            array_push($groups, $list['ccode']);
            array_push($label, $list['cdesc']);
        }

        return [
            'label' => $label,
            'groups' => $groups
        ]; 
    }

    function user_access () {
        global $con, $employee;

        $sql = "SELECT pageid FROM users_access WHERE userid = '$employee'";
        $query = mysqli_query($con, $sql);

        $pages = [];
        while($row = $query -> fetch_assoc()) {
            array_push($pages, $row['pageid']);
        }

        return $pages;
    }

    function Months(){
        for ($i = 1; $i <= 12; $i++) {
            $month = DateTime::createFromFormat('!m', $i);
            $months[] = $month->format('F');
        }
        return $months;
    }

    function Purchase() {
        global $company, $con;
    
        // Assuming that groupings() is a valid function that returns the required data
        $groupings = groupings();
        $groups = $groupings['groups'];
        $label = $groupings['label'];
        $list = [];
    
        $sql = "SELECT a.*, b.cclass FROM purchase_t a
                LEFT JOIN items b ON a.compcode = b.compcode AND a.citemno = b.cpartno
                WHERE a.compcode = '$company' AND cpono IN (
                    SELECT cpono FROM purchase WHERE compcode = '$company' AND lapproved = 1 AND lcancelled = 0 AND lvoid = 0
                )";
    
        $query = mysqli_query($con, $sql);
    
        if (!$query) {
            return ["valid" => false, "msg" => mysqli_error($con)];
        }
    
        for ($i = 0; $i < count($groups); $i++) {
            $total = 0;
            $class = $groups[$i];
            $query->data_seek(0); // Reset the result pointer before each iteration
            while ($row = $query->fetch_assoc()) {
                $classification = $row['cclass'];
                if ($classification == $class) {
                    $total += 1;
                }
            }
    
            $list[] = $total; // Use [] for array push
        }
    
        return [
            "valid" => true,
            "label" => $label,
            "data" => ToPercentage($list)
        ];
    }

    // Convert To Percentage
    function ToPercentage( $array ){
        $total = TotalValue($array);

        for($i = 0; $i < count($array); $i++) {
            $value = floatval($array[$i]);
            $calculate = ($value / $total) * 100;
            $array[$i] = round($calculate, 2); 
        }

        return $array;
    }

    // Calculate All Values in Array
    function TotalValue ( $array ) {
        $total = 0;
        for($i = 0; $i < count($array); $i++) {
            $total += floatval($array[$i]);
        }

        return $total;
    }

    if( in_array( "DashboardSales.php", user_access() ) ) {
        echo json_encode(Sales());
    } else if ( in_array( "DashboardPurchase.php", user_access() ) ) {
        echo json_encode(Purchase());    
    }