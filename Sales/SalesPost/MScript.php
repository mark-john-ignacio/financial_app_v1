<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

//POST RECORD
$company = $_SESSION['companyid'];
$tranno = $_REQUEST["csalesno"];
$dates = $_REQUEST["dcutdate"];

$preparedby = $_SESSION['employeeid'];
$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

//mysqli_query($con,"Update sales set lapproved=1 where compcode='$company' and ctranno='$tranno'");

//mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	//values('$tranno','$preparedby',NOW(),'POSTED','SALES INVOICE','$compname','Post Record')");

$status = "Posted";

mysqli_query($con,"Update sales_post set crem='Y' where csalesno='$tranno'");

?>

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script>
$(document).ready(function() {
	//alert("YES");
			$.ajax ({
				dataType: "text",
				url: "../../include/th_toAcc.php",
				data: { tran: "<?php echo $tranno; ?>", type: "SI" },
				async: false,
				success: function( data ) {
				//	alert(data.trim());
					if(data.trim()=="True"){
						top.location.href = "POS_Del.php";
					}
					else{
						itmstat = data.trim();	
					}
				}
			});

});

</script>