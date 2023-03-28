<?php
if(!isset($_SESSION)){
	session_start();
}

require_once "../../Connection/connection_string.php";


   $echeck="select * from accounts where compcode='".$_SESSION['companyid']."' and cacctid='".$_POST['id']."'";
   $echk=mysqli_query($con, $echeck);
   
   $ecount=mysqli_num_rows($echk);

  if($ecount!=0)
   {
      echo "Account No already exist!";
   }
  else{
	  echo "";
  }

?>
