<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Customers_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$cItemNo = $_REQUEST['itmno'];
$company= $_SESSION['companyid'];

if( $_REQUEST['q']=="ACTIVE"){	

	//INACTIVATE ITEM
	mysqli_query($con,"Update `customers` set `cstatus`='INACTIVE' Where`compcode` ='$company' and `cempid`='$cItemNo'");	


	//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'UPDATED','CUSTOMERS','$compname','INACTIVATED ITEM')");

}

if( $_REQUEST['q']=="INACTIVE"){	

	//INACTIVATE ITEM
	mysqli_query($con,"Update `customers` set `cstatus`='ACTIVE' Where`compcode` ='$company' and `cempid`='$cItemNo'");	


	//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'UPDATED','CUSTOMERS','$compname','ACTIVATED ITEM')");

}

?>
<form action="Customers_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cItemNo;?>" />
</form>
<script>
    document.forms['frmpos'].submit();
</script>