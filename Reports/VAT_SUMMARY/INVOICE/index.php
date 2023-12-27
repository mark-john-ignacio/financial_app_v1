<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $transaction = $_REQUEST['transaction'];
    $sales = [];

    $sql = "SELECT a.ctranno, a.creference, a.nqty, a.cunit, a.nprice, a.ndiscount, a.namount, b.ddate, b.dcutdate, b.cterms, b.cremarks, c.chouseno, c.ccity, c.ctin, c.cname, d.cpartno,  d.citemdesc, e.cvatcode, e.cvatdesc FROM sales_t a
            LEFT JOIN sales b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
            LEFT JOIN customers c ON a.compcode = c.compcode AND b.ccode = c.cempid
            LEFT JOIN items d ON a.compcode = d.compcode AND a.citemno = d.cpartno 
            LEFT JOIN vatcode e ON a.compcode = e.compcode AND a.ctaxcode = e.cvatcode
            WHERE a.compcode = '$company' AND a.ctranno = '$transaction'";
    $query = mysqli_query($con, $sql);
    
    while($list = $query -> fetch_assoc()) :
        $json = [
            'items' => $list['cpartno'],
            'description' => $list['citemdesc'],
            'quantity' => $list['nqty'],
            'UOM' => $list['cunit'],
            'price' => $list['nprice'],
            'discount' => $list['ndiscount'],
            'tax' => $list['cvatdesc'],
            'amount' => $list['namount']
        ];
        $customer = $list['cname'];
        $address = $list['chouseno'] . " " . $list['ccity'];
        $tin = $list['ctin'];
        $term = $list['cterms'];
        $notes = $list['cremarks'];
        $reference = $list['creference'];
        $transaction = $list['ctranno'];
        $due = date("F d, Y", strtotime($list['dcutdate']));
        $date = date("F d, Y", strtotime($list['ddate']));

        array_push($sales, $json);
    endwhile;

    if(!empty($sales)) {
        echo json_encode([
            'valid' => true,
            'data' => $sales,
            'customer' => $customer,
            'address' => $address,
            'tin' => $tin,
            'term' => $term,
            'reference' => $reference,
            'notes' => $notes,
            'transaction' => $transaction,
            'date' => $date,
            'due' => $due
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => ""
        ]);
    }