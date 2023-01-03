<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');
?>
<!DOCTYPE html>
<html>
<head>

	<title>COOPERATIVE SYSTEM</title>

<body>
<center>
<font face="Tahoma, Geneva, sans-serif"><B>GENERATING ACCOUNTING<br>ENTRIES</B>
<br>
<img src="../images/loader.gif" width="50" height="50">

<form name="frmapv" id="frmapv" method="POST" action="APV_putAccnt.php">
	<input type="hidden" name="rrno" id="rrno" value="<?php echo $_REQUEST["id"];?>">
    <input type="hidden" name="txtbx" id="txtbx" value="<?php echo $_REQUEST["nme"];?>">
    <input type="hidden" name="totamt" id="totamt" value="<?php echo $_REQUEST["amt"];?>">
</form>

<script>
	document.getElementById("frmapv").submit();
</script>
</center>
</body>