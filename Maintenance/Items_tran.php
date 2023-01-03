<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$cItemNo = $_REQUEST['itmno'];
$company= $_SESSION['companyid'];

if( $_REQUEST['q']=="ACTIVE"){	

	//INACTIVATE ITEM
	mysqli_query($con,"Update `items` set `cstatus`='INACTIVE' Where`compcode` ='$company' and `cpartno`='$cItemNo'");	


	//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'UPDATED','ITEM','$compname','INACTIVATED ITEM')");

}

if( $_REQUEST['q']=="INACTIVE"){	

	//INACTIVATE ITEM
	mysqli_query($con,"Update `items` set `cstatus`='ACTIVE' Where`compcode` ='$company' and `cpartno`='$cItemNo'");	


	//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'UPDATED','ITEM','$compname','ACTIVATED ITEM')");

}

?>
<form action="Items_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cItemNo;?>" />
</form>
<script>
    document.forms['frmpos'].submit();
</script>