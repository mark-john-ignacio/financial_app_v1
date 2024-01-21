<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    include ('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];

    $sql = "SELECT a.*, b.remarks, b.label, DATE_FORMAT(b.deffective,\"%m/%d/%Y\") as deffective, DATE_FORMAT(b.ddue,\"%m/%d/%Y\") as ddue, b.matrix, c.citemdesc, b.approved, b.cancelled, d.cacctdesc, b.cacctcode
    FROM discountmatrix_t a
    LEFT JOIN discountmatrix b ON a.compcode = b.compcode AND a.tranno = b.tranno
    LEFT JOIN items c ON a.compcode = c.compcode AND a.itemno = c.cpartno
    LEFT JOIN accounts d ON b.compcode = d.compcode AND b.cacctcode = d.cacctid
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
    