<?php
    if(!isset($_SESSION)){
        session_start();
    }
    require_once "../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $supplier = $_GET['name'];
    $trantype = intval($_GET['trantype']);
    $datefrom = $_GET['datefrom'];
    $dateto = $_GET['dateto'];

    $qrypos = "";
    if($trantype!=""){
        $qrypos = " and lapproved = '$trantype'";
    }

    $sql = "select cpayee, ctranno, lapproved, ddate
        from paybill 
        where compcode='$company' and ccode = '$supplier' ".$qrypos." and ddate BETWEEN  '$datefrom' and '$dateto'
        and ctranno in (Select c tranno from paybill_t where newtamt > 0)
        order by dtrandate DESC";

    @$arr = array();
    $result = mysqli_query($con, $sql); 

    if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $approve = "UnPosted";
        if($row['lapproved'] != 0){
            $approve = "Posted";
        } 
        $arr['paybill'] = array(
            'ctranno' => $row['ctranno'],
            'supplier' => $row['cpayee'],
            'date' => $row['ddate'],
            'approve' => $approve
        );
        
        $json2[] = $arr['paybill'];
    }

    if (count($arr) == 0){
        $json['msg'] = "NO";
			
        $json2[] = $json;
    }
        echo json_encode($json2);
    
?>