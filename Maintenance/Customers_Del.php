<?php
if(!isset($_SESSION)){
session_start();
}
//include('../Connection/connection_string.php');
//include('../Accounting/InsertToGL.php');
//include('../Inventory/InsertToInv.php');

require_once "../Connection/connection_string.php";
 $company = $_SESSION['companyid'];
 
 $c_id = $_REQUEST['id'];

//CHECK if id is existing

 $rescust = mysqli_query($con,"SELECT * FROM `customers` WHERE compcode='$company' and cempid='$c_id'");
 if (mysqli_num_rows($rescust)!=0) {


	//DELETE CUSTOMER
	if (!mysqli_query($con,"DELETE FROM customers WHERE compcode='$company' and cempid='$c_id'")) {
		printf("Errormessage2: %s\n", mysqli_error($con));
	}

?>
	Customer Successfully DELETED!

<?php	

 }
 
else{
		$chkSI = "NONE";

?>
<?php echo "INVALID CUSTOMER"?>
<?php
}
?>
