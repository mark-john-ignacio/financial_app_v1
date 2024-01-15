<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$compfr = $_POST['txtcopyfrom'];
	$compto = $_POST['txtcopyto'];
	$custcode = $_POST['txtcopycode'];

	$sql = "Select * from customers where compcode='$compfr' and cempid='$custcode'";
   	$result=mysqli_query($con,$sql);
	$customer = $result -> fetch_array(MYSQLI_ASSOC);

	$sql = "Select * from accounts where compcode='$compto' and cacctno='".$customer['cacctcodesales']."'";
   	$result=mysqli_query($con,$sql);

	if(mysqli_num_rows($result)>1){
		$caccounts = $result -> fetch_array(MYSQLI_ASSOC);
		$caccountsselected = $caccounts['cacctno'];
	}else{
		$caccountsselected = '';
	}
			              
	if (!mysqli_query($con, "INSERT INTO `customers`(`compcode`, `cempid`, `cname`, `ctradename`, `cacctcodesales`, `cacctcodetype`,`ccustomertype`, `ccustomerclass`, `cpricever`, `cvattype`, `cterms`, `ctin`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `cstatus`, `nlimit`, `csman`, `cGroup1`, `cGroup2`, `cGroup3`, `cGroup4`, `cGroup5`, `cGroup6`, `cGroup7`, `cGroup8`, `cGroup9`, `cGroup10`, `cdefaultcurrency`) Select '$compto', `cempid`, `cname`, `ctradename`, '$caccountsselected', `cacctcodetype`,`ccustomertype`, `ccustomerclass`, `cpricever`, `cvattype`, `cterms`, `ctin`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `cstatus`, `nlimit`, `csman`, `cGroup1`, `cGroup2`, `cGroup3`, `cGroup4`, `cGroup5`, `cGroup6`, `cGroup7`, `cGroup8`, `cGroup9`, `cGroup10`, `cdefaultcurrency` from customers where compcode='$compfr' cempid='$custcode'")) {
		echo "Error Contacts: ".mysqli_error($con);
	} else{
		$xcid = mysqli_insert_id($con);
	}

?>
