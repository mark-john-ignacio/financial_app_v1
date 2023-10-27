<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
		 
	$item = $_REQUEST['item'];
	$unit = $_REQUEST['unit'];
	$date = $_REQUEST['date'];
	


	$sql = "SELECT A.discount, A.type
    from discountmatrix_t A 
    left join discountmatrix B on A.compcode=B.compcode and A.tranno=B.tranno
	where A.compcode='$company' and A.itemno='$item' and A.unit='$unit' and B.approved = 1 and B.deffective <=  STR_TO_DATE('$date', '%m/%d/%Y') AND B.ddue >=  STR_TO_DATE('$date', '%m/%d/%Y')
    ORDER BY B.deffective DESC";
	
	//echo $sql;
	
	$result = mysqli_query ($con, $sql);

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            echo json_encode([
                'valid' => true,
                'data' => $row['discount'],
                'type' => $row['type']
            ]);
		}
	}
	else{
        echo json_encode([
            'valid' => false,
            'data' => 0,
            'type' => "PRICE"
        ]);
	}

?>
