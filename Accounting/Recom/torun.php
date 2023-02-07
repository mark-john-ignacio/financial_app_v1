<?php
if(!isset($_SESSION)){
	session_start();
}
include('../../Connection/connection_string.php');

$company = $_SESSION['companyid'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

<?php

$result=mysqli_query($con,"Select * From sales where compcode='$company' and lapproved=1");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	echo $row['ctranno'];
?>

	<script>
			$.ajax ({
								url: "../../include/th_toAcc.php",
								data: { tran: "<?=$row['ctranno']?>", type: "SI" },
								async: false,
								success: function( data ) {
									//alert(data.trim());
									if(data.trim()=="True"){
										document.write("OK<br>");
									}
									else{
										document.write("ERROR: "+itm+"<br>");		
									}
								}
							});
		</script>

<?php


}


?>
</body>
</html>


