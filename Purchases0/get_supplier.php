<?php
if(!isset($_SESSION)){
session_start();
}

$company = $_SESSION['companyid'];
require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select * from suppliers where compcode='$company' and cname LIKE '%$q%'";
$rsd = mysqli_query($con,$sql);
while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
	$cid = $rs['ccode'];
	$cname = $rs['cname'];
	echo "$cname|$cid\n";
}
?><!--<p><font color="#000000">recognize </font></p>-->
