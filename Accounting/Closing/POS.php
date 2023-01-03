<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

//POST RECORD
$company = $_SESSION['companyid'];
$queries = implode( "','", $_POST['chkTranNo'] );

mysqli_query($con,"Delete From sales_post");

				$sql = "select * from sales Where ctranno in ('$queries');";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				$csalesno = "";
				$dcutdate = "";	
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$csalesno = $row["ctranno"];
					$dcutdate = $row["dcutdate"];	
				
				mysqli_query($con,"Insert Into sales_post(compcode, csalesno, dcutdate, crem) Values ('$company','$csalesno','$dcutdate','N')");
				
				}
				
//if(!empty($_POST['chkTranNo'])) {
  //  foreach($_POST['chkTranNo'] as $check) {
           
		   

  //  }
//}
mysqli_close($con);


header("Location: POS_Del.php");
exit;
?>
