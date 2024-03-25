<?php
if(!isset($_SESSION)){
	session_start();
}
include('../../../Connection/connection_string.php');

$company = $_SESSION['companyid'];

$sqlhead = mysqli_query($con,"select nid from apv_temp where compcode='$company' and crem = 'N'");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	
	$id = $row['nid'];
?>

 <form action="uploadtran_script.php" name="frmsend" id="frmsend" method="post">
 	<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
	 <input type="hidden" name="crid" id="crid" value="<?php echo $_REQUEST['crid'];?>" />
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