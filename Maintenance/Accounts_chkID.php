<?php
require_once "../Connection/connection_string.php";


   $echeck="select * from accounts where cacctno='".$_POST['id']."'";
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
