<?php
    if(!isset($_SESSION)){
		session_start();
	}

    include('../../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];
    $dateto = $_POST['dateto'];
    $datefrom = $_POST['datefrom'];

    $sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.acctno, A.ndebit, A.ncredit, D.cname, D.ctin , C.cremarks
        From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
        left join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno
        left join customers D on C.compcode=D.compcode and C.ccode=D.cempid
        Where A.compcode='$company' and A.cmodule='OR' and A.ddate between STR_TO_DATE('$dateto', '%m/%d/%Y') and STR_TO_DATE('$datefrom', '%m/%d/%Y') Order By A.ddate, A.ctranno, A.ndebit desc, A.ncredit desc";

    $result = mysqli_query($con, $sql);
    $data = [];

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        array_push($data, $row);
    }

    echo json_encode([
        'valid' => true,
        'data' => $data
    ]);