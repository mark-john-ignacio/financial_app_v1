<?php 

if(!isset($_SESSION)){
    session_start();
}
$_SESSION['pageid'] = "CashBook.php";

include('../../Connection/connection_string.php');

$company = $_SESSION['companyid'];
$csalesno = $_REQUEST['tranno'];


$sqlhead = mysqli_query($con,"select a.*,b.citemdesc from dr_t a left join items b on a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno'");
$data = [];
while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
    array_push($data, $row);
}

echo json_encode([
    'valid' => true,
    'data' => $data
])
?>
