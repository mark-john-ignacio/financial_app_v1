<?php
if(!isset($_SESSION)){
session_start();
}

$company = $_SESSION['companyid'];
require_once "../Connection/connection_string.php";
$q = strtolower($_GET["q"]);
if (!$q) return;


	$sql = "select A.*, B.cunit as pounit, B.nfactor from items A left join items_factor B on A.compcode=B.compcode and A.cpartno=B.cpartno where A.compcode='$company' and A.citemdesc LIKE '%$q%'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$cid = $rs['cpartno'];
		$cname = $rs['citemdesc'];
		$nprice = $rs['npurchcost'];
		$nfactor = $rs['nfactor'];
		$cunit = $rs['pounit'];
		
				if(is_null($nfactor)){
					$nfactor = 1;
					$cunit = $rs['cunit'];
				}
		
		$npricefin = $nprice * $nfactor;
		echo "$cname|$cid|$npricefin|$cunit|$nfactor\n";
	}



//echo $sql;

?>
