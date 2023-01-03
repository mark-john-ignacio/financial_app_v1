<?php

include('../../Connection/connection_string.php');

$sqlhead = mysqli_query($con,"SELECT * FROM `transactions` Where cremarks='N' order by dcutdate, cnum, ddate");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	
	$ctyp = $row['ctype'];
	$cid = $row['ctranno'];
	$dte = $row['dcutdate'];
?>

 <form action="Recom_frame.php" name="frmsend" id="frmsend" method="post">
 	<input type="hidden" name="typ" id="typ" value="<?php echo $ctyp;?>" />
    <input type="hidden" name="id" id="id" value="<?php echo $cid;?>" />
    <input type="hidden" name="dte" id="dte" value="<?php echo $dte;?>" />
 </form>

 <script>
 	document.getElementById('frmsend').submit();
 </script>

<?php
}
else{
	
?>
 <script>
 	document.write("DONE");
 </script>
<?php
}
?>
