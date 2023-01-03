<?php
if(!isset($_SESSION)){
session_start();
}
$company = $_SESSION['companyid'];

require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;


	$sql = "select * from customers where compcode='$company' and cname LIKE '%$q%'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$cid = $rs['cempid'];
		$cname = $rs['cname'];
		echo "$cname|$cid\n";
	}



//echo $sql;

?>
