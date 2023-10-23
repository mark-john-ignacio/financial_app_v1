<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include('../../Connection/connection_string.php');

    $company = $_SESSION['companyid'];
    $date = date('Y-m-d');
    $item = $_REQUEST['item'];

    $sql = "select a.cpartno, a.cpartno as cscancode, a.citemdesc, 0 as nretailcost, 0 as npurchcost, a.cunit, a.cstatus, 0 as ltaxinc, a.cclass, 1 as nqty, a.cuserpic
            from items a 
            left join
                (
                    select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
                    From tblinventory a
                    right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
                    where a.compcode='$company' and  a.dcutdate <= '$date' and d.cstatus = 'ACTIVE'
                    group by a.citemno
                ) c on a.cpartno=c.citemno
            WHERE a.compcode='$company' and a.cstatus = 'ACTIVE' and a.ctradetype='Trade' and a.citemdesc LIKE '".$item."%' order by a.cclass, a.citemdesc";

    $query = mysqli_query($con, $sql);

    $data = [];
    while($row = $query -> fetch_assoc()){
        array_push($data, $row);
    }

    echo json_encode([
        'valid' => true,
        'data' => $data
    ]);