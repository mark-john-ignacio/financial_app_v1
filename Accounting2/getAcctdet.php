<?php
require_once "../Connection/connection_string.php";
$q = $_REQUEST["cCode"];
if (!$q) return;

$sql = "select * from accounts where cacctno = '$q'";
$rsd = mysqli_query($con,$sql);

while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
	$acctno = $rs['cacctno'];
	$acctdesc = $rs['cacctdesc'];
	//$crem = "SAMPLE DESC";
	echo "$acctdesc";
}
?>
