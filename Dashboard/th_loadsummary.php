<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];
    $pages = [];

    $sql = "SELECT pageid FROM users_access WHERE userid = '$employee' ";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        $pageid = $row['pageid'];
        array_push($pages, $pageid);
    }

    function Sales(){
        try{
            global $con, $company;
            $array = [];
    
            $sql = "SELECT b.cname as names, a.ctranno as tranno, a.dcutdate as dates, a.cremarks as remarks FROM sales a
            LEFT JOIN customers b ON a.compcode = b.compcode AND a.ccode = b.cempid
            WHERE a.compcode = '$company' ORDER BY a.dcutdate DESC LIMIT 10";
            $query = mysqli_query($con, $sql);
            while($row = $query -> fetch_assoc()){
                array_push($array, $row);
            }
        } catch(Exception $exc){
            return [
                "valid" => false,
                "msg" => $exc
            ];
        } finally {
            return [
                "valid" => true,
                "data" => $array
            ];
        }
        
    }
    
    function Purchase(){
        try {
            global $con, $company;
            $array = [];
    
            $sql = "SELECT b.cname as names, a.cpono as tranno, a.dneeded as dates, a.cremarks as remarks FROM purchase a
            LEFT JOIN suppliers b ON a.compcode = b.compcode AND a.ccode = b.ccode
            WHERE a.compcode = '$company' ORDER BY a.dneeded DESC LIMIT 10";

            if($query = mysqli_query($con, $sql)){
                while($row = $query -> fetch_assoc()){
                    array_push($array, $row);
                }
            } else {
                return [
                    "valid" => false,
                    "msg" => "False"
                ];
            }
            

        } catch (Exception $exc){
            return [
                "valid" => false,
                "msg" => $exc
            ];
        } finally {
            return [
                "valid" => true,
                "data" => $array
            ];
        }
    }

    if(in_array("DashboardSales.php", $pages)){
        echo json_encode(Sales());
    } elseif (in_array("DashboardPurchase.php", $pages)){
        echo json_encode(Purchase());
    }