<?php
//$con = mysqli_connect("localhost:3306","root","Cmi@2015","coopsys");
$connect = new PDO("mysql:host=localhost:3306;dbname=myxfinraw", "root", "");

$con = mysqli_connect("localhost:3306","root","","myxfinraw");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to ur database: " . mysqli_connect_error();
  }
else{
}

//$con2 = mysqli_connect("10.90.4.16","root","MyCoop2018","myxfinraw");

// Check connection
//if (mysqli_connect_errno())
//  {
 //	 echo "Failed to connect to ur server: " . mysqli_connect_error();
//  }
//else{
	// echo "MERPTESTER OK";
//}

?>

