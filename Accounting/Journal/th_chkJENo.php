<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
   $echeck="select * from journal where compcode='$company' and ctranno='".$_POST['id']."'";
   $echk=mysqli_query($con, $echeck);
   
   $ecount=mysqli_num_rows($echk);

  if($ecount!=0)
   {
      echo "Transaction number already exist!";
   }
  else{
	  echo "";
  }

?>
