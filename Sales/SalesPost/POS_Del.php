<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

//POST RECORD
$company = $_SESSION['companyid'];
$csalesno = "";
$dcutdate = "";
$sqlhead = mysqli_query($con,"select * from sales_post where crem = 'N' order by csalesno");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		
		$csalesno = $row["csalesno"];
		$dcutdate = $row["dcutdate"];
	}
}

mysqli_close($con);


if ($csalesno!=""){
?>


<form name="frmact" id="frmact" action="POS_Frame.php" method="POST">
	<input name="csalesno" id="csalesno" value="<?php echo $csalesno;?>" />
    <input name="dcutdate" id="dcutdate" value="<?php echo $dcutdate;?>" />
</form>

<script>
	document.getElementById("frmact").submit();
</script>

<?php

}

else{
?>
<script>
	window.location.href = "BatchPost.php";
</script>

<?php
}
?>