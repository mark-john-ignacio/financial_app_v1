<?php

include('../Connection/connection_string.php');

$sqlhead = mysqli_query($con2,"select * from itemcost where crem = 'N'");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	
	$citemno = $row['citemno'];
	$qty = $row['nactual'];
	$cnt = mysqli_num_rows($sqlhead);
?>

 <form action="uploadtran_frame.php" name="frmsend" id="frmsend" method="post">
 	<input type="hidden" name="citemno" id="citemno" value="<?php echo $citemno;?>" />
    <input type="hidden" name="cnt" id="cnt" value="<?php echo $cnt;?>" />
    <input type="hidden" name="qty" id="qty" value="<?php echo $qty;?>" />
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