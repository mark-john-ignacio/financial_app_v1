<?php

include('../Connection/connection_string.php');

$sqlhead = mysqli_query($con,"select * from sales where ctranno not in (Select ctranno from glactivity) and lapproved=1 order by ctranno");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	
	$citemno = $row['ctranno'];
	$qty = $row['dcutdate'];
	$cnt = mysqli_num_rows($sqlhead);
?>

 <form action="uploadtran_frame.php" name="frmsend" id="frmsend" method="post">
 	<input type="hidden" name="ctranno" id="ctranno" value="<?php echo $citemno;?>" />
    <input type="hidden" name="dcutdate" id="dcutdate" value="<?php echo $qty;?>" />
     <input type="hidden" name="cnt" id="cnt" value="<?php echo $cnt;?>" />
     <input type="hidden" name="type" id="type" value="SI" />
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