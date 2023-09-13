<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once "../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $supplier = $_GET['name'];
    $trantype = intval($_GET['trantype']);
    @$dateYear = $_GET['year'];

    $sql = "select ctranno, cpayee, lapproved, YEAR(ddate) as Ddate
    from paybill 
    where compcode='$company' and cpayee = '$supplier' and lapproved = '$trantype' and YEAR(ddate) in ('$dateYear') 
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
            'date' => $row['Ddate'],
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