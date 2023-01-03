<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Suppliers_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$cItemNo = $_REQUEST['itmno'];
$company= $_SESSION['companyid'];

if( $_REQUEST['q']=="ACTIVE"){	

	//INACTIVATE ITEM
	mysqli_query($con,"Update `suppliers` set `cstatus`='INACTIVE' Where`compcode` ='$company' and `ccode`='$cItemNo'");	


	//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'UPDATED','SUPPLIERS','$compname','INACTIVATED')");

}

if( $_REQUEST['q']=="INACTIVE"){	

	//INACTIVATE ITEM
	mysqli_query($con,"Update `suppliers` set `cstatus`='ACTIVE' Where`compcode` ='$company' and `ccode`='$cItemNo'");	


	//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'UPDATED','SUPPLIERS','$compname','ACTIVATED')");

}

?>
<form action="Suppliers_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cItemNo;?>" />
</form>
<script>
    document.forms['frmpos'].submit();
</script>