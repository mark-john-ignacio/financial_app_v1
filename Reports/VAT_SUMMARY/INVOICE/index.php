<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $transaction = $_REQUEST['transaction'];
    $sales = [];

    $sql = "SELECT a.ctranno, a.creference, a.nqty, a.cunit, a.nprice, a.ndiscount, b.cterms, b.cremarks, c.chouseno, c.ccity, c.ctin, c.cname, d.cpartno,  d.citemdesc, e.cvatcode, e.cvatdesc FROM sales_t a
            LEFT JOIN sales b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
            LEFT JOIN customers c ON a.compcode = c.compcode AND a.ccode = c.ccode
            LEFT JOIN items d ON a.compcode = d.compcode AND a.citemno = d.cpartno 
            LEFT JOIN vatcode e ON a.compcode = e.compcode AND a.ctaxcode = e.cvatcode
            WHERE a.compcode = '$company' AND a.ctranno = '$transaction'";
    $query = mysqli_query($con, $sql);
    
    while($list = $query -> fetch_assoc()) :
        $json = [
            'item' => $list['cpartno'],
            'description' => $list['citemdesc'],
            'quantity' => $list['nqty'],
            'UOM' => $list['cunit'],
            'price' => $list['nprice'],
            'discount' => $list['ndiscount'],
            'tax' => $list['cvatdesc']
        ];
        array_push($sales, $list);
    endwhile;

    if(!empty($sale)) {
        echo json_encode([
            'valid' => true,
            'data' => $sales
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => ""
        ]);
    }