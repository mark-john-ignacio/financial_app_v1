<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");
	
	$sql = "select a.cpartno, a.cscancode, a.citemdesc, a.nretailcost, a.npurchcost, a.ndiscount, a.cunit, a.ltaxinc, a.cstatus, ifnull(c.nqty,0) as nqty
			from items a 
			left join
				(
					select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
					From tblinventory a
					right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
					where a.compcode='$company' and  a.dcutdate <= '$date1' and d.cstatus = 'ACTIVE'
					group by a.citemno
				 ) c on a.cpartno=c.citemno
			WHERE a.compcode='$company' and a.citemdesc LIKE '%".$_REQUEST['query']."%'";

	$rsd = mysqli_query($con,$sql);
		
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		
	  if((float)$rs['nqty']>0){	
		$json['id'] = $rs['cpartno'];
		$json['value'] = $rs['citemdesc'];
		$json['name'] = $rs['citemdesc'];
		$json['nprice'] = $rs['nretailcost'];
		$json['cunit'] = $rs['cunit'];
		$json['nuprice'] = $rs['npurchcost'];
		$json['ndisc'] = $rs['ndiscount'];
		$json['nbal'] = $rs['nqty'];
		$json['cstatus'] = $rs['cstatus'];
		$json2[] = $json;
	  }
	
	}
	
	echo json_encode($json2);



//echo $sql;

?>
