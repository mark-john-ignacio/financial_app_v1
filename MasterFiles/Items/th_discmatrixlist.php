<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    include ('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];

    $sql = "SELECT a.*, b.remarks, b.label, b.deffective, b.ddue, b.matrix, c.citemdesc 
    FROM discountmatrix_t a
    LEFT JOIN discountmatrix b ON a.compcode = b.compcode AND a.tranno = b.tranno
    LEFT JOIN items c ON a.compcode = c.compcode AND a.itemno = c.cpartno
    WHERE a.compcode = '$company' AND a.tranno = '$tranno'";

    $data = [];
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0) {
        while($row = $query -> fetch_assoc()){
            array_push($data, $row);
        }

        echo json_encode([
            'valid' => true,
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "No Record"
        ]); 
    }
    