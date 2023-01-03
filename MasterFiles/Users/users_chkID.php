<?php
require_once "../../Connection/connection_string.php";


   $echeck="select * from users where Userid='".$_POST['id']."'";
   $echk=mysqli_query($con, $echeck);
   
   $ecount=mysqli_num_rows($echk);

  if($ecount!=0)
   {
      echo "User ID already exist!";
   }
  else{
	  echo "";
  }

?>
