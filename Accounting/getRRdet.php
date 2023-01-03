<?php
require_once "../Connection/connection_string.php";
$q = $_REQUEST["cCode"];
if (!$q) return;

$sql = "select * from receive where ctranno = '$q' and lapproved=1 and ctranno not in (Select crefno from apv_d)";
$rsd = mysqli_query($con,$sql);

while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
	$ngross = $rs['ngross'];
	$crem = $rs['cremarks'];
	$cust = $rs['ccode'];
	//$crem = "SAMPLE DESC";
	echo "$ngross,$crem,$cust";
}
?>
