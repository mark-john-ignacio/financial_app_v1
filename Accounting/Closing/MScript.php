<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['timestamp']=time();
include('../../Connection/connection_string.php');

//POST RECORD
$company = $_SESSION['companyid'];
$ctranno = $_REQUEST["ctranno"];
$ddate = $_REQUEST["ddate"];
$cmodule = $_REQUEST["cmodule"];

$preparedby = $_SESSION['employeeid'];
$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

//echo "Update trialbaltrans set cremarks='Y' where ctranno='$ctranno'";
mysqli_query($con,"Update trialbaltrans set cremarks='Y' where ctranno='$ctranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$ctranno','$preparedby',NOW(),'CLOSED','MONTHLY CLOSING','$compname','Close Record ".$cmodule."')");

?>

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script>
$(document).ready(function() {
	//alert("YES");
			$.ajax ({
				dataType: "text",
				url: "../../include/th_toCloseAcc.php",
				data: { tran: "<?php echo $ctranno; ?>", type: "<?php echo $cmodule; ?>" },
				async: false,
				success: function( data ) {
				//	alert(data.trim());
					if(data.trim()!="False"){
						location.href = "POS_Del.php";
					}
					else{
						document.write(data.trim());	
					}
				}
			});

});

</script>