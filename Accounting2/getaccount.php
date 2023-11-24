<?php
require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select * from accounts where cacctdesc LIKE '%$q%'";
$rsd = mysqli_query($con,$sql);
while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
	$cid = $rs['cacctno'];
	$cname = $rs['cacctdesc'];
	echo "$cname|$cid\n";
}
?><!--<p><font color="#000000">recognize </font></p>-->
